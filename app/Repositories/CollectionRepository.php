<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\CollectionRepositoryInterface;
use App\Models\Collection;

use Illuminate\Support\Facades\DB;
use function auth;

class CollectionRepository implements CollectionRepositoryInterface
{
    /**
     * Retrieve collections of logged-in user.
     *
     * @param mixed $query
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getCollections($query)
    {
        return  DB::table('collections')->where('title', 'LIKE', '%' . $query . '%')->where('user_id', auth()->id())->get();
    }
}
