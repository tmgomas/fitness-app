<?php

namespace App\Repositories\Agreement\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface AgreementRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllActive();
    public function findByVersion(string $version);
    public function getLatestActive();
}