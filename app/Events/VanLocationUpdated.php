<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VanLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $vanId;
    public $latitude;
    public $longitude;
    public $childIds;

    public function __construct($vanId, $latitude, $longitude, $childIds = [])
    {
        $this->vanId = $vanId;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->childIds = $childIds;
    }

    public function broadcastOn()
    {
        // Channel for dashboard
        $channels = [new Channel('van-location.'.$this->vanId)];
        
        // Channels for each parent
        foreach ($this->childIds as $childId) {
            $channels[] = new Channel('child-location.'.$childId);
        }
        
        return $channels;
    }
    
    public function broadcastAs()
    {
        return 'location.updated';
    }
}