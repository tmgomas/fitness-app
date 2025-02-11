<?php
// app/Repositories/HealthData/HealthDataRepository.php
namespace App\Repositories\HealthData;

use App\Models\UserHealthData;
use App\Repositories\Base\BaseRepository;
use App\Repositories\HealthData\Interfaces\HealthDataRepositoryInterface;

class HealthDataRepository extends BaseRepository implements HealthDataRepositoryInterface
{
    public function __construct(UserHealthData $model)
    {
        parent::__construct($model);
    }

    public function getAllForUser($userId)
    {
        return $this->model->where('user_id', $userId)
            ->orderBy('recorded_at', 'desc')
            ->get();
    }

    public function createForUser($userId, array $data)
    {
        return $this->model->create(array_merge($data, [
            'user_id' => $userId
        ]));
    }

    public function findForUser($userId, $healthId)
    {
        return $this->model->where('user_id', $userId)
            ->where('health_id', $healthId)
            ->firstOrFail();
    }

    public function updateForUser($userId, $healthId, array $data)
    {
        $record = $this->findForUser($userId, $healthId);
        $record->update($data);
        return $record;
    }

    public function deleteForUser($userId, $healthId)
    {
        $record = $this->findForUser($userId, $healthId);
        return $record->delete();
    }
}
