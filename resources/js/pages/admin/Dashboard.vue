<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import {
    ClipboardList,
    Coins,
    Users,
    ShieldCheck,
    Activity,
    Package as PackageIcon,
    Plus,
    CheckCircle,
    XCircle,
} from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
    email: string;
    is_admin: boolean;
    credits: number;
    observations_count: number;
    created_at: string;
}

interface RecentObservation {
    id: string;
    user_name: string;
    title: string;
    status: string;
    created_at: string;
}

interface Package {
    id: number;
    name: string;
    credits: number;
    price: number;
    is_active: boolean;
    is_popular: boolean;
    sort_order: number;
}

interface Props {
    users: User[];
    metrics: {
        totalUsers: number;
        totalObservations: number;
        totalCreditsUsed: number;
    };
    recentObservations: RecentObservation[];
    packages: Package[];
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: '/admin/dashboard',
    },
];

const getStatusDot = (status: string) => {
    return status === 'finalized'
        ? 'bg-green-500'
        : 'bg-yellow-500 animate-pulse';
};

const formatPrice = (cents: number) => {
    return `$${(cents / 100).toFixed(2)}`;
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold">Admin Dashboard</h1>
                    <p class="text-sm text-muted-foreground">
                        System overview and user management
                    </p>
                </div>
            </div>

            <!-- Metrics Cards -->
            <div class="grid gap-4 md:grid-cols-3">
                <!-- Total Users -->
                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between"
                    >
                        <CardTitle class="text-sm font-medium">
                            Total Users
                        </CardTitle>
                        <Users class="size-4 text-blue-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl font-bold text-foreground">
                            {{ metrics.totalUsers }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Registered users
                        </p>
                    </CardContent>
                </Card>

                <!-- Total Observations -->
                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between"
                    >
                        <CardTitle class="text-sm font-medium">
                            Total Observations
                        </CardTitle>
                        <ClipboardList class="size-4 text-green-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl font-bold text-foreground">
                            {{ metrics.totalObservations }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            All observations
                        </p>
                    </CardContent>
                </Card>

                <!-- Total Credits Used -->
                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between"
                    >
                        <CardTitle class="text-sm font-medium">
                            Total Credits Used
                        </CardTitle>
                        <Coins class="size-4 text-orange-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl font-bold text-foreground">
                            {{ metrics.totalCreditsUsed.toLocaleString() }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Credits consumed
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Packages -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle class="flex items-center gap-2">
                        <PackageIcon class="size-5" />
                        Credit Packages
                    </CardTitle>
                    <Button as-child size="sm">
                        <Link href="/admin/packages">
                            <Plus class="mr-2 size-4" />
                            Manage Packages
                        </Link>
                    </Button>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="packages.length === 0"
                        class="py-12 text-center"
                    >
                        <PackageIcon
                            class="mx-auto size-12 text-muted-foreground opacity-50"
                        />
                        <h3 class="mt-4 text-lg font-semibold">
                            No packages yet
                        </h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Create your first credit package to get started
                        </p>
                        <Button as-child class="mt-4">
                            <Link href="/admin/packages/create">
                                <Plus class="mr-2 size-4" />
                                Create Package
                            </Link>
                        </Button>
                    </div>

                    <div
                        v-else
                        class="overflow-x-auto rounded-lg border border-sidebar-border/70 dark:border-sidebar-border"
                    >
                        <table class="w-full">
                            <thead class="bg-muted/50">
                                <tr
                                    class="border-b border-sidebar-border/70 dark:border-sidebar-border"
                                >
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-muted-foreground"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-muted-foreground"
                                    >
                                        Name
                                    </th>
                                    <th
                                        class="px-4 py-3 text-right text-xs font-medium text-muted-foreground"
                                    >
                                        Credits
                                    </th>
                                    <th
                                        class="px-4 py-3 text-right text-xs font-medium text-muted-foreground"
                                    >
                                        Price
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-muted-foreground"
                                    >
                                        Popular
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-muted-foreground"
                                    >
                                        Order
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="pkg in packages"
                                    :key="pkg.id"
                                    class="border-b border-sidebar-border/70 transition-colors hover:bg-muted/50 dark:border-sidebar-border"
                                >
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <CheckCircle
                                                v-if="pkg.is_active"
                                                class="size-5 text-green-600"
                                            />
                                            <XCircle
                                                v-else
                                                class="size-5 text-red-600"
                                            />
                                            <span
                                                class="text-xs capitalize"
                                                :class="
                                                    pkg.is_active
                                                        ? 'text-green-600'
                                                        : 'text-red-600'
                                                "
                                            >
                                                {{
                                                    pkg.is_active
                                                        ? 'Active'
                                                        : 'Inactive'
                                                }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 font-medium">
                                        {{ pkg.name }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-right font-medium"
                                    >
                                        {{ pkg.credits.toLocaleString() }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-right font-medium"
                                    >
                                        {{ formatPrice(pkg.price) }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            v-if="pkg.is_popular"
                                            class="inline-flex rounded-full bg-primary px-2 py-1 text-xs font-medium text-primary-foreground"
                                        >
                                            Popular
                                        </span>
                                    </td>
                                    <td
                                        class="px-4 py-3 text-center text-sm text-muted-foreground"
                                    >
                                        {{ pkg.sort_order }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            <!-- Users Table -->
            <Card>
                <CardHeader>
                    <CardTitle>All Users</CardTitle>
                </CardHeader>
                <CardContent>
                    <div
                        class="overflow-x-auto rounded-lg border border-sidebar-border/70 dark:border-sidebar-border"
                    >
                        <table class="w-full">
                            <thead class="bg-muted/50">
                                <tr
                                    class="border-b border-sidebar-border/70 dark:border-sidebar-border"
                                >
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-muted-foreground"
                                    >
                                        Name
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-muted-foreground"
                                    >
                                        Email
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-muted-foreground"
                                    >
                                        Role
                                    </th>
                                    <th
                                        class="px-4 py-3 text-right text-xs font-medium text-muted-foreground"
                                    >
                                        Credits
                                    </th>
                                    <th
                                        class="px-4 py-3 text-right text-xs font-medium text-muted-foreground"
                                    >
                                        Observations
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-muted-foreground"
                                    >
                                        Joined
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="user in users"
                                    :key="user.id"
                                    class="border-b border-sidebar-border/70 transition-colors hover:bg-muted/50 dark:border-sidebar-border"
                                >
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium">{{
                                                user.name
                                            }}</span>
                                            <ShieldCheck
                                                v-if="user.is_admin"
                                                class="size-4 text-blue-600"
                                                title="Admin"
                                            />
                                        </div>
                                    </td>
                                    <td
                                        class="px-4 py-3 text-sm text-muted-foreground"
                                    >
                                        {{ user.email }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            :class="[
                                                'inline-flex rounded-full px-2 py-1 text-xs font-medium',
                                                user.is_admin
                                                    ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400'
                                                    : 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
                                            ]"
                                        >
                                            {{ user.is_admin ? 'Admin' : 'User' }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-4 py-3 text-right text-sm font-medium"
                                    >
                                        {{ user.credits.toLocaleString() }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-right text-sm text-muted-foreground"
                                    >
                                        {{ user.observations_count }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-sm text-muted-foreground"
                                    >
                                        {{ user.created_at }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            <!-- Recent Activity -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Activity class="size-5" />
                        Recent Observations
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="recentObservations.length === 0"
                        class="py-12 text-center"
                    >
                        <ClipboardList
                            class="mx-auto size-12 text-muted-foreground opacity-50"
                        />
                        <h3 class="mt-4 text-lg font-semibold">
                            No observations yet
                        </h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Observations will appear here once users create them
                        </p>
                    </div>

                    <div
                        v-else
                        class="overflow-x-auto rounded-lg border border-sidebar-border/70 dark:border-sidebar-border"
                    >
                        <table class="w-full">
                            <thead class="bg-muted/50">
                                <tr
                                    class="border-b border-sidebar-border/70 dark:border-sidebar-border"
                                >
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-muted-foreground"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-muted-foreground"
                                    >
                                        User
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-muted-foreground"
                                    >
                                        Title
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-muted-foreground"
                                    >
                                        Created
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="observation in recentObservations"
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
                                            <span
                                                class="text-xs text-muted-foreground capitalize"
                                            >
                                                {{ observation.status }}
                                            </span>
                                        </div>
                                    </td>
                                    <td
                                        class="px-4 py-3 text-sm text-muted-foreground"
                                    >
                                        {{ observation.user_name }}
                                    </td>
                                    <td class="px-4 py-3 font-medium">
                                        {{ observation.title }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-sm text-muted-foreground"
                                    >
                                        {{ observation.created_at }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
