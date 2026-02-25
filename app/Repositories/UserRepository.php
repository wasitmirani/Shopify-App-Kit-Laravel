<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

use function auth;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Retrieve authenticated user with plan and charge.
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getUserPlan()
    {
        return User::with(['subscribedPlan', 'activeCharge'])->withCount('reviews')->findOrFail(auth()->id());
    }

    /**
     * Retrieve authenticated user's page views count.
     *
     * @return mixed
     */
    public function getUserPageViewsCount()
    {
        return User::select('page_views')->findOrFail(auth()->id());
    }

    /**
     * Retrieve authenticated user's page views count.
     *
     * @return mixed
     */
    public function sendSegmentEvent($event)
    {
        $user = auth()->user();

        $extension_url = "https://" . $user->name . '/admin/themes/current/editor?context=apps&appEmbed=' . getThemeEmbedUuid() . '/' . getThemeBlockName();
        $events = [
            'click_on_tutorial' => 'Click On Tutorials',
            'clicked_on_library_of_icons' => 'Clicked on library of icons',
            'theme_activated' => 'Theme Activated'
        ];

        /** Send Block Created Event to Segment */
        sendSegmentTrackEvent($user->id,$events[$event], [
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
        $segment_events = $user['segment_events'];
        $segment_events[$event] = 1;
        auth()->user()->update(['segment_events' => $segment_events]);
    }
}
