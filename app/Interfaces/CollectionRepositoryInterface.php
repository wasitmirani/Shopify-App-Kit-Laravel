<?php

declare(strict_types=1);

namespace App\Interfaces;

interface CollectionRepositoryInterface
{
    public function getCollections($query);
}
