<?php

namespace App\Http\Resources\Employee;

use App\Http\Resources\Access\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'name' => $this->name,
            'position' => $this->position,
            'user_id' => $this->user_id,
            'users' => new UserResource($this->whenLoaded('users')),
        ];
    }
}
