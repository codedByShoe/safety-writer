<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';

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
}

interface Props {
    package: Package;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: '/admin/dashboard',
    },
    {
        title: 'Packages',
        href: '/admin/packages',
    },
    {
        title: 'Edit',
        href: `/admin/packages/${props.package.id}/edit`,
    },
];

const form = useForm({
    name: props.package.name,
    slug: props.package.slug,
    description: props.package.description || '',
    stripe_price_id: props.package.stripe_price_id,
    credits: props.package.credits,
    price: props.package.price,
    is_active: props.package.is_active,
    is_popular: props.package.is_popular,
    sort_order: props.package.sort_order,
});

const submit = () => {
    form.put(`/admin/packages/${props.package.id}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Edit Package: ${package.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold">Edit Package</h1>
                    <p class="text-sm text-muted-foreground">
                        Update {{ package.name }} details
                    </p>
                </div>
                <Button as-child variant="outline">
                    <a href="/admin/packages">
                        <ArrowLeft class="mr-2 size-4" />
                        Back to Packages
                    </a>
                </Button>
            </div>

            <!-- Form -->
            <Card>
                <CardHeader>
                    <CardTitle>Package Details</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-6 md:grid-cols-2">
                            <!-- Name -->
                            <div class="space-y-2">
                                <Label for="name">Package Name *</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    placeholder="e.g., Starter, Popular, Pro"
                                    required
                                />
                                <p
                                    v-if="form.errors.name"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <!-- Slug -->
                            <div class="space-y-2">
                                <Label for="slug">Slug</Label>
                                <Input
                                    id="slug"
                                    v-model="form.slug"
                                    type="text"
                                    placeholder="Auto-generated if left blank"
                                />
                                <p class="text-xs text-muted-foreground">
                                    URL-friendly identifier
                                </p>
                                <p
                                    v-if="form.errors.slug"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.slug }}
                                </p>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <Label for="description">Description</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                placeholder="e.g., Perfect for getting started"
                                rows="3"
                            />
                            <p
                                v-if="form.errors.description"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- Stripe Price ID -->
                        <div class="space-y-2">
                            <Label for="stripe_price_id"
                                >Stripe Price ID *</Label
                            >
                            <Input
                                id="stripe_price_id"
                                v-model="form.stripe_price_id"
                                type="text"
                                placeholder="price_xxxxxxxxxxxxx"
                                required
                            />
                            <p class="text-xs text-muted-foreground">
                                Get this from your Stripe Dashboard under
                                Products → Prices
                            </p>
                            <p
                                v-if="form.errors.stripe_price_id"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.stripe_price_id }}
                            </p>
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <!-- Credits -->
                            <div class="space-y-2">
                                <Label for="credits">Credits *</Label>
                                <Input
                                    id="credits"
                                    v-model.number="form.credits"
                                    type="number"
                                    min="1"
                                    required
                                />
                                <p class="text-xs text-muted-foreground">
                                    Number of credits included
                                </p>
                                <p
                                    v-if="form.errors.credits"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.credits }}
                                </p>
                            </div>

                            <!-- Price -->
                            <div class="space-y-2">
                                <Label for="price">Price (in cents) *</Label>
                                <Input
                                    id="price"
                                    v-model.number="form.price"
                                    type="number"
                                    min="1"
                                    required
                                />
                                <p class="text-xs text-muted-foreground">
                                    Price in cents (e.g., 599 = $5.99)
                                </p>
                                <p
                                    v-if="form.errors.price"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.price }}
                                </p>
                            </div>
                        </div>

                        <!-- Sort Order -->
                        <div class="space-y-2">
                            <Label for="sort_order">Sort Order</Label>
                            <Input
                                id="sort_order"
                                v-model.number="form.sort_order"
                                type="number"
                                min="0"
                            />
                            <p class="text-xs text-muted-foreground">
                                Lower numbers appear first
                            </p>
                            <p
                                v-if="form.errors.sort_order"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.sort_order }}
                            </p>
                        </div>

                        <!-- Checkboxes -->
                        <div class="space-y-4">
                            <div class="flex items-center space-x-2">
                                <Checkbox
                                    id="is_active"
                                    :checked="form.is_active"
                                    @update:checked="
                                        (checked) =>
                                            (form.is_active = checked as boolean)
                                    "
                                />
                                <Label
                                    for="is_active"
                                    class="cursor-pointer font-normal"
                                >
                                    Active (visible to users)
                                </Label>
                            </div>

                            <div class="flex items-center space-x-2">
                                <Checkbox
                                    id="is_popular"
                                    :checked="form.is_popular"
                                    @update:checked="
                                        (checked) =>
                                            (form.is_popular =
                                                checked as boolean)
                                    "
                                />
                                <Label
                                    for="is_popular"
                                    class="cursor-pointer font-normal"
                                >
                                    Mark as Popular (highlighted for users)
                                </Label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end gap-4">
                            <Button
                                type="button"
                                variant="outline"
                                @click="$inertia.visit('/admin/packages')"
                            >
                                Cancel
                            </Button>
                            <Button type="submit" :disabled="form.processing">
                                <Save class="mr-2 size-4" />
                                {{
                                    form.processing
                                        ? 'Updating...'
                                        : 'Update Package'
                                }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
