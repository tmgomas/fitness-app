<?php

namespace App\Services\Agreement\Interfaces;

interface AgreementServiceInterface
{
    public function getAllAgreements();
    public function getAgreement(string $id);
    public function createAgreement(array $data);
    public function updateAgreement(string $id, array $data);
    public function deleteAgreement(string $id);
    public function getLatestAgreement();
    public function checkUserAgreement(int $userId, string $agreementId = null);
    public function acceptAgreement(int $userId, string $agreementId, string $ipAddress = null, string $userAgent = null);
}