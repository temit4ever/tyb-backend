<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
      //dd();
        return [
            'id' => $this->id,
            'title' => $this->title,
            'avatar' => $this->avatar,
            'email' => $this->email,
            'avatar_url' => $this->avatar ? Storage::url('avatars/' . $this->avatar) : null,
            'api_token' => $this->api_token,
            'tasks' => Task::collection($this->whenLoaded('tasks')),
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ];
    }
}
