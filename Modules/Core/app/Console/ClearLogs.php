<?php

namespace Modules\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Modules\Core\Emails\LogClearedEmail;
use Modules\Core\Models\User;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class ClearLogs extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'logs:clear';

    /**
     * The console command description.
     */
    protected $description = 'Clear all laravel log files.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logs = storage_path('logs/*.log');
        $files = glob($logs);

        if ($files === [] || $files === false) {
            $this->info('No log files found.');

            return SymfonyCommand::SUCCESS;
        }

        foreach ($files as $file) {
            File::put($file, '');
        }

        $emails = User::query()->byRole('super_admin')->pluck('email');

        Mail::to($emails)->queue(new LogClearedEmail);

        $this->info('Application logs cleared');

        return SymfonyCommand::SUCCESS;
    }
}
