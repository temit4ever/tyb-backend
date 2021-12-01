<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Sprint extends JsonResource
{
    /**
     * @OA\Schema(
     *   schema="Sprint",
     *   allOf={
     *     @OA\Schema(
     *       @OA\Property(property="id", type="integer"),
     *       @OA\Property(property="title", type="string"),
     *       @OA\Property(property="start_date", type="string", format="date"),
     *       @OA\Property(property="end_date", type="string", format="date")
     *     ),
     *     @OA\Schema(ref="#/components/schemas/timestamps")
     *   }
     * )
     */
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'title' => $this->title,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
        return parent::toArray($request);
    }
}
