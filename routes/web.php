<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuestionSessionController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

$current_event = cache()->get('current_event_id');


Route::get('/dashboard',function()use($current_event){
    if(auth()->check() && $current_event){
        return to_route('dashboard',['event' => $current_event]);
    }

    return Inertia::render('create-event');
})->name('dashboard.noevent');


Route::post('/event', [EventController::class, 'store'])
->name('event.store');


// STEM Question Bank Routes
Route::middleware(['auth', 'verified', 'ensureEvent', 'ensureInEvent'])->group(function () {

    
    // -----------------------------------------------------------------------
    // Events
    // -----------------------------------------------------------------------
        
    Route::prefix('events/{event}')->group(function () {
            
        Route::get('switch/{id}', [EventController::class, 'switchEvent'])
            ->name('events.switch');

        // -----------------------------------------------------------------------
        // Dashboard
        // -----------------------------------------------------------------------
        Route::get('dashboard', DashboardController::class)
            ->name('dashboard');

        // -----------------------------------------------------------------------
        // Questions
        // -----------------------------------------------------------------------
        Route::prefix('questions')->group(function () {

            Route::get('/', [QuestionController::class, 'index'])
                ->name('questions');

            Route::get('/{question}', [QuestionController::class, 'show'])
                ->name('questions.show');

            Route::post('/', [QuestionController::class, 'store'])
                ->name('questions.store');

            Route::post('/import', [QuestionController::class, 'import'])
                ->name('questions.import');

            Route::patch('/{question}', [QuestionController::class, 'update'])
                ->name('questions.update');

            Route::patch('/{question}/status', [QuestionController::class, 'changeStatus'])
                ->name('questions.changeStatus');

            Route::patch('/{question}/has-been-asked', [QuestionController::class, 'updateHasBeenAsked'])
                ->name('questions.hasBeenAsked');

            Route::delete('/{question}', [QuestionController::class, 'destroy'])
                ->name('questions.destroy');

        });

        // -----------------------------------------------------------------------
        // Sessions
        // -----------------------------------------------------------------------
        Route::prefix('sessions')->group(function () {

            Route::get('/', [QuestionSessionController::class, 'index'])
                ->name('sessions');

            Route::post('/', [QuestionSessionController::class, 'createSession'])
                ->name('sessions.store');

            Route::patch('/{sessionId}/start', [QuestionSessionController::class, 'startSession'])
                ->name('sessions.start');

            Route::patch('/{sessionId}/end', [QuestionSessionController::class, 'endSession'])
                ->name('sessions.end');
        });

        // -----------------------------------------------------------------------
        // Subjects
        // -----------------------------------------------------------------------
        Route::prefix('subjects')->controller(SubjectController::class)->group(function () {

            Route::get('/', 'index')
                ->name('subjects');

            Route::get('/{subject}', 'show')
                ->name('subjects.show');

            Route::post('/', 'store')
                ->name('subjects.store');

            Route::patch('/{subject}', 'update')
                ->name('subjects.update');

            Route::delete('/{subject}', 'destroy')
                ->name('subjects.destroy');

        });

        // -----------------------------------------------------------------------
        // Topics
        // -----------------------------------------------------------------------
        Route::prefix('topics')->group(function () {

            Route::get('/', [TopicController::class, 'index'])
                ->name('topics');

        });

        // -----------------------------------------------------------------------
        // Exports
        // -----------------------------------------------------------------------
        Route::controller(ExportController::class)->prefix('exports')->group(function () {
            Route::get('', 'index')->name('exports');
            Route::post('/generate', 'generateExport')->name('exports.generate');
        });

        // -----------------------------------------------------------------------
        // Users
        // -----------------------------------------------------------------------
        Route::middleware(['canManageUser'])->controller(UserController::class)->prefix('users')->group(function () {
            Route::get('/', 'index')->name('users');
            Route::get('/{user}', 'show')->name('users.show');

            Route::post('/', 'store')->name('users.store');

            Route::patch('/{user}/edit', 'update')->name('users.update');

            Route::delete('/{user}', 'destroy')->name('users.destroy');
        });
    });

});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
