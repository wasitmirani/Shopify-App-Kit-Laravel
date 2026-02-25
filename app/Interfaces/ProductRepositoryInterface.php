<?php

declare(strict_types=1);

namespace App\Interfaces;

interface ProductRepositoryInterface
{
    public function getProducts($query);
}
