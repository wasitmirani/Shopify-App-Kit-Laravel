<?php

declare(strict_types=1);

namespace App\Interfaces;

interface ReviewRepositoryInterface
{
    public function store($data);
}
