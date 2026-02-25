<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\VisitorCount;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetPageViewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-page-views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It will reset and store page views command';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        info("ResetPageViewsCommand Start");
        $currentMonth = Carbon::now()->month;

        $previousMonth = Carbon::now()->subMonth();
        $previousMonth1 = Carbon::now()->subMonth();


        $freePlan = DB::table('plans')->where('name','STARTER')->first();
        User::chunk(100, function ($users) use($currentMonth, $freePlan, $previousMonth,$previousMonth1){
            foreach($users as $user){
                $page_views = $user->page_views;
                if($user->plan_id == $freePlan->id){
                    $exist = DB::table('visitor_counts')->where('user_id',$user->id)->whereBetween('created_at', [$previousMonth1->startOfMonth(), $previousMonth->endOfMonth()])->first();
                    if($exist){
                        $page_views = $page_views - $exist->count;
                    }
                }
                VisitorCount::create([
                    'user_id' => $user->id,
                    'month' => $currentMonth,
                    'count' => $page_views
                ]);
                if($user->plan_id != $freePlan->id){
                    User::where('id', $user->id)->update(['page_views' => 0, 'page_views_limit_crossed' => 0]);
                }
            }
        });
        info("ResetPageViewsCommand Finish");
    }
}
