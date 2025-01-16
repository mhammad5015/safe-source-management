<?php

namespace App\Listeners;

use App\Models\File;
use App\Models\FileBackup;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileBackupListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        try {
            DB::beginTransaction();
            $file = File::find($event->file_id);
            if (!$file) {
                Log::error("File not found for backup. File ID: {$event->file_id}");
                return;
            }

            $lastBackup = FileBackup::where('file_id', $file->id)->orderBy('versionId', 'desc')->first();
            $nextVersionId = $lastBackup ? $lastBackup->versionId + 1 : 1;

            $backupPath = str_replace("storage/", "", $file->filePath);
            $backupFileName = 'backup/file_' . $file->id . '/version_' . $nextVersionId . '_' . basename($backupPath);
            $backupFullPath = Storage::disk('public')->put($backupFileName, Storage::disk('public')->get($backupPath));
            $storagePath = 'storage/' . $backupFileName;

            DB::commit();
            FileBackup::create([
                "file_id" => $file->id,
                "filePath" => $storagePath,
                "versionId" => $nextVersionId
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error ocured in filebackup listner:\n{$e}");
            throw $e;
        }
    }
}
