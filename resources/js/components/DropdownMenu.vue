<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';

// Props
const props = defineProps({
    placement: { type: String, default: 'right' }, // "left" | "right"
    width: { type: String, default: '12rem' }, // e.g. "12rem", "16rem", "w-56"
    closeOnClick: { type: Boolean, default: true },
    transition: { type: String, default: 'fade-scale' }, // "fade-scale", "fade"
});

const isOpen = ref(false);
const menuRef = ref(null);

function toggleMenu() {
    isOpen.value = !isOpen.value;
}

function closeMenu(e) {
    if (menuRef.value && !menuRef.value.contains(e.target)) {
        isOpen.value = false;
    }
}

function onItemClick() {
    if (props.closeOnClick) isOpen.value = false;
}

onMounted(() => document.addEventListener('click', closeMenu));
onBeforeUnmount(() => document.removeEventListener('click', closeMenu));
</script>

<template>
    <div class="relative inline-block text-left" ref="menuRef">
        <!-- Trigger Slot -->
        <button @click="toggleMenu" class="focus:outline-none">
            <slot name="trigger" :open="isOpen">
                <!-- Default trigger if none is provided -->
                <img src="https://i.pravatar.cc/40" alt="User Avatar" class="h-10 w-10 rounded-full border border-gray-300 object-cover" />
            </slot>
        </button>

        <!-- Dropdown -->
        <transition
            v-if="transition === 'fade-scale'"
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 translate-y-1 scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-1 scale-95"
        >
            <div
                v-if="isOpen"
                class="absolute z-50 mt-2 origin-top rounded-lg bg-white shadow-lg focus:outline-none"
                :class="placement === 'right' ? 'right-0' : 'left-0'"
                :style="{ width: width }"
            >
                <div class="p-2 text-gray-700" @click="onItemClick">
                    <slot />
                </div>
            </div>
        </transition>
    </div>
</template>
