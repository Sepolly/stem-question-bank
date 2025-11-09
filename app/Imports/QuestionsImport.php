<?php

namespace App\Imports;

use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $subjectId = Subject::whereRaw('LOWER(name) = ?', [strtolower(trim($row['subject']))])->value('id');
        $topicId = Topic::whereRaw('LOWER(name) = ?', [strtolower(trim($row['topic']))])->value('id');

        if (!$subjectId || !$topicId) {
            throw new \Exception("Invalid subject or topic for question: {$row['question_text']}");
        }

        DB::beginTransaction();

        try {
            $question = Question::create([
                'subject_id' => $subjectId,
                'topic_id' => $topicId,
                'difficulty' => strtolower($row['difficulty']),
                'round' => $row['round'],
                'type' => Question::getType($row['question_type']),
                'question_text' => $row['question_text'],
                'bool_answer' => $row['bool_answer'] ?? false,
            ]);

            $question->answer()->create([
                'answer_text' => $row['answer_text'],
            ]);

            if($row['option_text']){
                $options = explode(',', $row['option_text']);
                $opt_len = count($options);
    
                if ($opt_len < 5) {
                    throw new \LengthException("This question '{$row['question_text']}' must have 5 options but $opt_len given");
                }
    
                $correctOption = strtolower(trim($options[$opt_len - 1]));
    
                foreach (array_slice($options, 0, -1) as $option) {
                    $question->options()->create([
                        'option_text' => trim($option),
                        'is_correct' => strtolower(trim($option)) === $correctOption,
                    ]);
                }
            }

            $question->subject()->increment('question_count');
            $question->topic()->increment('question_count');

            DB::commit();
            return $question;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
