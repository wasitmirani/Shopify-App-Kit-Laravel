<?php

namespace App\Console\Commands;

use App\Jobs\CreateSectionsInShopifyJob;
use App\Jobs\SynCollectionsJob;
use App\Jobs\SyncProductsJob;
use App\Models\CustomIcon;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MigrateAppDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It will sync existing shopify app data new version';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Database sync started successfully.');

        // Disable foreign key checks for faster import
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Start a transaction
        DB::beginTransaction();
        // Retrieve the list of shops
        DB::connection('mysql2')->table('shop')->where('status', 1)->orderBy('id')->chunk(100, function ($shops) {
            foreach ($shops as $shop) {
                try {
                    $start1 = microtime(true);
                    $this->info("Syncing data for shop: {$shop->id}");
                    info("");
                    info("Syncing data for shop: {$shop->id}", [$shop]);

                    // Sync current shop
                    $user_id = $this->syncUser($shop);

                    if($user_id === 'SHOP_NOT_FOUND'){
                        info($shop->id . ": SKIP - shop has error to call api");
                        continue;
                    }

                    // Sync visitor counts for the current shop
                    $this->syncVisitorCount($shop->id, $user_id);

                    // Sync review rating for the current shop
                    $this->syncReviews($shop->id, $user_id);

                    // Sync the collections for the current shop
                    $this->syncCollections($shop->id, $user_id);

                    // Sync the products for the current shop
                    $this->syncProducts($shop->id, $user_id);

                    // Sync blocks and icons for the current shop
                    $this->syncIconBlocks($shop->id, $user_id);

                    $this->info("Data synced for shop: {$shop->id}");
                    $end1 = microtime(true);

                    info("");
                    info($shop->id . ': Time to sync store (sec)', [$end1 - $start1]);

                    // Commit the transaction if everything is successful for a shop
                    DB::commit();

                    $user = User::find($user_id);

                    if ($user) {
                        info("");
                        info($shop->id . ": Dispatch Job to sync products, collections and create sections");

                        /** Jobs to sync shopify products and collections */
                        SyncProductsJob::dispatch($user)->onQueue('product');
                        SynCollectionsJob::dispatch($user)->onQueue('collection');

                        /** Create sections and snippets in current theme */
                        CreateSectionsInShopifyJob::dispatch($user);
                    }
                } catch (\Exception $e) {
                    // Rollback the transaction if an error occurs
                    DB::rollBack();
                    DB::table('sync_logs')->insert([
                        'status_code' => $e->getCode(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile(),
                        'error' => json_encode($e->getMessage()),
                        'payload' => json_encode($shop),
                    ]);
                    info("Error while syncing:", [
                        'code' => $e->getCode(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile(),
                        'message' => $e->getMessage(),
                    ]);
                    $this->error($e->getMessage());
                }
            }
        });

        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->info('Database sync completed successfully.');
    }

    /**
     * Sync shopify shop, register webhooks and current charge
     *
     * @param $shop
     * @return int|mixed
     */
    public function syncUser($shop)
    {
        $user_data = [
            'name' => $shop->shop_url,
            'email' => 'shop@' . $shop->shop_url,
            'page_views_limit_crossed' => $shop->page_view_limit_crossed_status,
            'segment_events' => $shop->segment_api_events,
            //'main_theme_id' => $shop->,
            'password' => $shop->token,
            'remember_token' => null,
            'created_at' => $shop->created_at,
            'updated_at' => $shop->created_at,
            //'plan_id' => $shop->,
            'page_views' => $shop->total_page_view,
            'is_extension_enabled' => $shop->theme_extension === 1,
            'extension_enabled_at' => now(),
        ];

        $exist = DB::table('users')->select('id')->where('name', $shop->shop_url)->first();

        if ($exist) {
            $user_id = $exist->id;
            DB::table('users')->where('id', $user_id)->update($user_data);
        } else {
            $user_id = DB::table('users')->insertGetId($user_data);
        }
        info('Shop User ID', [$user_id]);
        $user = User::where('name', $shop->shop_url)->firstOrFail();

        $_shop = $user->api()->rest('GET', '/admin/shop.json');

        info("Shop API RES", [$_shop]);
        if (isset($_shop['body']['shop'])) {
            $_shop = $_shop['body']['shop'];
            User::where('id', $user->id)->update([
                'shopify_id' => $_shop['id'],
                'owner_name' => $_shop['name'],
                'shop_owner' => $_shop['shop_owner'],
                'owner_email' => $_shop['email'],
                'phone' => $_shop['phone'],
                'country' => $_shop['country'],
                'currency' => $_shop['currency'],
                'language' => $_shop['primary_locale'],
                'shopify_plan_name' => $_shop['plan_name']
            ]);
        }

        if (@$_shop['errors']) {
            DB::table('shopify_api_errors_logs')->insert([
                'shop_id' => $shop->id,
                'title' => '/admin/shop.json',
                'error' => json_encode($_shop['body'])
            ]);
            DB::table('users')->where('id',$user_id)->delete();
            if(in_array($_shop['body'], ["Unavailable Shop","Not Found","[API] Invalid API key or access token (unrecognized login or wrong password)"])){
                return "SHOP_NOT_FOUND";
            }
        }

        // webhook topics to register
        $webhookTopics = [
            'app/uninstalled',
            'products/create',
            'products/update',
            'products/delete',
            'collections/create',
            'collections/update',
            'collections/delete',
            'themes/update',
            'themes/publish',
            'app_subscriptions/update'
        ];

        // Loop through the webhook topics and register each one
        foreach ($webhookTopics as $topic) {
            $url = str_replace(['_', '/'], '-', $topic);

            $res = $user->api()->rest('POST', '/admin/webhooks.json', [
                'webhook' => [
                    'topic' => $topic,
                    'address' => \env('SHOPIFY_WEBHOOK_URL') . '/webhook/' . $url,
                    'format' => 'json'
                ]
            ]);
            info("Webhook Register RES", [$res]);
        }

        $this->syncCurrentCharge($shop, $user);

        return $user_id;
    }


    /**
     * Sync active charge for the shop
     *
     * @param $shop
     * @param $user
     * @return void
     */
    public function syncCurrentCharge($shop, $user)
    {
        info("");
        info($shop->id . ": Syncing current charge");
        if (isset($shop->charge_id) && !empty($shop->charge_id)) {
            $charge = DB::connection('mysql2')
                ->table('recurringcharges')
                ->where('store_id', $shop->id)
                ->where('status', 'active')
                ->first();

            if ($charge) {
                $plan = $this->getPlanByName($charge->name);
                $usage_charge_created_at = $next_usage_charge_create_at = null;
                if ($plan->name == 'STARTER') {
                    $usage_charges = $user->api()->rest('GET', '/admin/recurring_application_charges/' . $charge->charge_id . '/usage_charges.json');

                    if (isset($usage_charges['body']['usage_charges'])) {
                        if (count($usage_charges['body']['usage_charges'])) {
                            $latest = getLatestObject($usage_charges['body']['usage_charges']);
                            $usage_charge_created_at = @$latest->created_at;
                            $next_usage_charge_create_at = Carbon::parse(@$usage_charge_created_at)->addDays(30);
                        }
                    }
                }

                DB::table('charges')->updateOrInsert([
                    'charge_id' => $charge->charge_id,
                    'user_id' => $user->id
                ], [
                    'test' => $charge->test,
                    'status' => 'ACTIVE',
                    'name' => $plan->name,
                    'terms' => ($plan->name == 'STARTER') ? 'Free for the first 5,000 page views' : null,
                    'capped_amount' => ($plan->name == 'STARTER') ? 4.99 : null,
                    'type' => $plan->type,
                    'price' => $charge->price,
                    'interval' => $plan->interval,
                    'trial_days' => $plan->trial_days,
                    'billing_on' => $charge->billing_on,
                    'activated_on' => $charge->activated_on,
                    'trial_ends_on' => $charge->trial_ends_on,
                    'cancelled_on' => $charge->cancelled_on,
                    'expires_on' => Carbon::parse($charge->activated_on)->addDays(30),
                    'plan_id' => $plan->id,
                    'created_at' => $charge->created_date,
                    'updated_at' => $charge->created_date,
                    'usage_charge_created_at' => $usage_charge_created_at,
                    'next_usage_charge_create_at' => $next_usage_charge_create_at,
                ]);

                DB::table('users')->where('id', $user->id)->update(['plan_id' => $plan->id]);
            } else {
                $_charge = $user->api()->rest('GET', '/admin/recurring_application_charges/' . $shop->charge_id . '.json');

                if (isset($_charge['body']['recurring_application_charge'])) {
                    $_charge = $_charge['body']['recurring_application_charge'];
                    $plan = $this->getPlanByName($_charge->name);
                    $usage_charge_created_at = $next_usage_charge_create_at = null;
                    if ($plan->name == 'STARTER') {
                        $usage_charges = $user->api()->rest('GET', '/admin/recurring_application_charges/' . $_charge->id . '/usage_charges.json');
                        if (isset($usage_charges['body']['usage_charges'])) {
                            if (count($usage_charges['body']['usage_charges'])) {
                                $latest = getLatestObject($usage_charges['body']['usage_charges']);
                                $usage_charge_created_at = @$latest->created_at;
                                $next_usage_charge_create_at = Carbon::parse(@$usage_charge_created_at)->addDays(30);
                            }
                        }
                    }

                    DB::table('charges')->insert([
                        'charge_id' => $_charge->id,
                        'user_id' => $user->id,
                        'test' => $_charge->test,
                        'status' => strtoupper($_charge->status),
                        'name' => $plan->name,
                        'terms' => ($plan->name == 'STARTER') ? 'Free for the first 5,000 page views' : null,
                        'capped_amount' => ($plan->name == 'STARTER') ? 4.99 : null,
                        'type' => $plan->type,
                        'price' => $_charge->price,
                        'interval' => $plan->interval,
                        'trial_days' => $plan->trial_days,
                        'billing_on' => $_charge->billing_on,
                        'activated_on' => $_charge->activated_on,
                        'trial_ends_on' => $_charge->trial_ends_on,
                        'cancelled_on' => $_charge->cancelled_on,
                        'expires_on' => Carbon::parse($_charge->activated_on)->addDays(30),
                        'plan_id' => $plan->id,
                        'created_at' => $_charge->created_at,
                        'updated_at' => $_charge->created_at,
                        'usage_charge_created_at' => $usage_charge_created_at,
                        'next_usage_charge_create_at' => $next_usage_charge_create_at,
                    ]);

                    DB::table('users')->where('id', $user->id)->update(['plan_id' => $plan->id]);
                }
            }
        }
        info($shop->id . ": Synced current charge");
    }

    /**
     * Retrive current plan by name
     *
     * @param $name
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|void|null
     */
    public function getPlanByName($name)
    {
        $freePlans = ['Monthly Plan', 'Free Plan', 'Starter Plan'];
        if (in_array($name, $freePlans)) {
            return DB::table('plans')->where('name', 'STARTER')->first();
        }
        if ($name === 'MVP (Monthly Plan)') {
            return DB::table('plans')->where('name', 'MVP (Recommended)')->first();
        }
        if ($name === 'VIP (Monthly Plan)') {
            return DB::table('plans')->where('name', 'VIP')->first();
        }
    }

    /**
     * Sync visitor count
     *
     * @param $shop_id
     * @param $user_id
     * @return void
     */
    public function syncVisitorCount($shop_id, $user_id)
    {
        info("");
        info($shop_id . ": Syncing visitor count");
        $visitor_counts = DB::connection('mysql2')->table('store_visitor_count')->where('store_id', $shop_id)->get();

        foreach ($visitor_counts as $visitor_count) {
            $date = Carbon::parse($visitor_count->created_at);
            $currentMonth = Carbon::now()->format('m'); // Get the current month as a two-digit string
            $currentYear = Carbon::now()->format('Y'); // Get the current year as a four-digit string

            if (date('m') == $visitor_count->current_month && $date->format('m') === $currentMonth && $date->format('Y') === $currentYear) {
                DB::table('users')->where('id', $user_id)->update([
                    'page_views' => $visitor_count->total_count,
                ]);
            } else {
                DB::table('visitor_counts')->updateOrInsert(
                    [
                        'user_id' => $user_id,
                        'month' => $visitor_count->current_month,
                        'created_at' => $visitor_count->created_at,
                    ], [
                    'count' => $visitor_count->total_count,
                    'updated_at' => $visitor_count->created_at
                ]);
            }
        }
        info($shop_id . ": Synced visitor count");
    }

    /**
     * Sync review rating of a shop
     *
     * @param $shop_id
     * @param $user_id
     * @return void
     */
    public function syncReviews($shop_id, $user_id)
    {
        info("");
        info($shop_id . ": Syncing reviews");
        $reviews = DB::connection('mysql2')->table('store_rating')->where('shop_id', $shop_id)->get();

        foreach ($reviews as $review) {
            DB::table('reviews')->updateOrInsert(
                [
                    'user_id' => $user_id,
                    'rate' => $review->rating,
                    'created_at' => $review->createAt,
                ], [
                'description' => $review->comment,
                'updated_at' => $review->createAt
            ]);
        }
        info($shop_id . ": Synced reviews");
    }

    /**
     * Sync shopify collections of a shop
     *
     * @param $shop_id
     * @param $user_id
     * @return void
     */
    private function syncCollections($shop_id, $user_id)
    {
        info("");
        info($shop_id . ": Syncing collections");
        $collections = DB::connection('mysql2')->table('collections')->where('store_id', $shop_id)->get();

        if (count($collections)) {
            foreach ($collections as $collection) {
                DB::table('collections')->updateOrInsert([
                    'user_id' => $user_id,
                    'collection_id' => $collection->collection_id,
                ], [
                    'title' => $collection->name,
                    'created_at' => $collection->created_at,
                    'updated_at' => $collection->updated_at
                ]);
            }
        }
        info($shop_id . ": Synced collections");
    }

    /**
     * Sync shopify products of a shop
     *
     * @param $shop_id
     * @param $user_id
     * @return void
     */
    private function syncProducts($shop_id, $user_id)
    {
        info("");
        info($shop_id . ": Syncing products");
        $products = DB::connection('mysql2')->table('products')->where('store_id', $shop_id)->get();

        if (count($products)) {
            foreach ($products as $collection) {
                DB::table('products')->updateOrInsert([
                    'user_id' => $user_id,
                    'product_id' => $collection->product_id,
                ], [
                    'title' => $collection->title,
                    'created_at' => $collection->created_at,
                    'updated_at' => $collection->updated_at
                ]);
            }
        }
        info($shop_id . ": Synced products");
    }

    /**
     * Sync custom icons in s3 and block and icons in DB
     *
     * @param $shop_id
     * @param $user_id
     * @return void
     */
    private function syncIconBlocks($shop_id, $user_id)
    {
        $this->cloneS3CustomIconDir($shop_id, $user_id);
        info("");
        info($shop_id . ": Syncing icon blocks");
        $blocks = DB::connection('mysql2')->table('icon_blocks')->where('shop_id', $shop_id)->get();

        if (count($blocks)) {
            info("Shop Has Blocks:", [count($blocks)]);
            foreach ($blocks as $block) {
                info("Block TO Create", [$block]);

                $header_text_settings = [
                    'font' => getTextFont($block->header_font),
                    'size' => $block->header_size,
                    'color' => $block->header_color,
                    'weight' => $block->header_weight,
                    'alignment' => $block->header_alignment,
                ];

                $color_settings = [
                    'icon_color' => $block->icon_color,
                    'title_color' => $block->icon_title_color,
                    'is_transparent' => (bool)$block->transparent_background,
                    'subtitle_color' => $block->icon_subtitle_color,
                    'block_background_color' => $block->icon_bg_color,
                ];

                $typography_settings = [
                    'title_font_size' => $block->icon_title_fontsize,
                    'title_font_style' => $block->icon_title_fontstyle,
                    'subtitle_font_size' => $block->icon_subtitle_fontsize,
                    'subtitle_font_style' => $block->icon_subtitle_fontstyle
                ];

                $exist = DB::table('blocks')
                    ->select('id')
                    ->where('user_id', $user_id)
                    ->where('name', $block->block_name)
                    ->where('created_at', $block->updated_at)
                    ->first();

                $data = [
                    'header_text' => $block->header_title,
                    'layout' => $block->icon_layout == 'horizontal' ? 'vertical' : 'horizontal',
                    'is_enabled' => $block->status,
                    'header_text_settings' => json_encode($header_text_settings),
                    'position' => getIconPosition($block->icon_position),
                    'manual_placement_id' => $block->manual_code,
                    'icons_per_row_desktop' => $block->desktop_max_icons_in_row,
                    'icons_per_row_mobile' => $block->mobile_max_icons_in_row,
                    'size' => $block->icon_size,
                    'color_settings' => json_encode($color_settings),
                    'typography_settings' => json_encode($typography_settings),
                    'block_size' => $block->block_size_range_block,
                    'goes_up' => $block->outside_space_top,
                    'goes_down' => $block->outside_space_bottom,
                    'space_between_blocks' => $block->space_in_between_block,
                    'updated_at' => $block->created_at,
                ];

                if ($exist) {
                    $block_id = $exist->id;
                    DB::table('blocks')->where('id', $block_id)->update($data);
                } else {
                    $data = array_merge($data, [
                        'name' => $block->block_name,
                        'user_id' => $user_id,
                        'created_at' => $block->updated_at
                    ]);
                    $block_id = DB::table('blocks')->insertGetId($data);
                }

                $this->syncSelectedProducts($block->selected_products, $block_id, $user_id);

                $this->syncSelectedCollections($block->selected_collections, $block_id, $user_id);

                $icons = json_decode($block->icons, true);
                if (isset($icons) && count($icons)) {
                    info("Block has icons", [count($icons)]);
                    DB::table('icons')->where('block_id', $block_id)->delete();
                    foreach ($icons as $key => $icon) {
                        $parts = explode("/", $icon['icon']);
                        info("");
                        info("Link Part", [$parts]);
                        $icon_type = 'app-icon';
                        if (@$parts[3] === 'public') {
                            $parts[3] = $parts[4];
                            $parts[4] = $parts[5];
                            $parts[5] = @$parts[6];
                        }
                        if (in_array(@$parts[3], ['default_icons', '3d_icons'])) {
                            $_icon = DB::table('app_icons')
                                ->where('type', $parts[3])
                                ->where('category', $parts[4])
                                ->where('name', urldecode($parts[5]))
                                ->first();
                        }

                        // Custom Uploaded Icon
                        if (@$parts[3] === 'uploads') {
                            $icon_host = config('app.aws_icon_host');
                            $_icon = CustomIcon::updateOrCreate([
                                'user_id' => $user_id,
                                'name' => urldecode($parts[5])
                            ], [
                                'url' => $icon_host . "/uploads/shop_custom_icons_" . $user_id . "/" . urldecode($parts[5]),
                            ]);
                            $icon_type = 'custom';
                        }
                        info("Icon ID", [$_icon]);
                        DB::table('icons')->insert([
                            'block_id' => $block_id,
                            'icon_id' => @$_icon->id,
                            'icon_type' => $icon_type,
                            'title' => $icon['title'],
                            'subtitle' => $icon['subtitle'],
                            'show_link' => $icon['link'] != '' && $icon['link'] != 'https://',
                            'link' => ($icon['link'] != 'https://') ? $icon['link'] : '',
                            'open_to_new_tab' => $icon['allow_new_tab'] === '1',
                            'show_condition' => $icon['product_tags'] !== '',
                            'tags' => $icon['product_tags'],
                            'position' => $key + 1,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }
        }
        info($shop_id . ": Synced icon blocks");
    }

    /**
     * Sync selected products for a block of a shop
     *
     * @param $block_selected_products
     * @param $block_id
     * @param $user_id
     * @return void
     */
    public function syncSelectedProducts($block_selected_products, $block_id, $user_id)
    {
        info("");
        info("Syncing selected product for a block: $block_id, user_id: $user_id");
        $shopify_product_ids = explode(",", $block_selected_products);
        $selected_products = DB::table('products')->select('id')->where('user_id', $user_id)->whereIn('product_id', $shopify_product_ids)->get()->pluck('id')->toArray();

        if (count($selected_products)) {
            foreach ($selected_products as $selected_product) {
                DB::table('selected_products')->updateOrInsert([
                    'block_id' => $block_id,
                    'product_id' => $selected_product,
                ], [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        info("Synced selected product for a block: $block_id, user_id: $user_id");
    }

    /**
     * Sync selected collection for a block of a shop
     *
     * @param $block_selected_collections
     * @param $block_id
     * @param $user_id
     * @return void
     */
    public function syncSelectedCollections($block_selected_collections, $block_id, $user_id)
    {
        info("");
        info("Syncing selected collections for a block: $block_id, user_id: $user_id");
        $shopify_collection_ids = explode(",", $block_selected_collections);
        $selected_collections = DB::table('collections')->select('id')->where('user_id', $user_id)->whereIn('collection_id', $shopify_collection_ids)->get()->pluck('id')->toArray();

        if (count($selected_collections)) {
            foreach ($selected_collections as $selected_collection) {
                DB::table('selected_collections')->updateOrInsert([
                    'block_id' => $block_id,
                    'collection_id' => $selected_collection,
                ], [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        info("Synced selected collections for a block: $block_id, user_id: $user_id");
    }

    /**
     * Sync custom icon directory in AWS S3
     *
     * @param $shop_id
     * @param $user_id
     * @return void
     */
    public function cloneS3CustomIconDir($shop_id, $user_id)
    {
        info("");
        info($shop_id . ": Coping S3 custom icon directory");
        $sourceDirectory = 'uploads/custom_icon_' . $shop_id;
        $destinationDirectory = 'uploads/shop_custom_icons_' . $user_id;
        info($shop_id . ": Source and Destination", [$sourceDirectory, $destinationDirectory]);
        $files = Storage::disk('s3')->allFiles($sourceDirectory);
        if (isset($files) && !empty($files)) {
            info("No. of files to copy", [count($files)]);
            foreach ($files as $file) {
                $newFileName = str_replace($sourceDirectory, $destinationDirectory, $file);
                if (!Storage::disk('s3')->exists($newFileName)) {
                    Storage::disk('s3')->copy($file, $newFileName);
                }
            }
        }
        info($shop_id . ": Copied S3 custom icon directory");
    }
}
