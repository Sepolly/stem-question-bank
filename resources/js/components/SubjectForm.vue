<script setup lang="ts">
import SubjectController from '@/actions/App/Http/Controllers/SubjectController';
import useEvent from '@/composables/useEvent';
import { Subject } from '@/types';
import { subjectForm, UpdateSubjectForm } from '@/types/forms';
import { useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import InputError from './InputError.vue';

const props = defineProps<{
    editingSubject: Subject | null;
}>();

const emit = defineEmits(['save', 'close']);

const { currentEvent } = useEvent();

const form = useForm<subjectForm>({
    name: props.editingSubject?.name ?? '',
    description: props.editingSubject?.description ?? '',
    topics: props.editingSubject?.topics ? Array.from(props.editingSubject.topics.map((t) => t.name)) : [''],
});

const updateForm = useForm<UpdateSubjectForm>({
    name: props.editingSubject?.name ?? '',
    description: props.editingSubject?.description ?? '',
    topics: props.editingSubject?.topics ? Array.from(props.editingSubject.topics.map((t) => ({ id: t.id, name: t.name }))) : [{ id: 0, name: '' }],
});

function addTopic() {
    if (props.editingSubject) {
        updateForm.topics.push({ id: 0, name: '' });
    } else {
        form.topics.push('');
    }
}

function removeTopic(index: number) {
    if (props.editingSubject) {
        if (updateForm.topics.length > 1) {
            updateForm.topics.splice(index, 1);
        } else {
            updateForm.topics[0] = { id: 0, name: '' };
        }
    } else {
        if (form.topics.length > 1) {
            form.topics.splice(index, 1);
        } else {
            form.topics[0] = '';
        }
    }
}

function saveSubject() {
    if (props.editingSubject) {
        updateForm.patch(
            SubjectController.update.url({
                event: currentEvent.value.id,
                subject: props.editingSubject.id,
            }),
            {
                onSuccess: () => {
                    updateForm.reset();
                    emit('close');
                },
            },
        );
    } else {
        form.post(
            SubjectController.store.url({
                event: currentEvent.value.id,
            }),
            {
                onSuccess: () => {
                    form.reset();
                    emit('close');
                },
            },
        );
    }
}
</script>

<template>
    <form @submit.prevent="saveSubject" class="mt-4 space-y-4" v-if="!editingSubject">
        <!-- Header -->
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-lg font-medium text-gray-900">Add Subject</h3>
        </div>

        <!-- Subject Name -->
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Subject Name *</label>
            <input
                v-model="form.name"
                type="text"
                placeholder="Enter subject name..."
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
            />
            <InputError :message="form.errors.name" />
        </div>

        <!-- Description -->
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Description</label>
            <textarea
                v-model="form.description"
                rows="3"
                placeholder="Enter subject description..."
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
            ></textarea>
            <InputError :message="form.errors.description" />
        </div>

        <!-- Topics -->
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Topics</label>
            <InputError :message="form.errors.topics" />
            <div class="space-y-2">
                <div v-for="(topic, index) in form.topics" :key="index" class="flex items-center space-x-2">
                    <input
                        v-model="form.topics[index]"
                        type="text"
                        placeholder="Enter topic name..."
                        class="flex-1 rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    />
                    <button type="button" @click="removeTopic(index)" class="text-red-600 hover:text-red-800" v-if="form.topics.length > 1">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                            />
                        </svg>
                    </button>
                </div>
                <button type="button" @click="addTopic" class="text-sm text-blue-600 hover:text-blue-800">+ Add Topic</button>
            </div>
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
                class="item-center flex cursor-pointer justify-center gap-2 rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
            >
                Add Subject
                <LoaderCircle v-if="form.processing" class="size-5 animate-spin" />
            </button>
        </div>
    </form>

    <!-- Update Form -->
    <form @submit.prevent="saveSubject" class="mt-4 space-y-4" v-else>
        <!-- Header -->
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-lg font-medium text-gray-900">Update Subject</h3>
        </div>
        <!-- Subject Name -->
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Subject Name *</label>
            <input
                v-model="updateForm.name"
                type="text"
                placeholder="Enter subject name..."
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
            />
            <InputError :message="updateForm.errors.name" />
        </div>

        <!-- Description -->
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Description</label>
            <textarea
                v-model="updateForm.description"
                rows="3"
                placeholder="Enter subject description..."
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
            ></textarea>
            <InputError :message="updateForm.errors.description" />
        </div>

        <!-- Topics -->
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Topics</label>
            <InputError :message="updateForm.errors.topics" />
            <div class="space-y-2">
                <div v-for="(topic, index) in updateForm.topics" :key="index" class="flex items-center space-x-2">
                    <input
                        v-model="updateForm.topics[index].name"
                        type="text"
                        placeholder="Enter topic name..."
                        class="flex-1 rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    />
                    <button type="button" @click="removeTopic(index)" class="text-red-600 hover:text-red-800" v-if="updateForm.topics.length > 1">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                            />
                        </svg>
                    </button>
                </div>
                <button type="button" @click="addTopic" class="text-sm text-blue-600 hover:text-blue-800">+ Add Topic</button>
            </div>
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
                class="item-center flex cursor-pointer justify-center gap-2 rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
            >
                Update Subject
                <LoaderCircle v-if="updateForm.processing" class="size-5 animate-spin" />
            </button>
        </div>
    </form>
</template>
