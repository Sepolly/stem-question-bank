<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Subject, Topic } from '@/types';
import { ref } from 'vue';

defineProps<{
    topics: Topic[];
    subjects: Subject[];
}>();

const selectedSubject = ref('');
const searchQuery = ref('');

// const topicForm = ref({
//     subjectId: '',
//     name: '',
//     description: '',
//     difficultyLevel: ''
// });
</script>

<template>
    <AppLayout>
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between border-b border-gray-200 pb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Topics</h1>
                    <p class="mt-1 text-sm text-gray-500">Manage topics within subjects for better organization</p>
                </div>
                <!-- <button @click="showTopicForm = true" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Topic
            </button> -->
            </div>

            <!-- Filters -->
            <div class="rounded-lg bg-white p-6 text-gray-700 shadow">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Filter by Subject</label>
                        <select
                            v-model="selectedSubject"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        >
                            <option value="">All Subjects</option>
                            <option v-for="subject in subjects" :key="subject.id" :value="subject.id">{{ subject.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Search Topics</label>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search topics..."
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        />
                    </div>
                </div>
            </div>

            <!-- Topics Table -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Topic</th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Question Count</th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Description</th>
                                <!-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th> -->
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="topic in topics" :key="topic.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100">
                                            <span class="text-sm font-medium text-green-600">{{ topic.name.charAt(0) }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ topic.name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                                        {{ topic.subject.name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">
                                        {{ topic.questionCount }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="max-w-xs truncate text-sm text-gray-900" :title="topic.description">
                                        {{ topic.description }}
                                    </div>
                                </td>
                                <!-- <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button @click="editTopic(topic)" class="cursor-pointer text-indigo-600 hover:text-indigo-900">Edit</button>
                                    <button @click="deleteTopic(topic)" class="cursor-pointer text-red-600 hover:text-red-900">Delete</button>
                                </div>
                            </td> -->
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
