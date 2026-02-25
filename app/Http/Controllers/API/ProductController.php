<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Interfaces\ProductRepositoryInterface;
use Illuminate\Http\Request;

use function sendResponse;

class ProductController extends Controller
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepo;

    public function __construct(ProductRepositoryInterface $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    /**
     * Retrieve products of logged-in user.
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        $query = $request->search;
        $products = $this->productRepo->getProducts($query);

        return sendResponse($products, 'Products Retrieved Successfully.');
    }
}
