<?php

use App\Models\Event;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->integer('round');
            $table->string('type');
            $table->string('difficulty');
            $table->longText('question_text');
            $table->string('status');

            $table->foreignIdFor(Subject::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(Topic::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(Event::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(User::class);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
