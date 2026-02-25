<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Interfaces\BlockRepositoryInterface;
use App\Interfaces\FAQRepositoryInterface;
use App\Interfaces\IntegrationRepositoryInterface;
use App\Interfaces\PlanRepositoryInterface;
use App\Interfaces\ThemeRepositoryInterface;
use App\Interfaces\TutorialRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;

use Illuminate\Http\Request;
use function sendResponse;

class UserController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepo;

    /**
     * @var PlanRepositoryInterface
     */
    private $planRepo;

    /**
     * @var TutorialRepositoryInterface
     */
    private $tutorialRepo;

    /**
     * @var FAQRepositoryInterface
     */
    private $faqRepo;

    /**
     * @var IntegrationRepositoryInterface
     */
    private $integrationRepo;

    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepo;

    public function __construct(UserRepositoryInterface $userRepo, PlanRepositoryInterface $planRepo, TutorialRepositoryInterface $tutorialRepo, FAQRepositoryInterface $faqRepo, IntegrationRepositoryInterface $integrationRepo, BlockRepositoryInterface $blockRepo, ThemeRepositoryInterface $themeRepo)
    {
        $this->userRepo = $userRepo;
        $this->planRepo = $planRepo;
        $this->blockRepo = $blockRepo;
        $this->tutorialRepo = $tutorialRepo;
        $this->faqRepo = $faqRepo;
        $this->integrationRepo = $integrationRepo;
        $this->themeRepo = $themeRepo;
    }

    /**
     * Retrieve authenticated user with plan anc charge.
     *
     * @return mixed
     */
    public function getUserPlan()
    {
        $response = $this->themeRepo->activateExtension();

        if ($response['enabled'] === false && auth()->user()->is_extension_enabled){
            auth()->user()->update(['is_extension_enabled' => false, 'extension_enabled_at' => null]);
        }

        if ($response['enabled'] && !auth()->user()->is_extension_enabled) {
            auth()->user()->update(['is_extension_enabled' => true, 'extension_enabled_at' => now()]);
            $this->userRepo->sendSegmentEvent('theme_activated');
        }

        $userPlan = $this->userRepo->getUserPlan();
        $userPlan['plans'] = $this->planRepo->index();
        $userPlan['icon_blocks'] = $this->blockRepo->index();
        $userPlan['tutorials'] = $this->tutorialRepo->index();
        $userPlan['faqs'] = $this->faqRepo->index();
        $userPlan['integrations'] = $this->integrationRepo->index();

        return sendResponse($userPlan, 'User and plan retrieved successfully.');
    }

    /**
     * Retrieve authenticated user's page views count.
     *
     * @return mixed
     */
    public function getUserPageViewsCount()
    {
        $pageViewsCount = $this->userRepo->getUserPageViewsCount();

        return sendResponse($pageViewsCount, 'Retrieved Page View Count Successfully.');
    }

    /**
     * Retrieve authenticated user's page views count.
     *
     * @return mixed
     */
    public function sendSegmentEvent(Request $request)
    {
        $this->userRepo->sendSegmentEvent($request->event);

        return sendSuccess( 'Retrieved Page View Count Successfully.');
    }
}
