<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Product;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use function in_array;
use function json_decode;

class ExecuteProductsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $webhookId;

    public $shopId;

    /**
     * Create a new job instance.
     *
     * @param mixed $webhookID
     * @param mixed $shopID
     */
    public function __construct($webhookID, $shopID)
    {
        $this->webhookId = $webhookID;
        $this->shopId = $shopID;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();

        try {
            $user = User::find($this->shopId);
            $webhook = Webhook::with('user')->where('is_executed', 0)->find($this->webhookId);
            if (empty($webhook)) {
                return;
            }
            $product = json_decode($webhook->data, true);
            if (in_array($webhook->topic, ['products/create', 'products/update'], true)) {
                Product::updateOrCreate([
                    'product_id' => $product['id'],
                ], [
                    'user_id' => $user->id,
                    'title' => $product['title'],
                    'handle' => $product['handle'],
                    'image_url' => @$product['image']['src'],
                ]);
            } elseif ($webhook->topic === 'products/delete') {
                Product::where('user_id', $user->id)->where('product_id', $webhook->shopify_id)->delete();
            }
            $webhook->is_executed = 1;
            $webhook->save();

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage() . ' while ExecuteProductsJob, Product Id:' . @$webhook->shopify_id);
        }
    }
}
