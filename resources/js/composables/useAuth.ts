import { User } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

export function useAuth() {
    const currentUser = ref<User>(usePage().props.auth.user);

    return {
        currentUser,
    };
}
