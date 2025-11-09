<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionSessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'difficulty' => $this->difficulty,
            'round' => $this->round,
            'type' => $this->type,
            'questions' => QuestionResource::collection($this->whenLoaded('questions')),
            'startsAt' => $this->starts_at,
            'endsAt' => $this->ends_at,
            'status' => $this->status,
        ];
    }
}
