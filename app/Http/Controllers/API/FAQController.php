<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Interfaces\FAQRepositoryInterface;

use function sendResponse;

class FAQController extends Controller
{
    /**
     * @var FAQRepositoryInterface
     */
    private $faqRepo;

    public function __construct(FAQRepositoryInterface $faqRepo)
    {
        $this->faqRepo = $faqRepo;
    }

    /**
     * Retrive list of faqs.
     *
     * @return mixed
     */
    public function index()
    {
        $faqs = $this->faqRepo->index();

        return sendResponse($faqs, 'Faqs Retrieved Successfully');
    }
}
