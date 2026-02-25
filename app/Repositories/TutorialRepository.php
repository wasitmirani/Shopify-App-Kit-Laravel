<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\TutorialRepositoryInterface;
use App\Models\Tutorial;

class TutorialRepository implements TutorialRepositoryInterface
{
    /**
     * Retrive list of tutorial.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return Tutorial::all();
    }
}
