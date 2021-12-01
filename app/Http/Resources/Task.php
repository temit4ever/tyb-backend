<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Task extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'type_id' => (int) $this->type_id,
            'type_name' => new TaskType($this->whenLoaded('taskType')),
            'priority' => (int) $this->priority,
            'blocked' => (int) $this->blocked,
            'dates' => TaskDate::collection($this->whenLoaded('taskDates')),
            'users' => User::collection($this->whenLoaded('users')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
