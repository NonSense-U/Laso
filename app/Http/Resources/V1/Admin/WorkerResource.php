<?php

namespace App\Http\Resources\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        dd($request);
        
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'email'    => $this->email,
            'phone_number' => $this->phone_number,
            'status'   => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
