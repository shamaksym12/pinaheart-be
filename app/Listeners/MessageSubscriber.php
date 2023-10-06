<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\Message\ReadMessages as ReadMessagesEvent;
use App\Dialog;
use App\Repositories\MessageRepository;

class MessageSubscriber implements ShouldQueue
{
    protected $messageRepository;

    public function __construct(MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    public function onGetDialogMessages($event)
    {
        $dialog = $event->dialog;
        $user = $event->user;
        $query = $user->inboxUnreadMessages()->wherePivot('dialog_id', $dialog->id);
        if($count = $query->count()) {
            $query->update(['read_at' => now()]);
            event(new ReadMessagesEvent($user, $count));
        }
    }

    public function onChangeBlock($event)
    {
        if($event->added) {
            $dialogs = Dialog::forUsers($event->who, $event->whom)->get();
            if($dialogs->count()) {
                $this->messageRepository->forceDeleteDialogs($dialogs);
            }
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        // $events->listen(
        //     'App\Events\Message\NewMessage',
        //     'App\Listeners\MessageSubscriber@onNewMessage'
        // );
        $events->listen(
            'App\Events\Message\GetDialogMessages',
            'App\Listeners\MessageSubscriber@onGetDialogMessages'
        );
        $events->listen(
            'App\Events\Person\ChangeBlock',
            'App\Listeners\MessageSubscriber@onChangeBlock'
        );
    }
}
