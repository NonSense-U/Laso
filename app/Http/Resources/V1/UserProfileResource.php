<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'full_name'     => $this->first_name . ' ' . $this->last_name,
            'username' => $this->username,
            'email'    => $this->email,
            'phone_number' => $this->phone_number,
            'password_last_changed_at' => $this->password_last_changed_at
        ];
    }
}
