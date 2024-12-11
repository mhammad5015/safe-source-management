<?php

namespace App\Listeners;

use App\Services\logging\FileLoggerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class FileActionListner
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        FileLoggerService::logForFile($event->file_id, $event->action, [
            'user_id' => $event->user_id,
            'timestamp' => now(),
        ]);
    }
}
