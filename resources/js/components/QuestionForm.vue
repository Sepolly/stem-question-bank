<script setup lang="ts">
import QuestionController from '@/actions/App/Http/Controllers/QuestionController';
import useEvent from '@/composables/useEvent';
import { Question, QuestionType, Subject } from '@/types';
import { QuestionForm } from '@/types/forms';
import { useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';
import InputError from './InputError.vue';

const emit = defineEmits(['close']);

const props = defineProps<{
    subjects: Subject[];
    editingQuestion?: Question;
}>();

const { currentEvent } = useEvent();
const _subjects = ref(props.subjects);

const form = useForm<QuestionForm>({
    subject_id: props.editingQuestion?.subject.id ?? null,
    topic_id: props.editingQuestion?.topic.id ?? null,
    difficulty: props.editingQuestion?.difficulty ?? '',
    round: props.editingQuestion?.round ?? 1,
    type: (props.editingQuestion?.type as QuestionType) ?? 'short_answer',
    question_text: props.editingQuestion?.questionText ?? '',
    answer_text: props.editingQuestion?.answer?.answerText ?? '',
    options: props.editingQuestion
        ? props.editingQuestion.options.map((option) => ({ option_text: option.optionText, is_correct: Boolean(option.isCorrect) }))
        : [
              { option_text: '', is_correct: false },
              { option_text: '', is_correct: false },
          ],
    bool_answer: props.editingQuestion?.boolAnswer ?? false,
});

const selectedSubject = ref<Subject | undefined>(props.editingQuestion?.subject);

const addOption = () => {
    form.options.push({ option_text: '', is_correct: false });
};

const removeOption = (index: number) => {
    form.options.splice(index, 1);
};

const onSelectChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const selectedId = +input.value;

    const found = _subjects.value.find((sub) => sub.id === selectedId);

    if (!found) return;

    selectedSubject.value = found;
};

function saveQuestion() {
    if (props.editingQuestion) {
        form.patch(
            QuestionController.update.url({
                event: currentEvent.value.id,
                question: props.editingQuestion.id,
            }),
            {
                onSuccess: () => {
                    form.reset();
                    emit('close');
                },
            },
        );
    } else {
        form.post(QuestionController.store.url({ event: currentEvent.value.id }), {
            onSuccess: () => {
                form.reset();
                emit('close');
            },
        });
    }
}
</script>

<template>
    <div class="mt-3">
        <!-- Header -->
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-lg font-medium text-gray-900">
                {{ props.editingQuestion ? 'Update Question' : 'Add New Question' }}
            </h3>
        </div>

        <!-- Form -->
        <form @submit.prevent="saveQuestion" class="mt-4 space-y-4 text-gray-700">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Subject *</label>
                    <select
                        @change="onSelectChange"
                        v-model="form.subject_id"
                        required
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    >
                        <option value="">Select Subject</option>
                        <option v-for="subject in subjects" :value="subject.id" :key="subject.id">
                            {{ subject.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.subject_id" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Topic *</label>
                    <select
                        v-model="form.topic_id"
                        required
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    >
                        <option value="">Select Topic</option>
                        <option v-for="topic in selectedSubject?.topics" :value="topic.id" :key="topic.id">
                            {{ topic.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.topic_id" />
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
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Question Text *</label>
                <textarea
                    v-model="form.question_text"
                    required
                    placeholder="Enter your question here..."
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >
                </textarea>
                <InputError :message="form.errors.question_text" />
            </div>

            <div v-if="!['mcq', 'true_false'].includes(form.type)">
                <label class="mb-2 block text-sm font-medium text-gray-700">Answer Text</label>
                <textarea
                    v-model="form.answer_text"
                    placeholder="Enter your answer here..."
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >
                </textarea>
                <InputError :message="form.errors.answer_text" />
            </div>

            <!-- MCQ Options (if applicable) -->
            <div v-if="form.type === 'mcq'" class="space-y-3">
                <label class="block text-sm font-medium text-gray-700">Multiple Choice Options</label>

                <div v-for="(option, index) in form.options" :key="index" class="flex items-center space-x-2">
                    <input
                        v-model="form.options[index].option_text"
                        type="text"
                        :placeholder="`Option ${index + 1}`"
                        class="flex-1 rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    />
                    <input
                        v-model="form.options[index].is_correct"
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <span class="text-sm text-gray-600">Correct</span>
                    <button v-if="form.options.length > 2" @click="removeOption(index)" type="button" class="text-red-600 hover:text-red-800">
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

                <button
                    @click="addOption"
                    type="button"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm leading-4 font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                >
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add Option
                </button>
            </div>

            <!-- True or False option -->
            <div v-if="form.type === 'true_false'" class="space-y-3">
                <label class="mb-2 block text-sm font-medium text-gray-700">Answer *</label>
                <select
                    name="type"
                    v-model="form.bool_answer"
                    required
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >
                    <option value="">Select Answer</option>
                    <option :value="true">True</option>
                    <option :value="false">False</option>
                </select>
                <!-- <InputError :message="form.errors.type" /> -->
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
                    {{ editingQuestion ? 'Update Question' : 'Add Question' }}
                </button>
            </div>
        </form>
    </div>
</template>
