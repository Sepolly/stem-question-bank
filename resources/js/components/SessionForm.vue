<script setup lang="ts">
import QuestionSessionController from '@/actions/App/Http/Controllers/QuestionSessionController';
import useEvent from '@/composables/useEvent';
import { SessionForm } from '@/types/forms';
import { useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import InputError from './InputError.vue';

const emit = defineEmits(['close']);

defineProps<{
    error?: string
}>()

const { currentEvent } = useEvent();

const form = useForm<SessionForm>({
    title: '',
    difficulty: 'easy',
    type: '',
    round: 1,
    ends_at: '',
    starts_at: '',
    number_of_questions: 10,
});

function createSession() {
    form.post(QuestionSessionController.createSession.url({ event: currentEvent.value.id }), {
        onSuccess: () => {
            form.reset();
            emit('close');
        },
    });
}
</script>

<template>
    <div class="mt-3">
        <!-- Header -->
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-lg font-medium text-gray-900">Create Session</h3>
        </div>

        <!-- Form -->
        <form @submit.prevent="createSession" class="mt-4 space-y-4 text-gray-700">
            <p v-if="error" class="text-red-500 p-2 text-center">
                {{ error }}
            </p>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Title</label>
                    <input
                        required
                        v-model="form.title"
                        type="text"
                        placeholder="Enter Title"
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    />
                    <InputError :message="form.errors.title" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Difficulty *</label>
                    <select
                        name="difficulty"
                        v-model="form.difficulty"
                        required
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    >
                        <option value="">Select Difficulty</option>
                        <option value="easy">Easy</option>
                        <option value="medium">Medium</option>
                        <option value="hard">Hard</option>
                    </select>
                    <InputError :message="form.errors.difficulty" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Round *</label>
                    <select
                        name="round"
                        v-model="form.round"
                        required
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    >
                        <option value="">Select Round</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                    <InputError :message="form.errors.round" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Question Type *</label>
                    <select
                        name="type"
                        v-model="form.type"
                        required
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    >
                        <option value="">Select Type</option>
                        <option value="mcq">Multiple Choice</option>
                        <option value="true_false">True/False</option>
                        <option value="short_answer">Short Answer</option>
                        <option value="long_answer">Long Answer</option>
                    </select>
                    <InputError :message="form.errors.type" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Number of Questions</label>
                    <input
                        v-model="form.number_of_questions"
                        type="number"
                        placeholder="Number of Questions..."
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    />
                    <InputError :message="form.errors.number_of_questions" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Starts At</label>
                    <input
                        v-model="form.starts_at"
                        type="time"
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    />
                    <InputError :message="form.errors.starts_at" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Ends At</label>
                    <input
                        v-model="form.ends_at"
                        type="time"
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    />
                    <InputError :message="form.errors.ends_at" />
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 border-t pt-4">
                <button
                    type="button"
                    @click="$emit('close')"
                    class="cursor-pointer rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="jstify-center flex cursor-pointer items-center gap-2 rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
                    :class="{ 'opacity-50': form.processing }"
                >
                    <LoaderCircle v-if="form.processing" class="size-5 animate-spin" />
                    Create Session
                </button>
            </div>
        </form>
    </div>
</template>
