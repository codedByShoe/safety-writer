<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import InputError from '@/components/ui/input-group/InputError.vue';
import Label from '@/components/ui/label/Label.vue';
import RadioGroup from '@/components/ui/radio-group/RadioGroup.vue';
import RadioGroupItem from '@/components/ui/radio-group/RadioGroupItem.vue';
import { Separator } from '@/components/ui/separator';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { store } from '@/actions/App/Http/Controllers/ObservationController';
import { observation } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Observation',
        href: observation().url,
    },
];

// Form data using Inertia's useForm
const form = useForm({
    discipline: '',
    company: '',
    intentionality: 'intentional',
    observationType: 'met',
    location: '',
    gap: '',
    whyDetails: '',
    consequence: '',
    impactfulAction: '',
    peerToPeer: '',
});

const handleSubmit = () => {
    form.post(store.url(), {
        preserveScroll: true,
        onError: () => {
            toast.error('Please fix the validation errors and try again.');
        },
    });
};

const handleClearForm = () => {
    form.reset();
};
</script>

<template>
    <Head title="Observation" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div
                class="relative flex min-h-[100vh] flex-1 flex-col rounded-xl border border-sidebar-border/70 bg-background md:min-h-min dark:border-sidebar-border"
            >
                <!-- Observation Header -->
                <div
                    class="border-b border-sidebar-border/70 px-6 py-4 dark:border-sidebar-border"
                >
                    <h2 class="text-lg font-semibold">
                        Safety Observation Form
                    </h2>
                    <p class="text-sm text-muted-foreground">
                        Document safety observations
                    </p>
                </div>

                <!-- Observation Form -->
                <div class="flex-1 overflow-y-auto p-6">
                    <form
                        @submit.prevent="handleSubmit"
                        class="mx-auto max-w-4xl space-y-8"
                    >
                        <!-- Observer Information Section -->
                        <div class="space-y-8">
                            <div>
                                <h3 class="text-base font-semibold">
                                    Observer Information
                                </h3>
                                <p class="text-sm text-muted-foreground">
                                    Information about the observation context
                                </p>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="discipline">Discipline</Label>
                                    <Input
                                        id="discipline"
                                        v-model="form.discipline"
                                        placeholder="e.g., Maintenance, Operations"
                                        :class="{ 'border-destructive': form.errors.discipline }"
                                    />
                                    <InputError :message="form.errors.discipline" />
                                </div>

                                <div class="space-y-2">
                                    <Label for="company">Company</Label>
                                    <Input
                                        id="company"
                                        v-model="form.company"
                                        placeholder="Company name"
                                        :class="{ 'border-destructive': form.errors.company }"
                                    />
                                    <InputError :message="form.errors.company" />
                                </div>
                            </div>
                            <div class="space-y-2">
                                <Label for="location">Location</Label>
                                <Input
                                    id="location"
                                    v-model="form.location"
                                    placeholder="Where did the observation take place?"
                                    :class="{ 'border-destructive': form.errors.location }"
                                />
                                <InputError :message="form.errors.location" />
                            </div>
                        </div>

                        <Separator />

                        <!-- Observation Details Section -->
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-base font-semibold">
                                    Observation Details
                                </h3>
                                <p class="text-sm text-muted-foreground">
                                    Type and nature of observation
                                </p>
                            </div>

                            <div class="grid gap-6 md:grid-cols-2">
                                <!-- Intentionality -->
                                <div class="space-y-3">
                                    <Label>Intentionality</Label>
                                    <RadioGroup
                                        v-model="form.intentionality"
                                        class="flex gap-4"
                                    >
                                        <div class="flex items-center gap-2">
                                            <RadioGroupItem
                                                id="intentional"
                                                value="intentional"
                                            />
                                            <Label
                                                for="intentional"
                                                class="cursor-pointer font-normal"
                                            >
                                                Intentional
                                            </Label>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <RadioGroupItem
                                                id="convenience"
                                                value="convenience"
                                            />
                                            <Label
                                                for="convenience"
                                                class="cursor-pointer font-normal"
                                            >
                                                Convenience
                                            </Label>
                                        </div>
                                    </RadioGroup>
                                </div>

                                <!-- Observation Type -->
                                <div class="space-y-3">
                                    <Label>Observation Type</Label>
                                    <RadioGroup
                                        v-model="form.observationType"
                                        class="flex gap-4"
                                    >
                                        <div class="flex items-center gap-2">
                                            <RadioGroupItem
                                                id="met"
                                                value="met"
                                            />
                                            <Label
                                                for="met"
                                                class="cursor-pointer font-normal"
                                            >
                                                Positive (Met)
                                            </Label>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <RadioGroupItem
                                                id="not-met"
                                                value="not-met"
                                            />
                                            <Label
                                                for="not-met"
                                                class="cursor-pointer font-normal"
                                            >
                                                Delta (Not Met)
                                            </Label>
                                        </div>
                                    </RadioGroup>
                                </div>
                            </div>
                        </div>

                        <Separator />

                        <!-- Gap Section -->
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-base font-semibold">Gap</h3>
                                <p class="text-sm text-muted-foreground">
                                    What behavior or standard was observed?
                                    Include activity details.
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="gap"
                                    >Describe what was observed</Label
                                >
                                <Textarea
                                    id="gap"
                                    v-model="form.gap"
                                    placeholder="Describe the behavior or standard that was met or not met, and what activity was being performed..."
                                    class="min-h-32"
                                    :class="{ 'border-destructive': form.errors.gap }"
                                />
                                <InputError :message="form.errors.gap" />
                            </div>
                        </div>

                        <Separator />

                        <!-- Why Section -->
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-base font-semibold">Why</h3>
                                <p class="text-sm text-muted-foreground">
                                    Why was the behavior/standard met or not
                                    met?
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="whyDetails">Explain why</Label>
                                <Textarea
                                    id="whyDetails"
                                    v-model="form.whyDetails"
                                    placeholder="Explain why the behavior or standard was met or not met. What did the individual say when asked?"
                                    class="min-h-32"
                                    :class="{ 'border-destructive': form.errors.whyDetails }"
                                />
                                <InputError :message="form.errors.whyDetails" />
                            </div>
                        </div>

                        <Separator />

                        <!-- Consequence Section (for Not Met) -->
                        <div
                            v-if="form.observationType === 'not-met'"
                            class="space-y-4"
                        >
                            <div>
                                <h3 class="text-base font-semibold">
                                    Consequence
                                </h3>
                                <p class="text-sm text-muted-foreground">
                                    What could happen as a result of this
                                    behavior?
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="consequence"
                                    >Describe the consequence</Label
                                >
                                <Textarea
                                    id="consequence"
                                    v-model="form.consequence"
                                    placeholder="What are the potential consequences of this behavior or standard not being met?"
                                    class="min-h-24"
                                    :class="{ 'border-destructive': form.errors.consequence }"
                                />
                                <InputError :message="form.errors.consequence" />
                            </div>
                        </div>

                        <Separator
                            v-if="form.observationType === 'not-met'"
                        />

                        <!-- Impactful Action Section -->
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-base font-semibold">
                                    Impactful Action
                                </h3>
                                <p class="text-sm text-muted-foreground">
                                    How was the condition addressed? What
                                    feedback was provided?
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="impactfulAction"
                                    >Action taken</Label
                                >
                                <Textarea
                                    id="impactfulAction"
                                    v-model="form.impactfulAction"
                                    placeholder="Describe the action taken to reinforce good performance or correct performance gaps..."
                                    class="min-h-32"
                                    :class="{ 'border-destructive': form.errors.impactfulAction }"
                                />
                                <InputError :message="form.errors.impactfulAction" />
                            </div>
                        </div>

                        <Separator />

                        <!-- Peer to Peer Section -->
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-base font-semibold">
                                    Peer to Peer Coaching
                                </h3>
                                <p class="text-sm text-muted-foreground">
                                    Did workers demonstrate peer coaching or was
                                    there a missed opportunity?
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="peerToPeer"
                                    >Peer coaching details</Label
                                >
                                <Textarea
                                    id="peerToPeer"
                                    v-model="form.peerToPeer"
                                    placeholder="Describe peer coaching that occurred or missed opportunities..."
                                    class="min-h-24"
                                    :class="{ 'border-destructive': form.errors.peerToPeer }"
                                />
                                <InputError :message="form.errors.peerToPeer" />
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div
                            class="flex items-center justify-end gap-4 border-t border-sidebar-border/70 pt-6 dark:border-sidebar-border"
                        >
                            <Button
                                type="button"
                                variant="outline"
                                @click="handleClearForm"
                                :disabled="form.processing"
                            >
                                Clear Form
                            </Button>
                            <Button type="submit" :disabled="form.processing">
                                {{
                                    form.processing
                                        ? 'Generating...'
                                        : 'Generate Observation'
                                }}
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
