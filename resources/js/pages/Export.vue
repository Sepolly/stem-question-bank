<script setup lang="ts">
import ExportController from '@/actions/App/Http/Controllers/ExportController';
import Modal from '@/components/Modal.vue';
import useEvent from '@/composables/useEvent';
import useQuestion from '@/composables/useQuestion';
import AppLayout from '@/layouts/AppLayout.vue';
import { ExportFilters, ExportRecord, ExportSettings, Question, Subject } from '@/types';
import { router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
    questions: Question[];
    subjects: Subject[];
}>();

const exportFilters = ref<ExportFilters>({
    subject: '',
    topic: '',
    difficulty: '',
    round: '',
    status: '',
    type: '',
    dateRange: '',
});

const exportSettings = ref<ExportSettings>({
    includeAnswers: true,
    includeExplanations: true,
    includeMetadata: true,
    randomizeOrder: false,
});

const exportFormat = ref('pdf');
const showPreview = ref(false);
const _subjects = ref(props.subjects);
const selectedSubject = ref<Subject | null>(null);
const { getDifficultyClass, getQuestionType } = useQuestion();
const { currentEvent } = useEvent();

const exportHistory = ref<ExportRecord[]>([
    {
        id: 1,
        date: '2024-01-15 14:30',
        format: 'pdf',
        questionCount: 45,
        filters: 'Subject: Mathematics, Difficulty: Medium',
    },
    {
        id: 2,
        date: '2024-01-14 09:15',
        format: 'excel',
        questionCount: 120,
        filters: 'All questions, Status: Approved',
    },
]);

const previewQuestions = computed(() => {
    let questions = [...props.questions];
    if (exportSettings.value.randomizeOrder) {
        questions = questions.sort(() => Math.random() - 0.5);
    }
    return questions.slice(0, 5); // Show first 5 questions in preview
});

const clearFilters = () => {
    exportFilters.value = {
        subject: '',
        topic: '',
        difficulty: '',
        round: '',
        status: '',
        type: '',
        dateRange: '',
    };
};

const previewExport = () => {
    if (props.questions.length === 0) {
        alert('No questions match your current filters. Please adjust your selection.');
        return;
    }
    showPreview.value = true;
};

const exportQuestions = () => {
    if (props.questions.length === 0) {
        alert('No questions match your current filters. Please adjust your selection.');
        return;
    }

    router.post(ExportController.generateExport({ event: currentEvent.value.id }), {
        questions: JSON.stringify(props.questions.map((q) => q.id)),
        includeAnswers: exportSettings.value.includeAnswers,
    });
};

const downloadExport = (exportRecord: ExportRecord) => {};

const onSelectChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const selectedName = input.value;

    const found = _subjects.value.find((sub) => sub.name.toLocaleLowerCase() === selectedName.toLocaleLowerCase());

    if (!found) return;

    selectedSubject.value = found;
};

