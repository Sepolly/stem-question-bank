<script setup lang="ts">
import SubjectController from '@/actions/App/Http/Controllers/SubjectController';
import Modal from '@/components/Modal.vue';
import QuestionForm from '@/components/QuestionForm.vue';
import SubjectForm from '@/components/SubjectForm.vue';
import { useAuth } from '@/composables/useAuth';
import useQuestion from '@/composables/useQuestion';
import useSubject from '@/composables/useSubject';
import AppLayout from '@/layouts/AppLayout.vue';
import { Subject } from '@/types';
import { ref } from 'vue';

defineProps<{
    subject: Subject;
}>();

const { currentUser } = useAuth();
const { showQuestion, getQuestionType } = useQuestion();
const {gotoSubjects} = useSubject();

const showQuestionForm = ref(false);
const showSubjectForm = ref(false)

const closeSubjectForm = () => {
    showSubjectForm.value  = false
}

const editSubject = () => {
    showSubjectForm.value  = true
}

</script>

<template>
    <AppLayout>
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="border-b border-gray-200 pb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            {{ subject.name }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">View and manage topics and questions for this subject</p>
                    </div>

                    <div class="flex space-x-3">
                        <button
                            @click="gotoSubjects"
                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                        >
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Subjects
                        </button>
                        <button
                            @click="editSubject"
                            v-if="currentUser.canManageSubject"
                            class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                        >
                            Edit Subject
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Main Area -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Topics Card -->
                    <div class="rounded-lg bg-white p-6 text-gray-700 shadow">
                        <div class="mb-4 flex items-center justify-between">
                            <h2 class="text-lg font-semibold">Topics</h2>
                            <!-- <button v-if="currentUser.canManageSubject" @click="showTopicForm = true"
                class="px-3 py-1.5 text-sm bg-green-600 text-white rounded-md shadow hover:bg-green-700">
                Add Topic
              </button> -->
                        </div>

                        <table class="w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-3 py-2 text-left">#</th>
                                    <th class="px-3 py-2 text-left">Topic</th>
                                    <th class="px-3 py-2 text-left">Questions Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(topic, idx) in subject.topics" :key="topic.id" class="border-b hover:bg-gray-50">
                                    <td class="px-3 py-2">{{ idx + 1 }}</td>
                                    <td class="px-3 py-2">{{ topic.name }}</td>
                                    <td class="px-3 py-2">{{ topic.questionCount }}</td>
                                </tr>
                                <tr v-if="!subject.topics.length">
                                    <td colspan="3" class="px-3 py-4 text-center text-gray-500">No topics found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Questions Card -->
                    <div class="rounded-lg bg-white p-6 text-gray-700 shadow">
                        <div class="mb-4 flex items-center justify-between">
                            <h2 class="text-lg font-semibold">Questions</h2>
                            <button
                                v-if="currentUser.canManageQuestion"
                                @click="showQuestionForm = true"
                                class="rounded-md bg-blue-600 px-3 py-1.5 text-sm text-white shadow hover:bg-blue-700"
                            >
                                Add Question
                            </button>
                        </div>

                        <div class="divide-y">
                            <div
                                v-for="q in subject.questions"
                                :key="q.id"
                                @click="() => showQuestion(q)"
                                class="flex cursor-pointer items-start justify-between rounded-md px-2 py-4 hover:bg-gray-50"
                            >
                                <div>
                                    <p class="font-medium text-gray-900">{{ q.questionText }}</p>
                                    <span class="flex gap-2">
                                        <p class="text-sm text-gray-500">Topic: {{ q.topic.name }}</p>
                                        <p class="text-sm text-gray-500">Type: {{ getQuestionType(q.type) }}</p>
                                        <p class="text-sm text-gray-500">Status: {{ q.status }}</p>
                                    </span>
                                </div>
                            </div>
                            <div v-if="!subject.questions.length" class="py-4 text-center text-gray-500">No questions found for this subject.</div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Info -->
                <div class="space-y-6">
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Subject Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Total Questions</label>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ subject.questionCount }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Created At</label>
                                <p class="text-sm text-gray-900">{{ subject.createdAt }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Last Modified</label>
                                <p class="text-sm text-gray-900">{{ subject.updatedAt }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showQuestionForm" class-name="max-w-xl">
            <QuestionForm :subjects="[subject]" @close="showQuestionForm = false" />
        </Modal>

        <!-- Subject Form Modal -->
        <Modal :show="showSubjectForm">
            <SubjectForm @close="closeSubjectForm" :editing-subject="subject" />
        </Modal>
    </AppLayout>
</template>
