<script setup lang="ts">
import EventController from '@/actions/App/Http/Controllers/EventController';
import { useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import InputError from './InputError.vue';

const form = useForm({
    name: '',
    start_date: '',
    end_date: '',
});

const saveEvent = () => {
    form.post(EventController.store.url(), {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>
<template>
    <section>
        <form @submit.prevent="saveEvent" class="mt-4 space-y-4 text-gray-700">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Event Name</label>
                <input
                    v-model="form.name"
                    placeholder="STEM Competition"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                />
                <InputError :message="form.errors.name" />
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Start Date</label>
                <input
                    v-model="form.start_date"
                    type="date"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                />
                <InputError :message="form.errors.start_date" />
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">End Date</label>
                <input
                    v-model="form.end_date"
                    type="date"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                />
                <InputError :message="form.errors.end_date" />
            </div>
            <button
                type="submit"
                class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
                :disabled="form.processing"
                :class="{ 'opacity-50': form.processing }"
            >
                <LoaderCircle v-if="form.processing" class="size-5 animate-spin" />
                Create Event
            </button>
        </form>
    </section>
</template>
