<?php

namespace App\Http\Controllers;

use App\Enums\QuestionStatus;
use App\Http\Requests\Questions\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Http\Resources\QuestionResource;
use App\Http\Resources\SubjectResource;
use App\Models\Question;
use App\Services\QuestionService;
use App\Services\SubjectService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class QuestionController extends Controller
{
    public function __construct(
        protected QuestionService $questionService,
        protected SubjectService $subjectService,
    ) {}

    public function index($eventId)
    {
        $search = request()->query('search');
        $subject = request()->query('subject');
        $status = request()->query('status');
        $difficulty = request()->query('difficulty');

        return Inertia::render('questions/index', [
            'subjects' => SubjectResource::collection($this->subjectService->getAllSubjects()),
            'questions' => QuestionResource::collection($this->questionService->getAllQuestions(
                search: $search,
                status: $status,
                difficulty: $difficulty,
                subject: $subject,
            )),
            'showForm' => request()->query('showForm'),
            'error' => session()->get('error'),
        ]);
    }

    public function show(int $eventId, Question $question)
    {
        if (request()->user()->cannot('view', $question)) {
            abort(404);
        }

        return Inertia::render('questions/show', [
            'question' => QuestionResource::make($this->questionService->showQuestion($question)),
            'subjects' => SubjectResource::collection($this->subjectService->getAllSubjects()),
        ]);
    }

    public function store(StoreQuestionRequest $request)
    {
        if ($request->user()->cannot('create', Question::class)) {
            abort(404);
        }

        $this->questionService->addQuestion($request->validated());

        return back()->with('success', 'question added successfully');

    }

    public function import(Request $request)
    {
        $request->validate([
            'questions' => ['required', 'file'],
        ]);

        $this->questionService->importQuestion($request->file('questions'));
        
    }

    public function update(int $eventId, UpdateQuestionRequest $request, Question $question)
    {

        if ($request->user()->cannot('update', $question)) {
            abort(404);
        }

        $this->questionService->updateQuestion($request->validated(), $question);

        return back()->with('success', 'question updated');
    }

    public function changeStatus(int $eventId, Request $request, Question $question)
    {
        if ($request->user()->cannot('update', $question)) {
            abort(404);
        }

        $request->validate(['status' => ['required', 'string', Rule::in(QuestionStatus::cases())]]);

        $this->questionService->changeStatus($request->status, $question);

        return back()->with('success', 'status updated');
    }

    public function updateHasBeenAsked(int $eventId, Request $request, Question $question)
    {
        $request->validate([
            'has_been_asked' => ['required', 'boolean'],
        ]);

        $this->questionService->updateHasBeenAsked($request->has_been_asked, $question);

        return back();
    }

    public function destroy(int $eventId, Question $question)
    {
        if (request()->user()->cannot('delete', $question)) {
            abort(404);
        }

        $this->questionService->deleteQuestion($question);

        return to_route('questions', ['event' => $eventId]);
    }
}
