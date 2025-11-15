<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubjectResource;
use Inertia\Inertia;
use App\Models\Subject;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;

class HomeController extends Controller
{
    
    public function __invoke()
    {
        $subjects = Subject::query()
            ->with(['topics','topics'])
            ->take(3)
            ->get();

        return Inertia::render('home',[
            'subjects' => SubjectResource::collection($subjects)
        ]);
    }
}
