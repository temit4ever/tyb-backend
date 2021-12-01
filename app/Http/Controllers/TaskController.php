<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskType;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\Task as TaskResource;
use Spatie\QueryBuilder\QueryBuilder;


class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
      $tasks = QueryBuilder::for(Task::class)
        ->allowedIncludes(['users', 'taskDates'])
        ->get();

      return TaskResource::collection($tasks);
    }

  /**
   * Store a newly created resource in storage.
   *
   * @param \App\Models\Task $task
   * @param \Illuminate\Http\Request $request
   * @return \App\Http\Resources\Task
   * @throws \Illuminate\Validation\ValidationException
   */
    public function store(Task $task, Request $request)
    {
      $this->validate($request, [
        'title' => 'required|min:3|max:100',
        'user_ids' => 'array',
        'user_ids.*' => 'integer|exists:users,id',
        'type_slug' => 'required|exists:task_types,slug',
        'priority' => 'required|integer',
        'blocked' => 'required|boolean',
      ]);
      $title = $request->get('title');
      $description = $request->get('description');
      $slug = $request->get('type_slug');
      $priority = $request->get('priority');
      $blocked = $request->get('blocked');
      $task_type = TaskType::where('slug', $slug)->first();

      $task = Task::create([
        'title' => $title,
        'description' => $description,
        'type_id' => $task_type->id,
        'priority' => $priority,
        'blocked' => $blocked,
      ]);


      $user_ids = $request->get('user_ids');
      if (count($user_ids) > 0) {
          $task->users()->attach($user_ids);
      }

      return new TaskResource($task);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     *
     * @return \App\Http\Resources\Task
     */
    public function show(Task $task)
    {
      $taskResult = QueryBuilder::for(Task::where('id', $task->id))
        ->allowedIncludes(['users', 'taskDates'])
        ->first();
      return new TaskResource($taskResult);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     *
     * @return \App\Http\Resources\Task
     */
    public function update(Request $request, Task $task)
    {
      $this->validate($request, [
        'title' => 'required|min:3|max:100',
        'user_ids' => 'array',
        'user_ids.*' => 'integer|exists:users,id',
        'type_slug' => 'required|exists:task_types,slug',
        'priority' => 'required|integer',
        'blocked' => 'required|boolean',
      ]);

      $task_type = TaskType::where('slug', $request->type_slug)->first();

      $task->update(array_merge($request->all(), ['type_id' => $task_type->id]));

      if ($request->has('user_ids'))
      {
          $task->users()->detach();
          $task->users()->attach($request->input('user_ids'));
      }

      return new TaskResource($task);
    }

  /**
   * Remove the specified resource from storage.
   *
   * @param \App\Models\Task $task
   *
   * @return void
   * @throws \Exception
   */
    public function destroy(Task $task)
    {
       $task->delete();
    }
}
