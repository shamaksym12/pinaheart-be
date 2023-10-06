<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use App\Events\User\BecomePaid as UserBecomePaidEvent;
use App\Events\User\BecomeFree as UserBecomeFreeEvent;

class CheckExpiredCoupons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coupons:checkexpired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make unpaid users with expired coupons';

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
        $expiderUsers = User::whereDate('coupon_to', $yesterday->format('Y-m-d'))->get();
        $expiderUsers->each(function($user){
            $user->update([
                'is_paid' => false,
                'coupon_to' => null,
            ]);
            event(new UserBecomeFreeEvent($user));
        });
    }
}
