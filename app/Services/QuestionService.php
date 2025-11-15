<?php

namespace App\Services;

use App\Enums\QuestionStatus;
use App\Enums\QuestionType;
use App\Imports\QuestionsImport;
use App\Jobs\ImportQuestionsJob;
use App\Models\Question;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class QuestionService
{
    public function __construct(
        protected EventService $eventService,
    ) {}

    public function getAllQuestions(
        bool $wantsPagination = true,
        ?string $search = '',
        ?string $subject = '',
        ?string $difficulty = '',
        ?string $status = '',
        ?string $type = ''
    ) {
        try {
            $questions = $this->eventService
                ->getCurrentEvent()
                ->questions()
                ->with(['subject', 'topic'])
                ->filterBySearch($search)
                ->filterBySubject($subject)
                ->filterByDifficulty($difficulty)
                ->filterByStatus($status)
                ->filterByType($type)
                ->latest();

            return $wantsPagination
                ? $questions->paginate(50)
                : $questions->get();

        } catch (\Throwable $e) {
            Log::error("Failed fetching questions: {$e->getMessage()}", [
                'trace' => $e->getTraceAsString(),
            ]);

            return $wantsPagination
                ? collect()->paginate(50)
                : collect();
        }
    }

    public function importQuestion(UploadedFile $data)
    {
        try {
            $path = Storage::put('/temp',$data);
            ImportQuestionsJob::dispatch($path)
            ->onQueue('questions')
            ->afterResponse(); 
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }
    }

    public function addQuestion(array $data): ?Question
    {

        try {

            return DB::transaction(function () use ($data) {

                $question = Question::create(
                    collect($data)->except(['options', 'answer_text'])->toArray()
                );

                if ($question->type->value === QuestionType::MCQ->value) {
                    $this->handleMCQOptions($data['options'], $question);
                }

                if (! empty($data['answer_text'])) {
                    $question->answer()->create([
                        'answer_text' => $data['answer_text'],
                    ]);
                }

                $question->subject()->increment('question_count');
                $question->topic()->increment('question_count');

                return $question;

            });

        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return null;
        }
    }

    public function showQuestion(Question $question): ?Question
    {
        try {
            $question->load(['subject.topics', 'topic', 'options', 'answer', 'author', 'lastModifier']);

            return $question;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return null;
        }
    }

    public function updateQuestion(array $data, Question $question): bool
    {
        try {

            DB::transaction(function () use ($data, $question) {

                $question->update(
                    collect($data)->except(['options', 'answer_text'])->toArray()
                );

                if (! empty($data['answer_text'])) {
                    $question->answer()->update([
                        'answer_text' => $data['answer_text'],
                    ]);
                }

                if ($data['options'] && $question->type->value === QuestionType::MCQ->value) {
                    $question->options()->delete();

                    $this->handleMCQOptions($data['options'], $question);

                }

                $question->lastModifier()->associate(request()->user());
                $question->save();
            });

            return true;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return false;
        }
    }

    public function changeStatus(string $status, Question $question)
    {
        try {
            $question->update([
                'status' => $status,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return false;
        }
    }

    public function updateHasBeenAsked(bool $hasBeenAsked, Question $question)
    {
        try {
            $question->update([
                'has_been_asked' => $hasBeenAsked,
            ]);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return null;
        }
    }

    public function deleteQuestion(Question $question): bool
    {
        try {
            DB::transaction(function () use ($question) {
                $question->options()?->delete();
                $question->answer()?->delete();

                if ($question->subject->question_count > 0) {
                    $question->subject()->decrement('question_count');
                }

                if ($question->topic->question_count > 0) {
                    $question->topic()->decrement('question_count');
                }

                $question->delete();
            });

            return true;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return false;
        }
    }

    public function getQuestionById(int $id): ?Question
    {
        try {
            $question = Question::findOrFail($id);

            return $question;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return null;
        }
    }

    private function handleMCQOptions(array $options, Question $question)
    {
        $options = collect($options);

        $correctCount = $options->where('is_correct', true)->count();

        if ($correctCount !== 1) {
            throw new \Exception('Options must have exactly one correct value');
        }

        $options
            ->filter(fn ($option) => ! empty($option['option_text']))
            ->each(function ($option) use ($question) {
                $question->options()->create([
                    'option_text' => $option['option_text'],
                    'is_correct' => $option['is_correct'],
                ]);
            });
    }
}
