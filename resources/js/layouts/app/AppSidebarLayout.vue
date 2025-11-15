<script setup lang="ts">
import AuthenticatedSessionController from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import DashboardController from '@/actions/App/Http/Controllers/DashboardController';
import EventController from '@/actions/App/Http/Controllers/EventController';
import Settings from '@/actions/App/Http/Controllers/Settings';
import DropdownMenu from '@/components/DropdownMenu.vue';
import { useAuth } from '@/composables/useAuth';
import useEvent from '@/composables/useEvent';
import { useInitials } from '@/composables/useInitials';
import { dashboard, exports, questions, sessions, subjects, topics, users } from '@/routes';
import type { BreadcrumbItemType, NavLinkType } from '@/types';
import {
    ArrowDownOnSquareIcon,
    BellIcon,
    BookOpenIcon,
    QuestionMarkCircleIcon,
    RocketLaunchIcon,
    ServerStackIcon,
    TagIcon,
    UserGroupIcon,
} from '@heroicons/vue/24/outline';
import { Link, router } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const { currentUser } = useAuth();
const { getInitials } = useInitials();
const { currentEvent, events } = useEvent();

const selectedEvent = ref<string | number>(currentEvent.value.id);
const loggingOut = ref(false);

const onSelectChange = () => {
    if (typeof selectedEvent.value === 'number') {
        router.get(EventController.switchEvent.url({
            id: selectedEvent.value,
            event: currentEvent.value.id
        }));
    } else {
        if (selectedEvent.value === 'create') {
            createEvent();
        }
    }
};

function createEvent() {
    router.visit(
        DashboardController(
            {
                event: currentEvent.value.id,
            },
            {
                query: {
                    showCreateEventModal: true,
                },
            },
        ),
    );
}

const logout = () => {
    loggingOut.value = true;
    router.post(
        AuthenticatedSessionController.destroy(),
        {},
        {
            onFinish: () => (loggingOut.value = false),
        },
    );
};

const NavLinks: NavLinkType[] = [
    {
        label: 'Dashboard',
        icon: ServerStackIcon,
        link: dashboard(currentEvent.value.id),
        visible: true
    },
    {
        label: 'Questions',
        icon: QuestionMarkCircleIcon,
        link: questions(currentEvent.value.id),
        visible: true
    },
    {
        label: 'Subjects',
        icon: BookOpenIcon,
        link: subjects(currentEvent.value.id),
        visible: true
    },
    {
        label: 'Topics',
        icon: TagIcon,
        link: topics(currentEvent.value.id),
        visible: true
    },
    {
        label: 'Sessions',
        icon: RocketLaunchIcon,
        link: sessions(currentEvent.value.id),
        visible: true
    },
    {
        label: 'Exports',
        icon: ArrowDownOnSquareIcon,
        link: exports(currentEvent.value.id),
        visible: true
    },
    {
        label: 'Users',
        icon: UserGroupIcon,
        link: users(currentEvent.value.id),
        visible: currentUser.value.isSuperAdmin
    },
];
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Top Navigation Bar -->
        <nav class="fixed z-50 ml-64 h-16 w-[calc(100vw-16rem)] border-b border-gray-200 bg-white shadow-sm">
            <div class="w-full px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <div class="flex items-center">
                        <select
                            v-model="selectedEvent"
                            @change="onSelectChange"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        >
                            <option value="create">Create New Event</option>
                            <option v-if="events?.length && events?.length <= 1" :value="currentEvent.id">
                                {{ currentEvent.name }}
                            </option>
                            <option :selected="currentEvent.id === event.id" v-for="event in events" :value="event.id" :key="event.id" v-else>
                                {{ event.name }}
                            </option>
                        </select>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <button class="relative text-gray-400 hover:text-gray-500">
                            <BellIcon class="size-10 relative p-2 text-gray-400 hover:text-gray-500" />
                            <span class="absolute top-1 right-1 block h-2 w-2 rounded-full bg-red-400"></span>
                        </button>

                        <!-- User Profile Menu -->
                        <div class="relative w-auto">
                            <DropdownMenu>
                                <template #trigger="{ open }">
                                    <button class="flex cursor-pointer items-center space-x-2 text-sm text-gray-700 hover:text-gray-900">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-500">
                                            <span class="font-medium text-white">
                                                {{ getInitials(currentUser?.name) }}
                                            </span>
                                        </div>
                                        <div class="flex flex-col text-start">
                                            <span>{{ currentUser?.name }}</span>
                                            <span class="text-gray-700/75" v-for="(role, idx) in currentUser.roles" :key="idx">
                                                {{ role }}
                                                <span v-if="currentUser.roles.length - 1 !== idx">|</span>
                                            </span>
                                        </div>
                                        <!-- Chevron -->
                                        <svg
                                            class="size-4 transition-transform duration-200"
                                            :class="{ 'rotate-180': open }"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </template>

                                <ul class="space-y-2 text-sm">
                                    <Link class="block rounded-md p-2 hover:bg-gray-50" :href="Settings.ProfileController.edit()"> Profile </Link>
                                    <Link class="block rounded-md p-2 hover:bg-gray-50"> Settings </Link>
                                    <Link class="block rounded-md p-2 hover:bg-gray-50"> Help/Documentation </Link>
                                    <div class="h-[0.1px] w-full bg-gray-300"></div>
                                    <button
                                        @click.stop="logout"
                                        class="flex w-full cursor-pointer items-center gap-4 rounded-md p-2 text-start hover:bg-gray-50"
                                        :disabled="loggingOut"
                                        :class="{ 'pointer-events-none opacity-50': loggingOut }"
                                    >
                                        Logout
                                        <LoaderCircle v-if="loggingOut" class="size-4 animate-spin text-gray-500" />
                                    </button>
                                </ul>
                            </DropdownMenu>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex">
            <!-- Sidebar -->
            <div class="fixed min-h-screen w-64 bg-white shadow-sm">
                <nav class="mt-5 px-2">
                    <div class="space-y-1">
                        <Link
                            v-for="(navLink, index) in NavLinks"
                            :key="index"
                            :href="navLink.link"
                            class="group flex items-center rounded-md px-2 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900"
                            :class="{'hidden': !navLink.visible }"
                        >
                            <component :is="navLink.icon" class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" />
                            {{ navLink.label }}
                        </Link>
                    </div>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="flex-1">
                <main class="mt-16 ml-64 p-6">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
