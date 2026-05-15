<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import { router } from '@inertiajs/vue3';


// const showHistory = ref(false);

// const restoreVersion = (revisionId) => {
//     if (confirm('Apakah Anda yakin ingin mengembalikan ke versi ini? Konten saat ini akan diganti.')) {
//         router.post(route('documents.restore', [props.document.slug, revisionId]), {}, {
//             onSuccess: () => {
//                 // Update isi editor secara manual setelah restore
//                 editor.value.commands.setContent(props.document.content);
//                 showHistory.value = false;
//             }
//         });
//     }
// };

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
            // Ketika menerima ketikan dari user lain, update isi editor kita
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
        console.error("Laravel Echo gagal dimuat. Periksa konfigurasi WebSocket Anda.");
        
    }
});

onBeforeUnmount(() => {
    if (editor.value) {
        editor.value.destroy();
    }
    window.Echo.leave(`document.${props.document.id}`);
});
</script>

<template>
    <div class="max-w-4xl mx-auto py-10 px-4">
        <div class="mb-4 flex justify-between items-center border-b pb-4">
            <h1 class="text-2xl font-bold">{{ document.title }}</h1>
            <p v-if="isTyping" class="text-xs text-blue-500 animate-pulse">
                Seseorang sedang mengetik...
            </p>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Online:</span>
                <div class="flex -space-x-2">
                    <div v-for="user in activeUsers" :key="user.id" 
                        class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs border-2 border-white"
                        :title="user.name">
                        {{ user.name.charAt(0) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="prose max-w-none border p-6 min-h-[500px] rounded-lg shadow-sm bg-white focus:outline-none">
            <editor-content :editor="editor" />
        </div>
    </div>
</template>

<style>
/* Styling dasar untuk Tiptap agar terlihat seperti kertas dokumen */
.ProseMirror {
    outline: none;
    min-height: 500px;
}
.ProseMirror p {
    margin-bottom: 1em;
}
</style>