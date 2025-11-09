<script setup lang="ts">
import QuestionController from '@/actions/App/Http/Controllers/QuestionController';
import Modal from '@/components/Modal.vue';
import QuestionForm from '@/components/QuestionForm.vue';
import { useAuth } from '@/composables/useAuth';
import useEvent from '@/composables/useEvent';
import useExport from '@/composables/useExport';
import useQuestion from '@/composables/useQuestion';
import AppLayout from '@/layouts/AppLayout.vue';
import { PaginatedQuestion, Subject } from '@/types';
import { router } from '@inertiajs/vue3';
import debounce from 'lodash.debounce';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
    subjects: Subject[];
    questions: PaginatedQuestion;
    showForm: boolean;
    error?: string;
}>();

const filters = ref({
    subject: '',
    difficulty: '',
    status: '',
});

const { getDifficultyClass, getQuestionType, getStatusClass, changeStatus, showQuestion } = useQuestion();
const { parseCSVFile, parseExcelFile, parseJSONFile, getExt } = useExport();
const { currentEvent } = useEvent();
const { currentUser } = useAuth();

const searchTerm = ref('');
const showQuestionForm = ref(props.showForm);
const showChooseImport = ref(false);
const fileInputRef = ref<HTMLInputElement>();
const importFileType = ref<'excel' | 'json'>('excel');

const showImportPreview = ref(false);
const selectedFile = ref<File | null>(null);
const previewHeaders = ref<string[]>([]);
const previewRows = ref<any[]>([]);
const parseError = ref<string>('');
const uploading = ref(false)

