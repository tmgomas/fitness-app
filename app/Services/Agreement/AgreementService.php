<?php

namespace App\Services\Agreement;

use App\Services\Agreement\Interfaces\AgreementServiceInterface;
use App\Repositories\Agreement\Interfaces\AgreementRepositoryInterface;
use App\Repositories\UserAgreement\Interfaces\UserAgreementRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AgreementService implements AgreementServiceInterface
{
    protected $agreementRepository;
    protected $userAgreementRepository;

    public function __construct(
        AgreementRepositoryInterface $agreementRepository,
        UserAgreementRepositoryInterface $userAgreementRepository
    ) {
        $this->agreementRepository = $agreementRepository;
        $this->userAgreementRepository = $userAgreementRepository;
    }

    public function getAllAgreements()
    {
        return $this->agreementRepository->all();
    }

    public function getAgreement(string $id)
    {
        return $this->agreementRepository->find($id);
    }

    public function createAgreement(array $data)
    {
        return $this->agreementRepository->create($data);
    }

    public function updateAgreement(string $id, array $data)
    {
        return $this->agreementRepository->update($id, $data);
    }

    public function deleteAgreement(string $id)
    {
        return $this->agreementRepository->delete($id);
    }

    public function getLatestAgreement()
    {
        return $this->agreementRepository->getLatestActive();
    }

    public function checkUserAgreement(int $userId, string $agreementId = null)
    {
        if ($agreementId) {
            return $this->userAgreementRepository->hasUserAcceptedAgreement($userId, $agreementId);
        } else {
            // Check if user has accepted the latest agreement
            $latestAgreement = $this->getLatestAgreement();
            if (!$latestAgreement) {
                return true; // No agreement to accept
            }
            return $this->userAgreementRepository->hasUserAcceptedAgreement($userId, $latestAgreement->id);
        }
    }

    public function acceptAgreement(int $userId, string $agreementId, string $ipAddress = null, string $userAgent = null)
    {
        try {
            // Check if agreement exists
            $agreement = $this->agreementRepository->find($agreementId);
            if (!$agreement) {
                throw new \Exception('Agreement not found');
            }

            // Create user agreement record
            return $this->userAgreementRepository->createUserAgreement([
                'user_id' => $userId,
                'agreement_id' => $agreementId,
                'accepted_at' => Carbon::now(),
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent
            ]);
        } catch (\Exception $e) {
            Log::error('Error accepting agreement: ' . $e->getMessage());
            throw $e;
        }
    }
}