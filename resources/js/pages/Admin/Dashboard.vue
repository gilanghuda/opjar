<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Card } from '@/components/ui/card';
import { User, Users, Book, Calendar } from 'lucide-vue-next';
import { index as lecturersIndex } from '@/routes/admin/dosen';
import { index as classesIndex } from '@/routes/admin/kelas';
import { index as subjectsIndex } from '@/routes/admin/mapel';
import { index as agendasIndex } from '@/routes/admin/agenda';

const cards = [
    {
        title: 'Dosen',
        description: 'Kelola data dosen dan profil mereka.',
        icon: User,
        href: lecturersIndex(),
        color: 'from-blue-500 to-cyan-500',
        accent: 'bg-blue-600/10',
    },
    {
        title: 'Kelas',
        description: 'Atur kelas, rombongan, dan jadwal.',
        icon: Users,
        href: classesIndex(),
        color: 'from-emerald-500 to-lime-500',
        accent: 'bg-emerald-600/10',
    },
    {
        title: 'Mapel',
        description: 'Kelola mata pelajaran dan silabus.',
        icon: Book,
        href: subjectsIndex(),
        color: 'from-sky-500 to-indigo-500',
        accent: 'bg-sky-600/10',
    },
    {
        title: 'Agenda',
        description: 'Buat dan lihat agenda perkuliahan.',
        icon: Calendar,
        href: agendasIndex(),
        color: 'from-amber-500 to-orange-500',
        accent: 'bg-amber-600/10',
    },
];
</script>

<template>
    <Head title="Dashboard - LAPJU Online" />

    <AppLayout>
        <div class="p-6">
            <div class="mx-auto max-w-7xl">
                <header class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-semibold">LAPJU Online</h1>
                        <p class="text-sm text-neutral-500">Sistem Informasi Akademik</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-sm text-neutral-600">Hai, <strong class="font-medium">{{ $page.props.auth.user.name ?? 'User' }}</strong></div>
                    </div>
                </header>

                <section>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <Card v-for="card in cards" :key="card.title" class="overflow-hidden transition-transform hover:-translate-y-1 hover:shadow-lg">
                            <div class="flex items-start gap-4 px-4 py-6">
                                <div :class="['rounded-lg p-3 text-white shadow', card.color]" style="background: linear-gradient(135deg, var(--tw-gradient-from), var(--tw-gradient-to));">
                                    <component :is="card.icon" class="h-6 w-6" />
                                </div>

                                <div class="flex flex-1 flex-col">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold">{{ card.title }}</h3>
                                    </div>
                                    <p class="text-sm text-neutral-500 mt-1">{{ card.description }}</p>
                                    <div class="mt-3">
                                        <Link :href="card.href" class="inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline">Lihat &rarr;</Link>
                                    </div>
                                </div>
                            </div>
                        </Card>
                    </div>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
