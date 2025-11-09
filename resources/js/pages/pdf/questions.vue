<script setup lang="ts">
import { Question } from '@/types';
import { computed } from 'vue';

const props = defineProps<{
    questions: Question[];
}>();

// Group questions by "round"
const groupedQuestions = computed(() => {
    const groups: Record<string, Question[]> = {};
    props.questions.forEach((q) => {
        const round = q.round ?? 'Ungrouped';
        if (!groups[round]) {
            groups[round] = [];
        }
        groups[round].push(q);
    });
    return groups;
});
</script>

<template>
    <div class="bg-white p-6 text-[12px] leading-relaxed text-black">
        <h1 class="mb-8 text-center text-[22px] font-bold tracking-wide uppercase">Questions List</h1>

        <div v-for="(roundQuestions, roundName) in groupedQuestions" :key="roundName" class="mb-12">
            <!-- Round Heading -->
            <h2 class="mb-4 border-b pb-1 text-[16px] font-semibold">
                {{ roundName }}
            </h2>

            <!-- Loop Through Questions in This Round -->
            <div v-for="(question, index) in roundQuestions" :key="question.id" class="mb-8 break-inside-avoid">
                <h3 class="mb-2 text-[14px] font-medium">{{ index + 1 }}. {{ question.questionText }}</h3>

                <!-- Metadata -->
                <div class="mb-2 space-x-4 text-[11px] text-gray-700">
                    <span><strong>Subject:</strong> {{ question.subject?.name ?? 'N/A' }}</span>
                    <span><strong>Topic:</strong> {{ question.topic?.name ?? 'N/A' }}</span>
                    <span
                        ><strong>Difficulty:</strong>
                        {{ question.difficulty ? question.difficulty.charAt(0).toUpperCase() + question.difficulty.slice(1) : 'N/A' }}</span
                    >
                </div>

                <!-- Question Types -->
                <template v-if="question.type === 'mcq' && question.options?.length">
                    <ul class="mt-2 list-[upper-alpha] pl-6">
                        <li v-for="opt in question.options" :key="opt.id" :class="opt.isCorrect ? 'font-bold text-green-600' : ''" class="mb-1">
                            {{ opt.optionText }}
                        </li>
                    </ul>
                </template>

                <template v-else-if="question.answer?.answerText">
                    <div class="mt-3 text-[12px]">
                        <strong>Answer:</strong>
                        <p>{{ question.answer.answerText ?? '_____________________________' }}</p>
                    </div>
                </template>

                <template v-else-if="question.boolAnswer">
                    <div class="mt-3 text-[12px]">
                        <strong>Answer:</strong>
                        <p>
                            True / False
                            <span class="font-bold text-green-600"> ({{ question.boolAnswer ? 'True' : 'False' }}) </span>
                        </p>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<style>
.break-inside-avoid {
    page-break-inside: avoid;
}
</style>
