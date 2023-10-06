<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use App\Repositories\UserRepository;
use App\UserNotifySetting;
use App\Notifications\NewActivities as NewActivitiesNotification;

class SendDailyNewActivities extends Command
{
    protected $userRepository;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:daily-new-activities';

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
            ['name', '=', UserNotifySetting::NAME_NEW_ACTIVITY,],
            ['value', '=', UserNotifySetting::VALUE_DAILY,]
        ];
        $users = $this->userRepository->getUserHasActivitiesAfter($date, $setting);
        foreach($users as $user) {
            $senders = $user->inboxActivityUsers->unique('id');
            $user->notify(new NewActivitiesNotification($senders));
        }
    }
}
