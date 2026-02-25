<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\BlockRepositoryInterface;
use App\Models\Block;
use App\Models\Icon;
use App\Models\SelectedCollection;
use App\Models\SelectedProduct;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use function array_column;
use function array_diff_key;
use function array_flip;
use function array_merge;
use function auth;
use function config;
use function explode;
use function file_get_contents;
use function info;
use function microtime;
use function now;
use function str_ends_with;

class BlockRepository implements BlockRepositoryInterface
{
    /**
     * Returns List Of Blocks.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return Block::with('icons')->where('user_id', auth()->id())->get();
    }

    /**
     * Retrive icon block details with svg.
     *
     * @param $id
     *
     * @return array|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function getSingleIconBlock($id)
    {
        $variousDownloadSvgId = DB::table('app_icons')
            ->where('name', 'various-download.svg')
            ->where('category', 'various')
            ->value('id');

        $block = Block::with(['appIcons.appIcon', 'customIcons.customIcon', 'selected_products:id', 'selected_collections:id'])->findOrFail($id);
        $block['add_more'] = [
            'name' => 'various-download.svg',
            'id' => $variousDownloadSvgId,
            'type' => 'app-icon',
            'svg' => Storage::disk('app_public')->get('images/download_icon.svg'),
        ];

        return $block;
    }

    /**
     * Save the entire block details.
     *
     * @param $data
     *
     * @throws \Exception
     */
    public function store($data)
    {
        try {
            DB::beginTransaction();
            $block_id = null;
            $product_page_blocks = 0;

            if (isset($data['edit_id'])) {
                $block_id = @$data['edit_id'];
            }

            $selected_products = $data['block_settings']['selected_products'];
            $selected_collections = $data['block_settings']['selected_collections'];
            if (in_array($data['block_settings'],['all-products', 'selected-products', 'collection-products'])) {
                $count = Block::where('position', ['all-products', 'selected-products', 'collection-products'])->where('user_id', auth()->id())->count();
                if ($count) {
                    $product_page_blocks = $count;
                }
            }
            $blockToCreate = array_merge($data['block_settings'], $data['icon_settings']);
            $blockToCreate['header_text_settings'] = $blockToCreate['headerTextSettings'];
            $blockToCreate['user_id'] = auth()->id();
            $remove = ['headerTextSettings', 'selected_products', 'selected_collections'];
            $blockToCreate = array_diff_key($blockToCreate, array_flip($remove));

            $block = Block::updateOrCreate(['id' => $block_id], $blockToCreate);

            // Sync Products
            $block->selected_products()->sync($selected_products);

            // Sync Collections
            $block->selected_collections()->sync($selected_collections);

            if ($block_id) {
                Icon::where('block_id', $block_id)->delete();
            }
            foreach ($data['icons'] as $icon) {
                Icon::create([
                    'block_id' => $block->id,
                    'icon_id' => $icon['icon']['id'],
                    'icon_type' => $icon['icon']['type'],
                    'title' => $icon['title'],
                    'subtitle' => $icon['subtitle'],
                    'show_link' => $icon['show_link'],
                    'link' => $icon['link'],
                    'open_to_new_tab' => $icon['open_to_new_tab'],
                    'show_condition' => $icon['show_condition'],
                    'tags' => $icon['tags'],
                    'position' => $icon['position'],
                ]);
            }

            DB::commit();
            $user = auth()->user();

            if(!$block_id && $user['segment_events']['created_block'] == 0){
                $extension_url = "https://" . $user->name . '/admin/themes/current/editor?context=apps&appEmbed=' . getThemeEmbedUuid() . '/' . getThemeBlockName();

                /** Send Block Created Event to Segment */
                sendSegmentTrackEvent($user->id,'Created Block', [
                    'app_name' => config('app.name'),
                    'country' => $user->country,
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                    'currency' => $user->currency,
                    'email' => $user->owner_email ? maskEmail($user->owner_email) : '',
                    'language' => $user->language,
                    'name' => $user->owner_name ? maskName($user->owner_name) : '',
                    'phone' => $user->phone ? maskPhone($user->phone) :'',
                    'shop_owner' => $user->shop_owner,
                    'shopify_plan_name' => $user->shopify_plan_name,
                    'store_url' => $user->name,
                    'theme_app_extension_url' => $extension_url,
                ]);
                $segment_events = $user['segment_events'];
                $segment_events['created_block'] = 1;
                auth()->user()->update(['segment_events' => $segment_events]);
            }

            return  Block::with('icons')->where('id', $block->id)->first();
        } catch (\Exception $e) {
            DB::rollback();
            info('Error', [$e]);

            throw $e;
        }
    }

