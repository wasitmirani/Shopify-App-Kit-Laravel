<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Interfaces\PlanRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;

use function sendResponse;

class PlanController extends Controller
{
    /**
     * @var PlanRepositoryInterface
     */
    private $planRepo;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepo;

    public function __construct(PlanRepositoryInterface $planRepo, UserRepositoryInterface $userRepo)
    {
        $this->planRepo = $planRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * Returns List Of Plans.
     *
     * @return mixed
     */
    public function index()
    {
        $plans = $this->planRepo->index();

        return sendResponse($plans, 'Plans Retrieved Successfully');
    }

    /**
     * Returns Current Authenticated User.
     *
     * @return mixed
     */
    public function getShop()
    {
        $res = $this->planRepo->getShop();

        return sendResponse($res, 'Shop Retrieved Successfully');
    }

    /**
     * Subscribe Free Plan.
     *
     * @return mixed
     */
    public function chooseFreePlan()
    {
        $this->planRepo->chooseFreePlan();

        $userPlan = $this->userRepo->getUserPlan();

        return sendResponse($userPlan, 'Free Plan Subscribed Successfully');
    }

    /**
     * Returns Current Active Plan.
     *
     * @return mixed
     */
    public function getActivePlan()
    {
        $res = $this->planRepo->getActivePlan();

        return sendResponse($res);
    }
}
