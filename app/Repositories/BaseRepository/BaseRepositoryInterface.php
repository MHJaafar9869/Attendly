<?php

declare(strict_types=1);

namespace App\Repositories\BaseRepository;

use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    public function select(string | array $columns): Builder;

    public function all(): Collection;

    public function allWithRelations(string | array $relations): Builder;

    public function paginate(): LengthAwarePaginator;

    public function addQuery(): Builder;

    public function find(int | string | BackedEnum $id);

    public function findWithRelation(int | string $id, string | array $relations): Builder;

    public function create(array $data);

    public function update(int | string $id, array $data);

    public function delete(int | string $id): bool;

    public function restore(int | string $id): bool;

    public function forceDelete(int | string $id): bool;

    public function findBy(string $column, mixed $value, bool $sanitize = false): ?Model;

    public function findOneBy(array $criteria): ?Model;

    public function count(array $criteria = []): int;

    public function exists(array $criteria): bool;
}
