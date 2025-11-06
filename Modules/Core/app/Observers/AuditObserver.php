<?php

namespace Modules\Core\Observers;

class AuditObserver
{
    private array $tableColumns = [];

    public function creating($model)
    {
        if ($this->check($model, 'created_by') && $model->created_by === null) {
            $model->created_by = jwtGuard()->id();
        }
    }

    public function updating($model)
    {
        if ($this->check($model, 'updated_by')) {
            $model->updated_by = jwtGuard()->id();
        }
    }

    public function deleting($model)
    {
        if ($this->check($model, 'deleted_by') && $model->deleted_by === null) {
            $model->deleted_by = jwtGuard()->id();
            $model->saveQuietly();
        }
    }

    public function restoring($model)
    {
        if ($this->check($model, 'deleted_by') && $model->deleted_by !== null) {
            $model->deleted_by = null;
        }
    }

    private function check($model, string $column): bool
    {
        if (! jwtGuard()->check()) {
            return false;
        }

        $table = $model->getTable();

        if (! isset($this->tableColumns[$table])) {
            $this->tableColumns[$table] = $model->getConnection()
                ->getSchemaBuilder()
                ->getColumnListing($table);
        }

        return in_array($column, $this->tableColumns[$table]);
    }
}
