<?php

declare(strict_types=1);

namespace App\Interfaces;

interface PlanRepositoryInterface
{
    public function index();

    public function plans();

    public function getShop();

    public function chooseFreePlan();

    public function getActivePlan();
}
