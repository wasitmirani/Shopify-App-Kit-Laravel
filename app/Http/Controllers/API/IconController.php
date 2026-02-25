<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Interfaces\IconRepositoryInterface;
use Illuminate\Http\Request;

use function info;
use function json_encode;
use function microtime;
use function sendResponse;

use const JSON_INVALID_UTF8_IGNORE;

class IconController extends Controller
{
    /**
     * @var IconRepositoryInterface
     */
    private $iconRepo;

    public function __construct(IconRepositoryInterface $iconRepo)
    {
        $this->iconRepo = $iconRepo;
    }

    /**
     * Returns Default Block Icons.
     *
     * @return false|string
     */
    public function getDefaultIcons()
    {
        $start1 = microtime(true);
        $response = $this->iconRepo->getDefaultIcons();
        $end1 = microtime(true);
        info('Time to get icons(sec)', [$end1 - $start1]);

        return json_encode([
            'data' => $response,
            'message' => 'Icons Retrieved Successfully',
        ], JSON_INVALID_UTF8_IGNORE);
    }

    /**
     * Retrive Regular or 3D Icons based on category and search query.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function getRegularIconsByCategory(Request $request)
    {
        $icons = $this->iconRepo->getRegularIconsByCategory($request);

        return sendResponse($icons, 'Icons Retrieved Successfully.');
    }

    /**
     * Retrive Regular or 3D Icons based on search query.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function getSearchIcons(Request $request)
    {
        $icons = $this->iconRepo->getSearchIcons($request);

        return sendResponse($icons, 'Icons Retrieved Successfully.');
    }

    /**
     * Retrive user uploaded icons.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function getCustomIcons(Request $request)
    {
        $icons = $this->iconRepo->getCustomIcons($request);

        return sendResponse($icons, 'Custom Icons Retrieved Successfully.');
    }

    /**
     * Upload custom icon.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function uploadIcon(Request $request)
    {
        $response = $this->iconRepo->uploadIcon($request);

        return sendResponse($response, 'Custom Icons Uploaded Successfully.');
    }

    /**
     * Retrieve particular icon with SVG.
     *
     * @param $id
     * @param Request $request
     *
     * @return false|string
     */
    public function getSingleIcon($id, Request $request)
    {
        $response = $this->iconRepo->getSingleIcon($id, $request->type);

        return json_encode([
            'data' => $response,
            'message' => 'Single Icon Retrieved Successfully',
        ]);
    }
}