watch(exportFilters.value, (filter) => {
    router.visit(
        ExportController.index(
            {
                event: currentEvent.value.id,
            },
            {
                query: filter,
            },
        ),
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
});
</script>

<template>
    <AppLayout>
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="border-b border-gray-200 pb-4">
                <h1 class="text-2xl font-bold text-gray-900">Export Questions</h1>
                <p class="mt-1 text-sm text-gray-500">Export questions in various formats for external use</p>
            </div>

            <!-- Export Filters -->
            <div class="rounded-lg bg-white p-6 text-gray-700 shadow">
                <h3 class="mb-4 text-lg font-medium text-gray-900">Export Filters</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Subject Filter -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Subject</label>
                        <select
                            @change="onSelectChange"
                            v-model="exportFilters.subject"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        >
                            <option value="">All Subjects</option>
                            <option :value="subject.name" v-for="subject in subjects" :key="subject.id">
                                {{ subject.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Topic Filter -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Topic</label>
                        <select
                            v-model="exportFilters.topic"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        >
                            <option value="">All Topics</option>
                            <option v-for="topic in selectedSubject?.topics" :key="topic.id" :value="topic.name">{{ topic.name }}</option>
                        </select>
                    </div>

                    <!-- Difficulty Filter -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Difficulty</label>
                        <select
                            v-model="exportFilters.difficulty"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        >
                            <option value="">All Difficulties</option>
                            <option value="easy">Easy</option>
                            <option value="medium">Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>

                    <!-- Round Filter -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Round</label>
                        <select
                            v-model="exportFilters.round"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        >
                            <option value="">All Rounds</option>
                            <option v-for="round in 5" :key="round" :value="round">Round {{ round }}</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Status</label>
                        <select
                            v-model="exportFilters.status"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        >
                            <option value="">All Statuses</option>
                            <option value="approved">Approved</option>
                            <option value="pending">Pending</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                    <!-- Question Type Filter -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Question Type</label>
                        <select
                            v-model="exportFilters.type"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        >
                            <option value="">All Types</option>
                            <option value="mcq">Multiple Choice</option>
                            <option value="true_false">True/False</option>
                            <option value="short_answer">Short Answer</option>
                            <option value="long_answer">Long Answer</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Date Range</label>
                        <select
                            v-model="exportFilters.dateRange"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        >
                            <option value="">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="quarter">This Quarter</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <button @click="clearFilters" class="text-sm text-gray-500 hover:text-gray-700">Clear Filters</button>
                    <span class="text-sm text-gray-500">{{ questions.length }} questions match your criteria</span>
                </div>
            </div>

            <!-- Export Options -->
            <div class="rounded-lg bg-white p-6 shadow">
                <h3 class="mb-4 text-lg font-medium text-gray-900">Export Options</h3>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Format Selection -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Export Format</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" v-model="exportFormat" value="pdf" class="mr-2" />
                                <span class="text-sm text-gray-700">PDF Document</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" v-model="exportFormat" value="excel" class="mr-2" />
                                <span class="text-sm text-gray-700">Excel Spreadsheet</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" v-model="exportFormat" value="csv" class="mr-2" />
                                <span class="text-sm text-gray-700">CSV File</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" v-model="exportFormat" value="json" class="mr-2" />
                                <span class="text-sm text-gray-700">JSON Data</span>
                            </label>
                        </div>
                    </div>

                    <!-- Export Settings -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Export Settings</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" v-model="exportSettings.includeAnswers" class="mr-2" />
                                <span class="text-sm text-gray-700">Include Correct Answers</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" v-model="exportSettings.includeExplanations" class="mr-2" />
                                <span class="text-sm text-gray-700">Include Explanations</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" v-model="exportSettings.includeMetadata" class="mr-2" />
                                <span class="text-sm text-gray-700">Include Question Metadata</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" v-model="exportSettings.randomizeOrder" class="mr-2" />
                                <span class="text-sm text-gray-700">Randomize Question Order</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="rounded-lg bg-white p-6 shadow">
                <div class="flex flex-col gap-4 sm:flex-row">
                    <button
                        @click="previewExport"
                        class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-6 py-3 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
                    >
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                            />
                        </svg>
                        Preview Export
                    </button>
                    <button
                        @click="exportQuestions"
                        class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
                    >
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>
                        Export Questions
                    </button>
                </div>
            </div>

            <!-- Export History -->
            <div class="rounded-lg bg-white p-6 shadow">
                <h3 class="mb-4 text-lg font-medium text-gray-900">Export History</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Format</th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Questions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Filters</th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="exportRecord in exportHistory" :key="exportRecord.id">
                                <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-900">{{ exportRecord.date }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                                        {{ exportRecord.format.toUpperCase() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-900">{{ exportRecord.questionCount }}</td>
                                <td class="max-w-xs truncate px-6 py-4 text-sm text-gray-500">{{ exportRecord.filters }}</td>
                                <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                    <button @click="downloadExport(exportRecord)" class="text-blue-600 hover:text-blue-900">Download</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Preview Modal -->
            <Modal :show="showPreview">
                <div class="mt-3">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Export Preview</h3>
                        <button @click="showPreview = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div v-for="question in previewQuestions" :key="question.id" class="rounded-lg border border-gray-200 p-4">
                            <div class="mb-2 flex items-start justify-between">
                                <span
                                    :class="getDifficultyClass(question.difficulty)"
                                    class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
                                >
                                    {{ question.difficulty }}
                                </span>
                                <span class="text-xs text-gray-500">{{ getQuestionType(question.type) }}</span>
                            </div>
                            <p class="mb-2 text-sm text-gray-900">{{ question.questionText }}</p>
                            <div class="text-xs text-gray-500">
                                <span class="font-medium">{{ question.subject.name }}</span> • {{ question.topic.name }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button
                            @click="showPreview = false"
                            class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button
                            @click="exportQuestions"
                            class="rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
                        >
                            Confirm Export
                        </button>
                    </div>
                </div>
            </Modal>
        </div>
    </AppLayout>
</template>
