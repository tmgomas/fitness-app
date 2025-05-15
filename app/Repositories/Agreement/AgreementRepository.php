<?php

namespace App\Repositories\Agreement;

use App\Models\Agreement;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Agreement\Interfaces\AgreementRepositoryInterface;

class AgreementRepository extends BaseRepository implements AgreementRepositoryInterface
{
    public function __construct(Agreement $model)
    {
        parent::__construct($model);
    }

    public function getAllActive()
    {
        return $this->model->where('is_active', true)
            ->orderBy('effective_date', 'desc')
            ->get();
    }

    public function findByVersion(string $version)
    {
        return $this->model->where('version', $version)->first();
    }

    public function getLatestActive()
    {
        return $this->model->where('is_active', true)
            ->orderBy('effective_date', 'desc')
            ->first();
    }
}