<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckThemeExtenstionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    /**
     * Create a new job instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $enabled = false;
        $main_theme = $this->user->api()->rest('GET', '/admin/themes.json', ['role' => 'main']);

        if (!isset($main_theme['body']['themes'][0])) {
            throw new \Exception('No Active Theme Found');
        }

        $main_theme_id = @$main_theme['body']['themes'][0]['id'];

        $theme_asset = $this->user->api()->rest('GET', "/admin/themes/{$main_theme_id}/assets.json", ['asset[key]' => 'config/settings_data.json']);

        if (!isset($theme_asset['body']['asset']['key'])) {
            throw new \Exception('No Setting Data Config File Found In Theme.');
        }

        $config_data = json_decode($theme_asset['body']['asset']['value'], true);

        $embed_block_name = config('shopify-app.embed_block_name');
        $embed_block_uuid = config('shopify-app.embed_block_id');
        $app_handle = config('shopify-app.theme_extension_app_handle');
        $block_name_alt = config('shopify-app.theme_extension_block_name_alt');

        if (empty($embed_block_uuid)) {
            $enabled = false;
        } else {
            $expectedTypes = [];
            if (!empty($app_handle)) {
                $expectedTypes[] = "shopify://apps/{$app_handle}/blocks/{$embed_block_name}/" . $embed_block_uuid;
                if (!empty($block_name_alt)) {
                    $expectedTypes[] = "shopify://apps/{$app_handle}/blocks/{$block_name_alt}/" . $embed_block_uuid;
                }
            }

            $uuidNormalized = str_replace('-', '', $embed_block_uuid);
            $blocks = $config_data['current']['blocks'] ?? [];
            $appEmbeds = $config_data['current']['app_embeds'] ?? [];
            $blocks = is_array($blocks) ? $blocks : [];
            $appEmbeds = is_array($appEmbeds) ? $appEmbeds : [];
            $blocksList = array_values(array_merge($blocks, $appEmbeds));

            $status = false;
            foreach ($blocksList as $block) {
                if (!is_array($block)) {
                    continue;
                }
                $type = $block['type'] ?? '';
                $disabled = !empty($block['disabled']);
                if ($expectedTypes && in_array($type, $expectedTypes, true) && !$disabled) {
                    $status = true;
                    break;
                }
                if ($type !== '' && !$disabled && (str_contains($type, $embed_block_uuid) || str_contains(str_replace('-', '', $type), $uuidNormalized))) {
                    $status = true;
                    break;
                }
            }
            $enabled = $status;
        }


        if ($enabled) {
            $this->user->update(['is_extension_enabled' => true, 'extension_enabled_at' => now()]);
        }else{
            $this->user->update(['is_extension_enabled' => false, 'extension_enabled_at' => null]);
        }
    }
}
