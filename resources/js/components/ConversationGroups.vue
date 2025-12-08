<script setup lang="ts">
import { show as observationShow } from '@/actions/App/Http/Controllers/ObservationController';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { Link, usePage } from '@inertiajs/vue3';
import { ClipboardList } from 'lucide-vue-next';
import { computed } from 'vue';

interface Observation {
    id: string;
    title: string;
    status: 'draft' | 'finalized';
    timestamp: string;
}

const page = usePage<{
    recentObservations: Observation[];
}>();

const observations = computed(() => page.props.recentObservations || []);
</script>

<template>
    <!-- Observations Group -->
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel class="flex items-center gap-2">
            <ClipboardList class="size-4" />
            <span class="group-data-[collapsible=icon]:hidden"
                >Observations</span
            >
        </SidebarGroupLabel>
        <SidebarMenu class="group-data-[collapsible=icon]:hidden">
            <SidebarMenuItem v-for="obs in observations" :key="obs.id">
                <SidebarMenuButton as-child>
                    <Link
                        :href="observationShow.url(obs.id)"
                        class="w-full"
                        prefetch
                    >
                        <div class="flex w-full items-center gap-2">
                            <div
                                :class="[
                                    'size-2 rounded-full flex-shrink-0',
                                    obs.status === 'finalized'
                                        ? 'bg-green-500'
                                        : 'bg-gray-400 dark:bg-gray-600',
                                ]"
                            />
                            <span class="truncate text-sm font-medium">{{
                                obs.title
                            }}</span>
                        </div>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
        <!-- Collapsed state: show just icon with tooltip -->
        <SidebarMenu class="hidden group-data-[collapsible=icon]:flex">
            <SidebarMenuItem>
                <TooltipProvider :delay-duration="0">
                    <Tooltip>
                        <TooltipTrigger as-child>
                            <SidebarMenuButton as-child>
                                <button class="w-full justify-center">
                                    <ClipboardList class="size-4" />
                                </button>
                            </SidebarMenuButton>
                        </TooltipTrigger>
                        <TooltipContent side="right">
                            <p>Observations ({{ observations.length }})</p>
                        </TooltipContent>
                    </Tooltip>
                </TooltipProvider>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
