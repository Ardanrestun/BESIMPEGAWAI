<?php

namespace App\Http\Resources\Access;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
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
            'icon' => $this->icon,
            'url' => $this->route,
            'roles' => $this->roles,
            'order' => $this->order,
            'parent_id' => $this->parent_id,
            'children' => MenuResource::collection($this->whenLoaded('children')),
        ];
    }
}
