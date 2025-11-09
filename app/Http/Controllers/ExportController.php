<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuestionResource;
use App\Http\Resources\SubjectResource;
use App\Models\Question;
use App\Services\QuestionService;
use App\Services\SubjectService;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExportController extends Controller
{
    public function __construct(
        protected QuestionService $questionService,
        protected SubjectService $subjectService,
    ) {}

    public function index()
    {
        $search = request()->query('search');
        $subject = request()->query('subject');
        $difficulty = request()->query('difficulty');
        $status = request()->query('status');
        $type = request()->query('type');

        return Inertia::render('Export', [
            'questions' => QuestionResource::collection($this->questionService->getAllQuestions(
                wantsPagination: false,
                search: $search,
                subject: $subject,
                difficulty: $difficulty,
                status: $status,
                type: $type,
            )),
            'subjects' => SubjectResource::collection($this->subjectService->getAllSubjects()),
        ]);
    }

    public function generateExport(Request $request)
    {
        $questionIds = json_decode($request->input('questions'), true);

        // dd($questionIds);

        $questions = Question::query()->whereIn('id', $questionIds)->get();

        // dd($questions);

        $questions->load(['options', 'subject', 'topic', 'answer']);

        // $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf/questions',[
        //     'questions' => $questions,
        // ]);

        return Inertia::render('pdf/questions', [
            'questions' => QuestionResource::collection($questions),
        ]);
    }
}
