<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Services\Results\PatientResultsService;
use Illuminate\Http\JsonResponse;

class ResultsController extends Controller
{
    public function __construct(
        private PatientResultsService $resultsService
    ) {}

    /**
     * GET /api/results
     */
    public function index(): JsonResponse
    {
        $patient = $this->getCurrentPatient();

        $response = $this->resultsService->buildResponse($patient);

        return response()->json($response);
    }

    /**
     * bierzemy aktualnego pacjenta.
     */

    protected function getCurrentPatient(): Patient
    { /** @var Patient $patient */
    $patient = auth('api')->user();

    return $patient->load('orders.results');
    }
}
