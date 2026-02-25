<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use Segment\Segment;
use Osiset\ShopifyApp\Actions\CancelCurrentPlan;
use Osiset\ShopifyApp\Contracts\Commands\Shop as IShopCommand;
use Osiset\ShopifyApp\Contracts\Queries\Shop as IShopQuery;

use function info;
use function now;
use function sendSegmentTrackEvent;
use function sleep;

class AppUninstalledJob extends \Osiset\ShopifyApp\Messaging\Jobs\AppUninstalledJob
{
    public function handle(
        IShopCommand $shopCommand,
        IShopQuery $shopQuery,
        CancelCurrentPlan $cancelCurrentPlanAction
    ): bool {
        $user = User::where('name', $this->domain)->first();
        info('Shop Uninstalled', [$user]);
        /*Segment::identify("user-" . $user->id, []);*/

        /* Send App Uninstall Event To Segment */
        sendSegmentTrackEvent($user->id,'Uninstalled', [
            'app_name' => config('app.name'),
            'country' => $user->country,
            'created_at' => $user->created_at->format('Y-m-d H:i:s'),
            'currency' => $user->currency,
            'email' => $user->owner_email ? maskEmail($user->owner_email) : '',
            'language' => $user->language,
            'name' => $user->owner_name ? maskName($user->owner_name) : '',
            'phone' => $user->phone ? maskPhone($user->phone) :'',
            'shop_owner' => $user->shop_owner,
            'store_url' => $user->name
        ]);

        return parent::handle($shopCommand, $shopQuery, $cancelCurrentPlanAction);
    }
}
