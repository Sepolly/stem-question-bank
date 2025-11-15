<script setup lang="ts">
import { cn } from '@/lib/utils'
import { Label, type LabelProps } from 'reka-ui'
import { computed, type HTMLAttributes } from 'vue'

const props = defineProps<LabelProps & { class?: HTMLAttributes['class'] }>()

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props

  return delegated
})
</script>

<template>
  <Label
    data-slot="label"
    v-bind="delegatedProps"
    :class="
      cn(
        'text-sm font-medium text-gray-700 select-none',
        'flex items-center gap-2',
        'peer-disabled:opacity-50 peer-disabled:cursor-not-allowed',
        'group-data-[disabled=true]:opacity-50 group-data-[disabled=true]:pointer-events-none',
        props.class
      )
    "
  >
    <slot />
  </Label>
</template>

