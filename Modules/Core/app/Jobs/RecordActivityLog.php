<?php

namespace Modules\Core\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

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
            DB::table('activity_logs')->insert($this->data);
            DB::afterCommit(fn () => logger()->info('Activities Recorded Successfully: #' . \count($this->data)));
            DB::afterRollBack(fn () => logger()->alert('Failed To Record Activities: #' . (\count($this->data) ?? 0)));
        });
    }
}
