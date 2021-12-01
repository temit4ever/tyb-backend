<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskDate as TaskDateResource;
use App\Http\Resources\TaskType as TaskTypeResource;
use App\Models\Task;
use App\Models\TaskDate;
use App\Models\TaskType;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaskDateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Task $task)
    {
        return TaskDateResource::collection($task->taskDates);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \App\Models\Task $task;
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Task $task)
    {
        $this->validate($request, [
            'activity_date' => 'required_unless:replace,true|array',
            'activity_date.*' => 'required_unless:replace,true|date',
            'replace' => 'sometimes|boolean',
        ]);

        // Delete old dates, if requested
        if ((bool) $request->get('replace', false)) {
            $task->taskDates()->delete();
        }

        foreach ($request->activity_date as $date) {
            TaskDate::firstOrCreate([
                'task_id' => $task->id,
                'activity_date' => Carbon::parse($date),
            ]);
        }

        return TaskDateResource::collection($task->taskDates()->get());
    }
}
