<?php

namespace Skimia\Assets\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BeforeMergeCollectionFiles extends Event
{
    use SerializesModels;

    protected $files = [];
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array &$collectionFiles)
    {
        $this->files = &$collectionFiles;
        //$this->files[] = 'google';
    }

    public function &getCollection(){
        return $this->files;
    }
    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
