<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $fileName;
    public $message;
    public $userName;
    public $group_id;

    public function __construct($fileName, $message, $userName, $group_id)
    {
        $this->fileName = $fileName;
        $this->message = $message;
        $this->userName = $userName;
        $this->group_id = $group_id;
    }

    public function broadcastOn()
    {
        return new Channel("group.$this->group_id");
    }

    public function broadcastWith()
    {
        $data = "{$this->fileName} has been {$this->message} by {$this->userName}";
        return ["message" => $data];
    }

    public function broadcastAs()
    {
        return 'SendNotification';
    }
}
