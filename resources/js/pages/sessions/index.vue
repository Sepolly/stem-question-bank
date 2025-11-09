<script setup lang="ts">
import QuestionController from '@/actions/App/Http/Controllers/QuestionController';
import QuestionSessionController from '@/actions/App/Http/Controllers/QuestionSessionController';
import Modal from '@/components/Modal.vue';
import SessionForm from '@/components/SessionForm.vue';
import { useAuth } from '@/composables/useAuth';
import useEvent from '@/composables/useEvent';
import AppLayout from '@/layouts/AppLayout.vue';
import { Question, Session } from '@/types';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    sessions: Session[];
    currentSession?: Session;
    error?: string
}>();

const { currentEvent } = useEvent();
const { currentUser } = useAuth();

const _currentSession = ref<Session | undefined>(props.currentSession);
const showSessionForm = ref(false);
const showCurrentSessionForm = ref(false);
const startingSession = ref(false);
const endingSession = ref(false);
const showAnswers = ref(false);
const currentQuestionIndex = ref(0);
const sessionHasStarted = computed(() => _currentSession.value && _currentSession.value?.status == 'ongoing');

/* --- Methods --- */
const openSession = (session: Session) => {
    _currentSession.value = session;
    showCurrentSessionForm.value = true;
    currentQuestionIndex.value = 0;
    showAnswers.value = false;
};

const startSession = (sessionId: number | undefined) => {
    if (sessionId) {
        startingSession.value = true;
        router.patch(
            QuestionSessionController.startSession({
                event: currentEvent.value.id,
                sessionId,
            }),
            {},
            {
                onFinish: () => {
                    startingSession.value = false;
                    if (_currentSession.value) _currentSession.value.status = 'ongoing';
                },
            },
        );
    }
};

const endSession = (sessionId: number | undefined) => {
    if (sessionId) {
        endingSession.value = true;
        router.patch(
            QuestionSessionController.endSession({
                event: currentEvent.value.id,
                sessionId,
            }),
            {},
            {
                onFinish: () => {
                    endingSession.value = false;
                    showCurrentSessionForm.value = false;
                    if (_currentSession.value) _currentSession.value.status = 'ended';
                },
            },
        );
    }
};

const nextQuestion = (lastId?: number) => {
    if (typeof lastId != 'number') {
        return;
    }

    router.patch(
        QuestionController.updateHasBeenAsked({
            event: currentEvent.value.id,
            question: lastId,
        }),
        {
            has_been_asked: true,
        },
    );

    if (_currentSession.value && currentQuestionIndex.value < _currentSession.value.questions.length - 1) {
        currentQuestionIndex.value++;
        showAnswers.value = false;
    }
};

const previousQuestion = () => {
    if (currentQuestionIndex.value > 0) {
        currentQuestionIndex.value--;
        showAnswers.value = false;
    }
};

/* --- Computed --- */
const currentQuestion = computed<Question | null>(() => {
    if (!_currentSession.value) return null;
    return _currentSession.value.questions[currentQuestionIndex.value];
});

const progressText = computed(() => {
    if (!_currentSession.value) return '';
    const total = _currentSession.value.questions.length;
    const current = currentQuestionIndex.value + 1;
    return `Question ${current} of ${total}`;
});
</script>

