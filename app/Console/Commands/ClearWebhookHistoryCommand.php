<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearWebhookHistoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-webhook-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It will keep latest 10,000 webhook history and delete the existing.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        info("ClearWebhookHistoryCommand Start");
        $count = DB::table('webhooks')->where('is_executed', 1)->count();

        if($count){
            $res = DB::table('webhooks')
                ->where('is_executed', 1)
                ->delete();
            info("Webhook History Delete count:",[$res]);
        }
        info("ClearWebhookHistoryCommand Finish");
    }
}
