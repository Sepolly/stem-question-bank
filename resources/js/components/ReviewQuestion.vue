<script setup lang="ts">
import { ref } from 'vue';

interface QuestionOption {
    text: string;
    isCorrect: boolean;
}

interface Question {
    id: number;
    text: string;
    subject: string;
    topic: string;
    difficulty: 'easy' | 'medium' | 'hard';
    status: 'pending' | 'approved' | 'rejected';
    type: string;
    options?: QuestionOption[];
    explanation?: string;
    tags?: string[];
}

interface Props {
    question: Question;
}

interface Emits {
    close: [];
    reviewed: [decision: string, comments: string, revisionRequirements?: string];
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const reviewDecision = ref('');
const reviewComments = ref('');
const revisionRequirements = ref('');

const getDifficultyClass = (difficulty: string) => {
    switch (difficulty) {
        case 'easy':
            return 'bg-green-100 text-green-800';
        case 'medium':
            return 'bg-yellow-100 text-yellow-800';
        case 'hard':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

const submitReview = () => {
    if (!reviewDecision.value) {
        alert('Please select a decision');
        return;
    }

    if (reviewDecision.value === 'request-revision' && !revisionRequirements.value.trim()) {
        alert('Please specify revision requirements');
        return;
    }

    emit('reviewed', reviewDecision.value, reviewComments.value, revisionRequirements.value);
};
</script>

<template>
    <div class="bg-opacity-50 fixed inset-0 z-50 h-full w-full overflow-y-auto bg-gray-600">
        <div class="relative top-10 mx-auto w-11/12 rounded-md border bg-white p-5 shadow-lg md:w-3/4 lg:w-1/2">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="text-lg font-medium text-gray-900">Review Question</h3>
                    <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Question Content -->
                <div class="mt-4 space-y-4">
                    <!-- Question Header -->
                    <div class="grid grid-cols-2 gap-4 text-sm md:grid-cols-4">
                        <div>
                            <span class="font-medium text-gray-500">Subject:</span>
                            <p class="text-gray-900">{{ question.subject }}</p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-500">Topic:</span>
                            <p class="text-gray-900">{{ question.topic }}</p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-500">Difficulty:</span>
                            <span :class="getDifficultyClass(question.difficulty)" class="inline-flex rounded-full px-2 py-1 text-xs font-semibold">
                                {{ question.difficulty }}
                            </span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-500">Type:</span>
                            <p class="text-gray-900">{{ question.type }}</p>
                        </div>
                    </div>

                    <!-- Question Text -->
                    <div class="border-t pt-4">
                        <h4 class="mb-2 font-medium text-gray-700">Question:</h4>
                        <div class="rounded-lg bg-gray-50 p-4 text-gray-900">
                            {{ question.text }}
                        </div>
                    </div>

                    <!-- MCQ Options -->
                    <div v-if="question.type === 'MCQ' && question.options" class="border-t pt-4">
                        <h4 class="mb-2 font-medium text-gray-700">Options:</h4>
                        <div class="space-y-2">
                            <div
                                v-for="(option, index) in question.options"
                                :key="index"
                                class="flex items-center space-x-3 rounded-lg bg-gray-50 p-3"
                            >
                                <span class="text-sm font-medium text-gray-500">{{ String.fromCharCode(65 + index) }}.</span>
                                <span class="flex-1 text-gray-900">{{ option.text }}</span>
                                <span
                                    v-if="option.isCorrect"
                                    class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800"
                                >
                                    Correct
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Explanation -->
                    <div v-if="question.explanation" class="border-t pt-4">
                        <h4 class="mb-2 font-medium text-gray-700">Explanation:</h4>
                        <div class="rounded-lg bg-blue-50 p-4 text-gray-900">
                            {{ question.explanation }}
                        </div>
                    </div>

                    <!-- Tags -->
                    <div v-if="question.tags && question.tags.length > 0" class="border-t pt-4">
                        <h4 class="mb-2 font-medium text-gray-700">Tags:</h4>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="tag in question.tags"
                                :key="tag"
                                class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800"
                            >
                                {{ tag }}
                            </span>
                        </div>
                    </div>

                    <!-- Review Form -->
                    <div class="border-t pt-4">
                        <h4 class="mb-2 font-medium text-gray-700">Review Decision:</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Decision *</label>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            v-model="reviewDecision"
                                            type="radio"
                                            value="approve"
                                            class="h-4 w-4 border-gray-300 text-green-600 focus:ring-green-500"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Approve</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            v-model="reviewDecision"
                                            type="radio"
                                            value="reject"
                                            class="h-4 w-4 border-gray-300 text-red-600 focus:ring-red-500"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Reject</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            v-model="reviewDecision"
                                            type="radio"
                                            value="request-revision"
                                            class="h-4 w-4 border-gray-300 text-yellow-600 focus:ring-yellow-500"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Request Revision</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Comments (Optional)</label>
                                <textarea
                                    v-model="reviewComments"
                                    rows="3"
                                    placeholder="Provide feedback or reasoning for your decision..."
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                ></textarea>
                            </div>

                            <div v-if="reviewDecision === 'request-revision'">
                                <label class="mb-2 block text-sm font-medium text-gray-700">Revision Requirements *</label>
                                <textarea
                                    v-model="revisionRequirements"
                                    rows="3"
                                    placeholder="Specify what needs to be changed..."
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-6 flex justify-end space-x-3 border-t pt-4">
                    <button
                        @click="$emit('close')"
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                    <button
                        @click="submitReview"
                        :disabled="!reviewDecision"
                        class="rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        Submit Review
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
