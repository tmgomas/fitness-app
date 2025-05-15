<?php

namespace App\Repositories\UserAgreement\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface UserAgreementRepositoryInterface extends BaseRepositoryInterface
{
    public function getUserAgreements(int $userId);
    public function hasUserAcceptedAgreement(int $userId, string $agreementId);
    public function createUserAgreement(array $data);
}