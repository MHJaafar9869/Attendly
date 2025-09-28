<?php

declare(strict_types=1);

namespace App\Repositories\BaseRepository;

use Illuminate\Contracts\Database\Eloquent\Builder;

interface BaseRepositoryInterface
{
    public function select(string|array $columns): Builder;

    public function all();

    public function allWithRelations(string|array $relations): Builder;

    public function paginate();

    public function addQuery();

    public function find(int|string $id);

    public function findWithRelation(int|string $id, string|array $relations): Builder;

    public function create(array $data);

    public function update(int|string $id, array $data);

    public function delete(int|string $id);

    public function restore(int|string $id);

    public function forceDelete(int|string $id);
}
