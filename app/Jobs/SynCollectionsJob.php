<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Collection;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use function explode;
use function info;
use function sleep;

class SynCollectionsJob implements ShouldQueue
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
            $this->syncCustomCollections();
            $this->syncSmartCollections();
        } catch (\Exception $exception) {
            info('Error while syncing collections: ' . $exception->getMessage());
        }
    }

    /**
     * Sync shopify custom collections.
     */
    public function syncCustomCollections()
    {
        $nextPage = null;
        do {
            $response = $this->getShopifyCollections($this->user, $nextPage, 'custom_collections');
            if (isset($response['body']['custom_collections'])) {
                $collections = $response['body']['custom_collections'];
                $_collections = [];

                foreach ($collections as $collection) {
                    $_collections[] = [
                        'user_id' => $this->user->id,
                        'collection_id' => $collection['id'],
                        'title' => $collection['title'],
                        'handle' => $collection['handle'],
                        'image_url' => @$collection['image']['src'],
                    ];
                }
                Collection::upsert($_collections, ['collection_id'], ['title', 'image_url', 'handle']);
            }

            if ($response['link'] !== null) {
                $nextPage = @explode(';', @$response['link']->container['next'])[0];
            }
        } while ($nextPage !== null);
    }

    /**
     * Sync shopify smart collections.
     */
    public function syncSmartCollections()
    {
        $nextPage = null;
        do {
            info('Syncing collections for ' . $this->user->name);
            $response = $this->getShopifyCollections($this->user, $nextPage, 'smart_collections');
            if (isset($response['body']['smart_collections'])) {
                $collections = $response['body']['smart_collections'];
                $_collections = [];

                foreach ($collections as $collection) {
                    $_collections[] = [
                        'user_id' => $this->user->id,
                        'collection_id' => $collection['id'],
                        'title' => $collection['title'],
                        'handle' => $collection['handle'],
                        'image_url' => @$collection['image']['src'],
                    ];
                }
                Collection::upsert($_collections, ['user_id', 'collection_id'], ['title', 'image_url', 'handle']);
            }

            if ($response['link'] !== null) {
                $nextPage = @explode(';', @$response['link']->container['next'])[0];
            }
        } while ($nextPage !== null);
    }

    /**
     *  Get Shopify collections.
     *
     * @param mixed $user
     * @param mixed|null $nextPage
     * @param mixed|null $type
     */
    protected function getShopifyCollections($user, $nextPage = null, $type = null)
    {
        \Log::info('getShopifyCollections called...');
        $params = [];
        $params['limit'] = 250;
        if ($nextPage !== null) {
            $params['page_info'] = $nextPage;
        } else {
            $params['status'] = 'active';
            $params['fields'] = 'id,title,image,handle';
        }
        $response = $user->api()->rest('GET', "/admin/{$type}.json", $params);
        if ($response['status'] === 429) {
            \Log::info('Many Request and wait');
            sleep(1);
            $response = $this->getShopifyCollections($user, $nextPage);
        }

        return $response;
    }
}
