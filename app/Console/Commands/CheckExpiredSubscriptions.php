<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Events\User\BecomeFree as UserBecomeFreeEvent;

class CheckExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:checkexpired';

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
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $yesterday = now()->subDay();
        $expiderUsers = User::whereDate('old_subscribe_to', $yesterday->format('Y-m-d'))->get();
        $expiderUsers->each(function($user){
            $user->update([
                'is_paid' => false,
                'old_subscribe_to' => null,
            ]);
            event(new UserBecomeFreeEvent($user));
        });
    }
}
