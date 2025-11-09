<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class QuestionResource extends JsonResource
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
            'round' => $this->round,
            'type' => $this->type,
            'difficulty' => $this->difficulty,
            'questionText' => $this->question_text,
            'status' => $this->status,
            'boolAnswer' => $this->bool_answer,
            'hasBeenAsked' => $this->has_been_asked,
            'options' => QuestionOptionResource::collection($this->whenLoaded('options')),
            'answer' => QuestionAnswerResource::make($this->whenLoaded('answer')),
            'subject' => SubjectResource::make($this->whenLoaded('subject')),
            'topic' => TopicResource::make($this->whenLoaded('topic')),
            'author' => UserResource::make($this->whenLoaded('author')),
            'createdAt' => Carbon::make($this->created_at)->format('d-m-Y'),
            'createdAtHuman' => Carbon::make($this->created_at)->diffForHumans(),
            'updatedAt' => Carbon::make($this->updated_at)->format('d-m-Y'),
            'updatedAtHuman' => Carbon::make($this->updated_at)->diffForHumans(),
            'lastModifier' => UserResource::make($this->whenLoaded('lastModifier')),
        ];
    }
}
