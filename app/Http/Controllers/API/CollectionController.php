<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Interfaces\CollectionRepositoryInterface;
use Illuminate\Http\Request;

use function sendResponse;

class CollectionController extends Controller
{
    /**
     * @var CollectionRepositoryInterface
     */
    private $collectionRepo;

    public function __construct(CollectionRepositoryInterface $collectionRepo)
    {
        $this->collectionRepo = $collectionRepo;
    }

    /**
     * Retrieve collections of logged-in user.
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        $query = $request->search;
        $collections = $this->collectionRepo->getCollections($query);

        return sendResponse($collections, 'Collections Retrieved Successfully.');
    }
}
