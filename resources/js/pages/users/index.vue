<script setup lang="ts">
import UserController from '@/actions/App/Http/Controllers/UserController';
import Modal from '@/components/Modal.vue';
import UserForm from '@/components/UserForm.vue';
import useEvent from '@/composables/useEvent';
import { getInitials } from '@/composables/useInitials';
import AppLayout from '@/layouts/AppLayout.vue';
import { PaginatedUser, User } from '@/types';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    users: PaginatedUser;
}>();

const showUserForm = ref(false);
const editingUser = ref<any | null>(null);

const { currentEvent } = useEvent();

const editUser = (user: any) => {
    editingUser.value = user;
    showUserForm.value = true;
};

const deleteUser = (user: User) => {
    if (confirm(`Do you want to delete ${user.name}?`)) {
        router.delete(
            UserController.destroy({
                event: currentEvent.value.id,
                user: user.id,
            }),
        );
    }
};

const closeUserForm = () => {
    showUserForm.value = false;
    editingUser.value = null;
};

const currentPage = ref(props.users.meta?.current_page);
const totalPages = ref(props.users.meta?.total);
const visiblePages = ref(props.users.meta?.links);

const previousPage = () => {
    if (!props.users.links?.prev) {
        return;
    }
    router.visit(props.users.links.prev);
};

const nextPage = () => {
    if (!props.users.links?.next) {
        return;
    }
    router.visit(props.users.links.next);
};

const goToPage = (pageNo: number | null) => {
    if (typeof pageNo === 'number') {
        router.visit(
            UserController.index(
                {
                    event: currentEvent.value.id,
                },
                {
                    query: {
                        page: pageNo,
                    },
                },
            ),
        );
    }
};
</script>

<template>
    <AppLayout>
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between border-b border-gray-200 pb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Users</h1>
                    <p class="mt-1 text-sm text-gray-500">Manage platform users and their roles</p>
                </div>
                <button
                    @click="showUserForm = true"
                    class="inline-flex cursor-pointer items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
                >
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add User
                </button>
            </div>

            <!-- Users Table -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Roles</th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                                <!-- Name -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100">
                                            <span class="text-sm font-medium text-green-600">
                                                {{ getInitials(user.name) }}
                                            </span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ user.name }}
                                            </div>
                                            <div class="text-sm text-gray-500">ID: {{ user.id }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Email -->
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ user.email }}</div>
                                </td>

                                <!-- Roles -->
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        <span
                                            v-for="(role, idx) in user.roles"
                                            :key="idx"
                                            class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-800"
                                        >
                                            {{ role }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 text-sm font-medium whitespace-nowrap" v-if="!user.isSuperAdmin">
                                    <div class="flex space-x-2">
                                        <button @click="editUser(user)" class="cursor-pointer text-indigo-600 hover:text-indigo-900">Edit</button>
                                        <button @click="deleteUser(user)" class="cursor-pointer text-red-600 hover:text-red-900">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                <div class="flex flex-1 justify-between sm:hidden">
                    <button
                        @click="previousPage"
                        :disabled="currentPage === 1"
                        class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        Previous
                    </button>
                    <button
                        @click="nextPage"
                        :disabled="currentPage === totalPages"
                        class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        Next
                    </button>
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">{{ users.meta?.from }}</span> to
                            <span class="font-medium">{{ users.meta?.to }}</span> of <span class="font-medium">{{ users.meta?.total }}</span> results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex -space-x-px rounded-md shadow-sm">
                            <button
                                @click="previousPage"
                                :disabled="currentPage === 1"
                                class="relative inline-flex items-center rounded-l-md border border-gray-300 bg-white px-2 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <span class="sr-only">Previous</span>
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        fill-rule="evenodd"
                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                            <button
                                v-for="(page, idx) in visiblePages"
                                :key="idx"
                                @click="goToPage(page.page)"
                                :class="
                                    page.active
                                        ? 'z-10 border-blue-500 bg-blue-50 text-blue-600'
                                        : 'border-gray-300 bg-white text-gray-500 hover:bg-gray-50'
                                "
                                class="relative inline-flex items-center border px-4 py-2 text-sm font-medium"
                            >
                                {{ page.page }}
                            </button>
                            <button
                                @click="nextPage"
                                :disabled="currentPage === totalPages"
                                class="relative inline-flex items-center rounded-r-md border border-gray-300 bg-white px-2 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <span class="sr-only">Next</span>
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- User Form Modal -->
            <Modal :show="showUserForm" class-name="h-auto max-w-xl">
                <UserForm @close="closeUserForm" :editing-user="editingUser" />
            </Modal>
        </div>
    </AppLayout>
</template>
