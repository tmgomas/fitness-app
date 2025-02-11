<?php

// app/Repositories/UserPreference/UserPreferenceRepository.php
namespace App\Repositories\UserPreference;

use App\Models\UserPreference;
use App\Repositories\Base\BaseRepository;
use App\Repositories\UserPreference\Interfaces\UserPreferenceRepositoryInterface;

class UserPreferenceRepository extends BaseRepository implements UserPreferenceRepositoryInterface
{
    public function __construct(UserPreference $model)
    {
        parent::__construct($model);
    }

    public function getAllForUser($userId)
    {
       
        return $this->model->where('user_id', $userId)
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function createForUser($userId, array $data)
    {
        return $this->model->create(array_merge($data, [
            'user_id' => $userId
        ]));
    }

    public function findForUser($userId, $prefId)
    {
        return $this->model->where('user_id', $userId)
            ->where('pref_id', $prefId)
            ->firstOrFail();
    }

    public function updateForUser($userId, $prefId, array $data)
    {
        $preference = $this->findForUser($userId, $prefId);
        $preference->update($data);
        return $preference;
    }

    public function deleteForUser($userId, $prefId)
    {
        $preference = $this->findForUser($userId, $prefId);
        return $preference->delete();
    }
}
