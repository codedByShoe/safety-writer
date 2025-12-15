<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { Link, usePage } from '@inertiajs/vue3';
import { Coins, LayoutDashboard, ShieldCheck } from 'lucide-vue-next';
import { computed } from 'vue';

const page = usePage<{
    auth: {
        user: {
            is_admin: boolean;
        };
    };
}>();

const isAdmin = computed(() => page.props.auth.user?.is_admin || false);
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel class="flex items-center gap-2">
            <LayoutDashboard class="size-4" />
            <span class="group-data-[collapsible=icon]:hidden">Navigation</span>
        </SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem>
                <SidebarMenuButton as-child>
                    <Link :href="dashboard()" prefetch class="w-full">
                        <LayoutDashboard class="size-4" />
                        <span>Dashboard</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
            <SidebarMenuItem>
                <SidebarMenuButton as-child>
                    <Link href="/checkout" prefetch class="w-full">
                        <Coins class="size-4" />
                        <span>Buy Credits</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
            <SidebarMenuItem v-if="isAdmin">
                <SidebarMenuButton as-child>
                    <Link href="/admin/dashboard" prefetch class="w-full">
                        <ShieldCheck class="size-4" />
                        <span>Admin Dashboard</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
