<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agreement\AcceptAgreementRequest;
use App\Http\Resources\AgreementResource;
use App\Services\Agreement\Interfaces\AgreementServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgreementController extends Controller
{
    protected $agreementService;

    public function __construct(AgreementServiceInterface $agreementService)
    {
        $this->agreementService = $agreementService;
    }

    public function getLatest(): JsonResponse
    {
        try {
            $agreement = $this->agreementService->getLatestAgreement();
            
            if (!$agreement) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No active agreements found',
                    'data' => null
                ]);
            }
            
            $userId = Auth::id();
            $hasAccepted = $this->agreementService->checkUserAgreement($userId, $agreement->id);
            
            return response()->json([
                'status' => 'success',
                'data' => new AgreementResource($agreement),
                'user_has_accepted' => $hasAccepted
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving latest agreement: ' . $e->getMessage()
            ], 500);
        }
    }

    public function accept(AcceptAgreementRequest $request): JsonResponse
    {
        try {
            $userId = Auth::id();
            $agreementId = $request->agreement_id;
            
            // Check if user has already accepted this agreement
            if ($this->agreementService->checkUserAgreement($userId, $agreementId)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'User has already accepted this agreement'
                ]);
            }
            
            $this->agreementService->acceptAgreement(
                $userId,
                $agreementId,
                $request->ip(),
                $request->userAgent()
            );
            
            return response()->json([
                'status' => 'success',
                'message' => 'Agreement accepted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error accepting agreement: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkStatus(): JsonResponse
    {
        try {
            $userId = Auth::id();
            $latestAgreement = $this->agreementService->getLatestAgreement();
            
            if (!$latestAgreement) {
                return response()->json([
                    'status' => 'success',
                    'needs_acceptance' => false,
                    'message' => 'No active agreements found'
                ]);
            }
            
            $hasAccepted = $this->agreementService->checkUserAgreement($userId, $latestAgreement->id);
            
            return response()->json([
                'status' => 'success',
                'needs_acceptance' => !$hasAccepted,
                'agreement' => $hasAccepted ? null : new AgreementResource($latestAgreement)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error checking agreement status: ' . $e->getMessage()
            ], 500);
        }
    }
}