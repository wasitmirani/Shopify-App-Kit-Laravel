<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;

use Illuminate\Support\Facades\DB;
use function auth;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * Retrieve products of logged-in user.
     *
     * @param mixed $query
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getProducts($query)
    {
        return DB::table('products')->where('title', 'LIKE', '%' . $query . '%')->where('user_id', auth()->id())->get();
    }
}
