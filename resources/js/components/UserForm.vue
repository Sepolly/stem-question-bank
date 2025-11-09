<script setup lang="ts">
import UserController from '@/actions/App/Http/Controllers/UserController';
import useEvent from '@/composables/useEvent';
import { User } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import InputError from './InputError.vue';

const props = defineProps<{
    editingUser: User;
}>();

const emit = defineEmits(['save', 'close']);

const roleOptions = ['admin', 'super admin', 'contributor', 'viewer'];

const { currentEvent } = useEvent();

// Create form
const form = useForm({
    name: props.editingUser?.name ?? '',
    email: props.editingUser?.email ?? '',
    role: props.editingUser?.roles[0] ?? [],
});

// Save handler
function saveUser() {
    if (props.editingUser) {
        form.patch(UserController.update.url({ user: props.editingUser.id, event: currentEvent.value.id }), {
            onSuccess: () => {
                form.reset();
                emit('close');
            },
        });
    } else {
        form.post(UserController.store.url({ event: currentEvent.value.id }), {
            onSuccess: () => {
                form.reset();
                emit('close');
            },
        });
    }
}
</script>

<template>
    <form @submit.prevent="saveUser" class="mt-4 space-y-4">
        <!-- Header -->
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-lg font-medium text-gray-900">
                {{ props.editingUser ? 'Update User' : 'Add New User' }}
            </h3>
        </div>

        <!-- Name -->
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Full Name *</label>
            <input
                v-model="form.name"
                type="text"
                placeholder="Enter full name..."
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
            />
            <InputError :message="form.errors.name" />
        </div>

        <!-- Email -->
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Email *</label>
            <input
                v-model="form.email"
                type="email"
                placeholder="Enter email address..."
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
            />
            <InputError :message="form.errors.email" />
        </div>

        <!-- Roles -->
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Role</label>
            <div class="space-y-2">
                <div v-for="role in roleOptions" :key="role" class="flex items-center space-x-2">
                    <input type="radio" :value="role" v-model="form.role" class="h-4 w-4 rounded border-gray-300 text-blue-600" />
                    <span class="text-sm text-gray-700">{{ role }}</span>
                </div>
            </div>
            <InputError :message="form.errors.role" />
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-3 border-t pt-4">
            <button
                type="button"
                @click="$emit('close')"
                class="cursor-pointer rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Cancel
            </button>
            <button
                type="submit"
                class="flex cursor-pointer items-center justify-center gap-2 rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
            >
                {{ props.editingUser ? 'Update User' : 'Add User' }}
                <LoaderCircle v-if="form.processing" class="size-5 animate-spin" />
            </button>
        </div>
    </form>
</template>
