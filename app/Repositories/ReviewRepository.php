<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\ReviewRepositoryInterface;
use App\Jobs\SendReviewRatingMailJob;
use App\Models\Review;

use function auth;
use function dispatch;

class ReviewRepository implements ReviewRepositoryInterface
{
    /**
     * Save review and send email to admin.
     *
     * @param $data
     */
    public function store($data)
    {
        Review::create($data + ['user_id' => auth()->id()]);

        dispatch(new SendReviewRatingMailJob($data, auth()->user()));
    }
}
