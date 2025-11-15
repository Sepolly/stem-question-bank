<script setup lang="ts">
import HomeController from '@/actions/App/Http/Controllers/HomeController';
import TextLink from '@/components/TextLink.vue';

// import AppLogoIcon from '@/components/AppLogoIcon.vue';
// import useEvent from '@/composables/useEvent';
// import { dashboard } from '@/routes';
// import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="min-h-svh flex items-center justify-center bg-gray-50/70 px-6 md:px-10 py-10"
    >
        <div
            class="w-full max-w-md bg-white p-10 rounded-3xl shadow-xl border border-gray-100"
        >
            <div class="flex flex-col gap-8">

                <!-- Heading -->
                <div class="text-center space-y-2">
                    <h1 class="text-3xl font-semibold tracking-tight text-gray-900">
                        {{ title }}
                    </h1>
                    <p class="text-sm text-gray-500 max-w-sm mx-auto leading-relaxed">
                        {{ description }}
                    </p>
                </div>

                <!-- Form slot -->
                <slot />

                <TextLink :href="HomeController()" class="text-center">go home</TextLink>
            </div>
        </div>
    </div>
</template>

