<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\ReviewRatingMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

use function config;

class SendReviewRatingMailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $data;

    public $user;

    /**
     * Create a new job instance.
     *
     * @param mixed $data
     * @param mixed $user
     */
    public function __construct($data, $user)
    {
        $this->data = $data;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to(config('app.admin_email'))->send(new ReviewRatingMail($this->data, $this->user));
    }
}
