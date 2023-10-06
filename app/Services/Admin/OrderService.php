<?php

namespace App\Services\Admin;

use App\User;
use Illuminate\Http\Request;
use App\Services\CoreService;
use App\Http\Resources\Admin\Order\All as OrderAllResource;
use App\Support\Collection;

class OrderService extends CoreService {
  public function all(Request $request) {
    $collection = User::whereNotNull('subscribe')->get();
    $items = [];    
    $subscribe = $collection->map(function($user) use (&$items) {
      $items[$user->subscribe][] = $user;
    });
    $results = [];
    collect($items)->each(function($type) use (&$results) {
      collect($type)->each(function($user) use (&$results) {
        $method = "{$user->subscribe}Payments";
        if (method_exists($user, $method)) {
          $payemnts = $user->{$method}()->get();        
          $result = $payemnts->map(function($payment) use ($user) {
            $name = "";
            if ( ! empty($payment->plan->type)) {
              $name = ucfirst($payment->plan->type) . " " . "({$payment->plan->name})";
            } else {
              if ( ! empty($payment->subscription)) {
                $name = "PayPal ({$payment->subscription->plan->name})";
              }
            }

            $type = $payment->subscription;
            return [
              'id' => $user->id,
              'name' => $user->first_name . " " . $user->last_name,
              'type' => $name,
              'date' => $payment->created_at->format("Y-m-d H:m:i"),
              'amount' => $payment->amount,
              'profile_id' => $user->profile_id,
            ];
          });
          $results = array_merge($results, $result->toArray());
        }
      });
    });
    
    $results = collect($results);

    if ($request->search) {
      $results = $results->filter(function($item) use ($request) {     
        return (stripos($item['name'], $request->search) !== false) || 
              (stripos($item['id'], $request->search) !== false);
      });
    }    

    $collection = (new Collection($results))->paginate(User::ADMIN_PAGINATE_PER_PAGE);    
    return response()->result($collection);
  }
}