<?php

namespace App\Console\Commands;

use App\Models\Question;
use Illuminate\Console\Command;

class DeleteQuestions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'questions:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('deleting questions...');
        Question::with(['answer','options','subject','topic'])->delete();
        $this->info('questions deleted successfully');
    }
}
