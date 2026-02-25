<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Plan;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Osiset\ShopifyApp\Objects\Values\ShopDomain;
use stdClass;

use function now;
use function response;
use function sendSegmentTrackEvent;

class AppSubscriptionsUpdateJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Shop's myshopify domain.
     *
     * @var ShopDomain|string
     */
    public $shopDomain;

    /**
     * The webhook data.
     *
     * @var object
     */
    public $data;

    /**
     * Create a new job instance.
     *
     * @param string   $shopDomain the shop's myshopify domain
     * @param stdClass $data       the webhook data (JSON decoded)
     */
    public function __construct($shopDomain, $data)
    {
        $this->shopDomain = $shopDomain;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $user = User::where('name', $this->shopDomain)->first();

        $plan = Plan::where('name', @$this->data->app_subscription->name)->first();
        $status = @$this->data->app_subscription->status;
        if(!is_null($status) && @$status === 'ACTIVE' && @$plan->name === 'VIP' || (@$plan->name === 'MVP (Recommended)' && $user->page_views < @$plan->page_views_threshold)){
            User::where('id', $user->id)->update(['page_views_limit_crossed' => 0]);
        }
        if(@$plan->name === 'STARTER'){
            $active_blocks_ids = DB::table('blocks')->where('user_id', $user->id)->get(['id'])->pluck('id')->toArray();
            if(isset($active_blocks_ids) && count($active_blocks_ids) > 1){
                array_shift($active_blocks_ids);
                DB::table('blocks')->whereIn('id', $active_blocks_ids)->update(['is_enabled' => 0]);
            }
        }

        /* Send Plan Subscription Decline Event To Segment */
        if($status === 'DECLINED'){
            info("Send Decline Event");
            sendSegmentTrackEvent($user->id,'Declined charge', [
                'amount_of_plan' => @$plan->price,
                'charge_applied_date' => date('Y-m-d H:i:s', strtotime(@$this->data->app_subscription->created_at)),
                'app_name' => config('app.name'),
                'charge_status' => $status,
                'country' => $user->country,
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                'currency' => $user->currency,
                'email' => $user->owner_email ? maskEmail($user->owner_email) : '',
                'language' => $user->language,
                'name' => $user->owner_name ? maskName($user->owner_name) : '',
                "plan_name" => @$user->subscribedPlan->name,
                'phone' => $user->phone ? maskPhone($user->phone) :'',
                'shop_owner' => $user->shop_owner,
                'store_url' => $user->name,
            ]);
        }

        /* Send Plan Subscription Active Event To Segment */
        if($status === 'ACTIVE'){
            info("Send Active Event");
            $extension_url = "https://" . $user->name . '/admin/themes/current/editor?context=apps&appEmbed=' . getThemeEmbedUuid() . '/' . getThemeBlockName();
            sendSegmentTrackEvent($user->id,'Subscription charge activated', [
                'amount_of_plan' => @$plan->price,
                'charge_applied_date' => date('Y-m-d H:i:s', strtotime(@$this->data->app_subscription->created_at)),
                'app_name' => config('app.name'),
                'charge_status' => $status,
                'country' => $user->country,
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                'currency' => $user->currency,
                'email' => $user->owner_email ? maskEmail($user->owner_email) : '',
                'language' => $user->language,
                'name' => $user->owner_name ? maskName($user->owner_name) : '',
                "plan_name" => @$user->subscribedPlan->name,
                'phone' => $user->phone ? maskPhone($user->phone) :'',
                'shop_owner' => $user->shop_owner,
                'store_url' => $user->name,
                'theme_app_extension_url' => $extension_url,
            ]);
        }
        return response(true, 200);
    }
}
