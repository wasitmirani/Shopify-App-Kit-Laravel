<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use function info;
use function now;
use function sendSegmentTrackEvent;

class AfterAuthenticateJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * ShopDomain Var.
     *
     * @var void
     */
    public $shopDomain;

    /**
     * Create a new job instance.
     *
     * @param mixed $shopDomain
     */
    public function __construct($shopDomain)
    {
        $this->shopDomain = $shopDomain['name'];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        info('AfterAuthenticateJob handle');
        $user = User::where('name', $this->shopDomain)->firstOrFail();

        $shop = $user->api()->rest('GET','/admin/shop.json');

        if(isset($shop['body']['shop'])){
            $shop = $shop['body']['shop'];
            User::where('id', $user->id)->update([
                'shopify_id' => $shop['id'],
                'owner_name' => $shop['name'],
                'shop_owner' => $shop['shop_owner'],
                'owner_email' => $shop['email'],
                'phone' => $shop['phone'],
                'country' => $shop['country'],
                'currency' => $shop['currency'],
                'language' => $shop['primary_locale'],
                'shopify_plan_name' => $shop['plan_name'],
                'segment_events' => [
                    'created_block' => 0,
                    'clicked_on_library_of_icons' => 0,
                    'theme_activated' => 0,
                    'click_on_tutorial' => 0
                ],
            ]);
        }

        /* Jobs to sync shopify products and collections */
        SyncProductsJob::dispatch($user)->onQueue('product');
        SynCollectionsJob::dispatch($user)->onQueue('collection');

        $extension_url = "https://" . $user->name . '/admin/themes/current/editor?context=apps&appEmbed=' . getThemeEmbedUuid() . '/' . getThemeBlockName();
        $user->refresh();

        /* Send App Install Event To Segment */
        sendSegmentTrackEvent($user->id,'Installed', [
            'app_name' => config('app.name'),
            'country' => $user->country,
            'created_at' => $user->created_at->format('Y-m-d H:i:s'),
            'currency' => $user->currency,
            'email' => $user->owner_email ? maskEmail($user->owner_email) : '',
            'language' => $user->language,
            'name' => $user->owner_name ? maskName($user->owner_name) : '',
            'phone' => $user->phone ? maskPhone($user->phone) :'',
            'shop_owner' => $user->shop_owner,
            'shopify_plan_name' => $user->shopify_plan_name,
            'store_url' => $user->name,
            'theme_app_extension_url' => $extension_url,
        ]);
        CreateSectionsInShopifyJob::dispatch($user);
    }
}
