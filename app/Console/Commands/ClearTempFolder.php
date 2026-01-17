<?php

namespace App\Console\Commands;

use App\Models\TemporaryFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ClearTempFolder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-temp-folder {--dry-run : Display what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete temporary upload folders older than 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = now();
        $isDryRun = $this->option('dry-run');
        $deletedFolders = 0;
        $deletedRecords = 0;
        $errors = 0;

        if ($isDryRun) {
            $this->info('Running in dry-run mode - no files will be deleted');
        }

        // Define paths to check
        $basePaths = ['posts/tmp', 'avatars/tmp'];
        $cutoffTime = now()->subHours(24)->timestamp;

        // Phase 1: Delete old directories and their DB records
        foreach ($basePaths as $basePath) {
            if (! Storage::exists($basePath)) {
                continue;
            }

            $directories = Storage::directories($basePath);

            if (empty($directories)) {
                continue;
            }

            foreach ($directories as $fullPath) {
                try {
                    $lastModified = Storage::lastModified($fullPath);

                    if ($lastModified < $cutoffTime) {
                        $folderName = basename($fullPath);

                        if ($isDryRun) {
                            $this->line("Would delete: {$fullPath} (modified ".now()->createFromTimestamp($lastModified)->diffForHumans().')');
                        } else {
                            // Delete the directory
                            if (Storage::deleteDirectory($fullPath)) {
                                $deletedFolders++;
                                $this->line("Deleted: {$fullPath}");

                                // Delete corresponding DB record if it exists
                                $tmpFile = TemporaryFile::where('folder', $folderName)->first();
                                if ($tmpFile) {
                                    $tmpFile->delete();
                                    $deletedRecords++;
                                }
                            } else {
                                $this->error("Failed to delete: {$fullPath}");
                                $errors++;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    $this->error("Error processing {$fullPath}: ".$e->getMessage());
                    $errors++;
                    Log::error('ClearTempFolder: Error processing directory', [
                        'path' => $fullPath,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        // Phase 2: Clean orphaned DB records (folders that were manually deleted)
        if (! $isDryRun) {
            $orphanedRecords = 0;
            $allRecords = TemporaryFile::all();

            foreach ($allRecords as $record) {
                $postsPath = 'posts/tmp/'.$record->folder;
                $avatarsPath = 'avatars/tmp/'.$record->folder;

                if (! Storage::exists($postsPath) && ! Storage::exists($avatarsPath)) {
                    try {
                        $record->delete();
                        $orphanedRecords++;
                        $deletedRecords++;
                    } catch (\Exception $e) {
                        $this->error("Error deleting orphaned record {$record->folder}: ".$e->getMessage());
                        $errors++;
                    }
                }
            }

            if ($orphanedRecords > 0) {
                $this->line("Cleaned {$orphanedRecords} orphaned database records");
            }
        }

        // Output summary
        $this->newLine();
        if ($isDryRun) {
            $this->info('Dry-run complete - no changes were made');
        } elseif ($deletedFolders === 0 && $deletedRecords === 0) {
            $this->info('No temporary folders to clean');
        } else {
            $this->info("Successfully deleted {$deletedFolders} folder(s) and {$deletedRecords} database record(s)");
        }

        if ($errors > 0) {
            $this->warn("Encountered {$errors} error(s) during cleanup");
        }

        // Log the results
        if (! $isDryRun) {
            $duration = $startTime->diffInSeconds(now());
            Log::info('ClearTempFolder: Command completed', [
                'deleted_folders' => $deletedFolders,
                'deleted_records' => $deletedRecords,
                'errors' => $errors,
                'duration_seconds' => $duration,
            ]);
        }

        return $errors > 0 ? 1 : 0;
    }
}