    /**
     * Delete icon block by ID.
     *
     * @param $id
     */
    public function delete($id)
    {
        $block = Block::findOrFail($id);
        SelectedProduct::where('block_id', $id)->delete();
        SelectedCollection::where('block_id', $id)->delete();
        $block->icons()->delete();
        $block->delete();
    }

    /**
     * Update Icon Block Status.
     *
     * @param $id
     * @param $status
     */
    public function updateStatus($id, $status)
    {
        $block = Block::findOrFail($id);

        $block->update(['is_enabled' => $status === 'true']);
    }

    /**
     * Duplicate particular icon block.
     *
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\HigherOrderBuilderProxy|\Illuminate\Support\HigherOrderCollectionProxy|mixed
     *
     * @throws \Exception
     */
    public function duplicate($id)
    {
        try {
            DB::beginTransaction();

            $block = Block::with(['icons', 'selected_products:id', 'selected_collections:id'])->findOrFail($id);
            $new_block = $block->replicate();
            $new_block->manual_placement_id = $new_block->manual_placement_id ? str_shuffle($new_block->manual_placement_id) : null;
            $new_block->save();

            $selectedProducts = array_column($new_block['selected_products']->toArray(), 'id');
            $selectedCollections = array_column($new_block['selected_collections']->toArray(), 'id');
            $new_block->selected_products()->attach($selectedProducts);
            $new_block->selected_collections()->attach($selectedCollections);

            $_icons = [];
            foreach ($new_block['icons']->toArray() as $icon) {
                $icon['block_id'] = $new_block->id;
                $icon['created_at'] = $icon['updated_at'] = now();
                unset($icon['id']);
                $_icons[] = $icon;
            }

            Icon::insert($_icons);

            DB::commit();

            return $new_block->id;
        } catch (\Exception $e) {
            DB::rollback();
            info('Error', [$e]);

            throw $e;
        }
    }

    /**
     * Retrive Blocks for Home page.
     *
     * @return array[]
     */
    public function indexBlocks()
    {
        return $this->commonBlocks('homepage');
    }

    /**
     * Retrive product page blocks from different position.
     *
     * @param $data
     *
     * @return array[]
     */
    public function productBlocks($data)
    {
        $response = $this->commonBlocks('all-products');
        $product = $response['all-products'];
        if ($response['all-products'] === null) {
            // Find Block By Selected Products
            $selected_product = null;
            if (@$data['product_id']) {
                $selected_product = Block::with(['icons', 'icons.appIcon','icons.customIcon'])
                    ->whereHas('selected_products', static function ($q) use ($data) {
                        $q->where('products.product_id', $data['product_id']);
                    })
                    ->where('position', 'selected-products')
                    ->where('user_id', auth()->id())
                    ->where('is_enabled', 1)
                    ->orderBy('created_at')
                    ->get();

                $selected_product = $selected_product->keyBy('position');
                $selected_product = $selected_product->get('selected-products');

                if (isset($selected_product['icons']) && !empty($selected_product['icons'])) {
                    $product = $this->fetchS3Icon($selected_product);
                }
            }

            if (!$selected_product && @$data['collection_ids']) {
                $collection_ids = explode(',', $data['collection_ids']);
                $selected_collections = Block::with(['icons', 'icons.appIcon','icons.customIcon'])
                    ->whereHas('selected_collections', static function ($q) use ($collection_ids) {
                        $q->whereIn('collections.collection_id', $collection_ids);
                    })
                    ->where('position', 'collection-products')
                    ->where('user_id', auth()->id())
                    ->where('is_enabled', 1)
                    ->orderBy('created_at')
                    ->get();

                $selected_collections = $selected_collections->keyBy('position');

                $selected_collections = $selected_collections->get('collection-products');

                if (isset($selected_collections['icons']) && !empty($selected_collections['icons'])) {
                    $product = $this->fetchS3Icon($selected_collections);
                }
            }
        }

        $response['product'] = $product;

        return $response;
    }

