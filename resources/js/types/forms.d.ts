import { QuestionType } from '.';

export interface subjectForm {
    name: string;
    description: string;
    topics: Array<string | null>;
}

export interface QuestionForm {
    subject_id: number | null;
    topic_id: number | null;
    difficulty: string;
    round: number;
    type: QuestionType;
    question_text: string;
    answer_text: string;
    options: Array<{ option_text: string; is_correct: boolean }>;
    bool_answer?: boolean;
}

export interface SessionForm {
    title: string;
    difficulty: 'easy' | 'medium' | 'hard';
    type: string;
    round: number;
    starts_at: string;
    ends_at: string;
    number_of_questions: number;
}

export interface UpdateSubjectForm {
    name: string;
    description: string;
    topics: Array<{ id: number; name: string }>;
}
