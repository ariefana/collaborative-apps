<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Selamat Datang - ColabDoc" />

    <div class="min-h-screen bg-gray-50 flex flex-col justify-between font-sans text-gray-900">
        
        <header class="w-full max-w-5xl mx-auto px-6 py-6 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="font-bold text-lg tracking-tight text-gray-800">ColabDoc</span>
            </div>

            <div v-if="canLogin" class="text-sm font-medium">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="text-blue-600 hover:underline">
                    Buka Dashboard &rarr;
                </Link>
                <template v-else>
                    <Link :href="route('login')" class="text-gray-600 hover:text-gray-900 mr-4">
                        Masuk
                    </Link>
                    <Link v-if="canRegister" :href="route('register')" class="text-blue-600 hover:underline">
                        Daftar Akun
                    </Link>
                </template>
            </div>
        </header>

        <main class="max-w-3xl mx-auto px-6 py-12 text-center my-auto">
            <h1 class="text-4xl font-bold text-gray-950 tracking-tight mb-4">
                Platform Editor Dokumen Kolaboratif
            </h1>
            
            <p class="text-base text-gray-500 max-w-xl mx-auto mb-8 leading-relaxed">
                Aplikasi pengerjaan dokumen bersama secara real-time untuk memenuhi tugas. Masuk dengan akun Anda atau buat akun baru untuk mulai menulis bersama tim.
            </p>

            <div class="flex justify-center items-center gap-3">
                <template v-if="$page.props.auth.user">
                    <Link :href="route('dashboard')" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition">
                        Masuk ke Dashboard
                    </Link>
                </template>
                <template v-else>
                    <Link :href="route('login')" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition">
                        Mulai Menulis
                    </Link>
                    <Link :href="route('register')" class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-lg shadow-sm transition">
                        Belum Punya Akun?
                    </Link>
                </template>
            </div>

            <div class="mt-12 bg-white border border-gray-200 rounded-xl h-48 flex items-center justify-center p-6 shadow-sm max-w-2xl mx-auto">
                <div class="text-gray-400 text-sm">
                    <p class="font-medium text-gray-500 mb-1">Fitur Utama Terintegrasi:</p>
                    <p>Real-time WebSockets &bull; Riwayat Revisi &bull; Proteksi Penghapusan</p>
                </div>
            </div>
        </main>

        <footer class="w-full text-center py-6 border-t border-gray-100 text-xs text-gray-400">
            Proyek Tugas Individu &bull; Built with Laravel & Vue
        </footer>

    </div>
</template>