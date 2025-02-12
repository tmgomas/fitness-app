<?php

namespace App\Repositories\Base;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);
        return $record->fresh();
    }

    public function delete($id)
    {
        return $this->find($id)->delete();
    }

    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    public function paginate(int $perPage = 10)
    {
        return $this->query()->paginate($perPage);
    }
}
