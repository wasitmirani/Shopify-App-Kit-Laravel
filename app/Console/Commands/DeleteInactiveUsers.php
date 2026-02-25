<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Block;
use App\Models\Charge;
use App\Models\Collection;
use App\Models\CustomIcon;
use App\Models\Icon;
use App\Models\Product;
use App\Models\Review;
use App\Models\SelectedCollection;
use App\Models\SelectedProduct;
use App\Models\User;
use App\Models\Webhook;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

use function count;
use function explode;
use function info;

class DeleteInactiveUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:inactive_users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete users and their content who uninstalled the app a year ago.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        info("DeleteInactiveUsers Command Start");
        $date = Carbon::now()->subYear();

        $users = User::onlyTrashed()
            ->where('deleted_at', '<=', $date)
            ->take(10)
            ->get();

        foreach ($users as $user) {
            info('Deleting Content For User', [$user->id, $user->name]);
            // Delete Custom Icons
            $custom_icons = CustomIcon::where('user_id', $user->id)->get();
            foreach ($custom_icons as $icon) {
                $parts = explode('/', $icon->url);
                if (count($parts) !== 6) {
                    continue;
                }
                $url = explode('.com', $icon->url)[1];
                info('Custom Icon To Delete', [$url]);
                Storage::disk('s3')->delete($url);
            }

            CustomIcon::where('user_id', $user->id)->delete();

            $blocks = Block::where('user_id', $user->id)->get(['id'])->pluck('id')->toArray();

            Icon::whereIn('block_id', $blocks)->delete();

            SelectedCollection::whereIn('block_id', $blocks)->delete();

            SelectedProduct::whereIn('block_id', $blocks)->delete();

            Product::where('user_id', $user->id)->delete();

            Collection::where('user_id', $user->id)->delete();

            Webhook::where('user_id', $user->id)->delete();

            Block::where('user_id', $user->id)->delete();

            Charge::where('user_id', $user->id)->delete();

            Review::where('user_id', $user->id)->delete();

            $user->forceDelete();
        }

        $count = count($users);

        if ($count > 0) {
            $this->info("Deleted {$count} old soft deleted users.");
        } else {
            $this->info('No old soft deleted users found.');
        }
        info("DeleteInactiveUsers Command Finish");
    }
}
