<?php

namespace App\Repositories;

use App\AdminActivityLog;

class ActivityLogsRepository
{
  public function create(array $data)
  {
    return AdminActivityLog::create($data);
  }

  public function all($input) {
    $query = AdminActivityLog::query();
    if ( ! empty($input['search'])) {
      $query = $query->where(function() use  (&$query, $input) {
        $query = $query->where('action', 'like', '%'.$input['search'].'%');
        $query->orWhereHas('staff', function($q) use ($input) {
          $q->where('first_name', 'like', '%'.$input['search'].'%');
          $q->orWhere('last_name', 'like', '%'.$input['search'].'%');
        });
        $query->orWhereHas('target', function($q) use ($input) {
          $q->where('first_name', 'like', '%'.$input['search'].'%');
          $q->orWhere('last_name', 'like', '%'.$input['search'].'%');
        });
      });
    } 

    
    if ( ! empty($input['name'])) {
      $query = $query->join('users', 'users.id', '=', 'admin_activity_logs.target_id');
      $query = $query->select('users.first_name as fname', 'admin_activity_logs.*');
      $query = $query->orderByRaw("FIELD(`fname`, '{$input['name']}') DESC");
    } else {  
      $query->orderBy('created_at', 'desc');
    }

    return $query->with(['staff', 'target'])->paginate();
  }
}
