<?php

namespace App\Events;

use App\Models\FileShare;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FileShared implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $fileShare;

    /**
     * Create a new event instance.
     */
    public function __construct(FileShare $fileShare)
    {
        $this->fileShare = $fileShare;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('project.'.$this->fileShare->project_id),
        ];
    }

    public function broadcastAs()
    {
        return 'file.shared';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->fileShare->id,
            'file_name' => $this->fileShare->file_name,
            'file_path' => $this->fileShare->file_path,
            'created_at' => $this->fileShare->created_at->toDateTimeString(),
            'user' => [
                'id' => $this->fileShare->user->id,
                'name' => $this->fileShare->user->name,
                'image_url' => $this->fileShare->user->image_url,
            ]
        ];
    }
}
