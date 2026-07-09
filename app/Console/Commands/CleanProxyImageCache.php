<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CleanProxyImageCache extends Command
{
    protected $signature = 'proxy-images:cleanup
                            {--days=30 : Delete files older than N days (based on last modified time)}
                            {--dry-run : List files that would be deleted without actually deleting them}';

    protected $description = 'Clean up old proxy image cache files from storage/app/proxy-images/';

    public function handle(): int
    {
        $days       = (int) $this->option('days');
        $dryRun     = (bool) $this->option('dry-run');
        $cutoff     = now()->subDays($days)->timestamp;
        $diskPath   = 'proxy-images'; // relative to storage/app/

        if ($dryRun) {
            $this->info("[DRY RUN] Files older than {$days} days would be deleted.");
        } else {
            $this->info("Cleaning proxy image cache older than {$days} days...");
        }

        // List all files in the proxy-images directory (non-recursive, no subdirs)
        try {
            $files = Storage::files($diskPath);
        } catch (\Exception $e) {
            $this->error("Failed to list files in {$diskPath}: " . $e->getMessage());
            Log::error('proxy-images:cleanup — failed to list directory', ['error' => $e->getMessage()]);
            return Command::FAILURE;
        }

        $checked  = count($files);
        $deleted  = 0;
        $failed   = 0;
        $freedBytes = 0;

        foreach ($files as $file) {
            try {
                // Keamanan: hanya proses file di dalam proxy-images/, skip folder
                if (Storage::directoryExists($file)) {
                    continue;
                }

                $lastModified = Storage::lastModified($file);
                if ($lastModified === false) {
                    $this->warn("  Could not read mtime for: {$file}");
                    $failed++;
                    continue;
                }

                // Skip file yang masih baru
                if ($lastModified >= $cutoff) {
                    continue;
                }

                $size = Storage::size($file);

                if ($dryRun) {
                    $ageHuman = now()->diffForHumans(now()->setTimestamp($lastModified), ['parts' => 1]);
                    $this->line("  [WOULD DELETE] {$file} ({$this->humanBytes($size)}, modified {$ageHuman})");
                    $deleted++;
                    $freedBytes += $size;
                    continue;
                }

                // Hapus file
                Storage::delete($file);
                $deleted++;
                $freedBytes += $size;

            } catch (\Exception $e) {
                // Satu file gagal → catat warning dan lanjutkan, jangan crash
                $this->warn("  Failed to process file {$file}: " . $e->getMessage());
                Log::warning('proxy-images:cleanup — failed to process file', [
                    'file'  => $file,
                    'error' => $e->getMessage(),
                ]);
                $failed++;
            }
        }

        $this->info('');
        $this->info('--- Summary ---');
        $this->info("  Files checked : {$checked}");
        $this->info("  Files deleted : {$deleted}");
        $this->info("  Failures      : {$failed}");
        $this->info("  Space freed   : " . $this->humanBytes($freedBytes));

        if ($failed > 0) {
            Log::warning("proxy-images:cleanup completed with {$failed} failures", [
                'checked' => $checked, 'deleted' => $deleted, 'freed_bytes' => $freedBytes,
            ]);
        } else {
            Log::info('proxy-images:cleanup completed', [
                'checked' => $checked, 'deleted' => $deleted, 'freed_bytes' => $freedBytes,
            ]);
        }

        return Command::SUCCESS;
    }

    private function humanBytes(int $bytes): string
    {
        if ($bytes >= 1024 * 1024) {
            return round($bytes / 1024 / 1024, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}