    /**
     * Retrive cart page icon blocks.
     *
     * @return array[]
     */
    public function cartBlocks()
    {
        return $this->commonBlocks('cart');
    }

    /**
     * Retrive site common page icon blocks.
     *
     * @return array[]
     */
    public function siteCommonBlocks()
    {
        return $this->commonBlocks('');
    }

    /**
     * Retrive common icon block(header, footer and manual placement) for all requested pages.
     *
     * @param $position
     *
     * @return array[]
     */
    public function commonBlocks($position)
    {
        $positions = ['header', 'footer', 'manual'];
        if($position){
            $positions = array_merge($positions, [ $position]);
        }

        $query = Block::with(['icons', 'icons.appIcon','icons.customIcon','selected_products','selected_collections'])
            ->whereIn('position', $positions)
            ->where('user_id', auth()->id())
            ->where('is_enabled', 1)
            ->orderBy('created_at', 'DESC')
            ->get();
        $blocks = $query->keyBy('position');
        $position_data = $blocks->get($position);

        $header = $blocks->get('header');
        $footer = $blocks->get('footer');

        $manualBlocks = [];
        $query->each(function ($collection, $index) use(&$manualBlocks){
            $collection = $collection->toArray();
            if($collection['position'] === 'manual'){
                if (isset($collection['icons']) && !empty($collection['icons'])) {
                    $collection = $this->fetchS3Icon($collection);
                }
                $manualBlocks[] = $collection;
            }
        });

        if (isset($position_data['icons']) && !empty($position_data['icons'])) {
            $position_data = $this->fetchS3Icon($position_data);
        }
        if (isset($header['icons']) && !empty($header['icons'])) {
            $header = $this->fetchS3Icon($header);
        }
        if (isset($footer['icons']) && !empty($footer['icons'])) {
            $footer = $this->fetchS3Icon($footer);
        }

        return [
            $position => $position_data,
            'header' => $header,
            'footer' => $footer,
            'manual' => $manualBlocks,
        ];
    }

    /**
     * Retrive SVG icon from cache if available otherwise fetch from S3.
     *
     * @param $block
     *
     * @return array
     */
    public function fetchS3Icon($block)
    {
        foreach ($block['icons'] as $key => $icon) {
            if($block['position'] == 'manual'){
                if ($icon['icon_type'] === 'app-icon' && @$icon['app_icon']['type'] === 'default_icons' && str_ends_with(@$icon['app_icon']['url'], 'svg')) {
                    $icon = $icon['app_icon'];
                    // Fetch SVG from S3
                    $path = config('app.cloudfront_icon_host') . '/' . $icon['type'] . '/' . $icon['category'] . '/' . $icon['name'];

                    $svg = Cache::rememberForever($path, static fn () => file_get_contents($path));
                    $block['icons'][$key]['app_icon']['svg'] = $svg;
                }
            }else{
                if ($icon['icon_type'] === 'app-icon' && @$icon['appIcon']['type'] === 'default_icons' && str_ends_with(@$icon['appIcon']['url'], 'svg')) {
                    $icon = $icon['appIcon'];
                    // Fetch SVG from S3
                    $path = config('app.cloudfront_icon_host') . '/' . $icon['type'] . '/' . $icon['category'] . '/' . $icon['name'];

                    $svg = Cache::rememberForever($path, static fn () => file_get_contents($path));
                    $block['icons'][$key]['appIcon']['svg'] = $svg;
                }
            }
        }

        return $block;
    }
}
