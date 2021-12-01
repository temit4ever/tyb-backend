<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskDate extends JsonResource
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
            'task_id' => (int) $this->task_id,
            'activity_date' => $this->activity_date->toDateString(),
            'recorded_date' => $this->created_at->toDateString(),
        ];
    }
}
