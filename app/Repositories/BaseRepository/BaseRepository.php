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

    /*
    |--------------------------------------------------------------------------
    | Read Operations
    |--------------------------------------------------------------------------
    */
    public function all(): Collection
    {
        return $this->addQuery()->get();
    }

    public function allWithRelations(string|array $relations, array $filters = []): Collection
    {
        $query = $this->addQuery()->with($relations);

        if (\count($filters) > 0) {
            foreach ($filters as $column => $value) {
                $query->where($column, $value);
            }
        }

        return $query->get();
    }

    public function paginate(PaginateDto $dto): LengthAwarePaginator
    {
        return $this->addQuery()->paginate(
            $dto->perPage,
            $dto->columns,
            $dto->pageName
        );
    }

    public function paginateWithRelations(
        int $perPage,
        string $pageName,
        array $columns = ['*'],
        string|array|null $relations = null,
        array $filters = []
    ): LengthAwarePaginator {
        $query = $this->addQuery()->with($relations);

        if (\count($filters) > 0) {
            foreach ($filters as $column => $value) {
                $query->where($column, $value);
            }
        }

        return $query->paginate(
            perPage: $perPage,
            columns: $columns,
            pageName: $pageName
        );
    }

    public function select(string|array $columns): Builder
    {
        return $this->addQuery()->select($columns);
    }

    public function find(int|string $id): ?Model
    {
        return $this->addQuery()->find($id);
    }

    public function findOrFail(int|string $id): Model
    {
        return $this->addQuery()->findOrFail($id);
    }

    public function findBy(array $criteria): Builder
    {
        $query = $this->addQuery();

        foreach ($criteria as $column => $value) {
            $query->where($column, $value);
        }

        return $query;
    }

    public function findOneBy(array $criteria): ?Model
    {
        return $this->findBy($criteria)->first();
    }

    public function findAndSelect(int|string $id, string|array $columns): ?Model
    {
        return $this->addQuery()
            ->withTrashed()
            ->select((array) $columns)
            ->find($id);
    }

    public function findWithRelations(int|string $id, string|array $relations): ?Model
    {
        return $this->addQuery()->withTrashed()->with($relations)->find($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Write Operations
    |--------------------------------------------------------------------------
    */

    public function create(array $data): Model
    {
        return $this->addQuery()->create($data);
    }

    public function update(int|string $id, array $data): Model
    {
        $model = $this->findOrFail($id);
        $model->update($data);

        return $model;
    }

    public function restore(int|string $id): bool
    {
        return $this->addQuery()->onlyTrashed()->findOrFail($id)->restore();
    }

    public function restoreMultiple(array $ids): bool
    {
        return $this->addQuery()->onlyTrashed()->whereIn('id', $ids)->restore();
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Operations
    |--------------------------------------------------------------------------
    */

    public function delete(int|string $id): bool
    {
        return $this->findOrFail($id)->delete();
    }

    public function deleteMultiple(array $ids): bool
    {
        return $this->addQuery()->whereIn('id', $ids)->delete();
    }

    public function forceDelete(int|string $id): bool
    {
        return $this->addQuery()->withTrashed()->findOrFail($id)->forceDelete();
    }

    public function forceDeleteMultiple(array $ids): bool
    {
        return $this->addQuery()->withTrashed()->whereIn('id', $ids)->forceDelete();
    }

    /*
    |--------------------------------------------------------------------------
    | Miscellaneous Operations
    |--------------------------------------------------------------------------
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
