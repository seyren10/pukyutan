<script setup lang="ts">
import ScrollReveal from '@/components/app/AppScrollReveal.vue'
import PuyoHowItWorksImage from '@/assets/puyo_how_it_works.webp'
const steps = [
    {
        title: 'Sign up',
        description: 'Create an account with email or Google. You\'re the only one who needs one.',
    },
    {
        title: 'Create a group',
        description: 'Set your group name, contribution amount, and schedule.',
    },
    {
        title: 'Add your members',
        description: 'Add names (and optional emails). Members don\'t need to log in or create accounts.',
    },
    {
        title: 'Set the payout order',
        description: 'Arrange the sequence for who receives the pot.',
    },
    {
        title: 'Activate and track cycles',
        description: 'Log payments, add optional notes and trigger disbursements. Cycles roll over automatically.',
    },
    {
        title: 'Invite trusted eyes (optional)',
        description: 'Invite co-organizers or members to view live progress with read-only access.',
    },
]
</script>

<template>
    <section id="how-it-works" class="py-16 sm:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-[1fr_auto] lg:items-start lg:gap-16">
                <div>
                    <ScrollReveal>
                        <h2 class="font-heading text-3xl font-semibold text-foreground sm:text-4xl">How it works</h2>
                        <p class="mt-4 max-w-lg text-lg text-muted-foreground">Set up and run a savings group in minutes—no sign-ups required for members.</p>
                    </ScrollReveal>

                    <ol class="relative mt-10 space-y-8">
                        <div class="absolute top-1 bottom-1 left-[17px] w-px bg-border" aria-hidden="true" />
                        <ScrollReveal v-for="(step, i) in steps" :key="step.title" :delay="i * 100">
                            <li class="relative flex gap-4">
                                <span
                                    class="relative flex size-9 shrink-0 items-center justify-center rounded-full bg-primary font-heading text-sm font-semibold text-primary-foreground">
                                    {{ i + 1 }}
                                </span>
                                <div>
                                    <h3 class="font-heading text-lg font-semibold text-foreground">{{ step.title }}
                                    </h3>
                                    <p class="mt-1 text-sm text-muted-foreground">{{ step.description }}</p>
                                </div>
                            </li>
                        </ScrollReveal>
                    </ol>
                </div>

                <ScrollReveal :delay="200" class="self-center lg:mx-0 lg:mt-24">
                    <img :src="PuyoHowItWorksImage" class="w-100" />
                </ScrollReveal>
            </div>
        </div>
    </section>
</template>