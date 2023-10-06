<?php

namespace App\Services\Admin;

use Illuminate\Http\Request;
use App\Services\CoreService;
use App\Repositories\ActivityLogsRepository;

class ActivityLogsService extends CoreService
{
  protected $activityLogsRepository;

  public function __construct(ActivityLogsRepository $activityLogsRepository)
  {
    $this->activityLogsRepository = $activityLogsRepository;
  }

  public function create($input) {
    return $this->activityLogsRepository->create($input);
  }

  public function all($input) {
    return response()->result($this->activityLogsRepository->all($input));
  }
}
