<?php

namespace App\Http\Resources\Employee;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->is_completed,
            'due_date' => optional($this->due_date)->format('Y-m-d'),
            'deadline_date' => optional($this->deadline_date)->format('Y-m-d'),
        ];
    }
}
