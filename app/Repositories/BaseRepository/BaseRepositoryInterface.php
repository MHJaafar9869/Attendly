<?php

declare(strict_types=1);

namespace App\Repositories\BaseRepository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Core\DTO\Elequent\PaginateDto;

interface BaseRepositoryInterface
{
    public function addQuery(): Builder;

    /*
    |--------------------------------------------------------------------------
    | Read Operations
    |--------------------------------------------------------------------------
    */

    public function all(): Collection;

    public function allWithRelations(string | array $relations, array $filters = []): Builder;

    public function paginate(PaginateDto $dto): LengthAwarePaginator;

    public function paginateWithRelations(
        PaginateDto $dto,
        string | array | null $relations = null,
        array $filters = []
    ): LengthAwarePaginator;

    public function find(int | string $id);

    public function findOrFail(int | string $id): Model;

    public function findBy(array $criteria): Builder;

    public function findOneBy(array $criteria): ?Model;

    public function findAndSelect(int | string $id, string | array $columns): ?Model;

    public function findWithRelations(int | string $id, string | array $relations): ?Model;

    public function select(string | array $columns): Builder;

    /*
    |--------------------------------------------------------------------------
    | Write Operations
    |--------------------------------------------------------------------------
    */

    public function create(array $data): Model;

    public function update(int | string $id, array $data): Model;

    public function restore(int | string $id): bool;

    /*
    |--------------------------------------------------------------------------
    | Delete Operations
    |--------------------------------------------------------------------------
    */

    public function delete(int | string $id): bool;

    public function deleteMultiple(array $ids): bool;

    public function forceDelete(int | string $id): bool;

    public function forceDeleteMultiple(array $ids): bool;

    /*
    |--------------------------------------------------------------------------
    | Miscellaneous Operations
    |--------------------------------------------------------------------------
    */

    public function count(array $criteria = []): int;

    public function exists(array $criteria): bool;
}
