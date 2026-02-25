<?php

namespace App\Console\Commands;

use App\Models\Charge;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UsageChargeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:usage-charge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It charge a shop based on usage with MVP plan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        info("UsageChargeCommand Start");
        DB::beginTransaction();
        try {
            Charge::with('user')
                ->where('status', 'ACTIVE')
                ->whereNotNull('capped_amount')
                ->whereNotNull('terms')
                ->whereDate('next_usage_charge_create_at', today())
                ->chunk(100, function ($charges) {
                    foreach ($charges as $charge) {
                        $response = $charge->user->api()->rest('POST', '/admin/recurring_application_charges/' . $charge->charge_id . '/usage_charges.json', [
                            'usage_charge' => [
                                'price' => 4.99, // The price of the usage charge
                                'description' => 'Recurring usage charge for MVP plan', // The description of the charge
                            ],
                        ]);

                        if(isset($response['errors'])){
                            DB::table('shopify_api_errors_logs')->insert([
                                'shop_id' => $charge->user->id,
                                'title' => '/admin/recurring_application_charges/' . $charge->charge_id . '/usage_charges.json',
                                'error' => json_encode($response['body'])
                            ]);
                        }
                        if (isset($response['body']['usage_charge'])) {
                            $application_charge = $charge->user->api()->rest('GET', '/admin/recurring_application_charges/' . $charge->charge_id . '.json');

                            $charge->user->api()->rest('POST', '/admin/recurring_application_charges/' . $charge->charge_id . '/activate.json',[
                                'recurring_application_charge' =>  [ @$application_charge['body']['recurring_application_charge']]
                            ]);

                            $usage_charge_created_at = @$response['body']['usage_charge']['created_at'];
                            DB::table('charges')->where('id', $charge->id)->update([
                                'updated_at' => now(),
                                'usage_charge_created_at' => $usage_charge_created_at,
                                'next_usage_charge_create_at' => Carbon::parse($usage_charge_created_at)->addDays(30),
                            ]);
                        }
                    }
                });
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            info("Error while charging recurring usage charge:",[
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'message' => $e->getMessage(),
            ]);
        }
        info("UsageChargeCommand Finish");
    }
}
