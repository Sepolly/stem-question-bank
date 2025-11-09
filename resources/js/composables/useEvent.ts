import { Event } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

export default function useEvent() {
    const currentEvent = ref<Event>(usePage().props.currentEvent);
    const events = ref<Event[]>(usePage().props.events);

    return {
        currentEvent,
        events,
    };
}
