<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({ documents: Array });

const form = useForm({
    title: '',
});

const createDocument = () => {
    form.post(route('documents.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <div class="p-6">
        <form @submit.prevent="createDocument" class="mb-6">
            <input v-model="form.title" type="text" placeholder="Judul Dokumen Baru" class="rounded border-gray-300" required>
            <button type="submit" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded">Buat</button>
        </form>

        <div v-for="doc in documents" :key="doc.id" class="p-4 border-b">
            <a :href="route('documents.show', doc.slug)" class="text-blue-500 font-bold">
                {{ doc.title }}
            </a>
            <p class="text-sm text-gray-500">Dibuat pada: {{ doc.created_at }}</p>
        </div>
    </div>
</template>