<?php

declare(strict_types=1);

namespace App\Repositories\BaseRepository;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function addQuery(): Builder
    {
        return $this->model->query();
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function allWithRelations(string | array $relations): Builder
    {
        return $this->model->with($relations);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function select(string | array $columns): Builder
    {
        return $this->model->query()->select($columns);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    public function find(int | string $id)
    {
        return $this->model->findOrFail($id);
    }

    public function findWithRelation(int | string $id, string | array $relations): Builder
    {
        return $this->model->with($relations)->where($this->model->getKeyName(), $id);
    }

    public function update(int | string $id, array $data)
    {
        $model = $this->find($id);
        $model->update($data);

        return $model;
    }

    public function delete(int | string $id): bool
    {
        return $this->find($id)->delete();
    }

    public function restore(int | string $id): bool
    {
        return $this->model->onlyTrashed()->findOrFail($id)->restore();
    }

    public function forceDelete(int | string $id): bool
    {
        return $this->model->withTrashed()->findOrFail($id)->forceDelete();
    }

    public function findBy(string $column, mixed $value): ?Model
    {
        return $this->model->where($column, $value)->first();
    }

    /**
     * Find a model by multiple criteria.
     */
    public function findOneBy(array $criteria): ?Model
    {
        $query = $this->addQuery();

        foreach ($criteria as $column => $value) {
            $query->where($column, $value);
        }

        return $query->first();
    }

    /**
     * Count models matching the given criteria.
     */
    public function count(array $criteria = []): int
    {
        $query = $this->model->query();

        foreach ($criteria as $column => $value) {
            $query->where($column, $value);
        }

        return $query->count();
    }

    /**
     * Check if any models exist matching the given criteria.
     */
    public function exists(array $criteria): bool
    {
        return $this->count($criteria) > 0;
    }
}
