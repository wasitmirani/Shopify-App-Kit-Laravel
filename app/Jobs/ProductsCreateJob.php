<?php

declare(strict_types=1);

namespace App\Jobs;

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

use function json_decode;
use function json_encode;
use function response;

class ProductsCreateJob implements ShouldQueue
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
        /*$user = DB::table('users')->select('id')->where('name', $this->shopDomain)->first();
        $product = json_encode($this->data);
        $shopifyId = json_decode($product)->id;
        $id = DB::table('webhooks')->insertGetId([
            'user_id' => $user->id,
            'shopify_id' => $shopifyId,
            'topic' => 'products/update',
            'data' => $product,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ExecuteProductsJob::dispatch($id, $user->id)->onQueue('product');*/

        return response(true, 200);
    }
}
