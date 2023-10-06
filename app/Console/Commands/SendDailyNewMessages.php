<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use App\Repositories\UserRepository;
use App\UserNotifySetting;
use App\Notifications\NewMessages as NewMessagesNotification;

class SendDailyNewMessages extends Command
{
    protected $userRepository;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:daily-new-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(UserRepository $userRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $date = now()->subDay();
        $setting = [
            ['type', '=', UserNotifySetting::TYPE_EMAIL,],
            ['name', '=', UserNotifySetting::NAME_NEW_MESSAGE,],
            ['value', '=', UserNotifySetting::VALUE_DAILY,]
        ];
        $users = $this->userRepository->getUserHasMessagesAfter($date, $setting);
        foreach($users as $user) {
            $senders = $user->inboxUnreadMessages->pluck('dialog.sender')->unique('id');
            $user->notify(new NewMessagesNotification($senders));
        }
        // Notification::send($users, new NewMessagesNotification);
    }
}
