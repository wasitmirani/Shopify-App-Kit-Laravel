<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\IconRepositoryInterface;
use App\Models\AppIcon;
use App\Models\Block;
use App\Models\CustomIcon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use function auth;
use function config;
use function file_get_contents;
use function info;
use function str_ends_with;
use function time;

class IconRepository implements IconRepositoryInterface
{
    /**
     * Returns Default Block Icons.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDefaultIcons()
    {
        return Cache::rememberForever('default_icons', static function () {
            $ecommerceBoxSvgId = DB::table('app_icons')
                ->where('name', 'ecommerce-box.svg')
                ->where('category', 'ecommerce')
                ->value('id');

            $recycleSvgId = DB::table('app_icons')
                ->where('name', 'recycle-computer-recycling.svg')
                ->where('category', 'recycling')
                ->value('id');

            $ecommerceVerifySvgId = DB::table('app_icons')
                ->where('name', 'ecommerce1-verify.svg')
                ->where('category', 'ecommerce')
                ->value('id');

            $variousDownloadSvgId = DB::table('app_icons')
                ->where('name', 'various-download.svg')
                ->where('category', 'various')
                ->value('id');

            $icons = [
                [
                    'name' => 'ecommerce-box.svg',
                    'id' => $ecommerceBoxSvgId,
                    'type' => 'app-icon',
                    'svg' => Storage::disk('app_public')->get('default_icons/ecommerce/ecommerce-box.svg'),
                ], [
                    'name' => 'recycle-computer-recycling.svg',
                    'id' => $recycleSvgId,
                    'type' => 'app-icon',
                    'svg' => Storage::disk('app_public')->get('default_icons/recycling/recycle-computer-recycling.svg'),
                ], [
                    'name' => 'ecommerce1-verify.svg',
                    'id' => $ecommerceVerifySvgId,
                    'type' => 'app-icon',
                    'svg' => Storage::disk('app_public')->get('default_icons/ecommerce/ecommerce1-verify.svg'),
                ],
            ];

            return [
                'icons' => $icons,
                'add_more' => [
                    'name' => 'various-download.svg',
                    'id' => $variousDownloadSvgId,
                    'type' => 'app-icon',
                    'svg' => Storage::disk('app_public')->get('images/download_icon.svg'),
                ],
            ];
        });
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
        $type = ($request->type === 'regular') ? 'default_icons' : '3d_icons';
        $category = $request->category;
        $search = $request->search;

        $query = AppIcon::select(['id', 'name', 'url'])->where('type', $type);

        if ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%')
                ->orWhere('category', 'LIKE', '%' . $search . '%');
        } else {
            if ($category) {
                $query->where('category', $category);
            }
        }

        return Cache::rememberForever($type . '-' . $category . '-' . $search, static fn () => $query->get()->toArray());
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
        $type = ($request->type === 'regular') ? 'default_icons' : '3d_icons';
        $search = $request->search;

        $query = AppIcon::select(['id', 'category', 'name', 'url'])->where('type', $type);

        if ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%')
                ->orWhere('category', 'LIKE', '%' . $search . '%');
        }

        return Cache::rememberForever($type . '-' . $search, static fn () => $query->get()->groupBy('category')->toArray());
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
        return CustomIcon::select(['id', 'name', 'url'])->where('user_id', auth()->id())->get()->toArray();
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
        $response = [];

        // if ($request->hasFile('icon')) {
        //     $file = $request->file('icon');
        //     $dir = 'uploads/shop_custom_icons_' . auth()->id();
        //     $filename = time() . '.' . $file->getClientOriginalExtension();
        //     $uploadedFilePath = Storage::disk('s3')->putFileAs($dir, $file, $filename, 'public');

        //     $icon = CustomIcon::create([
        //         'user_id' => auth()->id(),
        //         'name' => $filename,
        //         'url' => config('app.aws_icon_host') . '/' . $uploadedFilePath,
        //     ]);

        //     $response = [
        //         'name' => $filename,
        //         'id' => $icon->id,
        //         'type' => 'custom',
        //         'url' => config('app.aws_icon_host') . '/' . $uploadedFilePath,
        //     ];
        // }
        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $dir = 'uploads/shop_custom_icons_' . auth()->id();
            $filename = time() . '.' . $file->getClientOriginalExtension();
        
            // Store the file in the public storage directory
            $file->storeAs($dir, $filename, 'public');
        
            $icon = CustomIcon::create([
                'user_id' => auth()->id(),
                'name' => $filename,
                'url' => asset('storage/' . $dir . '/' . $filename),
            ]);
        
            $response = [
                'name' => $filename,
                'id' => $icon->id,
                'type' => 'custom',
                'url' => asset('storage/' . $dir . '/' . $filename),
            ];
        }
        return $response;
    }

    /**
     * Retrieve particular icon with SVG.
     *
     * @param $id
     * @param $type
     *
     * @return string|null
     */
    public function getSingleIcon($id, $type)
    {
        $svg = '';
        if ($type === 'app-icon') {
            $icon = AppIcon::findOrFail($id);
            if ($icon->type === '3d_icons' || str_ends_with($icon->url, 'png')) {
                $svg = $icon->url;
            } else {
                $path = config('app.cloudfront_icon_host') . '/' . $icon->type . '/' . $icon->category . '/' . $icon->name;

                $svg = Cache::rememberForever($path, static fn () => file_get_contents($path));
            }
        } else {
            $icon = CustomIcon::findOrFail($id);
            $svg = $icon->url;
        }

        return $svg;
    }
}
