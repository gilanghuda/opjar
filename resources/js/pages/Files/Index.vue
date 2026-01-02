<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { index as lecturersIndex } from '@/routes/admin/dosen';
import { index as subjectsIndex } from '@/routes/admin/mapel';
import { index as classesIndex } from '@/routes/admin/kelas';
import { index as agendasIndex } from '@/routes/admin/agenda';
import { ref, onMounted } from 'vue';
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue';
import Input from '@/components/ui/input/Input.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import { showToast } from '@/composables/toast';

const items = ref<any[]>([]);
const meta = ref<any>(null);
const loading = ref(false);
const file = ref<File | null>(null);
const title = ref('');
const errors = ref<Record<string,string>>({});
const subjectsOptions = ref<any[]>([]);
const classesOptions = ref<any[]>([]);
const selectedSubject = ref('');
const selectedClass = ref('');
const editingId = ref<number|null>(null);
const confirmOpen = ref(false);
const pendingDeleteId = ref<number|null>(null);
const pendingDeleteTitle = ref<string|null>(null);

async function deleteFileConfirmed() {
    const id = pendingDeleteId.value;
    pendingDeleteId.value = null;
    confirmOpen.value = false;
    if (!id) return;
    try {
        const res = await fetch(`/api/files/${id}`, { method: 'DELETE' });
        if (res.ok) {
            showToast('File dihapus.', 'success');
            await load();
        } else {
            showToast('Gagal menghapus file.', 'error');
        }
    } catch (e) {
        showToast('Gagal menghapus file.', 'error');
    }
}

async function load(page = 1) {
    loading.value = true;
    const res = await fetch(`/api/files?page=${page}`);
    const data = await res.json();
    items.value = data.data || [];
    meta.value = data.meta ?? null;
    loading.value = false;
}

async function submit() {
    errors.value = {};
    if (!file.value) return errors.value.file = 'Pilih file terlebih dahulu.';
    const fd = new FormData();
    fd.append('title', title.value);
    fd.append('file', file.value);
    if (selectedSubject.value) fd.append('subject_id', selectedSubject.value);
    if (selectedClass.value) fd.append('class_id', selectedClass.value);
    const wasEditing = !!editingId.value;
    const url = wasEditing ? `/api/files/${editingId.value}` : '/api/files';
    const method = wasEditing ? 'POST' : 'POST';
    // Note: many servers accept POST with _method=PUT for file uploads; if API supports PUT with multipart, change method accordingly.
    if (wasEditing) fd.append('_method', 'PUT');

    const res = await fetch(url, { method, body: fd });
    if (res.ok) {
        title.value = '';
        file.value = null;
        selectedSubject.value = '';
        selectedClass.value = '';
        editingId.value = null;
        await load();
        showToast(wasEditing ? 'File berhasil diperbarui.' : 'File berhasil diunggah.', 'success');
    } else if (res.status === 422) {
        const json = await res.json();
        errors.value = json.errors || {};
    } else alert('Gagal upload file.');
}

async function editFile(f: any) {
    // Prepare edit form: we won't allow changing binary via this simple UI
    editingId.value = f.id;
    title.value = f.title || '';
    selectedSubject.value = f.subject_id || '';
    selectedClass.value = f.class_id || '';
    // show notice to user that re-uploading is optional to replace file
}

function askDeleteFile(id: number, title?: string) {
    pendingDeleteId.value = id;
    pendingDeleteTitle.value = title ?? null;
    confirmOpen.value = true;
}

async function loadOptions() {
    try {
        const [sRes, cRes] = await Promise.all([
            fetch('/api/subjects?per_page=200'),
            fetch('/api/classes?per_page=200'),
        ]);
        const [sJson, cJson] = await Promise.all([sRes.json(), cRes.json()]);
        subjectsOptions.value = sJson.data || sJson || [];
        classesOptions.value = cJson.data || cJson || [];
    } catch (e) {
        // ignore
    }
}

onMounted(() => {
    load();
    loadOptions();
});
</script>

<template>
    <Head title="File Pembelajaran" />
    <AppLayout>
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-semibold">File Pembelajaran</h1>
                    <div class="mt-1 flex gap-2 text-sm">
                        <Link :href="lecturersIndex()" class="text-primary">Dosen</Link>
                        <Link :href="subjectsIndex()" class="text-primary">Mata Pelajaran</Link>
                        <Link :href="classesIndex()" class="text-primary">Kelas</Link>
                        <Link :href="agendasIndex()" class="text-primary">Agenda</Link>
                    </div>
                </div>
            </div>

            <Card class="mb-4">
                <template #default>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                        <div>
                            <label class="block text-sm text-muted">Judul</label>
                            <Input v-model="title" />
                            <p v-if="errors.title" class="text-xs text-red-600">{{ errors.title }}</p>
                        </div>
                        <div>
                            <label class="block text-sm text-muted">Mata Pelajaran (opsional)</label>
                            <select v-model="selectedSubject" class="w-full rounded border p-2">
                                <option value="">-- Pilih Mapel --</option>
                                <option v-for="s in subjectsOptions" :key="s.id" :value="s.id">{{ s.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-muted">Kelas (opsional)</label>
                            <select v-model="selectedClass" class="w-full rounded border p-2">
                                <option value="">-- Pilih Kelas --</option>
                                <option v-for="c in classesOptions" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm text-muted">File</label>
                            <input
                                type="file"
                                @change="(event) => {
                                    const target = event.target as HTMLInputElement
                                    if (target.files) {
                                    file = target.files[0]
                                    }
                                }"
                                />
                            <p v-if="errors.file" class="text-xs text-red-600">{{ errors.file }}</p>
                        </div>
                        <div class="md:col-span-3 flex gap-2">
                            <Button variant="default" @click="submit">Upload</Button>
                            <Button @click="() => { title = ''; file = null; selectedSubject=''; selectedClass='' }">Reset</Button>
                        </div>
                    </div>
                </template>
            </Card>

            <Card>
                <template #default>
                    <div v-if="loading">Memuat...</div>
                    <ul v-else class="space-y-2">
                        <li v-for="f in items" :key="f.id" class="p-3 border rounded flex items-start justify-between">
                            <div class="mr-4">
                                <div class="font-semibold">{{ f.title }}</div>
                                <div class="text-sm text-muted">{{ f.original_name }}</div>
                            </div>
                            <div class="flex gap-2">
                                <a :href="`/api/files/${f.id}/download`" class="btn">Download</a>
                                <Button size="sm" variant="ghost" @click.prevent="() => editFile(f)">Edit</Button>
                                <Button size="sm" variant="destructive" @click.prevent="() => askDeleteFile(f.id, f.title)">Hapus</Button>
                            </div>
                        </li>
                    </ul>

                    <ConfirmDialog v-model="confirmOpen" title="Hapus File" :description="pendingDeleteTitle ? `Hapus file: ${pendingDeleteTitle}` : 'Anda yakin ingin menghapus file ini?'" confirmLabel="Hapus" @confirm="deleteFileConfirmed" />

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
