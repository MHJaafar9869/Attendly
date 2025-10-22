<?php

namespace Modules\Core\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\ActivityLog;

class RecordActivityLog implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected array $data) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            if (isset($this->data[0]) && is_array($this->data[0])) {
                ActivityLog::insertOrIgnore($this->data);
            } else {
                ActivityLog::create($this->data);
            }
        });
    }
}
