<?php

namespace App\Listeners;

use App\Services\logging\UserLoggerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserActionListener
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
        UserLoggerService::logForUser($event->user_id,$event->group_id,$event->action, [
            'user_id' => $event->user_id,
            'group_id' => $event->group_id,
            'timestamp' => now(),
        ]);
    }
}
