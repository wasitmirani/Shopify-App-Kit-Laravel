<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Interfaces\TutorialRepositoryInterface;

use function sendResponse;

class TutorialController extends Controller
{
    /**
     * @var TutorialRepositoryInterface
     */
    private $tutorialRepo;

    public function __construct(TutorialRepositoryInterface $tutorialRepo)
    {
        $this->tutorialRepo = $tutorialRepo;
    }

    /**
     * Retrive list of tutorial.
     *
     * @return mixed
     */
    public function index()
    {
        $tutorials = $this->tutorialRepo->index();

        return sendResponse($tutorials, 'Tutorials Retrieved Successfully');
    }
}
