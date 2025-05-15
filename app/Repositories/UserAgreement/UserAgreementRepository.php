<?php

namespace App\Repositories\UserAgreement;

use App\Models\UserAgreement;
use App\Repositories\Base\BaseRepository;
use App\Repositories\UserAgreement\Interfaces\UserAgreementRepositoryInterface;

class UserAgreementRepository extends BaseRepository implements UserAgreementRepositoryInterface
{
    public function __construct(UserAgreement $model)
    {
        parent::__construct($model);
    }

    public function getUserAgreements(int $userId)
    {
        return $this->model->where('user_id', $userId)
            ->with('agreement')
            ->get();
    }

    public function hasUserAcceptedAgreement(int $userId, string $agreementId)
    {
        return $this->model->where('user_id', $userId)
            ->where('agreement_id', $agreementId)
            ->exists();
    }

    public function createUserAgreement(array $data)
    {
        return $this->model->create($data);
    }
}