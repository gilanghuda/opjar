<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard as adminDashboard } from '@/routes/admin';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, User, Book, Users, FileText, Calendar, Key, Shield } from 'lucide-vue-next';
import { index as lecturersIndex } from '@/routes/admin/dosen';
import { index as subjectsIndex } from '@/routes/admin/mapel';
import { index as classesIndex } from '@/routes/admin/kelas';
import { index as agendasIndex } from '@/routes/admin/agenda';
import { index as filesIndex } from '@/routes/admin/files';
import { index as wewenangIndex } from '@/routes/admin/wewenang';
import { index as rolesIndex } from '@/routes/admin/roles';
import AppLogo from './AppLogo.vue';



const mainNavItems: NavItem[] = [
    {
        title: 'Home',
        href: adminDashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Dosen',
        href: lecturersIndex(),
        icon: User,
    },
    {
        title: 'Mapel',
        href: subjectsIndex(),
        icon: Book,
    },
    {
        title: 'Kelas',
        href: classesIndex(),
        icon: Users,
    },
    {
        title: 'Agenda',
        href: agendasIndex(),
        icon: Calendar,
    },
    {
        title: 'File Pembelajaran',
        href: filesIndex(),
        icon: FileText,
    },
    {
        title: 'Wewenang',
        href: wewenangIndex(),
        icon: Key,
    },
    {
        title: 'Hak Akses',
        href: rolesIndex(),
        icon: Shield,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];

const page = usePage();
const auth = computed(() => page.props.auth);

const visibleMainNavItems = computed(() => {
    console.log('AUTH:', auth.value);
console.log('ROLES:', auth.value?.user?.roles);

    const roles: string[] = auth.value?.user?.roles?.map((r: any) => r.name) || [];
    if (roles.includes('admin')) return mainNavItems;
    if (roles.includes('lecturer')) {
        return mainNavItems.filter((i) => ['Home', 'Agenda', 'File Pembelajaran'].includes(i.title));
    }
    return mainNavItems.filter((i) => i.title === 'Home');
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="adminDashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
                <NavMain :items="visibleMainNavItems" />
            </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot/>
</template>
