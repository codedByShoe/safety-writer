<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

interface Props {
    message?: string;
    class?: HTMLAttributes['class'];
}

const props = defineProps<Props>();
</script>

<template>
    <p v-if="message" :class="cn('text-sm text-destructive', props.class)">
        {{ message }}
    </p>
</template>
