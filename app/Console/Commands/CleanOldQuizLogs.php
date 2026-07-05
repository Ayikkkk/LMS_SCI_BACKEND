<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanOldQuizLogs extends Command
{
    protected $signature   = 'quiz:clean-logs {--days=30 : Hapus log lebih dari N hari}';
    protected $description = 'Hapus quiz activity logs yang sudah lebih dari N hari (default 30)';

    public function handle(): int
    {
        $days    = (int) $this->option('days');
        $cutoff  = now()->subDays($days);

        try {
            $deleted = DB::connection('mysql_log')
                ->table('quiz_activity_logs')
                ->where('created_at', '<', $cutoff)
                ->delete();

            $this->info("Deleted $deleted quiz log records older than $days days.");
            Log::info("CleanOldQuizLogs: deleted $deleted records older than {$cutoff->toDateString()}");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to clean quiz logs: ' . $e->getMessage());
            Log::error('CleanOldQuizLogs failed: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
