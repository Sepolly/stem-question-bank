import { User } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedSessionController from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import RegisteredUserController from '@/actions/App/Http/Controllers/Auth/RegisteredUserController';

export function useAuth() {
    const currentUser = ref<User>(usePage().props.auth.user);
    const userHasEvent = ref<boolean>(!!currentUser.value?.events)

    const SignIn = () => {
        router.get(AuthenticatedSessionController.create())
    }

    const LogOut = () => {
        router.post(AuthenticatedSessionController.destroy())
    }

    const SignUp = () => {
        router.get(RegisteredUserController.create())
    }

    return {
        currentUser,
        SignUp,
        SignIn,
        LogOut,
        userHasEvent
    };
}
