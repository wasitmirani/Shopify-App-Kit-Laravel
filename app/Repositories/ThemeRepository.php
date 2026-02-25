<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\ThemeRepositoryInterface;

use function array_walk;
use function auth;
use function config;
use function json_decode;

class ThemeRepository implements ThemeRepositoryInterface
{
    /**
     * Retrive list of tutorial.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function activateExtension()
    {
        $response = ['enabled' => false, 'theme_id' => null];
        $user = auth()->user();
        $main_theme_id = $user->main_theme_id;
        if(!$main_theme_id){
            $main_theme = $user->api()->rest('GET', '/admin/themes.json', ['role' => 'main']);

            if (!isset($main_theme['body']['themes'][0])) {
                \Log::error('No Active Theme Found');
                return $response;
            }

            $main_theme_id = @$main_theme['body']['themes'][0]['id'];
        }
        $response['theme_id'] = $main_theme_id;

        // Cache the extension check for 5 minutes to avoid repeated API calls
        $cacheKey = "theme_extension_check_{$user->id}_{$main_theme_id}";
        $cachedResult = \Cache::get($cacheKey);
        if ($cachedResult !== null) {
            return $cachedResult;
        }

        $theme_asset = $user->api()->rest('GET', "/admin/themes/{$main_theme_id}/assets.json", ['asset[key]' => 'config/settings_data.json']);

        // Check if the asset actually exists (new themes may not have settings_data.json)
        if (!isset($theme_asset['body']['asset']) || $theme_asset['body']['asset'] === null) {
            \Log::info('Theme extension check: settings_data.json not found (theme may be new/uncustomized)', [
                'theme_id' => $main_theme_id,
                'shop' => $user->name,
                'hint' => 'Customize the theme in Shopify editor to enable the app extension',
            ]);
            \Cache::put($cacheKey, $response, now()->addMinutes(5));
            return $response;
        }

        $config_data = json_decode($theme_asset['body']['asset']['value'], true);
        if (!is_array($config_data)) {
            \Log::warning('Failed to decode theme settings_data.json', [
                'theme_id' => $main_theme_id,
                'shop' => $user->name,
            ]);
            \Cache::put($cacheKey, $response, now()->addMinutes(5));
            return $response;
        }

        $embed_block_name = config('shopify-app.embed_block_name');
        $embed_block_uuid = config('shopify-app.embed_block_id');
        // Try to extract a valid UUID from the configured value in case the .env
        // contains extra characters (some environments accidentally append
        // additional data). Use the first UUID-like match if present.
        if (!empty($embed_block_uuid)) {
            if (preg_match('/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/i', $embed_block_uuid, $m)) {
                if ($m[0] !== $embed_block_uuid) {
                    \Log::warning('Theme extension: normalizing embed UUID from config');
                }
                $embed_block_uuid = $m[0];
            }
        }
        $app_handle = config('shopify-app.theme_extension_app_handle');
        $block_name_alt = config('shopify-app.theme_extension_block_name_alt');

        if (empty($embed_block_uuid)) {
            \Log::warning('Theme extension: SHOPIFY_ICONITO_APP_EXTENSION_ID is empty in .env');
            return $response;
        }

        $expectedTypes = [];
        if (!empty($app_handle)) {
            // Primary expectation includes the UUID when available.
            if (!empty($embed_block_uuid)) {
                $expectedTypes[] = "shopify://apps/{$app_handle}/blocks/{$embed_block_name}/" . $embed_block_uuid;
                if (!empty($block_name_alt)) {
                    $expectedTypes[] = "shopify://apps/{$app_handle}/blocks/{$block_name_alt}/" . $embed_block_uuid;
                }
            }
            // Also allow matching by block prefix (handle + block name) as a fallback
            // when the UUID is missing or not present in the theme types exactly.
            $expectedTypes[] = "shopify://apps/{$app_handle}/blocks/{$embed_block_name}";
            if (!empty($block_name_alt)) {
                $expectedTypes[] = "shopify://apps/{$app_handle}/blocks/{$block_name_alt}";
            }
        }

        $uuidNormalized = str_replace('-', '', $embed_block_uuid);

        $blocks = $config_data['current']['blocks'] ?? [];
        $appEmbeds = $config_data['current']['app_embeds'] ?? [];
        $blocks = is_array($blocks) ? $blocks : [];
        $appEmbeds = is_array($appEmbeds) ? $appEmbeds : [];
        $blocksList = array_values(array_merge($blocks, $appEmbeds));

        if (empty($blocksList)) {
            return $response;
        }

        if (config('app.debug')) {
            $blockTypes = array_map(static fn($b) => is_array($b) ? ($b['type'] ?? null) : null, $blocksList);
            \Log::debug('Theme extension check', [
                'expected_types' => $expectedTypes,
                'embed_block_uuid' => $embed_block_uuid,
                'block_types_in_theme' => array_slice(array_filter($blockTypes), 0, 20),
            ]);
        }

        $status = false;
        foreach ($blocksList as $block) {
            if (!is_array($block)) {
                continue;
            }
            $type = $block['type'] ?? '';
            $disabled = !empty($block['disabled']);
            if ($expectedTypes && (in_array($type, $expectedTypes, true) || array_reduce($expectedTypes, static fn($carry, $exp) => $carry || str_starts_with($type, $exp), false)) && !$disabled) {
                $status = true;
                break;
            }
            if ($type !== '' && !$disabled && (str_contains($type, $embed_block_uuid) || str_contains(str_replace('-', '', $type), $uuidNormalized))) {
                $status = true;
                break;
            }
        }

        $response['enabled'] = $status;

        // Cache the successful check result for 5 minutes
        \Cache::put($cacheKey, $response, now()->addMinutes(5));

        return $response;
    }
}
