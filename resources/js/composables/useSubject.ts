import QuestionController from "@/actions/App/Http/Controllers/QuestionController";
import useEvent from "./useEvent";
import { router } from "@inertiajs/vue3";

export default function useSubject(){
    const {currentEvent} = useEvent()

    const gotoSubjects = () => {
        router.get(QuestionController.index({
            event: currentEvent.value.id
        }))
    }

    return {
        gotoSubjects
    }
}