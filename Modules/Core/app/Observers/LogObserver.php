<?php

namespace Modules\Core\Observers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Modules\Core\Jobs\RecordActivityLog;
use Throwable;

class LogObserver
{
    protected static array $stack = [];

    protected static ?Carbon $timelimit = null;

    protected int $threshold = 25;

    protected int $timeoutSeconds = 180;

    public function created($model): void
    {
        $this->record('created', $model);
    }

    public function updated($model): void
    {
        $this->record('updated', $model);
    }

    public function deleted($model): void
    {
        $this->record('deleted', $model);
    }

    private function record(string $action, $model): void
    {
        if ($model instanceof ActivityLog) {
            return;
        }

        $trackables = method_exists($model, 'trackables') ? $model->trackables() : [];

        $dirty = collect();

        if ($action === 'updated') {
            $original = collect($model->getOriginal());
            $current = collect($model->getAttributes());

            if ($trackables === ['all']) {
                $original->except(['created_at', 'updated_at', 'deleted_at']);
                $current->except(['created_at', 'updated_at', 'deleted_at']);
            } elseif ($trackables !== []) {
                $original->only($trackables);
                $current->only($trackables);
            } else {
                return;
            }

            $dirty = $current->diffAssoc($original);

            if ($dirty->isEmpty()) {
                return;
            }
        }

        $actorId = sanctumUser()?->id;
        $now = now();
        $roles = ['system'];
        if ($actorId) {
            $roles = DB::table('user_roles')
                ->join('roles', 'user_roles.role_id', '=', 'roles.id')
                ->where('user_roles.user_id', $actorId)
                ->pluck('roles.name')
                ->toArray();

            $roles = ! empty($roles) ? $roles : ['user'];
        }

        $data = [
            'user_id' => $actorId ?? null,
            'action_type' => $action,
            'loggable_type' => \get_class($model),
            'loggable_id' => $model?->id ?? null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'meta' => [
                'roles' => $roles,
                'action' => $action,
            ],
            'created_at' => $now,
            'updated_at' => $now,
        ];

        if ($action === 'updated') {
            $data['meta']['changes'] = $dirty->toArray();
        }

        self::$stack[] = $data;

        if (! self::$timelimit instanceof Carbon) {
            self::$timelimit = $now->addSeconds($this->timeoutSeconds);
        }

        $flushByThreshold = \count(self::$stack) >= $this->threshold;
        $flushByTimelimit = $now->gte(self::$timelimit);

        if ($flushByThreshold || $flushByTimelimit) {
            RecordActivityLog::dispatch(self::$stack)->onQueue('activity_logs');
            self::$stack = [];
            self::$timelimit = null;
        }
    }

    public function __destruct()
    {
        if (self::$stack !== []) {
            try {
                RecordActivityLog::dispatch(self::$stack)->onQueue('activity_logs');
            } catch (Throwable $e) {
                logger()->error('Failed to flush activity log stack', ['error' => $e->getMessage()]);
            } finally {
                self::$stack = [];
            }
        }
    }
}
