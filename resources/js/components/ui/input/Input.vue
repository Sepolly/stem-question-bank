<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'
import { useVModel } from '@vueuse/core'

const props = defineProps<{
  defaultValue?: string | number
  modelValue?: string | number
  class?: HTMLAttributes['class']
}>()

const emits = defineEmits<{
  (e: 'update:modelValue', payload: string | number): void
}>()

const modelValue = useVModel(props, 'modelValue', emits, {
  passive: true,
  defaultValue: props.defaultValue,
})
</script>

<template>
  <input
    v-model="modelValue"
    data-slot="input"
    :class="
      cn(
        'flex text-black h-11 w-full rounded-xl border border-gray-300 bg-white/60 px-4 py-2',
        'md:text-sm placeholder:text-gray-400 shadow-sm',
        'transition-all duration-200 outline-none',
        'focus:border-gray-900 focus:ring-4 focus:ring-gray-300/40',
        'disabled:pointer-events-none disabled:opacity-50',
        'file:border-0 file:bg-transparent file:text-sm file:font-medium',
        props.class
      )
    "
    v-bind="$attrs"
  />
</template>

