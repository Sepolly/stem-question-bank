<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubjectResource;
use App\Http\Resources\TopicResource;
use App\Models\Subject;
use App\Models\Topic;
use App\Services\SubjectService;
use App\Services\TopicService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TopicController extends Controller
{
    public function __construct(
        protected TopicService $topicService,
        protected SubjectService $subjectService,
    ) {}

    public function index()
    {
        // dd($this->topicService->getAllTopics());
        return Inertia::render('topics/index', [
            'topics' => TopicResource::collection($this->topicService->getAllTopics()),
            'subjects' => SubjectResource::collection($this->subjectService->getAllSubjects()),
        ]);
    }

    public function store(Request $request, Subject $subject)
    {
        $this->topicService->addTopic($request->validated(), $subject);

        return back()->with('success', 'Topic added');
    }

    public function update(Request $request, Topic $topic)
    {
        $this->topicService->updateTopic($topic, $request->validated());

        return back()->with('success', 'Topic updated');
    }

    public function deleteTopic(Topic $topic)
    {
        $this->topicService->deleteTopic($topic);

        return back()->with('success', 'Topic deleted');
    }
}