<template>
    <AppLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-gray-200 pb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Sessions</h1>
                    <p class="mt-1 text-sm text-gray-500">Manage sessions for questions</p>
                </div>
                <button
                    v-if="currentUser.canManageSubject"
                    @click="showSessionForm = true"
                    class="inline-flex cursor-pointer items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
                >
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Start Session
                </button>
            </div>

            <!-- Sessions Table -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Questions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Start Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">End Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="session in sessions" :key="session.id" @click="openSession(session)" class="cursor-pointer hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-blue-600">{{ session.title }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ session.questions.length }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ session.startsAt }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ session.endsAt }}</td>
                                <td
                                    class="px-6 py-4 text-sm font-semibold"
                                    :class="{
                                        'text-green-600': session.status === 'ongoing',
                                        'text-yellow-600': session.status === 'pending',
                                        'text-gray-600': session.status === 'ended',
                                    }"
                                >
                                    {{ session.status }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Create Session Modal -->
            <Modal :show="showSessionForm" :show-close-btn="true" @close="showSessionForm = false">
                <SessionForm :error />
            </Modal>

            <!-- Current Session Modal -->
            <Modal
                :show="showCurrentSessionForm"
                :show-close-btn="true"
                @close="showCurrentSessionForm = false"
                :class-name="sessionHasStarted ? `max-w-2/3` : ``"
            >
                <!-- Session not started -->
                <section v-if="_currentSession && _currentSession.status !== 'ongoing'">
                    <h2 class="mb-4 text-center text-lg font-bold">{{ _currentSession.title }}</h2>
                    <p>Questions: {{ _currentSession.questions.length }}</p>
                    <p>Difficulty: {{ _currentSession.difficulty }}</p>
                    <p>Type: {{ _currentSession.type }}</p>
                    <p>Round: {{ _currentSession.round }}</p>
                    <p>Status: {{ _currentSession.status }}</p>

                    <button
                        @click="startSession(_currentSession.id)"
                        :disabled="startingSession"
                        class="mt-4 inline-flex cursor-pointer items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 disabled:opacity-70"
                    >
                        {{ startingSession ? 'Starting...' : 'Start Session' }}
                    </button>
                </section>

                <!-- Session ongoing -->
                <section v-else-if="_currentSession && _currentSession.status === 'ongoing'" class="space-y-6">
                    <div class="flex items-center justify-between border-b pb-3">
                        <h2 class="text-xl font-bold text-gray-800">{{ _currentSession.title }}</h2>
                        <p class="text-red-500" v-if="currentQuestion?.hasBeenAsked">This question has been asked</p>
                        <span class="text-sm text-gray-500">{{ progressText }}</span>
                    </div>

                    <div class="rounded-lg bg-gray-50 p-4 shadow-inner">
                        <h3 class="mb-2 text-lg font-semibold text-gray-900">
                            {{ currentQuestion?.questionText }}
                        </h3>
                        <div v-if="currentQuestion && currentQuestion.type === 'mcq' && currentQuestion.options" class="mb-6">
                            <div class="space-y-2">
                                <div
                                    v-for="(option, index) in currentQuestion.options"
                                    :key="index"
                                    :class="option.isCorrect && showAnswers ? 'border-green-300 bg-green-50' : 'border-gray-200 bg-white'"
                                    class="flex items-center rounded-lg border p-3"
                                >
                                    <div
                                        :class="option.isCorrect && showAnswers ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600'"
                                        class="mr-3 flex h-6 w-6 items-center justify-center rounded-full text-sm font-medium"
                                    >
                                        {{ String.fromCharCode(65 + index) }}
                                    </div>
                                    <span :class="option.isCorrect && showAnswers ? 'font-medium text-green-800' : 'text-gray-700'" class="flex-1">{{
                                        option.optionText
                                    }}</span>
                                    <div v-if="option.isCorrect && showAnswers" class="text-green-600">
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

                        <div v-if="showAnswers && currentQuestion?.type != 'mcq'" class="mt-4 font-semibold text-green-700">
                            Correct Answer:
                            <span v-if="currentQuestion?.answer">{{ currentQuestion?.answer?.answerText }}</span>
                            <span v-if="currentQuestion?.boolAnswer">{{ currentQuestion.boolAnswer ? 'True' : 'False' }}</span>
                        </div>
                    </div>

                    <!-- Controls -->
                    <div class="my-4 flex flex-col">
                        <div class="space-x-2">
                            <button
                                @click="previousQuestion"
                                :disabled="currentQuestionIndex === 0"
                                class="cursor-pointer rounded-md bg-gray-200 px-3 py-1 text-gray-800 hover:bg-gray-300 disabled:opacity-50"
                            >
                                Previous
                            </button>
                            <button
                                @click="nextQuestion(currentQuestion?.id)"
                                :disabled="currentQuestionIndex >= _currentSession.questions.length - 1"
                                class="cursor-pointer rounded-md bg-gray-200 px-3 py-1 text-gray-800 hover:bg-gray-300 disabled:opacity-50"
                            >
                                Next
                            </button>
                        </div>

                        <div class="mt-4 flex items-center justify-between">
                            <button
                                @click="showAnswers = !showAnswers"
                                class="cursor-pointer rounded-md bg-blue-600 px-3 py-1 text-white hover:bg-blue-700"
                            >
                                {{ showAnswers ? 'Hide Answer' : 'Show Answer' }}
                            </button>

                            <button
                                @click="endSession(_currentSession.id)"
                                :disabled="endingSession"
                                class="cursor-pointer rounded-md bg-red-600 px-3 py-1 text-white hover:bg-red-700 disabled:opacity-70"
                            >
                                {{ endingSession ? 'Ending...' : 'End Session' }}
                            </button>
                        </div>
                    </div>
                </section>

                <section v-else class="text-center text-gray-500">
                    <p>No session data available.</p>
                </section>
            </Modal>
        </div>
    </AppLayout>
</template>
