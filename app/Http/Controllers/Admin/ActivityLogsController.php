<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\ActivityLogsService;

class ActivityLogsController extends Controller
{
    protected $activityLogsService;

    public function __construct(ActivityLogsService $activityLogsService)
    {
        $this->activityLogsService = $activityLogsService;
    }

    public function create(Request $request)
    {
        return $this->activityLogsService->create($request->all());
    }

    public function getAll(Request $request) 
    {
        return $this->activityLogsService->all($request->all());
    }
}
