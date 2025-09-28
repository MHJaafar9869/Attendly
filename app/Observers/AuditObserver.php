<?php

namespace App\Observers;

class AuditObserver
{
    public function creating($model)
    {
        if ($this->check($model, 'created_at') && empty($model->created_by)) {
            $model->created_by = auth()->id();
        }
    }

    public function updating($model)
    {
        if ($this->check($model, 'updated_at')) {
            $model->updated_by = auth()->id();
        }
    }

    private function check($model, string $fillable): bool
    {
        return auth()->check() && $model->isFillable($fillable);
    }
}
