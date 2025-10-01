<?php

namespace App\Observers;

class AuditObserver
{
    public function creating($model)
    {
        if ($this->check($model, 'created_by') && empty($model->created_by)) {
            $model->created_by = auth()->id();
        }
    }

    public function updating($model)
    {
        if ($this->check($model, 'updated_by')) {
            $model->updated_by = auth()->id();
        }
    }

    public function deleting($model)
    {
        if ($this->check($model, 'deleted_by') && empty($model->deleted_by)) {
            $model->deleted_by = auth()->id();
        }
    }

    public function restoring($model)
    {
        if ($this->check($model, 'deleted_by')) {
            $model->deleted_by = null;
        }
    }

    private function check($model, string $column): bool
    {
        return auth()->check() && $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), $column);
    }
}
