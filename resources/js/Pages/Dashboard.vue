<script setup>
import { useForm, router, usePage } from '@inertiajs/vue3';

const props = defineProps({ documents: Array });

const form = useForm({
    title: '',
});

const createDocument = () => {
    form.post(route('documents.store'), {
        onSuccess: () => form.reset(),
    });
};

const page = usePage();
const currentUserId = page.props.auth.user.id;

const deleteDocument = (slug) => {
    if (confirm('Apakah Anda yakin ingin menghapus dokumen ini beserta seluruh riwayatnya?')) {
        router.delete(route('documents.destroy', slug), {
            onSuccess: () => {
                alert('Dokumen berhasil dihapus.');
            }
        });
    }
};
</script>

<template>
    <div class="p-6 max-w-4xl mx-auto">
        <form @submit.prevent="createDocument" class="mb-6 flex gap-2">
            <input v-model="form.title" type="text" placeholder="Judul Dokumen Baru" class="rounded border-gray-300 flex-1" required>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Buat</button>
        </form>

        <div class="bg-white rounded-lg shadow divide-y">
            <div v-for="doc in documents" :key="doc.id" class="p-4 flex justify-between items-center hover:bg-gray-50 transition">
                <div>
                    <a :href="route('documents.show', doc.slug)" class="text-blue-500 font-bold hover:underline text-lg">
                        {{ doc.title }}
                    </a>
                    <p class="text-sm text-gray-500 mt-1">
                        Pemilik: <span class="font-medium text-gray-700">{{ doc.user?.name || 'Anonim' }}</span> | 
                        Dibuat: {{ new Date(doc.created_at).toLocaleDateString('id-ID') }}
                    </p>
                </div>

                <div v-if="doc.user_id === currentUserId">
                    <button 
                        @click="deleteDocument(doc.slug)" 
                        class="px-3 py-1 bg-red-100 hover:bg-red-200 text-red-600 rounded text-sm font-semibold transition">
                        Hapus
                    </button>
                </div>
            </div>

            <div v-if="documents.length === 0" class="p-8 text-center text-gray-500 italic">
                Belum ada dokumen yang dibuat.
            </div>
        </div>
    </div>
</template>