<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import { router } from '@inertiajs/vue3';


const showHistory = ref(false);

const restoreVersion = (revisionId) => {
    if (confirm('Apakah Anda yakin ingin mengembalikan ke versi ini? Konten saat ini akan diganti.')) {
        router.post(route('documents.restore', [props.document.slug, revisionId]), {}, {
            onSuccess: () => {
                // Update isi editor secara manual setelah restore
                editor.value.commands.setContent(props.document.content);
                showHistory.value = false;
            }
        });
    }
};

const props = defineProps({
    document: Object,
});

const saveDocument = (content) => {
    // Kirim data ke Laravel menggunakan router.put atau axios
    axios.put(route('documents.update', props.document.slug), {
        content: content
    }).then(() => {
        console.log('Dokumen berhasil di-autosave');
    });
};

const activeUsers = ref([]);
const isTyping = ref(false);
let saveTimeout = null;

// Inisialisasi Tiptap Editor
const editor = useEditor({
    extensions: [StarterKit],
    content: props.document.content || '<p>Mulai mengetik di sini...</p>',
    onUpdate: ({ editor }) => {
        const content = editor.getHTML();
        
        // 1. Broadcast ketikan ke user lain via Laravel Echo (Reverb)
        window.Echo?.join(`document.${props.document.id}`)
            .whisper('typing', {
                content: content,
            });
            
        // 2. Logika Auto-save (Debounce 3 detik)
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => {
            saveDocument(content);
        }, 3000);
    }
});

onMounted(() => {
    // Bergabung ke Presence Channel Reverb
    if (window.Echo){
        window.Echo.join(`document.${props.document.id}`)
        .here((users) => {
            activeUsers.value = users; // Siapa saja yang sedang buka dokumen ini?
        })
        .joining((user) => {
            activeUsers.value.push(user); // Ada user baru masuk
        })
        .leaving((user) => {
            activeUsers.value = activeUsers.value.filter(u => u.id !== user.id); // User keluar
        })
        .listenForWhisper('typing', (e) => {
            isTyping.value = true;

            setTimeout(() => {
                isTyping.value = false;
            }, 2000);
            // Ketika menerima ketikan dari user lain, update isi editor
            if (editor.value && editor.value.getHTML() !== e.content) {
                // Simpan posisi kursor
                const { from, to } = editor.value.state.selection;
                
                // Update konten
                editor.value.commands.setContent(e.content, false);
                
                // Kembalikan posisi kursor agar tidak loncat ke awal
                editor.value.commands.setTextSelection({ from, to });
            }
        });
    } else {
        console.error("Laravel Echo gagal dimuat. Periksa konfigurasi WebSocket.");
        
    }
});

onBeforeUnmount(() => {
    if (editor.value) {
        editor.value.destroy();
    }
    window.Echo.leave(`document.${props.document.id}`);
});

const deleteAllHistory = () => {
    if (confirm('Apakah Anda yakin ingin menghapus SEMUA riwayat versi? Tindakan ini tidak dapat dibatalkan.')) {
        router.delete(route('documents.history.destroy', props.document.slug), {
            onSuccess: () => {
                alert('Semua riwayat versi telah dibersihkan.');
            }
        });
    }
}
</script>

<template>
    <div class="max-w-6xl mx-auto py-10 px-4 flex gap-6">
        
        <div class="flex-1">
            
            <div class="flex justify-between items-center mb-6 border-b pb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ document.title }}</h1>
                    <p v-if="isTyping" class="text-xs text-blue-500 animate-pulse">
                        Seseorang sedang mengetik...
                    </p>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2 bg-white-100 px-3 py-1.5 rounded-full border">
                        <span class="flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        </span>
                        <span class="text-xs font-semibold text-gray-600">
                            {{ activeUsers.length }} Online:
                        </span>
                        <div class="flex -space-x-2 overflow-hidden">
                            <div 
                                v-for="user in activeUsers" 
                                :key="user.id"
                                class="inline-block h-7 w-7 rounded-full bg-blue-600 text-white border-2 border-white flex items-center justify-center text-xs font-bold uppercase shadow-sm"
                                :title="user.name"
                            >
                                {{ user.name.charAt(0) }}
                            </div>
                        </div>
                    </div>

                    <button 
                        @click="showHistory = !showHistory" 
                        class="text-sm font-medium bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg shadow-sm transition"
                    >
                        {{ showHistory ? 'Tutup History' : 'Lihat History' }}
                    </button>
                </div>
            </div>
            
            <div class="prose max-w-none border border-gray-200 p-2 min-h-[500px] bg-white rounded-xl shadow-sm">
                <editor-content :editor="editor" />
            </div>
        </div>

        <div v-if="showHistory" class="w-64 border-l pl-4 bg-gray-50 p-4 rounded-lg shadow-sm">
            <div class="flex justify-between items-center mb-4 border-b pb-2">
                <h2 class="font-bold text-gray-700">Riwayat Versi</h2>
                <button 
                    v-if="document.revisions && document.revisions.length > 0"
                    @click="deleteAllHistory" 
                    class="text-[10px] bg-red-100 hover:bg-red-200 text-red-600 px-2 py-1 rounded font-semibold transition"
                >
                    Hapus Semua
                </button>
            </div>
            
            <div v-if="!document.revisions || document.revisions.length === 0" class="text-sm text-gray-500 italic">
                Belum ada riwayat perubahan.
            </div>

            <div v-else class="space-y-4 overflow-y-auto max-h-[500px]">
                <div v-for="rev in document.revisions" :key="rev.id" class="p-2 border rounded bg-white text-xs shadow-sm">
                    <p class="font-semibold text-blue-600">{{ rev.user?.name || 'Anonim' }}</p>
                    <p class="text-gray-500">{{ new Date(rev.created_at).toLocaleString('id-ID') }}</p>
                    <button @click="restoreVersion(rev.id)" class="mt-2 text-blue-500 hover:text-blue-700 font-bold underline block">
                        Restore Versi Ini
                    </button>
                </div>
            </div>
        </div>

    </div>
</template>

<style>
/* Styling dasar Tiptap agar terlihat seperti kertas dokumen */
.ProseMirror {
    outline: none;
    min-height: 500px;
}
.ProseMirror p {
    margin-bottom: 1em;
}
</style>