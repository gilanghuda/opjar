<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { index as lecturersIndex } from '@/routes/admin/dosen';
import { index as classesIndex } from '@/routes/admin/kelas';
import { index as agendasIndex } from '@/routes/admin/agenda';
import { index as filesIndex } from '@/routes/admin/files';
import { ref, onMounted } from 'vue';
import Input from '@/components/ui/input/Input.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';

const items = ref<any[]>([]);
const meta = ref<any>(null);
const loading = ref(false);
const form = ref({ code: '', name: '', description: '' });
const showForm = ref(false);
const errors = ref<Record<string,string>>({});

async function load(page = 1) {
    loading.value = true;
    const res = await fetch(`/api/subjects?page=${page}`);
    const data = await res.json();
    items.value = data.data || [];
    meta.value = data.meta ?? null;
    loading.value = false;
}

async function submit() {
    errors.value = {};
    const res = await fetch('/api/subjects', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(form.value) });
    if (res.ok) {
        form.value = { code: '', name: '', description: '' };
        showForm.value = false;
        await load();
    } else if (res.status === 422) {
        const json = await res.json();
        errors.value = json.errors || {};
    } else alert('Gagal menyimpan data.');
}

onMounted(() => load());
</script>

<template>
    <Head title="Mata Pelajaran" />
    <AppLayout>
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-semibold">Mata Pelajaran</h1>
                    <div class="mt-1 flex gap-2 text-sm">
                        <Link :href="lecturersIndex()" class="text-primary">Dosen</Link>
                        <Link :href="classesIndex()" class="text-primary">Kelas</Link>
                        <Link :href="agendasIndex()" class="text-primary">Agenda</Link>
                        <Link :href="filesIndex()" class="text-primary">File</Link>
                    </div>
                </div>
                <Button @click="showForm = !showForm">{{ showForm ? 'Tutup' : 'Tambah Mapel' }}</Button>
            </div>

            <Card class="mb-4">
                <template #default>
                    <div v-if="showForm" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm text-muted">Kode</label>
                            <Input v-model="form.code" />
                            <p v-if="errors.code" class="text-xs text-red-600">{{ errors.code }}</p>
                        </div>
                        <div>
                            <label class="block text-sm text-muted">Nama</label>
                            <Input v-model="form.name" />
                            <p v-if="errors.name" class="text-xs text-red-600">{{ errors.name }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm text-muted">Deskripsi</label>
                            <Input v-model="form.description" />
                        </div>
                        <div class="md:col-span-2 flex gap-2">
                            <Button variant="primary" @click="submit">Simpan</Button>
                            <Button @click="showForm = false">Batal</Button>
                        </div>
                    </div>
                    <div v-else>
                        <p class="text-sm text-muted">Klik tombol untuk menambah mata pelajaran baru.</p>
                    </div>
                </template>
            </Card>

            <Card>
                <template #default>
                    <div v-if="loading">Memuat...</div>
                    <ul v-else class="space-y-2">
                        <li v-for="m in items" :key="m.id" class="p-3 border rounded">
                            <div class="font-semibold">{{ m.name }} <span class="text-sm text-muted">({{ m.code }})</span></div>
                            <div class="text-sm">{{ m.description }}</div>
                        </li>
                    </ul>

                    <div v-if="meta" class="mt-4 flex items-center justify-between">
                        <div class="text-sm text-muted">Halaman {{ meta.current_page }} dari {{ meta.last_page }}</div>
                        <div class="flex gap-2">
                            <Button :disabled="meta.current_page <= 1" @click="() => load(meta.current_page - 1)">Sebelumnya</Button>
                            <Button :disabled="meta.current_page >= meta.last_page" @click="() => load(meta.current_page + 1)">Selanjutnya</Button>
                        </div>
                    </div>
                </template>
            </Card>
        </div>
    </AppLayout>
</template>
