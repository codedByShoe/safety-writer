<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    CheckCircle,
    Edit,
    Plus,
    Trash2,
    XCircle,
    Package as PackageIcon,
} from 'lucide-vue-next';
import { ref } from 'vue';

interface Package {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    stripe_price_id: string;
    credits: number;
    price: number;
    is_active: boolean;
    is_popular: boolean;
    sort_order: number;
    created_at: string;
    updated_at: string;
}

interface Props {
    packages: Package[];
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: '/admin/dashboard',
    },
    {
        title: 'Packages',
        href: '/admin/packages',
    },
];

const deleteDialogOpen = ref(false);
const packageToDelete = ref<Package | null>(null);

const openDeleteDialog = (pkg: Package) => {
    packageToDelete.value = pkg;
    deleteDialogOpen.value = true;
};

const handleDelete = () => {
    if (!packageToDelete.value) return;

    router.delete(`/admin/packages/${packageToDelete.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            deleteDialogOpen.value = false;
            packageToDelete.value = null;
        },
    });
};

const formatPrice = (cents: number) => {
    return `$${(cents / 100).toFixed(2)}`;
};
</script>

<template>
    <Head title="Manage Packages" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold">Package Management</h1>
                    <p class="text-sm text-muted-foreground">
                        Manage credit packages and pricing
                    </p>
                </div>
                <Button as-child>
                    <Link href="/admin/packages/create">
                        <Plus class="mr-2 size-4" />
                        New Package
                    </Link>
                </Button>
            </div>

            <!-- Packages Table -->
            <Card>
                <CardHeader>
                    <CardTitle>All Packages</CardTitle>
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
                            Get started by creating your first credit package
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
                                        class="px-4 py-3 text-left text-xs font-medium text-muted-foreground"
                                    >
                                        Slug
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
                                    <th
                                        class="px-4 py-3 text-right text-xs font-medium text-muted-foreground"
                                    >
                                        Actions
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
                                        class="px-4 py-3 font-mono text-sm text-muted-foreground"
                                    >
                                        {{ pkg.slug }}
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
                                    <td class="px-4 py-3 text-right">
                                        <div
                                            class="flex items-center justify-end gap-2"
                                        >
                                            <Button
                                                as-child
                                                variant="ghost"
                                                size="sm"
                                            >
                                                <Link
                                                    :href="`/admin/packages/${pkg.id}/edit`"
                                                >
                                                    <Edit class="size-4" />
                                                    <span class="sr-only"
                                                        >Edit</span
                                                    >
                                                </Link>
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                @click="openDeleteDialog(pkg)"
                                            >
                                                <Trash2
                                                    class="size-4 text-destructive"
                                                />
                                                <span class="sr-only"
                                                    >Delete</span
                                                >
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Delete Package</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to delete "{{
                            packageToDelete?.name
                        }}"? This action cannot be undone and may affect users
                        who are trying to purchase this package.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="deleteDialogOpen = false">
                        Cancel
                    </Button>
                    <Button variant="destructive" @click="handleDelete">
                        Delete
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
