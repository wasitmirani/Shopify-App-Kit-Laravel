<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\IntegrationRepositoryInterface;
use App\Models\Integration;
use Illuminate\Support\Facades\Cache;

class IntegrationRepository implements IntegrationRepositoryInterface
{
    /**
     * Retrive list of integrations.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return Cache::rememberForever('app_integrations', static fn () => Integration::all());
    }
}
