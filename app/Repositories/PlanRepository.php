<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\PlanRepositoryInterface;
use App\Models\Charge;
use App\Models\Plan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

use function auth;
use function mb_strtoupper;
use function now;
use function sendSegmentTrackEvent;

class PlanRepository implements PlanRepositoryInterface
{
    /**
     * Returns List Of Plans with features.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return Cache::rememberForever('available_plans', static fn () => Plan::with('plan_features')->get());
    }

    /**
     * Returns List Of Plans.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function plans()
    {
        return Plan::all();
    }

    /**
     * Returns Current Authenticated User.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function getShop()
    {
        return auth()->user();
    }

    /**
     * Subscribe Free Plan.
     *
     * @return true
     */
    public function chooseFreePlan()
    {
        $this->deactivateCurrentPlan();

        $user = auth()->user();
        $freePlan = $this->getFreePlan();
        $user->plan_id = $freePlan->id;
        $user->shopify_freemium = true;
        $user->save();

        Log::info('Free Plan', [$user]);
    }

    /**
     * Return Free Plan.
     */
    public function getFreePlan()
    {
        return Cache::rememberForever('free_plan', static fn () => Plan::where('name', Plan::FREE_PLAN)->where('price', 0)->first());
    }

    /**
     * Deactivate Current Plan And Updates In DB.
     *
     * @return array
     */
    public function deactivateCurrentPlan()
    {
        $user = auth()->user();
        $charge = Charge::where('user_id', $user->id)->where('status', 'ACTIVE')->first();

        if (!$charge) {
            return;
        }
        $chargeUrl = '/admin/recurring_application_charges/' . $charge->charge_id . '.json';

        try {
            $response = $user->api()->rest('DELETE', $chargeUrl);

            if ($response['errors']) {
                return;
            }

            if ($response['status'] === 200) {
                $response = $user->api()->rest('GET', $chargeUrl);
                if ($response['errors']) {
                    return;
                }
                $response = $response['body']->recurring_application_charge;
                $charge->status = mb_strtoupper($response['status']);
                $charge->name = $response['name'];
                $charge->cancelled_on = $response['cancelled_on'];
                $charge->expires_on = $response['cancelled_on'];
                $charge->save();
            }
        } catch (\Exception $exception) {
            Log::info('deactivateCurrentPlan Error', [$exception]);

            return;
        }
    }

    /**
     * Returns Current Active Plan.
     *
     * @return mixed
     */
    public function getActivePlan()
    {
        return Plan::findOrFail(@auth()->user()->plan_id);
    }
}
