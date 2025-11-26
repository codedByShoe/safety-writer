<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import Avatar from '@/components/ui/avatar/Avatar.vue';
import AvatarFallback from '@/components/ui/avatar/AvatarFallback.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

// Placeholder chat messages
const messages = ref([
    {
        id: 1,
        role: 'user',
        content: 'Hello! Can you help me write a safety procedure?',
        timestamp: '10:30 AM',
    },
    {
        id: 2,
        role: 'assistant',
        content:
            "Of course! I'd be happy to help you write a safety procedure. What type of safety procedure are you looking to create? For example, is it for workplace safety, chemical handling, emergency response, or something else?",
        timestamp: '10:30 AM',
    },
    {
        id: 3,
        role: 'user',
        content:
            'I need to create a procedure for operating a forklift in our warehouse.',
        timestamp: '10:31 AM',
    },
    {
        id: 4,
        role: 'assistant',
        content:
            "Great! I'll help you create a comprehensive forklift safety procedure. Let's start with the key sections: Pre-operation inspection, Operating guidelines, Load handling, and Emergency procedures. Would you like me to draft these sections for you?",
        timestamp: '10:32 AM',
    },
    {
        id: 5,
        role: 'user',
        content: 'Yes, please! That would be very helpful.',
        timestamp: '10:32 AM',
    },
    {
        id: 6,
        role: 'assistant',
        content:
            "Perfect! I'll create a detailed forklift safety procedure for you. Here's what I'm including:\n\n1. Pre-Operation Inspection\n2. Operating Procedures\n3. Load Handling Guidelines\n4. Emergency Procedures\n5. Maintenance Requirements\n\nWould you like me to add any specific requirements or modify any sections?",
        timestamp: '10:33 AM',
    },
]);

const newMessage = ref('');
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div
                class="relative flex min-h-[100vh] flex-1 flex-col rounded-xl border border-sidebar-border/70 bg-background md:min-h-min dark:border-sidebar-border"
            >
                <!-- Chat Header -->
                <div
                    class="border-b border-sidebar-border/70 px-6 py-4 dark:border-sidebar-border"
                >
                    <h2 class="text-lg font-semibold">AI Safety Writer</h2>
                    <p class="text-muted-foreground text-sm">
                        Your assistant for creating safety procedures
                    </p>
                </div>

                <!-- Messages Area -->
                <div class="flex-1 space-y-4 overflow-y-auto p-6">
                    <div
                        v-for="message in messages"
                        :key="message.id"
                        :class="[
                            'flex gap-3',
                            message.role === 'user'
                                ? 'justify-end'
                                : 'justify-start',
                        ]"
                    >
                        <!-- Avatar for AI messages (left side) -->
                        <Avatar
                            v-if="message.role === 'assistant'"
                            class="size-8"
                        >
                            <AvatarFallback class="bg-primary text-primary-foreground">
                                AI
                            </AvatarFallback>
                        </Avatar>

                        <!-- Message Content -->
                        <div
                            :class="[
                                'max-w-[70%] rounded-lg px-4 py-2',
                                message.role === 'user'
                                    ? 'bg-primary text-primary-foreground'
                                    : 'bg-muted',
                            ]"
                        >
                            <p class="whitespace-pre-wrap text-sm">
                                {{ message.content }}
                            </p>
                            <span
                                :class="[
                                    'mt-1 block text-xs',
                                    message.role === 'user'
                                        ? 'text-primary-foreground/70'
                                        : 'text-muted-foreground',
                                ]"
                            >
                                {{ message.timestamp }}
                            </span>
                        </div>

                        <!-- Avatar for User messages (right side) -->
                        <Avatar
                            v-if="message.role === 'user'"
                            class="size-8"
                        >
                            <AvatarFallback class="bg-accent">
                                U
                            </AvatarFallback>
                        </Avatar>
                    </div>
                </div>

                <!-- Input Area -->
                <div
                    class="border-t border-sidebar-border/70 p-4 dark:border-sidebar-border"
                >
                    <div class="flex gap-2">
                        <Input
                            v-model="newMessage"
                            placeholder="Type your message..."
                            class="flex-1"
                            @keydown.enter.prevent
                        />
                        <Button>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="size-4"
                            >
                                <path d="M22 2 11 13" />
                                <path d="m22 2-7 20-4-9-9-4z" />
                            </svg>
                            <span class="ml-2">Send</span>
                        </Button>
                    </div>
                    <p class="text-muted-foreground mt-2 text-xs">
                        Press Enter to send your message
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
