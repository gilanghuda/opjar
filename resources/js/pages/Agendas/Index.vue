<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { index as lecturersIndex } from '@/routes/admin/dosen';
import { index as subjectsIndex } from '@/routes/admin/mapel';
import { index as classesIndex } from '@/routes/admin/kelas';
import { index as filesIndex } from '@/routes/admin/files';
import { ref, onMounted } from 'vue';
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue';
import { showToast } from '@/composables/toast';
import Input from '@/components/ui/input/Input.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';

const items = ref<any[]>([]);
const meta = ref<any>(null);
const loading = ref(false);
const form = ref({ title: '', date: '', start_time: '', end_time: '', lecturer_id: '', subject_id: '', class_id: '', material: '' });
const showForm = ref(false);
const errors = ref<Record<string,string>>({});
const lecturersOptions = ref<any[]>([]);
const subjectsOptions = ref<any[]>([]);
const classesOptions = ref<any[]>([]);

async function loadOptions() {
    try {
        const [lRes, sRes, cRes] = await Promise.all([
            fetch('/api/lecturers?per_page=200'),
            fetch('/api/subjects?per_page=200'),
            fetch('/api/classes?per_page=200'),
        ]);
        const [lJson, sJson, cJson] = await Promise.all([lRes.json(), sRes.json(), cRes.json()]);
        lecturersOptions.value = lJson.data || lJson || [];
        subjectsOptions.value = sJson.data || sJson || [];
        classesOptions.value = cJson.data || cJson || [];
    } catch (e) {
        // silent - options are optional
    }
}

async function load(page = 1) {
    loading.value = true;
    const res = await fetch(`/api/agendas?page=${page}`);
    const data = await res.json();
    items.value = data.data || [];
    meta.value = data.meta ?? null;
    loading.value = false;
}

async function submit() {
    errors.value = {};
    const fd = new FormData();
    Object.entries(form.value).forEach(([k,v]) => fd.append(k, v ?? ''));
    const wasEditing = !!editingId.value;
    const url = wasEditing ? `/api/agendas/${editingId.value}` : '/api/agendas';
    const method = wasEditing ? 'PUT' : 'POST';

    const res = await fetch(url, { method, body: fd });
    if (res.ok) {
        form.value = { title: '', date: '', start_time: '', end_time: '', lecturer_id: '', subject_id: '', class_id: '', material: '' };
        editingId.value = null;
        showForm.value = false;
        await load();
        showToast(wasEditing ? 'Agenda berhasil diperbarui.' : 'Agenda berhasil dibuat.', 'success');
    } else if (res.status === 422) {
        const json = await res.json();
        errors.value = json.errors || {};
    } else alert('Gagal menyimpan agenda.');
}

const editingId = ref<number|null>(null);

async function editAgenda(a: any) {
    editingId.value = a.id;
    form.value = {
        title: a.title || '',
        date: a.date || '',
        start_time: a.start_time || '',
        end_time: a.end_time || '',
        lecturer_id: a.lecturer_id || '',
        subject_id: a.subject_id || '',
        class_id: a.class_id || '',
        material: a.material || '',
    };
    showForm.value = true;
}

const confirmOpen = ref(false);
const pendingDeleteId = ref<number|null>(null);
const pendingDeleteTitle = ref<string | null>(null);

function askDeleteAgenda(id: number, title?: string) {
    pendingDeleteId.value = id;
    pendingDeleteTitle.value = title ?? null;
    confirmOpen.value = true;
}

async function deleteAgendaConfirmed() {
    const id = pendingDeleteId.value;
    pendingDeleteId.value = null;
    confirmOpen.value = false;
    if (!id) return;
    try {
        const res = await fetch(`/api/agendas/${id}`, { method: 'DELETE' });
        if (res.ok) {
            showToast('Agenda dihapus.', 'success');
            await load();
        } else {
            showToast('Gagal menghapus agenda.', 'error');
        }
    } catch (e) {
        showToast('Gagal menghapus agenda.', 'error');
    }
}

onMounted(() => {
    load();
    loadOptions();
});
</script>

