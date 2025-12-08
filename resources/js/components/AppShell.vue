<script setup lang="ts">
import { SidebarProvider } from '@/components/ui/sidebar';
import { Toaster } from '@/components/ui/sonner';
import { useAppearance } from '@/composables/useAppearance';
import { usePage } from '@inertiajs/vue3';

const { appearance, updateAppearance } = useAppearance();

interface Props {
    variant?: 'header' | 'sidebar';
}

defineProps<Props>();

const isOpen = usePage().props.sidebarOpen;
</script>

<template>
    <div v-if="variant === 'header'" class="flex min-h-screen w-full flex-col">
        <slot />
        <Toaster position="top-right" />
    </div>
    <SidebarProvider v-else :default-open="isOpen">
        <slot />
        <Toaster position="top-right" :theme="appearance" />
    </SidebarProvider>
</template>
