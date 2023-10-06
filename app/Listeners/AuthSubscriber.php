<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use App\Services\Client\ProfileService;
use App\Notifications\Auth\Recovery as AuthRecoveryNotification;

class AuthSubscriber implements ShouldQueue
{
    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function onRegister($event)
    {
        $this->profileService->setDefaultUserMatch($event->user);
        $this->profileService->setDefaultProfileParams($event->user);
        $this->profileService->setDefaultNotifySetting($event->user);
    }

    public function onRecovery($event)
    {
        $event->user->notify(new AuthRecoveryNotification($event->link));
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\Auth\Register',
            'App\Listeners\AuthSubscriber@onRegister'
        );
        $events->listen(
            'App\Events\Auth\Recovery',
            'App\Listeners\AuthSubscriber@onRecovery'
        );
    }
}
