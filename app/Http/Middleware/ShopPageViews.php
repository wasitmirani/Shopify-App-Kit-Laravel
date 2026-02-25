<?php

namespace App\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ShopPageViews
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $storefront_request = @$request->header('x-iconito-storefront');

        $user = User::with('subscribedPlan')->findOrFail(auth()->id());
        if(!isset($user->subscribedPlan->name)){return \response([], 200);}
        if($storefront_request === '1'){
            if(@$user->subscribedPlan->name == 'MVP (Recommended)'){
                $event = '';
                if($user->page_views === 40000){
                    $event = '40k';
                }

                if($user->page_views === 45000){
                    $event = '45k';
                }

                if($event){
                    info("Event $event");
                    /* Send Page Views Usage Event To Segment */
                    sendSegmentTrackEvent($user->id,"$event limit", [
                        'app_name' => config('app.name'),
                        'country' => $user->country,
                        'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                        'currency' => $user->currency,
                        'email' => $user->owner_email ? maskEmail($user->owner_email) : '',
                        'language' => $user->language,
                        'name' => $user->owner_name ? maskName($user->owner_name) : '',
                        "page_views_crossed" => $event == '40k' ? 40000 : 45000,
                        "plan_name" => $user->subscribedPlan->name,
                        'phone' => $user->phone ? maskPhone($user->phone) :'',
                        'shop_owner' => $user->shop_owner,
                        'store_url' => $user->name,
                        'plan_list_page_url' => config('app.url') . '/login',
                    ]);
                }
            }


            if(($user && @$user->subscribedPlan->page_views_threshold != null && $user->page_views >= $user->subscribedPlan->page_views_threshold) || $user->page_views_limit_crossed === 1){
                if(!$user->page_views_limit_crossed){
                    User::where('id', $user->id)->update(['page_views_limit_crossed' => 1]);
                    $user = $user->refresh();
                    if(@$user->subscribedPlan->name == 'MVP (Recommended)' && $user->page_views === 50000){
                        /* Send 50k Page Views Event To Segment */
                        sendSegmentTrackEvent($user->id,'50k limit', [
                            'app_name' => config('app.name'),
                            'country' => $user->country,
                            'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                            'currency' => $user->currency,
                            'email' => $user->owner_email ? maskEmail($user->owner_email) : '',
                            'language' => $user->language,
                            'name' => $user->owner_name ? maskName($user->owner_name) : '',
                            "page_views_crossed" => 50000,
                            "plan_name" => $user->subscribedPlan->name,
                            'phone' => $user->phone ? maskPhone($user->phone) :'',
                            'shop_owner' => $user->shop_owner,
                            'store_url' => $user->name,
                            'plan_list_page_url' => config('app.url') . '/login',
                        ]);
                    }
                }

                if($user->subscribedPlan->name == 'STARTER'){
                    if($user->page_views_limit_crossed){
                        // Create the usage charge
                        $charge = DB::table('charges')->where('user_id', $user->id)->where('status','ACTIVE')->first();

                        if($charge) {
                            $response = $user->api()->rest('POST', '/admin/recurring_application_charges/' . $charge->charge_id . '/usage_charges.json', [
                                'usage_charge' => [
                                    'price' => 4.99, // The price of the usage charge
                                    'description' => 'Crossed 5,000 page views for STARTER plan', // The description of the charge
                                ],
                            ]);

                            if (isset($response['body']['usage_charge'])) {
                                $application_charge = $user->api()->rest('GET', '/admin/recurring_application_charges/' . $charge->charge_id . '.json');

                                $user->api()->rest('POST', '/admin/recurring_application_charges/' . $charge->charge_id . '/activate.json', [
                                    'recurring_application_charge' => [@$application_charge['body']['recurring_application_charge']]
                                ]);

                                $mvpPlan = DB::table('plans')->where('name', 'MVP (Recommended)')->first();

                                $usage_charge_created_at = @$response['body']['usage_charge']['created_at'];
                                DB::table('charges')->where('user_id', $user->id)->where('status', 'ACTIVE')->update([
                                    'plan_id' => $mvpPlan->id,
                                    'description' => 'Crossed 5000 page views limit and switched to MVP plan',
                                    'updated_at' => now(),
                                    'usage_charge_created_at' => $usage_charge_created_at,
                                    'next_usage_charge_create_at' => Carbon::parse($usage_charge_created_at)->addDays(30),
                                ]);

                                DB::table('users')->where('id', $user->id)->update([
                                    'plan_id' => $mvpPlan->id,
                                    'page_views_limit_crossed' => 0,
                                ]);

                                return $next($request);
                            }
                        }
                    }
                }
                Log::error("Store($user->name) reached page views limit {$user->subscribedPlan->page_views_threshold}.");
                return \response([], 200);
            }

            if($user){
                User::where('id', $user->id)->increment('page_views');
            }
        }
        return $next($request);
    }
}
