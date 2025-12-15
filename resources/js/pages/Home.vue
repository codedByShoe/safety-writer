<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';
import { login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import { Bolt, CheckCircle, Clipboard, HardHat, Lock } from 'lucide-vue-next';
import type { Component } from 'vue';

type creditPackage = {
   id: number
   name: string
   price: number
   credits: number
}

withDefaults(
    defineProps<{
        canRegister: boolean;
        creditPackages: Array<creditPackage>;
    }>(),
    {
        canRegister: true,
    },
);

const pricingPlan = [
    {
        name: 'Starter',
        credits: 1000,
        price: '$4.99',
        description: 'Perfect for getting started',
        features: [
            '1,000 credits',
            'AI-powered generation',
            'All observation types',
        ],
        button_text: 'Get Started',
        path: '/checkout',
        highlight: false,
        discount: null,
        originalPrice: null,
    },
    {
        name: 'Popular',
        credits: 5000,
        price: '$19.99',
        originalPrice: '$24.99',
        description: 'Best value for regular use',
        features: [
            '5,000 credits',
            'Save 20%',
            'AI-powered generation',
            'All observation types',
        ],
        button_text: 'Buy Popular',
        path: '/checkout',
        highlight: true,
        discount: '20% OFF',
    },
    {
        name: 'Pro',
        credits: 10000,
        price: '$34.99',
        originalPrice: '$49.99',
        description: 'Maximum savings for power users',
        features: [
            '10,000 credits',
            'Save 30%',
            'AI-powered generation',
            'All observation types',
        ],
        button_text: 'Buy Pro',
        path: '/checkout',
        highlight: false,
        discount: '30% OFF',
    },
];
const features: Array<{
    icon: Component;
    title: string;
    desc: string;
}> = [
    {
        icon: Bolt,
        title: 'AI-Powered Generation',
        desc: 'Generate detailed safety observations using advanced AI that understands industry standards.',
    },
    {
        icon: HardHat,
        title: 'Multiple Disipline Support',
        desc: 'Support for all departments and trades including carpenters, electricians, plumbers, welders, and more.',
    },
    {
        icon: Clipboard,
        title: 'Guided Observation Creation',
        desc: 'Step-by-step form helps you quickly build precise safety observations without missing key details.',
    },
    {
        icon: Lock,
        title: 'Secure & Private',
        desc: 'Your data is encrypted and secure. We take privacy seriously and protect all your safety observations.',
    },
];
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
<AppHeaderLayout>
    <div
        class="min-h-screen space-y-24 bg-background transition-colors duration-200"
    >
        <!-- Hero Section -->
        <div class="relative mt-12 overflow-hidden">
            <div class="mx-auto max-w-7xl">
                <div
                    class="relative z-10 pb-8 sm:pb-16 md:pb-20 lg:w-full lg:max-w-2xl lg:pb-28 xl:pb-32"
                >
                    <main
                        class="mx-auto mt-10 max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28"
                    >
                        <div class="sm:text-center lg:text-left">
                            <h1
                                class="text-4xl font-extrabold tracking-tight text-foreground sm:text-5xl md:text-6xl"
                            >
                                <span class="block">AI-Powered Safety</span>
                                <span class="block text-primary"
                                    >Observations Made Easy</span
                                >
                            </h1>
                            <p
                                class="mt-3 text-base text-muted-foreground sm:mx-auto sm:mt-5 sm:max-w-xl sm:text-lg md:mt-5 md:text-xl lg:mx-0"
                            >
                                Improve your safety observations in seconds with
                                our AI-powered platform.
                            </p>
                            <div
                                class="mt-5 gap-3 sm:mt-8 sm:flex sm:justify-center lg:justify-start"
                            >
                                <Button as-child size="lg">
                                    <Link prefetch :href="register()">
                                        Register
                                    </Link>
                                </Button>
                                <Button as-child variant="outline" size="lg">
                                    <a href="#features"> Learn More </a>
                                </Button>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
            <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
                <img
                    class="h-56 w-full rounded-lg object-cover sm:h-72 md:h-96 lg:h-full lg:w-full"
                    src="/img/cooling-towers.jpg"
                    alt="Construction site"
                />
            </div>
        </div>
        <!-- Features Section -->
        <div
            id="features"
            class="bg-muted/50 py-12 transition-colors duration-200"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="lg:text-center">
                    <h2
                        class="text-base font-semibold tracking-wide text-primary uppercase"
                    >
                        Features
                    </h2>
                    <p
                        class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-foreground sm:text-4xl"
                    >
                        Everything you need for safety observations
                    </p>
                    <p
                        class="mt-4 max-w-2xl text-xl text-muted-foreground lg:mx-auto"
                    >
                        Our platform helps construction professionals generate
                        comprehensive safety observations quickly and
                        efficiently.
                    </p>
                </div>
                <div class="mt-10">
                    <dl
                        class="space-y-10 md:grid md:grid-cols-2 md:space-y-0 md:gap-x-8 md:gap-y-10"
                    >
                        <div
                            v-for="feature in features"
                            :key="feature.title"
                            class="relative"
                        >
                            <dt>
                                <div
                                    class="absolute flex h-12 w-12 items-center justify-center rounded-md bg-primary text-primary-foreground"
                                >
                                    <component
                                        :is="feature.icon"
                                        class="size-6"
                                    />
                                </div>
                                <p
                                    class="ml-16 text-lg leading-6 font-medium text-foreground"
                                >
                                    {{ feature.title }}
                                </p>
                            </dt>
                            <dd
                                class="mt-2 ml-16 text-base text-muted-foreground"
                            >
                                {{ feature.desc }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
        <!-- Pricing Section -->
        <div
            class="rounded-lg border border-border bg-card transition-colors duration-200"
        >
            <div class="px-4 py-12 sm:px-6 lg:px-8 lg:py-20">
                <div class="mx-auto max-w-4xl text-center">
                    <h2
                        class="text-3xl font-extrabold text-foreground sm:text-4xl"
                    >
                        Simple, transparent pricing
                    </h2>
                    <p class="mt-4 text-xl text-muted-foreground">
                        Buy credits once, use them anytime. No subscriptions.
                    </p>
                </div>

                <div
                    class="mx-auto mt-12 grid max-w-md gap-8 lg:max-w-7xl lg:grid-cols-3"
                >
                    <div
                        v-for="plan in pricingPlan"
                        :key="plan.name"
                        class="relative overflow-hidden rounded-lg border bg-card shadow-lg transition-all duration-200 hover:shadow-xl"
                        :class="
                            plan.highlight
                                ? 'scale-105 border-2 border-primary'
                                : 'border-border'
                        "
                    >
                        <!-- Discount Badge -->
                        <div
                            v-if="plan.discount"
                            class="absolute top-4 right-4 rounded-full bg-primary px-3 py-1 text-xs font-semibold text-primary-foreground"
                        >
                            {{ plan.discount }}
                        </div>

                        <div class="px-6 py-8">
                            <h3
                                class="text-center text-2xl font-semibold text-card-foreground"
                            >
                                {{ plan.name }}
                            </h3>
                            <p
                                class="mt-2 text-center text-sm text-muted-foreground"
                            >
                                {{ plan.description }}
                            </p>
                            <div class="mt-6 text-center">
                                <div
                                    v-if="plan.originalPrice"
                                    class="text-sm text-muted-foreground line-through"
                                >
                                    {{ plan.originalPrice }}
                                </div>
                                <div class="mt-1">
                                    <span
                                        class="text-5xl font-extrabold text-foreground"
                                        >{{ plan.price }}</span
                                    >
                                </div>
                                <p class="mt-2 text-sm text-muted-foreground">
                                    One-time purchase
                                </p>
                            </div>
                            <ul class="mt-8 space-y-3">
                                <li
                                    v-for="feature in plan.features"
                                    :key="feature"
                                    class="flex items-center"
                                >
                                    <CheckCircle
                                        class="size-5 shrink-0 text-primary"
                                    />
                                    <span
                                        class="ml-3 text-sm text-card-foreground"
                                        >{{ feature }}</span
                                    >
                                </li>
                            </ul>
                            <div class="mt-8">
                                <Button
                                    as-child
                                    :variant="
                                        plan.highlight ? 'default' : 'outline'
                                    "
                                    class="w-full"
                                    size="lg"
                                >
                                    <Link :href="plan.path" prefetch>
                                        {{ plan.button_text }}
                                    </Link>
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing Note -->
                <p class="mt-8 text-center text-sm text-muted-foreground">
                    Credits never expire. Use them at your own pace.
                </p>
            </div>
        </div>
        <!-- CTA Section -->
        <div
            class="mb-24 rounded-lg bg-muted/50 py-12 transition-colors duration-200"
        >
            <div
                class="mx-auto max-w-7xl px-4 py-12 text-center sm:px-6 lg:px-8 lg:py-16"
            >
                <h2
                    class="text-3xl font-extrabold text-foreground sm:text-4xl"
                >
                    <span class="block"
                        >Ready to improve safety observations?</span
                    >
                    <span class="block">Start generating today.</span>
                </h2>
                <div class="mt-8 flex justify-center gap-3">
                    <Button as-child variant="default" size="lg">
                        <Link :href="register()" prefetch> Get Started </Link>
                    </Button>
                    <Button
                        as-child
                        variant="outline"
                        size="lg"
                    >
                        <Link :href="login()" prefetch> Sign In </Link>
                    </Button>
                </div>
            </div>
        </div>
    </div>
</AppHeaderLayout>
</template>
