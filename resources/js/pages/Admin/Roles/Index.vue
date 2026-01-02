<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Hak Akses - LAPJU Online" />

    <AppLayout>
        <div class="p-6">
            <div class="mx-auto max-w-4xl">
                <h1 class="text-xl font-semibold">Hak Akses</h1>
                <p class="text-sm text-neutral-500">Kelola role / hak akses pengguna. (Placeholder)</p>
                <div class="mt-6">
                    <div class="rounded-xl border p-6">Daftar role akan ditampilkan di sini.</div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
