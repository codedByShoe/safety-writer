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
        :class="className"
    >
        <!-- Shield background - zinc-700 -->
        <path
            d="M32 4 L54 12 L54 28 C54 44 44 54 32 60 C20 54 10 44 10 28 L10 12 Z"
            fill="#3f3f46"
            stroke="#27272a"
            stroke-width="2"
        />

        <!-- Inner shield highlight - zinc-600 -->
        <path
            d="M32 8 L50 15 L50 28 C50 42 41 50 32 55 C23 50 14 42 14 28 L14 15 Z"
            fill="#52525b"
        />

        <!-- Document - zinc-100 -->
        <rect
            x="22"
            y="16"
            width="20"
            height="26"
            rx="2"
            fill="#f4f4f5"
            stroke="#e4e4e7"
            stroke-width="1"
        />

        <!-- Document lines - zinc-300 -->
        <line
            x1="26"
            y1="22"
            x2="38"
            y2="22"
            stroke="#d4d4d8"
            stroke-width="2"
            stroke-linecap="round"
        />
        <line
            x1="26"
            y1="28"
            x2="38"
            y2="28"
            stroke="#d4d4d8"
            stroke-width="2"
            stroke-linecap="round"
        />
        <line
            x1="26"
            y1="34"
            x2="34"
            y2="34"
            stroke="#d4d4d8"
            stroke-width="2"
            stroke-linecap="round"
        />

        <!-- Pen/Pencil - zinc-400/zinc-300 -->
        <g transform="rotate(45 44 38)">
            <rect x="40" y="24" width="6" height="22" rx="1" fill="#a1a1aa" />
            <polygon points="40,46 43,52 46,46" fill="#27272a" />
            <rect x="40" y="24" width="6" height="4" fill="#d4d4d8" />
        </g>

        <!-- Checkmark accent - zinc-800 with zinc-100 check -->
        <circle cx="42" cy="18" r="6" fill="#27272a" />
        <path
            d="M39 18 L41 20 L45 15"
            stroke="#f4f4f5"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            fill="none"
        />
    </svg>
</template>
