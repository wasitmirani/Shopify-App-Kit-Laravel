<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Interfaces\ReviewRepositoryInterface;
use Illuminate\Http\Request;

use function sendSuccess;

class ReviewController extends Controller
{
    /**
     * @var ReviewRepositoryInterface
     */
    private $reviewRepo;

    public function __construct(ReviewRepositoryInterface $reviewRepo)
    {
        $this->reviewRepo = $reviewRepo;
    }

    /**
     * Save review and send email to admin.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $this->reviewRepo->store($data);

        return sendSuccess('Review Stored And Mail Send Successfully');
    }
}
