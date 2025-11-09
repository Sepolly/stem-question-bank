<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Http\Resources\SubjectResource;
use App\Models\Subject;
use App\Services\QuestionService;
use App\Services\SubjectService;
use Inertia\Inertia;

class SubjectController extends Controller
{
    public function __construct(
        protected QuestionService $questionService,
        protected SubjectService $subjectService,
    ) {}

    public function index()
    {
        return Inertia::render('subjects/index', [
            'subjects' => SubjectResource::collection($this->subjectService->getAllSubjects()),
        ]);
    }

    public function show(int $eventId, Subject $subject)
    {
        return Inertia::render('subjects/show', [
            'subject' => SubjectResource::make($this->subjectService->showSubject($subject)),
        ]);
    }

    public function store(int $eventId, StoreSubjectRequest $request)
    {
        $this->subjectService->addSubject($request->validated());

        return back()->with('success', 'subject added.');
    }

    public function update(int $eventId, UpdateSubjectRequest $request, Subject $subject)
    {
        $this->subjectService->updateSubject(data: $request->validated(), subject: $subject);

        return back()->with('success', 'subject updated.');
    }

    public function destroy(int $eventId, Subject $subject)
    {
        $this->subjectService->deleteSubject($subject);

        return back()->with('success', 'subject deleted.');
    }
}
