<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

use function array_splice;
use function count;
use function in_array;
use function info;
use function is_array;
use function is_object;
use function json_decode;
use function json_encode;
use function preg_replace;

class CreateSectionsInShopifyJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Determines the job will not time out.
     *
     * @var int
     */
    public $timeout = 0;

    /**
     * @var User
     */
    private $user;

    /**
     * Create a new job instance.
     *
     * @param mixed $user
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
        info('CreateSectionsInShopifyJob handle');
        if (!$this->user) {
            throw new \Exception('User not found while creating sections in shopify');
        }

        $main_theme = $this->user->api()->rest('GET', '/admin/themes.json', ['role' => 'main']);

        if (!isset($main_theme['body']['themes'][0])) {
            throw new \Exception('No Active Theme Found');
        }

        $main_theme_id = @$main_theme['body']['themes'][0]['id'];
        User::where('id', $this->user->id)->update(['main_theme_id' => $main_theme_id]);

        $this->createIndexPageSection($main_theme_id);
        $this->createCartPageSection($main_theme_id);
        $this->createHeaderSection($main_theme_id);
        $this->createFooterSection($main_theme_id);
    }

    public function createIndexPageSection($main_theme_id)
    {
        info('CREATING SECTION FOR INDEX PAGE');
        $theme_asset = $this->user->api()->rest('GET', "/admin/themes/{$main_theme_id}/assets.json", ['asset[key]' => 'sections/iconito_app_index.liquid']);

        if (!isset($theme_asset['body']['asset']['key']) && $theme_asset['status'] === 404) {
            info('iconito_app_index.liquid does not exist, will create for store: ' . $this->user->name);
            $section = [
                'asset' => [
                    'key' => 'sections/iconito_app_index.liquid',
                    'value' => <<<'EOF'
                    <div id="render_iconito_block_index"></div>
                    {% schema %}
                      {
                        "name": "Iconito Section Index",
                        "settings": [],
                        "blocks": [
                          {
                            "type": "@app"
                          }
                        ],
                        "presets": [
                          {
                            "name": "App wrapper"
                          }
                        ]
                      }
                    {% endschema %}
                    EOF
                ],
            ];

            $this->user->api()->rest('PUT', "/admin/themes/{$main_theme_id}/assets.json", $section);
        }

        $theme_asset = $this->user->api()->rest('GET', "/admin/themes/{$main_theme_id}/assets.json", ['asset[key]' => 'templates/index.json']);

        if (!isset($theme_asset['body']['asset']['key'])) {
            info('Not Found: templates/index.json');

            return;
        }
        $value = json_decode(@$theme_asset['body']['asset']['value'], true);

        if (!in_array('iconito_app_index', [$value['order']], true)) {
            info('Section not added, will add');

            $value['sections']['iconito_app_index'] = [
                'type' => 'iconito_app_index',
            ];

            array_splice($value['order'], 1, 0, 'iconito_app_index');
            $value = $this->convertEmptyArrayToObject($value);
            $asset = ['asset' => ['key' => 'templates/index.json', 'value' => json_encode($value)]];
            $this->user->api()->rest('PUT', "/admin/themes/{$main_theme_id}/assets.json", $asset);
        }
        info('SECTION ADDED FOR INDEX PAGE');
    }

    public function createCartPageSection($main_theme_id)
    {
        info('CREATING SECTION FOR CART PAGE');
        $theme_asset = $this->user->api()->rest('GET', "/admin/themes/{$main_theme_id}/assets.json", ['asset[key]' => 'sections/iconito_app_cart.liquid']);

        if (!isset($theme_asset['body']['asset']['key']) && $theme_asset['status'] === 404) {
            info('iconito_app_cart.liquid does not exist, will create for store: ' . $this->user->name);
            $section = [
                'asset' => [
                    'key' => 'sections/iconito_app_cart.liquid',
                    'value' => <<<'EOF'
                    <div id="render_iconito_block_cart"></div>
                    {% schema %}
                      {
                        "name": "Iconito Section Cart",
                        "settings": [],
                        "blocks": [
                          {
                            "type": "@app"
                          }
                        ],
                        "presets": [
                          {
                            "name": "App wrapper"
                          }
                        ]
                      }
                    {% endschema %}
                    EOF
                ],
            ];

            $this->user->api()->rest('PUT', "/admin/themes/{$main_theme_id}/assets.json", $section);
        }

        $theme_asset = $this->user->api()->rest('GET', "/admin/themes/{$main_theme_id}/assets.json", ['asset[key]' => 'templates/cart.json']);

        if (!isset($theme_asset['body']['asset']['key'])) {
            info('Not Found: templates/cart.json');

            return;
        }
        $value = json_decode(@$theme_asset['body']['asset']['value'], true);

        if (!in_array('iconito_app_cart', [$value['order']], true)) {
            info('Section not added, will add');

            $value['sections']['iconito_app_cart'] = [
                'type' => 'iconito_app_cart',
            ];
            $position = $value['order'] >= 2 ? 3 : count($value['order']) + 1;
            array_splice($value['order'], $position, 0, 'iconito_app_cart');
            $value = $this->convertEmptyArrayToObject($value);

            $asset = ['asset' => ['key' => 'templates/cart.json', 'value' => json_encode($value)]];
            $this->user->api()->rest('PUT', "/admin/themes/{$main_theme_id}/assets.json", $asset);
        }
        info('SECTION ADDED FOR CART PAGE');
    }

    public function createHeaderSection($main_theme_id)
    {
        info('CREATING SECTION FOR HEADER SNIPPET');
        $theme_asset = $this->user->api()->rest('GET', "/admin/themes/{$main_theme_id}/assets.json", ['asset[key]' => 'snippets/iconito_app_header.liquid']);

        if (!isset($theme_asset['body']['asset']['key']) && $theme_asset['status'] === 404) {
            info('iconito_app_header.liquid does not exist, will create for store: ' . $this->user->name);
            $section = [
                'asset' => [
                    'key' => 'snippets/iconito_app_header.liquid',
                    'value' => '<div id="render_iconito_block_header"></div>',
                ],
            ];

            $this->user->api()->rest('PUT', "/admin/themes/{$main_theme_id}/assets.json", $section);
        }

        $theme_asset = $this->user->api()->rest('GET', "/admin/themes/{$main_theme_id}/assets.json", ['asset[key]' => 'sections/header.liquid']);

        if (!isset($theme_asset['body']['asset']['key'])) {
            info('Not Found: sections/header.liquid');

            return;
        }
        $value = @$theme_asset['body']['asset']['value'];

        $codeToPrepend = "{% include 'iconito_app_header' %}\n";

        if (!Str::contains($value, $codeToPrepend)) {
            $pattern = '/<header\s[^>]*>/i';
            $replacement = $codeToPrepend . '$0';

            $modifiedString = preg_replace($pattern, $replacement, $value, 1);

            $asset = ['asset' => ['key' => 'sections/header.liquid', 'value' => $modifiedString]];
            $this->user->api()->rest('PUT', "/admin/themes/{$main_theme_id}/assets.json", $asset);
        }

        /*if(!in_array("iconito_app_header",[$value['order']])){
            info("Section not added, will add");

            $value['sections']['iconito_app_header'] = [
                "type" => "iconito_app_header"
            ];
            $position = count($value['order']) >= 1 ? 1 : count($value['order']);
            array_splice($value['order'], $position, 0, 'iconito_app_header');
            $value = $this->convertEmptyArrayToObject($value);

            $asset = [ 'asset' => [ 'key' => 'sections/header-group.json', 'value' => json_encode($value) ]];
            $this->user->api()->rest('PUT', "/admin/themes/{$main_theme_id}/assets.json", $asset );

        }*/
        info('SECTION ADDED FOR HEADER SNIPPET');
    }

    public function createFooterSection($main_theme_id)
    {
        info('CREATING SECTION FOR FOOTER SNIPPET');
        $theme_asset = $this->user->api()->rest('GET', "/admin/themes/{$main_theme_id}/assets.json", ['asset[key]' => 'snippets/iconito_app_footer.liquid']);

        if (!isset($theme_asset['body']['asset']['key']) && $theme_asset['status'] === 404) {
            info('iconito_app_footer.liquid does not exist, will create for store: ' . $this->user->name);
            $section = [
                'asset' => [
                    'key' => 'snippets/iconito_app_footer.liquid',
                    'value' => '<div id="render_iconito_block_footer"></div>',
                ],
            ];

            $this->user->api()->rest('PUT', "/admin/themes/{$main_theme_id}/assets.json", $section);
        }

        $theme_asset = $this->user->api()->rest('GET', "/admin/themes/{$main_theme_id}/assets.json", ['asset[key]' => 'sections/footer.liquid']);

        if (!isset($theme_asset['body']['asset']['key'])) {
            info('Not Found: sections/footer.liquid');

            return;
        }
        $value = @$theme_asset['body']['asset']['value'];

        $codeToAppend = "\n\t{% include 'iconito_app_footer' %}";
        if (!Str::contains($value, $codeToAppend)) {
            $pattern = '/<footer\s[^>]*>/i';
            $replacement = '$0' . $codeToAppend;

            $modifiedString = preg_replace($pattern, $replacement, $value, 1);

            $asset = ['asset' => ['key' => 'sections/footer.liquid', 'value' => $modifiedString]];
            $this->user->api()->rest('PUT', "/admin/themes/{$main_theme_id}/assets.json", $asset);
        }

        //        if(!in_array("iconito_app_footer",[$value['order']])){
        //            info("Section not added, will add");

        /*   $value['sections']['iconito_app_footer'] = [
               "type" => "iconito_app_footer"
           ];

           array_splice($value['order'], 0, 0, 'iconito_app_footer');
           $value = $this->convertEmptyArrayToObject($value);*/

        //        }
        info('SECTION ADDED FOR FOOTER SECTION');
    }

    public function convertEmptyArrayToObject($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if ($key === 'settings' && is_array($value) && empty($value)) {
                    $data[$key] = (object) $this->convertEmptyArrayToObject($value);
                } else {
                    $data[$key] = $this->convertEmptyArrayToObject($value);
                }
            }
        } elseif (is_object($data)) {
            foreach ($data as $key => $value) {
                if ($key === 'settings' && is_array($value) && empty($value)) {
                    $data->{$key} = (object) $this->convertEmptyArrayToObject($value);
                } else {
                    $data->{$key} = $this->convertEmptyArrayToObject($value);
                }
            }
        }

        return $data;
    }
}
