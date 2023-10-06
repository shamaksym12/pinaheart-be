<?php

namespace App\Repositories;

use App\Plan;

class PaymentRepository
{
    public function createPlan(array $data)
    {
        return Plan::create($data);
    }
}
