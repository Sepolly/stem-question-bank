<script setup lang="ts">
import { X } from 'lucide-vue-next';
import { twMerge } from 'tailwind-merge';

defineProps<{
    show: boolean;
    showCloseBtn?: boolean;
    className?: string;
}>();
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 text-gray-700">
        <div :class="twMerge(`relative flex max-h-[90vh] w-full max-w-md flex-col overflow-auto rounded-lg bg-white p-6`, className)">
            <button v-if="showCloseBtn" class="absolute top-2 right-2 cursor-pointer text-gray-500 hover:text-gray-800" @click="$emit('close')">
                <X class="h-5 w-5" />
            </button>
            <slot />
        </div>
    </div>
</template>
