<?php

namespace Modules\Core\Observers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Request;
use Modules\Core\Jobs\RecordActivityLog;

class LogObserver
{
    protected static array $stack = [];
    protected static int $threshold = 10;
    protected static int $timeoutSeconds = 180;
    protected static ?Carbon $timelimit = null;

    public function creating($model): void
    {
        $this->record('created', $model);
    }

    public function updating($model): void
    {
        $this->record('updated', $model);
    }

    public function deleting($model): void
    {
        $this->record('deleted', $model);
    }

    private function record(string $action, $model): void
    {
        $actor = jwtGuard()->user() ?? null;

        if ($model instanceof ActivityLog) {
            return;
        }

        $trackables = method_exists($model, 'trackables') ? $model->trackables() : [];

        if ($action === 'updated') {
            $dirty = collect($model->getDirty())->only($trackables)->toArray();
            if (empty($dirty)) {
                return;
            }
        } else {
            $dirty = [];
        }

        $data = [
            'user_id' => $actor?->id ?? null,
            'action_type' => $action,
            'loggable_type' => get_class($model),
            'loggable_id' => $model->id ?? null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'meta' => [
                'roles' => $actor->getRoles() ?? ['system'],
                'changes' => $dirty,
            ],
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ];

        self::$stack[] = $data;

        if (self::$timelimit === null) {
            self::$timelimit = now()->addSeconds(self::$timeoutSeconds);
        }

        if (count(self::$stack) >= self::$threshold || now()->gte(self::$timelimit)) {
            RecordActivityLog::dispatch(self::$stack)->onQueue('activity_logs');
            self::$stack = [];
            self::$timelimit = null;
        }
    }

    public function __destruct()
    {
        if (! empty(self::$stack)) {
            try {
                RecordActivityLog::dispatch(self::$stack)->onQueue('activity_logs');
            } catch (\Throwable $e) {
                logger()->error('Failed to flush activity log stack', ['error' => $e->getMessage()]);
            } finally {
                self::$stack = [];
            }
        }
    }
}
