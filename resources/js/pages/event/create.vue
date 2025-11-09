<script setup lang="ts">
import Modal from '@/components/Modal.vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    showCreateEventModal: boolean;
}>();
</script>
<template>
    <AppLayout>
        <Modal :show="showCreateEventModal"> Create Event </Modal>
    </AppLayout>
</template>
