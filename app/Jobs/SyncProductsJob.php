<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Product;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use function explode;
use function info;
use function sleep;

class SyncProductsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Determines the job will not time out.
     *
     * @var int
     */
    public $timeout = 0;

    /**
     * @var User
     */
    private $user;

    /**
     * Create a new job instance.
     *
     * @param mixed $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $nextPage = null;
            do {
                info('Syncing products for ' . $this->user->name);
                $response = $this->getShopifyProducts($this->user, $nextPage);
                if (isset($response['body']['products'])) {
                    $products = $response['body']['products'];
                    $_products = [];

                    foreach ($products as $product) {
                        $_products[] = [
                            'user_id' => $this->user->id,
                            'product_id' => $product['id'],
                            'title' => $product['title'],
                            'handle' => $product['handle'],
                            'image_url' => @$product['image']['src'],
                        ];
                    }
                    Product::upsert($_products, ['product_id'], ['title', 'image_url', 'handle']);
                }

                if ($response['link'] !== null) {
                    $nextPage = null;
                    if(@$response['link']->container['next'][0]){
                        $nextPage = @explode(';', @$response['link']->container['next'])[0];
                    }
                }
            } while ($nextPage !== null);
        } catch (\Exception $exception) {
            info('Error while syncing products: ' . $exception->getMessage());
        }
    }

    /**
     *  Get Shopify products.
     *
     * @param mixed $user
     * @param mixed|null $nextPage
     */
    protected function getShopifyProducts($user, $nextPage = null)
    {
        \Log::info('getShopifyProducts called...');
        $params = [];
        $params['limit'] = 250;
        if ($nextPage !== null) {
            $params['page_info'] = $nextPage;
        } else {
            $params['status'] = 'active';
            $params['fields'] = 'id,title,image,handle';
        }
        $response = $user->api()->rest('GET', '/admin/products.json', $params);
        if ($response['status'] === 429) {
            \Log::info('Many Request and wait');
            sleep(1);
            $response = $this->getShopifyProducts($user, $nextPage);
        }

        return $response;
    }
}
