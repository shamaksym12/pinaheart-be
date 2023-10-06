<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use App\Repositories\MessageRepository;
use App\Activity;
use App\Dialog;
use App\Mail\User\SetOff as SetOffMail;

class UserSubscriber implements ShouldQueue
{
    protected $messageRepository;

    public function __construct(MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    public function onBecomePaid($event)
    {
        $user = $event->user;
        $user->sentMessages()->where('is_paid', false)->update(['is_paid' => true]);
        $user->inboxMessages()->where('is_paid', false)->update(['is_paid' => true]);
    }

    public function onBecomeFree($event)
    {
        $user = $event->user;
        $user->sentMessages()->where('is_paid', true)->notHavePaidUserInDialog()->update(['is_paid' => false]);
        $user->inboxMessages()->where('is_paid', true)->notHavePaidUserInDialog()->update(['is_paid' => false]);
    }

    public function onSetOff($event)
    {
        $user = $event->user;
        Mail::to(config('mail.admin_email'))->send(new SetOffMail($user, $event->reason));
        //Delete dialogs
        $dialogs = Dialog::user($user)->get();
        $this->messageRepository->forceDeleteDialogs($dialogs);
        //Delete activities
        Activity::user($user)->delete();
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\User\BecomePaid',
            'App\Listeners\UserSubscriber@onBecomePaid'
        );
        $events->listen(
            'App\Events\User\BecomeFree',
            'App\Listeners\UserSubscriber@onBecomeFree'
        );
        $events->listen(
            'App\Events\User\SetOff',
            'App\Listeners\UserSubscriber@onSetOff'
        );
    }
}
