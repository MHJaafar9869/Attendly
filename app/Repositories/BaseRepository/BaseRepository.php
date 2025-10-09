<?php

declare(strict_types=1);

namespace App\Repositories\BaseRepository;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function select(string | array $columns): Builder
    {
        return $this->model->query()->select($columns);
    }

    public function all()
    {
        return $this->model->all();
    }

    public function allWithRelations(string | array $relations): Builder
    {
        return $this->model->with($relations);
    }

    public function paginate()
    {
        return $this->model->paginate();
    }

    public function find(int | string $id)
    {
        return $this->model->findOrFail($id);
    }

    public function findWithRelation(int | string $id, string | array $relations): Builder
    {
        return $this->model->query()->findOrFail($id)->with($relations);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int | string $id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);

        return $record;
    }

    public function delete(int | string $id)
    {
        $record = $this->find($id);

        return $record->delete();
    }

    public function restore(int | string $id)
    {
        $item = $this->model->onlyTrashed()->findOrFail($id);
        $item->restore();

        return $item;
    }

    public function forceDelete(int | string $id)
    {
        return $this->model->withTrashed()->find($id)->forceDelete();
    }

    public function addQuery(): Builder
    {
        return $this->model->query();
    }
}
