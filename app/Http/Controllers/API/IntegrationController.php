<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Interfaces\IntegrationRepositoryInterface;

use function sendResponse;

class IntegrationController extends Controller
{
    /**
     * @var IntegrationRepositoryInterface
     */
    private $integrationRepo;

    public function __construct(IntegrationRepositoryInterface $integrationRepo)
    {
        $this->integrationRepo = $integrationRepo;
    }

    /**
     * Retrive list of integrations.
     *
     * @return mixed
     */
    public function index()
    {
        $integrations = $this->integrationRepo->index();

        return sendResponse($integrations, 'Integrations Retrieved Successfully');
    }
}
