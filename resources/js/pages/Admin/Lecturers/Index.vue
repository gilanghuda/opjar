<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { index as subjectsIndex } from '@/routes/admin/mapel';
import { index as classesIndex } from '@/routes/admin/kelas';
import { index as agendasIndex } from '@/routes/admin/agenda';
import { index as filesIndex } from '@/routes/admin/files';
import { ref, onMounted } from 'vue';
import Input from '@/components/ui/input/Input.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';

const lecturers = ref<any[]>([]);
const meta = ref<any>(null);
const loading = ref(false);
const form = ref({ user_id: '', nidn: '', phone: '', bio: '' });
const showForm = ref(false);
const errors = ref<Record<string,string>>({});

async function load(page = 1) {
    loading.value = true;
    const res = await fetch(`/api/lecturers?page=${page}`);
    const data = await res.json();
    lecturers.value = data.data || [];
    meta.value = data.meta ?? null;
    loading.value = false;
}

async function submit() {
    errors.value = {};
    const fd = new FormData();
    fd.append('user_id', form.value.user_id ?? '');
    fd.append('nidn', form.value.nidn);
    fd.append('phone', form.value.phone);
    fd.append('bio', form.value.bio);

    const res = await fetch('/api/lecturers', { method: 'POST', body: fd });
    if (res.ok) {
        form.value = { user_id: '', nidn: '', phone: '', bio: '' };
        showForm.value = false;
        await load();
    } else if (res.status === 422) {
        const json = await res.json();
        errors.value = json.errors || {};
    } else {
        alert('Terjadi kesalahan saat menyimpan.');
    }
}

onMounted(() => load());
</script>

<template>
    <Head title="Manajemen Dosen" />

    <AppLayout>
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-semibold">Daftar Dosen</h1>
                    <div class="mt-1 flex gap-2 text-sm">
                        <Link :href="subjectsIndex()" class="text-primary">Mata Pelajaran</Link>
                        <Link :href="classesIndex()" class="text-primary">Kelas</Link>
                        <Link :href="agendasIndex()" class="text-primary">Agenda</Link>
                        <Link :href="filesIndex()" class="text-primary">File</Link>
                    </div>
                </div>
                <Button @click="showForm = !showForm">{{ showForm ? 'Tutup' : 'Tambah Dosen' }}</Button>
            </div>

            <Card class="mb-4">
                <template #default>
                    <div v-if="showForm" class="space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm text-muted">NIDN</label>
                                <Input v-model="form.nidn" />
                                <p v-if="errors.nidn" class="text-xs text-red-600">{{ errors.nidn }}</p>
                            </div>
                            <div>
                                <label class="block text-sm text-muted">Telepon</label>
                                <Input v-model="form.phone" />
                                <p v-if="errors.phone" class="text-xs text-red-600">{{ errors.phone }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm text-muted">Bio</label>
                            <Input v-model="form.bio" />
                        </div>

                        <div class="flex items-center gap-2">
                            <Button variant="default" @click="submit">Simpan</Button>
                            <Button @click="showForm = false">Batal</Button>
                        </div>
                    </div>
                    <div v-else>
                        <p class="text-sm text-muted">Klik "Tambah Dosen" untuk menambahkan data dosen baru.</p>
                    </div>
                </template>
            </Card>

            <Card>
                <template #default>
                    <div v-if="loading">Memuat...</div>
                    <table v-else class="min-w-full table-auto">
                        <thead>
                            <tr>
                                <th class="px-2 py-1 text-left">ID</th>
                                <th class="px-2 py-1 text-left">Nama</th>
                                <th class="px-2 py-1 text-left">NIDN</th>
                                <th class="px-2 py-1 text-left">Telepon</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="l in lecturers" :key="l.id" class="border-t">
                                <td class="px-2 py-2">{{ l.id }}</td>
                                <td class="px-2 py-2">{{ l.user?.name ?? '-' }}</td>
                                <td class="px-2 py-2">{{ l.nidn }}</td>
                                <td class="px-2 py-2">{{ l.phone }}</td>
                            </tr>
                        </tbody>
                    </table>

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
