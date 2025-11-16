<script setup lang='ts'>
    import EventForm from '@/components/EventForm.vue';
    import AuthBase from '@/layouts/AuthLayout.vue'
</script>
<template>
    <AuthBase title="Create Event" description="Create an event">
        <EventForm class="relative flex w-full max-w-md flex-col overflow-auto rounded-lg p-6" />
    </AuthBase>
</template>
