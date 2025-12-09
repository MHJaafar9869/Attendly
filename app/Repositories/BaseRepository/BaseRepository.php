<?php

declare(strict_types=1);

namespace App\Repositories\BaseRepository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Core\DTO\Elequent\PaginateDto;

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
        return $this->addQuery()->all();
    }

    public function allWithRelations(string|array $relations, array $filters = []): Builder
    {
        $query = $this->addQuery()->with($relations);

        if (\count($filters) > 0) {
            foreach ($filters as $column => $value) {
                $query->where($column, $value);
            }
        }

        return $query;
    }

    public function paginateWithRelations(int $perPage, string $pageName, string|array|null $relations = null, array $filters = []): LengthAwarePaginator
    {
        $query = $this->addQuery()->with($relations);

        if (\count($filters) > 0) {
            foreach ($filters as $column => $value) {
                $query->where($column, $value);
            }
        }

        if ($filters['columns']) {
            return $query->paginate($perPage, $filters['columns'], $pageName);
        }

        return $query->paginate(perPage: $perPage, pageName: $pageName);
    }

    public function create(array $data)
    {
        return $this->addQuery()->create($data);
    }

    public function select(string|array $columns): Builder
    {
        return $this->addQuery()->select($columns);
    }

    public function paginate(PaginateDto $dto): LengthAwarePaginator
    {
        return $this->addQuery()->paginate($dto->perPage, $dto->columns, $dto->pageName);
    }

    public function find(int|string $id): ?Model
    {
        return $this->addQuery()->find($id);
    }

    public function findAndSelect(int|string $id, string|array $columns): Model
    {
        return $this->addQuery()
            ->select((array) $columns)
            ->find($id);
    }

    public function findWithRelations(int|string $id, string|array $relations): Builder
    {
        return $this->addQuery()->with($relations)->where($this->model->getKeyName(), $id);
    }

    public function update(int|string $id, array $data)
    {
        $model = $this->find($id);
        $model->update($data);

        return $model;
    }

    public function delete(int|string $id): bool
    {
        return $this->find($id)->delete();
    }

    public function restore(int|string $id): bool
    {
        return $this->addQuery()->onlyTrashed()->findOrFail($id)->restore();
    }

    public function forceDelete(int|string $id): bool
    {
        return $this->addQuery()->withTrashed()->findOrFail($id)->forceDelete();
    }

    public function findBy(string $column, mixed $value, bool $sanitize = false): ?Model
    {
        $value = $sanitize ? sanitize($value, true) : $value;

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
