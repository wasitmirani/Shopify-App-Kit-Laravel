<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\FAQRepositoryInterface;
use App\Models\FAQ;

class FAQRepository implements FAQRepositoryInterface
{
    /**
     * Retrive list of faqs.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return FAQ::all();
    }
}
