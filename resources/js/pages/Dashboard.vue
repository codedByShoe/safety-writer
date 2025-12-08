<script setup lang="ts">
import { store } from '@/actions/App/Http/Controllers/ObservationController';
import { show } from '@/actions/App/Http/Controllers/ObservationController';
import Button from '@/components/ui/button/Button.vue';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    BarChart3,
    CheckCircle2,
    ChevronLeft,
    ChevronRight,
    ClipboardList,
    Coins,
    Plus,
    TrendingUp,
} from 'lucide-vue-next';
import { computed } from 'vue';

interface Observation {
    id: string;
    title: string;
    status: 'draft' | 'finalized';
    type: 'met' | 'not-met' | 'unknown';
    created_at: string;
    created_at_human: string;
}

interface PaginatedObservations {
    data: Observation[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
}

interface Props {
    stats: {
        total: number;
        positive: number;
        delta: number;
    };
    observations: PaginatedObservations;
    credits: number;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const positivePercentage = computed(() => {
    if (props.stats.total === 0) return 0;
    return Math.round((props.stats.positive / props.stats.total) * 100);
});

const deltaPercentage = computed(() => {
    if (props.stats.total === 0) return 0;
    return Math.round((props.stats.delta / props.stats.total) * 100);
});

const handlePagination = (page: number) => {
    router.get(
        dashboard().url,
        { page },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

const getStatusDot = (status: 'draft' | 'finalized') => {
    return status === 'finalized'
        ? 'bg-green-500'
        : 'bg-yellow-500 animate-pulse';
};

const getTypeLabel = (type: string) => {
    if (type === 'met') return 'Positive';
    if (type === 'not-met') return 'Delta';
    return 'Unknown';
};

const getTypeBadgeClass = (type: string) => {
    if (type === 'met')
        return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
    if (type === 'not-met')
        return 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400';
    return 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400';
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <!-- Header with New Observation Button -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold">Dashboard</h1>
                    <p class="text-sm text-muted-foreground">
                        Overview of your safety observations
                    </p>
                </div>
                <Button as-child>
                    <Link :href="store.form().action" prefetch>
                        <Plus class="mr-2 size-4" />
                        New Observation
                    </Link>
                </Button>
            </div>

            <!-- Credits Card - Full Width -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle class="text-sm font-medium">
                        Available Credits
                    </CardTitle>
                    <Coins class="size-4 text-blue-600" />
                </CardHeader>
                <CardContent>
                    <div class="text-3xl font-bold text-blue-600">
                        {{ credits.toLocaleString() }}
                    </div>
                    <p class="text-xs text-muted-foreground">
                        Credits available for AI-powered observations
                    </p>
                </CardContent>
            </Card>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-3">
                <!-- Total Observations -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between">
                        <CardTitle class="text-sm font-medium">
                            Total Observations
                        </CardTitle>
                        <ClipboardList
                            class="size-4 text-muted-foreground"
                        />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ stats.total }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            All time observations
                        </p>
                    </CardContent>
                </Card>

                <!-- Positive Observations -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between">
                        <CardTitle class="text-sm font-medium">
                            Positive Observations
                        </CardTitle>
                        <CheckCircle2 class="size-4 text-green-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">
                            {{ stats.positive }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            {{ positivePercentage }}% of total observations
                        </p>
                    </CardContent>
                </Card>

                <!-- Delta Observations -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between">
                        <CardTitle class="text-sm font-medium">
                            Delta Observations
                        </CardTitle>
                        <TrendingUp class="size-4 text-orange-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-orange-600">
                            {{ stats.delta }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            {{ deltaPercentage }}% of total observations
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Chart Visualization -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <BarChart3 class="size-5" />
                        Observation Distribution
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <!-- Positive Bar -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-medium text-green-600">
                                    Positive (Met)
                                </span>
                                <span class="text-muted-foreground">
                                    {{ stats.positive }} ({{ positivePercentage }}%)
                                </span>
                            </div>
                            <div class="h-8 w-full overflow-hidden rounded-full bg-muted">
                                <div
                                    class="h-full bg-green-600 transition-all duration-500"
                                    :style="{
                                        width: `${positivePercentage}%`,
                                    }"
                                ></div>
                            </div>
                        </div>

                        <!-- Delta Bar -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-medium text-orange-600">
                                    Delta (Not Met)
                                </span>
                                <span class="text-muted-foreground">
                                    {{ stats.delta }} ({{ deltaPercentage }}%)
                                </span>
                            </div>
                            <div class="h-8 w-full overflow-hidden rounded-full bg-muted">
                                <div
                                    class="h-full bg-orange-600 transition-all duration-500"
                                    :style="{ width: `${deltaPercentage}%` }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Observations Table -->
            <Card>
                <CardHeader>
                    <CardTitle>Recent Observations</CardTitle>
                </CardHeader>
                <CardContent>
                    <div v-if="observations.data.length === 0" class="py-12 text-center">
                        <ClipboardList
                            class="mx-auto size-12 text-muted-foreground opacity-50"
                        />
                        <h3 class="mt-4 text-lg font-semibold">
                            No observations yet
                        </h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Get started by creating your first observation
                        </p>
                        <Button as-child class="mt-4">
                            <Link :href="store.form().action">
                                <Plus class="mr-2 size-4" />
                                Create Observation
                            </Link>
                        </Button>
                    </div>

                    <div v-else class="space-y-4">
                        <!-- Table -->
                        <div class="overflow-x-auto rounded-lg border border-sidebar-border/70 dark:border-sidebar-border">
                            <table class="w-full">
                                <thead class="bg-muted/50">
                                    <tr class="border-b border-sidebar-border/70 dark:border-sidebar-border">
                                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground">
                                            Status
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground">
                                            Title
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground">
                                            Type
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground">
                                            Date
                                        </th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-muted-foreground">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="observation in observations.data"
                                        :key="observation.id"
                                        class="border-b border-sidebar-border/70 transition-colors hover:bg-muted/50 dark:border-sidebar-border"
                                    >
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <div
                                                    :class="[
                                                        'size-2 rounded-full',
                                                        getStatusDot(
                                                            observation.status,
                                                        ),
                                                    ]"
                                                ></div>
                                                <span class="text-xs capitalize text-muted-foreground">
                                                    {{ observation.status }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <Link
                                                :href="show.url(observation.id)"
                                                class="font-medium hover:underline"
                                                prefetch
                                            >
                                                {{ observation.title }}
                                            </Link>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span
                                                :class="[
                                                    'inline-flex rounded-full px-2 py-1 text-xs font-medium',
                                                    getTypeBadgeClass(
                                                        observation.type,
                                                    ),
                                                ]"
                                            >
                                                {{ getTypeLabel(observation.type) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-muted-foreground">
                                            <div class="flex flex-col">
                                                <span>{{ observation.created_at }}</span>
                                                <span class="text-xs">
                                                    {{ observation.created_at_human }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <Button
                                                as-child
                                                variant="ghost"
                                                size="sm"
                                            >
                                                <Link
                                                    :href="show.url(observation.id)"
                                                    prefetch
                                                >
                                                    View
                                                </Link>
                                            </Button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div
                            v-if="observations.last_page > 1"
                            class="flex items-center justify-between"
                        >
                            <div class="text-sm text-muted-foreground">
                                Showing
                                <span class="font-medium">{{ observations.from }}</span>
                                to
                                <span class="font-medium">{{ observations.to }}</span>
                                of
                                <span class="font-medium">{{ observations.total }}</span>
                                results
                            </div>
                            <div class="flex items-center gap-2">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    :disabled="observations.current_page === 1"
                                    @click="handlePagination(observations.current_page - 1)"
                                >
                                    <ChevronLeft class="size-4" />
                                    Previous
                                </Button>
                                <div class="flex items-center gap-1">
                                    <span class="text-sm">
                                        Page {{ observations.current_page }} of
                                        {{ observations.last_page }}
                                    </span>
                                </div>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    :disabled="observations.current_page === observations.last_page"
                                    @click="handlePagination(observations.current_page + 1)"
                                >
                                    Next
                                    <ChevronRight class="size-4" />
                                </Button>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
