<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BlockRequest;
use App\Interfaces\BlockRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use function config;
use function file_get_contents;
use function info;
use function json_encode;
use function microtime;
use function sendResponse;
use function sendSuccess;
use function str_ends_with;

class BlockController extends Controller
{
    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepo;

    public function __construct(BlockRepositoryInterface $blockRepo)
    {
        $this->blockRepo = $blockRepo;
    }

    /**
     * Returns List Of Blocks.
     *
     * @return mixed
     */
    public function index()
    {
        $blocks = $this->blockRepo->index();

        return sendResponse($blocks, 'Blocks Retrieved Successfully');
    }

    /**
     * Save the entire block details.
     *
     * @return mixed
     */
    public function store(BlockRequest $request)
    {
        $data = $request->all();
        $response = $this->blockRepo->store($data);

        return sendResponse($response, 'Block Created Successfully');
    }

    /**
     * Retrive particular icon block details with svg.
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function getSingleIconBlock($id)
    {
        $block = $this->blockRepo->getSingleIconBlock($id);

        foreach (@$block['appIcons'] as $key => $app_icon) {
            if (@$app_icon['appIcon']['type'] === 'default_icons' && str_ends_with(@$app_icon['appIcon']['url'], 'svg')) {
                $icon = $app_icon['appIcon'];
                // Fetch SVG from S3
                $path = config('app.cloudfront_icon_host') . '/' . $icon->type . '/' . $icon->category . '/' . $icon->name;

                $svg = Cache::rememberForever($path, static fn () => file_get_contents($path));
                $block['appIcons'][$key]['svg'] = $svg;
            }
        }

        return sendResponse($block, 'Block Retrieved Successfully');
    }

    /**
     * Delete icon block by ID.
     *
     * @param $id
     *
     * @return mixed
     */
    public function delete($id)
    {
        $this->blockRepo->delete($id);

        return sendSuccess('Block Deleted Successfully');
    }

    /**
     * Update Icon Block Status.
     *
     * @param $id
     * @param $status
     *
     * @return mixed
     */
    public function updateStatus($id, $status)
    {
        $this->blockRepo->updateStatus($id, $status);

        return sendSuccess('Block Status Updated Successfully');
    }

    /**
     * Duplicate particular icon block.
     *
     * @param $id
     *
     * @return mixed
     */
    public function duplicate($id)
    {
        $id = $this->blockRepo->duplicate($id);

        return sendResponse($id, 'Block Duplicated Successfully');
    }

    /**
     * Retrive Blocks for Home page.
     *
     * @return mixed
     */
    public function indexBlocks()
    {
        $blocks = $this->blockRepo->indexBlocks();

        return sendResponse($blocks, 'Blocks Retrieved Successfully');
    }

    /**
     * Retrive product page blocks from different position.
     *
     * @param Request $request
     *
     * @return false|string
     */
    public function productBlocks(Request $request)
    {
        $blocks = $this->blockRepo->productBlocks($request->all());

        return json_encode([
            'data' => $blocks,
            'message' => 'Blocks Retrieved Successfully',
        ]);
    }

    /**
     * Retrive cart page icon blocks.
     *
     * @return false|string
     */
    public function cartBlocks()
    {
        $blocks = $this->blockRepo->cartBlocks();

        return json_encode([
            'data' => $blocks,
            'message' => 'Blocks Retrieved Successfully',
        ]);
    }

    /**
     * Retrive site common page icon blocks.
     *
     * @return false|string
     */
    public function siteCommonBlocks()
    {
        $blocks = $this->blockRepo->siteCommonBlocks();

        return json_encode([
            'data' => $blocks,
            'message' => 'Blocks Retrieved Successfully',
        ]);
    }
}
