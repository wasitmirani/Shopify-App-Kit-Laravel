<?php

declare(strict_types=1);

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function getUserPlan();

    public function getUserPageViewsCount();

    public function sendSegmentEvent($event);
}
