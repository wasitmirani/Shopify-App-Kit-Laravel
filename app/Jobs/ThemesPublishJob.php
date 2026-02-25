<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Osiset\ShopifyApp\Objects\Values\ShopDomain;
use stdClass;

use function info;
use function response;

class ThemesPublishJob implements ShouldQueue
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
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function handle()
    {
        info('THEME PUBLISH');
        $user = User::where('name', $this->shopDomain)->first();
        if (!$user) {
            return response('', 200);
        }
        CheckThemeExtenstionJob::dispatch($user);
        CreateSectionsInShopifyJob::dispatch($user);
    }
}
