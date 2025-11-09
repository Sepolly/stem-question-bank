<script setup lang="ts">
import Modal from '@/components/Modal.vue';
import QuestionForm from '@/components/QuestionForm.vue';
import { useAuth } from '@/composables/useAuth';
import { getInitials } from '@/composables/useInitials';
import useQuestion from '@/composables/useQuestion';
import AppLayout from '@/layouts/AppLayout.vue';
import { Question, Subject } from '@/types';
import { ref } from 'vue';

const props = defineProps<{
    question: Question;
    subjects: Subject[];
}>();

const editingQuestion = ref<Question | undefined>();
const showEditingQuestionForm = ref(false);

const { getDifficultyClass, getQuestionType, getStatusClass, changeStatus, destroyQuestion, goBack } = useQuestion();
const { currentUser } = useAuth();

const editQuestion = (question: Question) => {
    editingQuestion.value = question;
    showEditingQuestionForm.value = true;
};

const close = () => {
    editingQuestion.value = undefined;
    showEditingQuestionForm.value = false;
};

const approveQuestion = () => {
    changeStatus(props.question.id, 'approved');
};

const rejectQuestion = () => {
    changeStatus(props.question.id, 'rejected');
};

const duplicateQuestion = () => {};

const deleteQuestion = (question: Question) => {
    if (confirm('Are you sure you want to delete this question? This action cannot be undone.')) {
        destroyQuestion(question.id);
    }
};

// const viewRelatedQuestion = (relatedQuestion: Question) => {

// };
</script>