<template>
    <Head title="Agenda Pengajaran" />
    <AppLayout>
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-semibold">Agenda Pengajaran</h1>
                    <span v-if="editingId" class="inline-block bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded text-sm">Edit Mode</span>
                    <div class="mt-1 flex gap-2 text-sm">
                        <Link :href="lecturersIndex()" class="text-primary">Dosen</Link>
                        <Link :href="subjectsIndex()" class="text-primary">Mata Pelajaran</Link>
                        <Link :href="classesIndex()" class="text-primary">Kelas</Link>
                        <Link :href="filesIndex()" class="text-primary">File</Link>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Button v-if="editingId" @click="() => { editingId = null; showForm = false; form = { title: '', date: '', start_time: '', end_time: '', lecturer_id: '', subject_id: '', class_id: '', material: '' } }">Batal Edit</Button>
                    <Button @click="showForm = !showForm">{{ showForm ? 'Tutup' : (editingId ? 'Ubah Agenda' : 'Tambah Agenda') }}</Button>
                </div>
            </div>

            <Card class="mb-4">
                <template #default>
                    <div v-if="showForm" class="space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm text-muted">Judul</label>
                                <Input v-model="form.title" />
                                <p v-if="errors.title" class="text-xs text-red-600">{{ errors.title }}</p>
                            </div>
                            <div>
                                <label class="block text-sm text-muted">Tanggal</label>
                                <Input v-model="form.date" type="date" />
                                <p v-if="errors.date" class="text-xs text-red-600">{{ errors.date }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm text-muted">Mulai</label>
                                <Input v-model="form.start_time" type="time" />
                            </div>
                            <div>
                                <label class="block text-sm text-muted">Selesai</label>
                                <Input v-model="form.end_time" type="time" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-sm text-muted">Dosen</label>
                                <select v-model="form.lecturer_id" class="w-full rounded border p-2">
                                    <option value="">-- Pilih Dosen --</option>
                                    <option v-for="l in lecturersOptions" :key="l.id" :value="l.id">{{ l.user?.name ?? l.id }}</option>
                                </select>
                                <p v-if="errors.lecturer_id" class="text-xs text-red-600">{{ errors.lecturer_id }}</p>
                            </div>
                            <div>
                                <label class="block text-sm text-muted">Mata Pelajaran</label>
                                <select v-model="form.subject_id" class="w-full rounded border p-2">
                                    <option value="">-- Pilih Mapel --</option>
                                    <option v-for="s in subjectsOptions" :key="s.id" :value="s.id">{{ s.name }}</option>
                                </select>
                                <p v-if="errors.subject_id" class="text-xs text-red-600">{{ errors.subject_id }}</p>
                            </div>
                            <div>
                                <label class="block text-sm text-muted">Kelas</label>
                                <select v-model="form.class_id" class="w-full rounded border p-2">
                                    <option value="">-- Pilih Kelas --</option>
                                    <option v-for="c in classesOptions" :key="c.id" :value="c.id">{{ c.name }}</option>
                                </select>
                                <p v-if="errors.class_id" class="text-xs text-red-600">{{ errors.class_id }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm text-muted">Materi</label>
                            <Input v-model="form.material" />
                        </div>

                        <div class="flex gap-2">
                            <Button variant="default" @click="submit">Simpan</Button>
                            <Button @click="showForm = false">Batal</Button>
                        </div>
                    </div>
                    <div v-else>
                        <p class="text-sm text-muted">Klik tombol untuk menambahkan agenda baru atau pilih agenda di daftar.</p>
                    </div>
                </template>
            </Card>

            <Card>
                <template #default>
                    <div v-if="loading">Memuat...</div>
                    <ul v-else class="space-y-2">
                        <li v-for="a in items" :key="a.id" class="p-3 border rounded flex items-start justify-between">
                            <div class="mr-4">
                                <div class="font-semibold">{{ a.title || 'Agenda #' + a.id }} <span class="text-sm text-muted">{{ a.date }}</span></div>
                                <div class="text-sm">{{ a.material }}</div>
                            </div>
                            <div class="flex gap-2">
                                <Button size="sm" variant="ghost" @click.prevent="() => editAgenda(a)">Edit</Button>
                                <Button size="sm" variant="destructive" @click.prevent="() => askDeleteAgenda(a.id, a.title)">Hapus</Button>
                            </div>
                        </li>
                    </ul>

                    <ConfirmDialog v-model="confirmOpen" title="Hapus Agenda" :description="pendingDeleteTitle ? `Hapus agenda: ${pendingDeleteTitle}` : 'Anda yakin ingin menghapus agenda ini?'" confirmLabel="Hapus" @confirm="deleteAgendaConfirmed" />

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