const clearFilters = () => {
    filters.value = {
        subject: '',
        difficulty: '',
        status: '',
    };
    searchTerm.value = '';
    router.visit(
        QuestionController.index(
            {
                event: currentEvent.value.id,
            },
            {
                query: { ...filters.value },
            },
        ),
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

function closeForm() {
    showQuestionForm.value = false;
    if (props.showForm) router.visit(QuestionController.index({ event: currentEvent.value.id }));
}

watch(filters.value, (filter) => {
    router.visit(
        QuestionController.index(
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

const applySearch = debounce(async (searchTerm: string) => {
    router.visit(
        QuestionController.index(
            {
                event: currentEvent.value.id,
            },
            {
                query: { search: searchTerm, ...filters.value },
            },
        ),
        {
            preserveState: true,
        },
    );
}, 500);

const currentPage = ref(props.questions.meta?.current_page);
const totalPages = ref(props.questions.meta?.total);
const visiblePages = ref(props.questions.meta?.links);


const previousPage = () => {
    if (!props.questions.links?.prev) {
        return;
    }
    router.visit(props.questions.links.prev);
};

const nextPage = () => {
    if (!props.questions.links?.next) {
        return;
    }
    router.visit(props.questions.links.next);
};

const goToPage = (pageNo: number | null) => {
    if (typeof pageNo === 'number') {
        router.visit(
            QuestionController.index(
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

const approveQuestion = (questionId: number) => {
    changeStatus(questionId, 'approved');
};

const rejectQuestion = (questionId: number) => {
    changeStatus(questionId, 'rejected');
};

watch(
    () => searchTerm.value,
    async (val: string) => {
        await applySearch(val);
    },
);

const accept = computed(() => {
    return importFileType.value === 'excel' ? '.csv,.xls,.xlsx' : '.json';
});

const chooseImport = () => {
    showChooseImport.value = !showChooseImport.value;
};

const openFilePicker = (fileType: 'excel' | 'json') => {
    importFileType.value = fileType;
    fileInputRef.value?.click();
};

const handleFileChange = async (e: Event) => {
    const input = e.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) return;

    showChooseImport.value = false;
    selectedFile.value = file;
    parseError.value = '';
    previewHeaders.value = [];
    previewRows.value = [];

    try {
        if (importFileType.value === 'json') {
            const { rows, headers } = await parseJSONFile(file);
            previewRows.value = rows;
            previewHeaders.value = headers;
        } else {
            const ext = getExt(file.name);
            if (ext === 'csv') {
                const { rows, headers } = await parseCSVFile(file);
                previewRows.value = rows;
                previewHeaders.value = headers;
            } else if (ext === 'xls' || ext === 'xlsx') {
                const { rows, headers } = await parseExcelFile(file);
                previewRows.value = rows;
                previewHeaders.value = headers;
            } else {
                // fallback: try CSV first, then Excel
                try {
                    const { rows, headers } = await parseCSVFile(file);
                    previewRows.value = rows;
                    previewHeaders.value = headers;
                } catch {
                    const { rows, headers } = await parseExcelFile(file);
                    previewRows.value = rows;
                    previewHeaders.value = headers;
                }
            }
        }
        if (!previewRows.value.length) {
            parseError.value = 'No rows found in the selected file.';
        } else {
            showImportPreview.value = true;
        }
    } catch (err: any) {
        parseError.value = err?.message || 'Failed to parse file.';
    } finally {
        // reset input so selecting same file again still triggers change
        if (fileInputRef.value) fileInputRef.value.value = '';
    }
};

const confirmImport = () => {
    uploading.value = true
    router.post(QuestionController.importMethod({ event: currentEvent.value.id }), {
        questions: selectedFile.value,
    },{
        onFinish: ()=> uploading.value = false
    });
};
</script>

<template>
    <AppLayout>
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between border-b border-gray-200 pb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Questions</h1>
                    <p class="mt-1 text-sm text-gray-500">Manage and review all questions in the system</p>
                </div>
                <!-- Actions -->
                <div class="flex gap-3" v-if="currentUser.canAddQuestion">
                    <button
                        @click="showQuestionForm = true"
                        class="inline-flex cursor-pointer items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
                    >
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add
                    </button>

                    <div class="relative">
                        <button
                            @click="chooseImport"
                            class="inline-flex cursor-pointer items-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700"
                        >
                            <svg
                                class="mr-2 h-4 w-4"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 3.75H6.912a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859M12 3v8.25m0 0-3-3m3 3 3-3"
                                />
                            </svg>
                            Import
                        </button>

                        <!-- Dropdown -->
                        <div
                            v-show="showChooseImport"
                            class="absolute top-10 right-0 z-10 flex w-40 flex-col space-y-2 rounded bg-white p-2 text-gray-700 shadow-lg"
                        >
                            <button @click="openFilePicker('excel')" class="cursor-pointer px-2 py-1 text-left transition-all hover:bg-gray-50">
                                CSV/Excel
                            </button>
                            <button @click="openFilePicker('json')" class="cursor-pointer px-2 py-1 text-left transition-all hover:bg-gray-50">
                                JSON
                            </button>
                        </div>

                        <!-- Hidden file input OUTSIDE of the button -->
                        <input ref="fileInputRef" type="file" class="hidden" :accept="accept" @change="handleFileChange" />
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="rounded-lg bg-white p-6 shadow">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Subject Filter -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Subject</label>
                        <select
                            v-model="filters.subject"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        >
                            <option value="">All Subjects</option>
                            <option :selected="filters.subject === subject.name" v-for="subject in subjects" :value="subject.name" :key="subject.id">
                                {{ subject.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Difficulty Filter -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Difficulty</label>
                        <select
                            v-model="filters.difficulty"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        >
                            <option value="">All Difficulties</option>
                            <option value="easy">Easy</option>
                            <option value="medium">Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Status</label>
                        <select
                            v-model="filters.status"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        >
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                    <!-- Search -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Search</label>
                        <input
                            v-model="searchTerm"
                            type="text"
                            placeholder="Search questions..."
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        />
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <button @click="clearFilters" class="cursor-pointer text-sm text-gray-500 hover:text-gray-700">Clear Filters</button>
                </div>
            </div>

            <!-- Questions Table -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="w-1/3 px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Question</th>
                                <th class="w-1/6 px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Subject</th>
                                <th class="w-1/6 px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Topic</th>
                                <th class="w-1/12 px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Difficulty</th>
                                <th class="w-1/12 px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Status</th>
                                <th
                                    v-if="currentUser.canManageQuestion"
                                    class="w-1/6 px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr
                                v-for="question in questions.data"
                                :key="question.id"
                                class="cursor-pointer hover:bg-gray-50"
                                @click="() => showQuestion(question)"
                            >
                                <td class="px-6 py-4">
                                    <div class="max-w-xs text-sm text-gray-900">
                                        <div class="mb-1 font-medium text-gray-900">
                                            {{ question.questionText.substring(0, 80) }}{{ question.questionText.length > 80 ? '...' : '' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Type: {{ getQuestionType(question.type) }} | Round: {{ question.round }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-900">{{ question.subject.name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-900">{{ question.topic.name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        :class="getDifficultyClass(question.difficulty)"
                                        class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
                                    >
                                        {{ question.difficulty }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="getStatusClass(question.status)" class="inline-flex rounded-full px-2 py-1 text-xs font-semibold">
                                        {{ question.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium" v-if="currentUser.canManageQuestion">
                                    <div class="flex flex-col space-y-1">
                                        <div v-if="question.status === 'pending'" class="flex space-x-1">
                                            <button
                                                @click.stop="approveQuestion(question.id)"
                                                class="cursor-pointer text-xs text-green-600 hover:text-green-900"
                                            >
                                                ✓ Approve
                                            </button>
                                            <button
                                                @click.stop="rejectQuestion(question.id)"
                                                class="cursor-pointer text-xs text-red-600 hover:text-yellow-900"
                                            >
                                                ✗ Reject
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                    <!-- <div class="flex flex-1 justify-between sm:hidden">
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
                    </div> -->
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium">{{ questions.meta?.from }}</span> to
                                <span class="font-medium">{{ questions.meta?.to }}</span> of
                                <span class="font-medium">{{ questions.meta?.total }}</span> results
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
            </div>

            <!-- Question Form Modal -->
            <Modal :show="showQuestionForm" @close="showQuestionForm = false" class-name="max-w-xl">
                <QuestionForm :subjects @close="closeForm" />
            </Modal>

            <!-- preview import modal -->
            <Modal :show="showImportPreview" :show-close-btn="true" class-name="max-w-full" @close="showImportPreview = false">
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold">Preview import</h2>
                    <div v-if="parseError" class="rounded bg-red-50 p-3 text-sm text-red-700">
                        {{ parseError }}
                    </div>

                    <div v-else>
                        <p class="mb-2 text-sm text-gray-600">
                            File: <span class="font-medium">{{ selectedFile?.name }}</span> — Rows detected:
                            <span class="font-medium">{{ previewRows.length }}</span>
                        </p>

                        <div class="overflow-auto rounded border">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            v-for="h in previewHeaders"
                                            :key="h"
                                            class="px-3 py-2 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                        >
                                            {{ h }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr v-for="(row, i) in previewRows.slice(0, 10)" :key="i">
                                        <td v-for="h in previewHeaders" :key="h" class="px-3 py-2 text-sm text-gray-700">
                                            {{ row[h] }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <p class="mt-2 text-xs text-gray-500">Showing first 10 rows.</p>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button
                            class="cursor-pointer rounded-md border px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                            @click="showImportPreview = false"
                        >
                            Cancel
                        </button>
                        <button
                            class="cursor-pointer rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 disabled:opacity-60"
                            :disabled="uploading || !!parseError || !previewRows.length"
                            @click="confirmImport"
                        >
                            {{ uploading ? 'Uploading…' : 'Confirm Upload' }}
                        </button>
                    </div>
                </div>
            </Modal>
        </div>
    </AppLayout>
</template>
