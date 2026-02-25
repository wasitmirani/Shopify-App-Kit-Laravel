<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Interfaces\ThemeRepositoryInterface;
use App\Models\User;

use App\Repositories\UserRepository;
use function auth;
use function now;
use function sendResponse;

class ThemeController extends Controller
{
    /**
     * @var ThemeRepositoryInterface
     */
    private $themeRepo;

    public function __construct(ThemeRepositoryInterface $themeRepo, UserRepository $userRepo)
    {
        $this->themeRepo = $themeRepo;
        $this->userRepo = $userRepo;
    }

    public function activateExtension()
    {
        $response = $this->themeRepo->activateExtension();

        if (null === $response['theme_id']) {
            $user = auth()->user();
            $main_theme = $user->api()->rest('GET', '/admin/themes.json', ['role' => 'main']);
            $response['theme_id'] = @$main_theme['body']['themes'][0]['id'];
        }

        if ($response['enabled'] === false && auth()->user()->is_extension_enabled) {
            auth()->user()->update(['is_extension_enabled' => false, 'extension_enabled_at' => null]);
        }

        if ($response['enabled'] && !auth()->user()->is_extension_enabled) {
            auth()->user()->update(['is_extension_enabled' => true, 'extension_enabled_at' => now()]);
        }

        $user = User::withCount('reviews')->findOrFail(auth()->id());
        $response['user'] = $user;

        return sendResponse($response, 'Iconito App Extension Response Retrieved Successfully');
    }
}
