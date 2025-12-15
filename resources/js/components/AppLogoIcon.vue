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
    <img
        src="/safety-writer-logo.png"
        alt="Safety Writer"
        class="h-8 w-auto object-contain"
    />
</template>
