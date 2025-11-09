<script setup lang="ts">
import QuestionController from '@/actions/App/Http/Controllers/QuestionController';
import EventForm from '@/components/EventForm.vue';
import Modal from '@/components/Modal.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Event, Subject, type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import Chart from 'chart.js/auto';
import { onMounted, ref } from 'vue';
import { exports } from '@/routes'

const props = defineProps<{
    showCreateEventModal: boolean;
    currentEvent: Event;
    events?: Event[];
    questionCount: number;
    approvedQuestionCount: number;
    pendingQuestionCount: number;
    rejectedQuestionCount: number;
    subjects: Subject[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const subjectChart = ref<HTMLCanvasElement>();


onMounted(() => {
    if (subjectChart.value) {
        const ctx = subjectChart.value.getContext('2d');
        if (ctx) {
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: [...props.subjects.map((s) => s.name)],
                    datasets: [
                        {
                            data: [...props.subjects.map((s) => s.questionCount)],
                            backgroundColor: [
                                '#3B82F6', // Blue
                                '#10B981', // Green
                                '#F59E0B', // Yellow
                                '#EF4444', // Red
                                '#8B5CF6', // Purple
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                            },
                        },
                    },
                },
            });
        }
    }
});

const showCloseBtn = ref<boolean>(Boolean(props.currentEvent));

function addQuestion() {
    router.visit(QuestionController.index({ event: props.currentEvent.id }, { query: { showForm: true } }));
}

function closeModal() {
    router.visit(document.location.pathname);
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs" :currentEvent :events>
        <div class="space-y-6">
            <!-- Quick Actions - Moved to top -->
            <div class="rounded-xl border border-blue-200 bg-gradient-to-r from-blue-50 to-indigo-50 p-6">
                <h3 class="mb-4 text-lg font-semibold text-blue-900">Quick Actions</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <button
                        @click="addQuestion"
                        class="inline-flex cursor-pointer items-center justify-center rounded-lg border border-transparent bg-blue-600 px-4 py-3 text-sm font-medium text-white shadow-sm transition-colors duration-200 hover:bg-blue-700"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="mr-2 size-5"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>

                        Add New Question
                    </button>
                    <button
                        @click="()=>router.get(exports(currentEvent.id))"
                        class="inline-flex cursor-pointer items-center justify-center rounded-lg border border-transparent bg-green-600 px-4 py-3 text-sm font-medium text-white shadow-sm transition-colors duration-200 hover:bg-green-700"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="mr-2 size-5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25"
                            />
                        </svg>

                        Export Questions
                    </button>
                    <button
                        class="inline-flex cursor-pointer items-center justify-center rounded-lg border border-transparent bg-purple-600 px-4 py-3 text-sm font-medium text-white shadow-sm transition-colors duration-200 hover:bg-purple-700"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="mr-2 size-5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"
                            />
                        </svg>

                        View Reports
                    </button>
                </div>
            </div>

            <!-- Page Header -->
            <div class="border-b border-gray-200 pb-4">
                <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                <p class="mt-1 text-sm text-gray-500">Overview of {{ currentEvent.name }}</p>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                <!-- Total Questions -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-blue-500">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="size-6"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"
                                        />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">Total Questions</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ questionCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Approved Questions -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-green-500">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="size-6"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                        />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">Approved Questions</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ approvedQuestionCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Questions -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-yellow-500">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="size-6"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">Pending Review</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ pendingQuestionCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rejected Questions -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-red-500">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="size-6"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                        />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">Rejected</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ rejectedQuestionCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Analytics -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Questions by Subject -->
                <div class="rounded-lg bg-white p-6 shadow">
                    <h3 class="mb-4 text-lg font-medium text-gray-900">Questions by Subject</h3>
                    <div class="h-64">
                        <canvas ref="subjectChart" class="h-full w-full"></canvas>
                    </div>
                </div>
                <!-- Recent Activity -->
                <div class="rounded-lg bg-white p-6 shadow">
                    <h3 class="mb-4 text-lg font-medium text-gray-900">Recent Activity</h3>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4 rounded-lg p-3 transition-colors duration-150 hover:bg-gray-50">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-blue-100">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium text-gray-900">New question added</div>
                                <div class="text-sm text-gray-500">Calculus derivative problem</div>
                            </div>
                            <div class="text-xs text-gray-400">2 min ago</div>
                        </div>

                        <div class="flex items-center space-x-4 rounded-lg p-3 transition-colors duration-150 hover:bg-gray-50">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-green-100">
                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium text-gray-900">Question approved</div>
                                <div class="text-sm text-gray-500">Physics mechanics question</div>
                            </div>
                            <div class="text-xs text-gray-400">15 min ago</div>
                        </div>

                        <div class="flex items-center space-x-4 rounded-lg p-3 transition-colors duration-150 hover:bg-gray-50">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-yellow-100">
                                <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium text-gray-900">Review requested</div>
                                <div class="text-sm text-gray-500">Biology photosynthesis question</div>
                            </div>
                            <div class="text-xs text-gray-400">1 hour ago</div>
                        </div>

                        <div class="flex items-center space-x-4 rounded-lg p-3 transition-colors duration-150 hover:bg-gray-50">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-purple-100">
                                <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                                    />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium text-gray-900">New topic created</div>
                                <div class="text-sm text-gray-500">Quantum Physics</div>
                            </div>
                            <div class="text-xs text-gray-400">2 hours ago</div>
                        </div>

                        <div class="flex items-center space-x-4 rounded-lg p-3 transition-colors duration-150 hover:bg-gray-50">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100">
                                <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                    />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium text-gray-900">User registered</div>
                                <div class="text-sm text-gray-500">New reviewer account created</div>
                            </div>
                            <div class="text-xs text-gray-400">3 hours ago</div>
                        </div>

                        <div class="flex items-center space-x-4 rounded-lg p-3 transition-colors duration-150 hover:bg-gray-50">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-red-100">
                                <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium text-gray-900">Question rejected</div>
                                <div class="text-sm text-gray-500">Chemistry formula error</div>
                            </div>
                            <div class="text-xs text-gray-400">4 hours ago</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showCreateEventModal" :show-close-btn @close="closeModal" class-name="h-auto">
            <EventForm />
        </Modal>
    </AppLayout>
</template>
