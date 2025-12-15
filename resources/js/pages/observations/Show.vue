<script setup lang="ts">
import { update } from '@/actions/App/Http/Controllers/ObservationController';
import Badge from '@/components/ui/badge/Badge.vue';
import Button from '@/components/ui/button/Button.vue';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import Label from '@/components/ui/label/Label.vue';
import { Separator } from '@/components/ui/separator';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { observation } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import {
    Check,
    ChevronDown,
    ClipboardList,
    Copy,
    Edit3,
    FileText,
    Loader2,
    X,
} from 'lucide-vue-next';
import { computed, onUnmounted, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

interface Props {
    id: string;
    title: string;
    status?: 'draft' | 'finalized';
    response?: string | null;
    formData?: {
        discipline: string;
        company: string;
        intentionality: string;
        observationType: string;
        location: string;
        gap: string;
        whyDetails: string;
        consequence?: string;
        impactfulAction: string;
        peerToPeer: string;
    };
}

const props = withDefaults(defineProps<Props>(), {
    status: 'draft',
    response: null,
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Observations',
        href: observation().url,
    },
    {
        title: props.title ?? 'Observation #' + props.id,
        href: '#',
    },
];

const currentStatus = ref(props.status);
const isFormOpen = ref(false);
const copied = ref(false);
const showRefinementPanel = ref(false);
const refinementRequest = ref('');

// Check if observation is still being generated
const isGenerating = computed(() => {
    return props.response === null || props.response === '';
});

// Format the observation content for display
const formattedObservation = computed(() => {
    return props.response || 'No observation content available.';
});

// Poll for updates when generating
let pollInterval: ReturnType<typeof setInterval> | null = null;
let wasGenerating = ref(false);
let pollStartTime = ref<number | null>(null);
const MAX_POLL_TIME = 120000; // 2 minutes max polling time

watch(
    isGenerating,
    (generating) => {
        if (generating) {
            wasGenerating.value = true;
            pollStartTime.value = Date.now();

            // Start polling every 2 seconds
            pollInterval = setInterval(() => {
                // Check if we've exceeded max polling time
                if (
                    pollStartTime.value &&
                    Date.now() - pollStartTime.value > MAX_POLL_TIME
                ) {
                    if (pollInterval) {
                        clearInterval(pollInterval);
                        pollInterval = null;
                    }
                    toast.error(
                        'Observation generation is taking longer than expected. Please check back later or contact support.',
                    );
                    router.visit(observation().url);
                    return;
                }

                router.reload({
                    only: ['response'],
                    onError: (errors) => {
                        // If observation was deleted (404), redirect to observations list
                        if (pollInterval) {
                            clearInterval(pollInterval);
                            pollInterval = null;
                        }
                        toast.error(
                            'Observation generation failed. Please try again.',
                        );
                        router.visit(observation().url);
                    },
                });
            }, 2000); // Poll every 2 seconds (reduced from 500ms)
        } else {
            // Stop polling when response is ready
            if (pollInterval) {
                clearInterval(pollInterval);
                pollInterval = null;
            }
            pollStartTime.value = null;

            // Show success toast if we just finished generating
            if (wasGenerating.value && props.response) {
                toast.success('Observation generated successfully!');
                wasGenerating.value = false;
            }
        }
    },
    { immediate: true },
);

// Cleanup on unmount
onUnmounted(() => {
    if (pollInterval) {
        clearInterval(pollInterval);
    }
});

const handleCopy = async () => {
    // Strip markdown formatting for clean text
    const plainText = (props.response || '')
        .replace(/\*\*(.*?)\*\*/g, '$1') // Remove bold **text**
        .replace(/\*(.*?)\*/g, '$1') // Remove italic *text*
        .replace(/#{1,6}\s/g, '') // Remove headers
        .replace(/\n{3,}/g, '\n\n'); // Clean up extra newlines

    try {
        // Try modern clipboard API first
        if (navigator.clipboard && navigator.clipboard.writeText) {
            await navigator.clipboard.writeText(plainText);
        } else {
            // Fallback for older browsers or insecure contexts
            const textarea = document.createElement('textarea');
            textarea.value = plainText;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
        }

        copied.value = true;
        setTimeout(() => {
            copied.value = false;
        }, 2000);
    } catch (error) {
        console.error('Failed to copy text:', error);
    }
};

const handleFinalize = () => {
    router.put(
        update.url(props.id),
        { action: 'finalize' },
        {
            preserveScroll: true,
            onSuccess: () => {
                currentStatus.value = 'finalized';
                toast.success('Observation finalized successfully!');
            },
            onError: () => {
                toast.error(
                    'Failed to finalize observation. Please try again.',
                );
            },
        },
    );
};

const handleRequestChanges = () => {
    showRefinementPanel.value = !showRefinementPanel.value;
    if (!showRefinementPanel.value) {
        refinementRequest.value = '';
    }
};

const handleSubmitRefinement = () => {
    if (!refinementRequest.value.trim()) return;

    router.put(
        update.url(props.id),
        { refinement_request: refinementRequest.value },
        {
            preserveScroll: true,
            onSuccess: () => {
                showRefinementPanel.value = false;
                refinementRequest.value = '';
                toast.success('Observation updated successfully!');
            },
            onError: () => {
                toast.error('Failed to update observation. Please try again.');
            },
        },
    );
};
</script>

<template>
    <Head :title="`Observation #${id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div
                class="relative flex min-h-[100vh] flex-1 flex-col rounded-xl border border-sidebar-border/70 bg-background md:min-h-min dark:border-sidebar-border"
            >
                <!-- Sticky Action Bar -->
                <div
                    class="sticky top-0 z-10 border-b border-sidebar-border/70 bg-background px-6 py-4 dark:border-sidebar-border"
                >
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <h2 class="text-lg font-semibold">
                                {{ title ?? `Observation #${id}` }}
                            </h2>
                            <Badge
                                :variant="
                                    currentStatus === 'finalized'
                                        ? 'default'
                                        : 'secondary'
                                "
                            >
                                <Check
                                    v-if="currentStatus === 'finalized'"
                                    class="mr-1 size-3"
                                />
                                {{
                                    currentStatus === 'finalized'
                                        ? 'Finalized'
                                        : 'Draft'
                                }}
                            </Badge>
                            <Badge
                                v-if="isGenerating"
                                variant="outline"
                                class="animate-pulse"
                            >
                                <Loader2 class="mr-1 size-3 animate-spin" />
                                Generating...
                            </Badge>
                        </div>

                        <div class="flex items-center gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                @click="handleCopy"
                                :disabled="isGenerating"
                            >
                                <Copy v-if="!copied" class="size-4" />
                                <Check v-else class="size-4" />
                                <span class="ml-2">{{
                                    copied ? 'Copied!' : 'Copy'
                                }}</span>
                            </Button>
                            <Button
                                v-if="currentStatus === 'draft'"
                                variant="outline"
                                size="sm"
                                @click="handleRequestChanges"
                                :disabled="isGenerating"
                                :class="[
                                    showRefinementPanel
                                        ? 'bg-primary text-primary-foreground hover:bg-primary/90'
                                        : '',
                                ]"
                            >
                                <Edit3 class="size-4" />
                                <span class="ml-2">Request Changes</span>
                            </Button>
                            <Button
                                v-if="currentStatus === 'draft'"
                                size="sm"
                                @click="handleFinalize"
                                :disabled="isGenerating"
                            >
                                <FileText class="size-4" />
                                <span class="ml-2">Finalize</span>
                            </Button>
                        </div>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="flex-1 overflow-y-auto p-6">
                    <div class="mx-auto max-w-4xl space-y-6">
                        <!-- Collapsible Original Form Data -->
                        <Collapsible v-model:open="isFormOpen">
                            <CollapsibleTrigger
                                class="flex w-full items-center justify-between rounded-lg border border-sidebar-border/70 bg-muted/50 px-4 py-3 text-sm font-medium transition-colors hover:bg-muted dark:border-sidebar-border"
                            >
                                <div class="flex items-center gap-2">
                                    <ClipboardList class="size-4" />
                                    <span>Original Form Data</span>
                                </div>
                                <ChevronDown
                                    :class="[
                                        'size-4 transition-transform',
                                        isFormOpen ? 'rotate-180' : '',
                                    ]"
                                />
                            </CollapsibleTrigger>
                            <CollapsibleContent
                                class="mt-3 space-y-4 rounded-lg border border-sidebar-border/70 bg-muted/30 p-4 dark:border-sidebar-border"
                            >
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div class="space-y-1">
                                        <Label
                                            class="text-xs text-muted-foreground"
                                            >Discipline</Label
                                        >
                                        <p class="text-sm">
                                            {{ formData?.discipline || 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="space-y-1">
                                        <Label
                                            class="text-xs text-muted-foreground"
                                            >Company</Label
                                        >
                                        <p class="text-sm">
                                            {{ formData?.company || 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="space-y-1">
                                        <Label
                                            class="text-xs text-muted-foreground"
                                            >Location</Label
                                        >
                                        <p class="text-sm">
                                            {{ formData?.location || 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="space-y-1">
                                        <Label
                                            class="text-xs text-muted-foreground"
                                            >Intentionality</Label
                                        >
                                        <p class="text-sm capitalize">
                                            {{
                                                formData?.intentionality ||
                                                'N/A'
                                            }}
                                        </p>
                                    </div>
                                    <div class="space-y-1">
                                        <Label
                                            class="text-xs text-muted-foreground"
                                            >Type</Label
                                        >
                                        <p class="text-sm capitalize">
                                            {{
                                                formData?.observationType ===
                                                'met'
                                                    ? 'Positive (Met)'
                                                    : 'Delta (Not Met)'
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <Separator />

                                <div class="space-y-3">
                                    <div class="space-y-1">
                                        <Label
                                            class="text-xs text-muted-foreground"
                                            >Gap</Label
                                        >
                                        <p class="text-sm">
                                            {{ formData?.gap || 'N/A' }}
                                        </p>
                                    </div>

                                    <div class="space-y-1">
                                        <Label
                                            class="text-xs text-muted-foreground"
                                            >Why</Label
                                        >
                                        <p class="text-sm">
                                            {{ formData?.whyDetails || 'N/A' }}
                                        </p>
                                    </div>

                                    <div
                                        v-if="
                                            formData?.observationType ===
                                            'not-met'
                                        "
                                        class="space-y-1"
                                    >
                                        <Label
                                            class="text-xs text-muted-foreground"
                                            >Consequence</Label
                                        >
                                        <p class="text-sm">
                                            {{ formData?.consequence || 'N/A' }}
                                        </p>
                                    </div>

                                    <div class="space-y-1">
                                        <Label
                                            class="text-xs text-muted-foreground"
                                            >Impactful Action</Label
                                        >
                                        <p class="text-sm">
                                            {{
                                                formData?.impactfulAction ||
                                                'N/A'
                                            }}
                                        </p>
                                    </div>

                                    <div class="space-y-1">
                                        <Label
                                            class="text-xs text-muted-foreground"
                                            >Peer to Peer</Label
                                        >
                                        <p class="text-sm">
                                            {{ formData?.peerToPeer || 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </CollapsibleContent>
                        </Collapsible>

                        <!-- Observation Content - Document Style -->
                        <div
                            class="rounded-lg border border-sidebar-border/70 bg-background p-8 dark:border-sidebar-border"
                        >
                            <!-- Loading State -->
                            <div
                                v-if="isGenerating"
                                class="animate-pulse space-y-4"
                            >
                                <div class="h-4 w-3/4 rounded bg-muted"></div>
                                <div class="h-4 w-full rounded bg-muted"></div>
                                <div class="h-4 w-5/6 rounded bg-muted"></div>
                                <div class="h-4 w-full rounded bg-muted"></div>
                                <div class="h-4 w-4/5 rounded bg-muted"></div>
                                <div class="h-4 w-full rounded bg-muted"></div>
                                <div class="pt-4">
                                    <p
                                        class="flex items-center gap-2 text-sm text-muted-foreground"
                                    >
                                        <Loader2 class="size-4 animate-spin" />
                                        AI is generating your observation...
                                    </p>
                                </div>
                            </div>

                            <!-- Actual Content -->
                            <div
                                v-else
                                class="prose prose-sm dark:prose-invert max-w-none"
                                v-html="
                                    formattedObservation.replace(
                                        /\*\*(.*?)\*\*/g,
                                        '<strong>$1</strong>',
                                    )
                                "
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- Refinement Panel (Slides up from bottom) -->
                <Transition
                    enter-active-class="transition-all duration-300 ease-out"
                    enter-from-class="translate-y-full opacity-0"
                    enter-to-class="translate-y-0 opacity-100"
                    leave-active-class="transition-all duration-300 ease-in"
                    leave-from-class="translate-y-0 opacity-100"
                    leave-to-class="translate-y-full opacity-0"
                >
                    <div
                        v-if="showRefinementPanel"
                        class="border-t border-sidebar-border/70 bg-muted/30 p-6 dark:border-sidebar-border"
                    >
                        <div class="mx-auto max-w-4xl">
                            <div class="mb-4 flex items-center justify-between">
                                <h3 class="font-semibold">Request Changes</h3>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    @click="handleRequestChanges"
                                >
                                    <X class="size-4" />
                                </Button>
                            </div>
                            <form
                                @submit.prevent="handleSubmitRefinement"
                                class="space-y-4"
                            >
                                <Textarea
                                    v-model="refinementRequest"
                                    placeholder="Describe the changes you'd like to make to this observation..."
                                    class="min-h-32 resize-none"
                                    autofocus
                                />
                                <div
                                    class="flex items-center justify-end gap-2"
                                >
                                    <Button
                                        type="button"
                                        variant="outline"
                                        @click="handleRequestChanges"
                                    >
                                        Cancel
                                    </Button>
                                    <Button
                                        type="submit"
                                        :disabled="!refinementRequest.trim()"
                                    >
                                        <Edit3 class="mr-2 size-4" />
                                        Submit Request
                                    </Button>
                                </div>
                            </form>
                            <p class="mt-2 text-xs text-muted-foreground">
                                AI will update the observation based on your
                                request
                            </p>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>
    </AppLayout>
</template>
