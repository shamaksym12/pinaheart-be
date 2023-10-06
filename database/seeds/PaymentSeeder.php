<?php

use Illuminate\Database\Seeder;
use App\User;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $service = app()->make(\App\Services\Client\PaymentService::class);
        $service->createPaypalPlans();
        $service->createDefaultWebhooks();
        $service->createStripePlans();
    }
}
