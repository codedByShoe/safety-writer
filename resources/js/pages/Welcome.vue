<script setup lang="ts">
import StreamingIndicator from '@/components/StreamingIndicator.vue';
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import { useStream } from '@laravel/stream-vue';
import { onMounted, onUnmounted, ref, watch } from 'vue';

type Message = {
    type: 'response' | 'error' | 'prompt';
    content: string;
};

const messages = ref<Message[]>([]);
const { data, send, cancel, isStreaming, id } = useStream('chats');
const handleEscape = (e: KeyboardEvent) => {
    if (e.key === 'Escape') {
        cancel();
    }
};
const getCookie = (name: string) => {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop()?.split(';').shift();
};

const updateUrl = () => {
    const url = new URL(window.location.href);
    url.searchParams.set('chat', getCookie('chat_id') || '');
    window.history.pushState({}, '', url);
};

onMounted(() => {
    window.addEventListener('keydown', handleEscape);
});
onUnmounted(() => {
    window.removeEventListener('keydown', handleEscape);
});
watch([isStreaming, data], () => {
    updateUrl();
    if (isStreaming.value) {
        window.scrollTo(0, document.body.scrollHeight);
    }
});

const submit = (e: Event) => {
    e.preventDefault();
    const form = e.target as HTMLFormElement;
    const input = form.querySelector('input') as HTMLInputElement;
    const query = input?.value;
    const toAdd: Message[] = [];
    if (data.value) {
        toAdd.push({
            type: 'response',
            content: data.value,
        });
    }
    if (query) {
        toAdd.push({
            type: 'prompt',
            content: query,
        });
    }
    messages.value = [...messages.value, ...toAdd];
    console.log('messages', messages.value);
    send({ messages: [...messages.value] });
    input.value = '';
};

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="">
        <header
            class="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl"
        >
            <nav class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                    >
                        Log in
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="register()"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>

        <div class="flex min-h-screen flex-col bg-white">
            <div class="mx-auto w-full max-w-4xl flex-1 px-6 py-8">
                <div class="mb-24 space-y-6">
                    <div v-if="messages.length === 0" class="py-12 text-center">
                        <div class="text-lg text-gray-400">
                            How can I help you today?
                        </div>
                    </div>

                    <div
                        v-for="(message, index) in messages"
                        :key="index"
                        class="space-y-4"
                    >
                        <div
                            v-if="message.type === 'prompt'"
                            class="flex justify-end"
                        >
                            <div
                                class="max-w-xs rounded-2xl bg-blue-500 px-4 py-3 text-white sm:max-w-md lg:max-w-lg"
                            >
                                <span
                                    v-html="
                                        message.content.replace(/\n/g, '<br>')
                                    "
                                />
                            </div>
                        </div>

                        <div
                            v-if="message.type === 'response'"
                            class="flex justify-start"
                        >
                            <div
                                class="max-w-xs rounded-2xl bg-gray-100 px-4 py-3 text-gray-900 sm:max-w-md lg:max-w-2xl"
                            >
                                <span
                                    v-html="
                                        message.content.replace(/\n/g, '<br>')
                                    "
                                />
                            </div>
                        </div>
                    </div>

                    <div v-if="data" class="flex justify-start">
                        <div
                            class="relative max-w-xs rounded-2xl bg-gray-100 px-4 py-3 text-gray-900 sm:max-w-md lg:max-w-2xl"
                        >
                            <span v-html="data.replace(/\n/g, '<br>')" />
                            <StreamingIndicator
                                :id="id"
                                class="ml-2 inline-block"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="fixed right-0 bottom-0 left-0 border-t border-gray-200 bg-white px-6 py-4"
            >
                <div class="mx-auto max-w-4xl">
                    <form class="flex gap-3" @submit="submit">
                        <input
                            type="text"
                            placeholder="Type your message..."
                            class="flex-1 rounded-2xl border border-gray-300 bg-white px-4 py-3 text-gray-900 focus:border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        />
                        <button
                            type="submit"
                            class="rounded-2xl bg-blue-500 px-6 py-3 font-medium text-white transition-colors hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        >
                            Send
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
