<script setup lang="ts">
import SubjectController from '@/actions/App/Http/Controllers/SubjectController';
import Modal from '@/components/Modal.vue';
import SubjectForm from '@/components/SubjectForm.vue';
import { useAuth } from '@/composables/useAuth';
import useEvent from '@/composables/useEvent';
import AppLayout from '@/layouts/AppLayout.vue';
import { Subject } from '@/types';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps<{
    subjects: Subject[];
}>();

const { currentEvent } = useEvent();
const { currentUser } = useAuth();
const showSubjectForm = ref(false);
const editingSubject = ref<Subject | null>(null);

const editSubject = (subject: Subject) => {
    editingSubject.value = subject;
    showSubjectForm.value = true;
};

const deleteSubject = (subject: Subject) => {
    if (confirm('Do you want to delete this subject')) {
        router.delete(SubjectController.destroy({ event: currentEvent.value.id, subject: subject.id }));
    }
};

const closeSubjectForm = () => {
    showSubjectForm.value = false;
    editingSubject.value = null;
};

const gotoSubject = (subject: Subject) => {
    router.get(
        SubjectController.show({
            event: currentEvent.value.id,
            subject: subject.id,
        }),
    );
};
</script>

<template>
    <AppLayout>
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between border-b border-gray-200 pb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Subjects</h1>
                    <p class="mt-1 text-sm text-gray-500">Manage subjects for organizing questions</p>
                </div>
                <button
                    v-if="currentUser.canManageSubject"
                    @click="showSubjectForm = true"
                    class="inline-flex cursor-pointer items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
                >
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add Subject
                </button>
            </div>

            <!-- Subjects Table -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Question Count</th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Topics</th>
                                <th
                                    v-if="currentUser.canManageSubject"
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="subject in subjects" :key="subject.id" @click="gotoSubject(subject)" class="cursor-pointer hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100">
                                            <span class="text-sm font-medium text-blue-600">{{ subject.name.charAt(0) }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ subject.name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="max-w-xs truncate text-sm text-gray-900" :title="subject.description">
                                        {{ subject.description }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                                        {{ subject.questionCount }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        <span
                                            v-for="topic in subject.topics.slice(0, 3)"
                                            :key="topic.id"
                                            class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-800"
                                        >
                                            {{ topic.name }}
                                        </span>
                                        <span
                                            v-if="subject.topics.length > 3"
                                            class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-800"
                                        >
                                            +{{ subject.topics.length - 3 }} more
                                        </span>
                                    </div>
                                </td>
                                <td v-if="currentUser.canManageSubject" class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                    <div class="flex space-x-2">
                                        <button @click.stop="editSubject(subject)" class="cursor-pointer text-indigo-600 hover:text-indigo-900">
                                            Edit
                                        </button>
                                        <button @click.stop="deleteSubject(subject)" class="cursor-pointer text-red-600 hover:text-red-900">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Subject Form Modal -->
            <Modal :show="showSubjectForm">
                <SubjectForm @close="closeSubjectForm" :editing-subject="editingSubject" />
            </Modal>
        </div>
    </AppLayout>
</template>