<template>
    <AppLayout>
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="border-b border-gray-200 pb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Question Details</h1>
                        <p class="mt-1 text-sm text-gray-500">View and manage question information</p>
                    </div>

                    <div class="flex space-x-3">
                        <button
                            @click="goBack"
                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                        >
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Questions
                        </button>
                        <button
                            v-if="currentUser.canManageQuestion || (question.status === 'pending' && currentUser.id === question.author.id)"
                            @click="editQuestion(question)"
                            class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                        >
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                />
                            </svg>
                            Edit Question
                        </button>
                    </div>
                </div>
            </div>

            <!-- Question Content -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Main Question Content -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Question Card -->
                    <div class="rounded-lg bg-white p-6 shadow">
                        <div class="mb-4 flex items-start justify-between">
                            <div class="flex items-center space-x-3">
                                <span
                                    :class="getDifficultyClass(question.difficulty)"
                                    class="inline-flex rounded-full px-3 py-1 text-sm font-semibold"
                                >
                                    {{ question.difficulty }}
                                </span>
                                <span :class="getStatusClass(question.status)" class="inline-flex rounded-full px-3 py-1 text-sm font-semibold">
                                    {{ question.status }}
                                </span>
                                <span class="inline-flex rounded-full bg-purple-100 px-3 py-1 text-sm font-semibold text-purple-800">
                                    {{ getQuestionType(question.type) }}
                                </span>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500">Question ID</div>
                                <div class="text-lg font-semibold text-gray-900">#{{ question.id }}</div>
                            </div>
                        </div>

                        <!-- Question Text -->
                        <div class="mb-6">
                            <h2 class="mb-3 text-xl font-semibold text-gray-900">Question</h2>
                            <div class="rounded-lg border-l-4 border-blue-500 bg-gray-50 p-4">
                                <p class="text-lg leading-relaxed text-gray-800">{{ question.questionText }}</p>
                            </div>
                        </div>

                        <!-- MCQ Options (if applicable) -->
                        <div v-if="question.type === 'mcq' && question.options" class="mb-6">
                            <h3 class="mb-3 text-lg font-semibold text-gray-900">Multiple Choice Options</h3>
                            <div class="space-y-2">
                                <div
                                    v-for="(option, index) in question.options"
                                    :key="index"
                                    :class="option.isCorrect ? 'border-green-300 bg-green-50' : 'border-gray-200 bg-white'"
                                    class="flex items-center rounded-lg border p-3"
                                >
                                    <div
                                        :class="option.isCorrect ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600'"
                                        class="mr-3 flex h-6 w-6 items-center justify-center rounded-full text-sm font-medium"
                                    >
                                        {{ String.fromCharCode(65 + index) }}
                                    </div>
                                    <span :class="option.isCorrect ? 'font-medium text-green-800' : 'text-gray-700'" class="flex-1">{{
                                        option.optionText
                                    }}</span>
                                    <div v-if="option.isCorrect" class="text-green-600">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Answer Text -->
                        <div class="mb-6" v-if="question.answer">
                            <h2 class="mb-3 text-xl font-semibold text-gray-900">Answer</h2>
                            <div class="rounded-lg border-l-4 border-green-500 bg-gray-50 p-4">
                                <p class="text-lg leading-relaxed text-gray-800">{{ question.answer.answerText }}</p>
                            </div>
                        </div>

                        <!-- Bool Answer -->
                        <div class="mb-6" v-if="question.boolAnswer">
                            <h2 class="mb-3 text-xl font-semibold text-gray-900">Answer</h2>
                            <div class="rounded-lg border-l-4 border-green-500 bg-gray-50 p-4">
                                <p class="text-lg leading-relaxed text-gray-800">
                                    {{ question.boolAnswer ? 'True' : 'False' }}
                                </p>
                            </div>
                        </div>

                        <!-- Explanation (if available) -->
                        <div v-if="question.explanation" class="mb-6">
                            <h3 class="mb-3 text-lg font-semibold text-gray-900">Explanation</h3>
                            <div class="rounded-lg border-l-4 border-blue-400 bg-blue-50 p-4">
                                <p class="text-blue-800">{{ question.explanation }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Question Actions -->
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Question Actions</h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div v-if="question.status === 'pending' && currentUser.canManageQuestion" class="space-y-3">
                                <button
                                    @click="approveQuestion"
                                    class="inline-flex w-full items-center justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700"
                                >
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Approve Question
                                </button>
                                <button
                                    @click="rejectQuestion"
                                    class="inline-flex w-full items-center justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
                                >
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Reject Question
                                </button>
                            </div>
                            <div class="space-y-3">
                                <button
                                    @click="duplicateQuestion"
                                    class="inline-flex w-full items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
                                        />
                                    </svg>
                                    Duplicate Question
                                </button>
                                <button
                                    @click="deleteQuestion(question)"
                                    class="inline-flex w-full items-center justify-center rounded-md border border-red-300 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100"
                                >
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                        />
                                    </svg>
                                    Delete Question
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Information -->
                <div class="space-y-6">
                    <!-- Question Metadata -->
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Question Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Subject</label>
                                <div class="mt-1 flex items-center">
                                    <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-blue-100">
                                        <span class="text-sm font-medium text-blue-600">{{ question.subject.name.charAt(0) }}</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ question.subject.name }}</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500">Topic</label>
                                <div class="mt-1">
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                        {{ question.topic.name }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500">Round</label>
                                <div class="mt-1">
                                    <span
                                        class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800"
                                    >
                                        Round {{ question.round }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500">Author</label>
                                <div class="mt-1 flex items-center">
                                    <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-blue-100">
                                        <span class="text-sm font-medium text-blue-600">{{ getInitials(question.author.name) }}</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ question.author.name }}({{ question.author.roles[0] }})</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500">Created Date</label>
                                <div class="mt-1 text-sm text-gray-900">
                                    {{ question.createdAt || 'Not specified' }}({{ question.createdAtHuman }})
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500">Last Modified</label>
                                <div class="mt-1 text-sm text-gray-900">
                                    {{ question.updatedAt || 'Not specified' }} ({{ question.updatedAtHuman }})
                                </div>
                            </div>

                            <div v-if="question.lastModifier">
                                <label class="block text-sm font-medium text-gray-500">Last Modified By</label>
                                <div class="mt-1 flex items-center">
                                    <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-blue-100">
                                        <span class="text-sm font-medium text-blue-600">{{ getInitials(question.lastModifier.name) }}</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900"
                                        >{{ question.lastModifier.name }}({{ question.lastModifier.roles[0] }})</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Question Statistics -->
                    <!-- <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Times Used</span>
                                <span class="text-sm font-medium text-gray-900">{{ question.timesUsed || 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Success Rate</span>
                                <span class="text-sm font-medium text-gray-900">{{ question.successRate || 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Average Time</span>
                                <span class="text-sm font-medium text-gray-900">{{ question.avgTime || 'N/A' }}</span>
                            </div>
                        </div>
                    </div> -->

                    <!-- Related Questions -->
                    <!-- <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Related Questions</h3>
                        <div class="space-y-3">
                            <div v-for="related in relatedQuestions" :key="related.id" 
                                 class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer"
                                 @click="viewRelatedQuestion(related)">
                                <div class="text-sm font-medium text-gray-900 mb-1">{{ related.text.substring(0, 60) }}...</div>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>{{ related.subject }}</span>
                                    <span :class="getDifficultyClass(related.difficulty)" class="px-2 py-1 rounded-full">
                                        {{ related.difficulty }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
        <Modal :show="showEditingQuestionForm" class-name="max-w-xl">
            <QuestionForm :editing-question="editingQuestion" :subjects @close="close" />
        </Modal>
    </AppLayout>
</template>
