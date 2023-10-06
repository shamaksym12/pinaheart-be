<?php

namespace App\Events\Message;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\User;
use App\Dialog;

class GetDialogMessages
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $dialog;
    public $user;

    public function __construct(Dialog $dialog, User $user)
    {
        $this->dialog = $dialog;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new PrivateChannel('user.'.$this->recipeintId);
    }

    public function broadcastAs()
    {
        // return 'onNewMessage';
    }

    public function broadcastWith()
    {
        // $this->message->me = false;
        // return ['message' => new MessageResourse($this->message)];
    }
}
