<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 64 64"
        width="64"
        height="64"
    >
        <!-- Bold shield outline style -->
        <path
            d="M32 6 L52 13 L52 28 C52 42 43 52 32 58 C21 52 12 42 12 28 L12 13 Z"
            fill="none"
            stroke="#71717a"
            stroke-width="3"
        />

        <!-- Inner shield fill -->
        <path
            d="M32 10 L48 16 L48 28 C48 40 40 48 32 53 C24 48 16 40 16 28 L16 16 Z"
            fill="#27272a"
        />

        <!-- Stylized pencil/document combo -->
        <!-- Pencil body diagonal -->
        <rect
            x="24"
            y="20"
            width="6"
            height="24"
            rx="1"
            fill="#f4f4f5"
            transform="rotate(-15 27 32)"
        />
        <polygon
            points="24,44 27,50 30,44"
            fill="#a1a1aa"
            transform="rotate(-15 27 32)"
        />

        <!-- Writing lines coming from pencil -->
        <line
            x1="30"
            y1="22"
            x2="42"
            y2="22"
            stroke="#71717a"
            stroke-width="2"
            stroke-linecap="round"
        />
        <line
            x1="32"
            y1="28"
            x2="42"
            y2="28"
            stroke="#71717a"
            stroke-width="2"
            stroke-linecap="round"
        />
        <line
            x1="34"
            y1="34"
            x2="42"
            y2="34"
            stroke="#71717a"
            stroke-width="2"
            stroke-linecap="round"
        />

        <!-- Small check accent -->
        <path
            d="M36 40 L39 43 L45 37"
            stroke="#f4f4f5"
            stroke-width="2.5"
            stroke-linecap="round"
            stroke-linejoin="round"
            fill="none"
        />
    </svg>
</template>
