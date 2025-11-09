import QuestionController from '@/actions/App/Http/Controllers/QuestionController';
import { Question } from '@/types';
import { router } from '@inertiajs/vue3';
import useEvent from './useEvent';

export default function useQuestion() {
    const { currentEvent } = useEvent();

    //form actions
    const showQuestion = (question: Question) => {
        router.visit(
            QuestionController.show({
                question: question.id,
                event: currentEvent.value.id,
            }),
        );
    };

    const changeStatus = (questionId: number, status: string) => {
        router.patch(
            QuestionController.changeStatus({
                event: currentEvent.value.id,
                question: questionId,
            }),
            {
                status: status,
            },
        );
    };

    const destroyQuestion = (questionId: number) => {
        router.delete(
            QuestionController.destroy({
                event: currentEvent.value.id,
                question: questionId,
            }),
            {
                onSuccess: () => goBack(),
            },
        );
    };

    const goBack = () => {
        router.visit(QuestionController.index({ event: currentEvent.value.id }));
    };

    // style helpers
    const getDifficultyClass = (difficulty: string) => {
        switch (difficulty) {
            case 'easy':
                return 'bg-green-100 text-green-800';
            case 'medium':
                return 'bg-yellow-100 text-yellow-800';
            case 'hard':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const getStatusClass = (status: string) => {
        switch (status) {
            case 'pending':
                return 'bg-yellow-100 text-yellow-800';
            case 'approved':
                return 'bg-green-100 text-green-800';
            case 'rejected':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const getQuestionType = (type: string) => {
        switch (type) {
            case 'mcq':
                return 'Multiple Choice';
            case 'short_answer':
                return 'Short Answer';
            case 'long_answer':
                return 'Long Answer';
            case 'true_false':
                return 'True/False';
        }
    };

    return {
        getDifficultyClass,
        getQuestionType,
        getStatusClass,
        changeStatus,
        destroyQuestion,
        showQuestion,
        goBack,
    };
}
