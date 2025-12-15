<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { CheckCircle, Coins } from 'lucide-vue-next';
import axios from 'axios';
import { ref } from 'vue';

interface Package {
    id: number;
    slug: string;
    name: string;
    description: string | null;
    credits: number;
    price: number;
    is_popular: boolean;
    discount_percentage: number | null;
    original_price: number | null;
}

interface Props {
    packages: Package[];
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Purchase Credits',
        href: '/checkout',
    },
];

const formatPrice = (cents: number) => {
    return `$${(cents / 100).toFixed(2)}`;
};

const processingPackage = ref<string | null>(null);

const handlePurchase = async (slug: string) => {
    if (processingPackage.value) return;

    processingPackage.value = slug;

    try {
        const response = await axios.post<{ url: string }>(`/checkout/${slug}`);
        window.location.href = response.data.url;
    } catch (error) {
        console.error('Checkout error:', error);
        processingPackage.value = null;
    }
};
</script>

<template>
    <Head title="Purchase Credits" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <!-- Header -->
            <div class="text-center">
                <h1 class="text-3xl font-bold text-foreground">
                    Purchase Credits
                </h1>
                <p class="mt-2 text-muted-foreground">
                    Buy credits once, use them anytime. No subscriptions.
                </p>
            </div>

            <!-- Pricing Cards -->
            <div
                class="mx-auto grid w-full max-w-6xl gap-6"
                :class="{
                    'md:grid-cols-1': packages.length === 1,
                    'md:grid-cols-2': packages.length === 2,
                    'md:grid-cols-3': packages.length >= 3,
                }"
            >
                <Card
                    v-for="pkg in packages"
                    :key="pkg.id"
                    class="relative overflow-hidden transition-all duration-200 hover:shadow-xl"
                    :class="
                        pkg.is_popular
                            ? 'scale-105 border-2 border-primary'
                            : ''
                    "
                >
                    <!-- Discount Badge -->
                    <div
                        v-if="pkg.discount_percentage"
                        class="absolute right-4 top-4 rounded-full bg-primary px-3 py-1 text-xs font-semibold text-primary-foreground"
                    >
                        {{ pkg.discount_percentage }}% OFF
                    </div>

                    <CardHeader>
                        <CardTitle class="text-center text-2xl">
                            {{ pkg.name }}
                        </CardTitle>
                        <p
                            v-if="pkg.description"
                            class="text-center text-sm text-muted-foreground"
                        >
                            {{ pkg.description }}
                        </p>
                    </CardHeader>

                    <CardContent class="space-y-6">
                        <!-- Price -->
                        <div class="text-center">
                            <div
                                v-if="pkg.original_price"
                                class="text-sm text-muted-foreground line-through"
                            >
                                {{ formatPrice(pkg.original_price) }}
                            </div>
                            <div class="mt-1">
                                <span
                                    class="text-5xl font-extrabold text-foreground"
                                >
                                    {{ formatPrice(pkg.price) }}
                                </span>
                            </div>
                            <p class="mt-2 text-sm text-muted-foreground">
                                One-time purchase
                            </p>
                        </div>

                        <!-- Features -->
                        <ul class="space-y-3">
                            <li class="flex items-center">
                                <CheckCircle
                                    class="size-5 shrink-0 text-primary"
                                />
                                <span class="ml-3 text-sm text-card-foreground">
                                    {{ pkg.credits.toLocaleString() }} credits
                                </span>
                            </li>
                            <li
                                v-if="pkg.discount_percentage"
                                class="flex items-center"
                            >
                                <CheckCircle
                                    class="size-5 shrink-0 text-primary"
                                />
                                <span class="ml-3 text-sm text-card-foreground">
                                    Save {{ pkg.discount_percentage }}%
                                </span>
                            </li>
                            <li class="flex items-center">
                                <CheckCircle
                                    class="size-5 shrink-0 text-primary"
                                />
                                <span class="ml-3 text-sm text-card-foreground">
                                    AI-powered generation
                                </span>
                            </li>
                            <li class="flex items-center">
                                <CheckCircle
                                    class="size-5 shrink-0 text-primary"
                                />
                                <span class="ml-3 text-sm text-card-foreground">
                                    All observation types
                                </span>
                            </li>
                        </ul>

                        <!-- Purchase Button -->
                        <Button
                            class="w-full"
                            :variant="pkg.is_popular ? 'default' : 'outline'"
                            size="lg"
                            :disabled="processingPackage !== null"
                            @click="handlePurchase(pkg.slug)"
                        >
                            <Coins class="mr-2 size-4" />
                            {{
                                processingPackage === pkg.slug
                                    ? 'Redirecting...'
                                    : `Buy ${pkg.name}`
                            }}
                        </Button>
                    </CardContent>
                </Card>
            </div>

            <!-- No packages message -->
            <div
                v-if="packages.length === 0"
                class="mx-auto max-w-2xl py-12 text-center"
            >
                <p class="text-muted-foreground">
                    No packages available at this time. Please check back later
                    or contact support.
                </p>
            </div>

            <!-- Info Section -->
            <div v-else class="mx-auto max-w-2xl text-center">
                <p class="text-sm text-muted-foreground">
                    Credits never expire. Use them at your own pace. Secure
                    payment processing powered by Stripe.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
