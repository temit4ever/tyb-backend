<?php

namespace App\Http\Controllers;

use App\Models\TaskType;
use Illuminate\Http\Request;
use App\Http\Resources\TaskType as TaskTypeResource;
class TaskTypeController extends Controller
{

  /**
   * Display a listing of the resource.
   *
   * @param \App\Models\TaskType $taskType
   *
   * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
   */
    public function index()
    {
      return TaskTypeResource::collection(TaskType::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \App\Http\Resources\TaskType
     */
    public function store(Request $request)
    {
      $this->validate($request, [
        'title' => 'required|min:3|max:100',
        'slug' => 'required|min:5|max:100',
      ]);

      return new TaskTypeResource(TaskType::create($request->all()));
    }

  /**
   * Display the specified resource.
   *
   * @param \Illuminate\Http\Request $request
   * @param \App\Models\TaskType $taskType
   *
   */
    public function show(TaskType $taskType)
    {
      return new TaskTypeResource($taskType);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TaskType  $taskType
     *
     * @return \App\Http\Resources\TaskType
     */
    public function update(Request $request, TaskType $taskType)
    {
      $this->validate($request, [
        'title' => 'required|min:3|max:100',
        'slug' => 'required|min:5|max:100',
      ]);
      $taskType->update($request->all());
      return new TaskTypeResource($taskType);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TaskType  $taskType
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaskType $taskType)
    {
        $taskType->delete();
    }
}
