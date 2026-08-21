<script setup lang="ts">
import ScrollReveal from '@/components/app/AppScrollReveal.vue'
import PuyoHowItWorksImage from '@/assets/puyo_how_it_works.webp'
const steps = [
    {
        title: 'Create a group',
        description: 'Name your group and set the contribution amount and schedule — daily, weekly, or monthly.',
    },
    {
        title: 'Add your members',
        description: 'Add everyone by hand, or share an invite code so members can join themselves.',
    },
    {
        title: 'Set the payout order',
        description: 'Arrange who takes the pot each cycle — drag to reorder any time before the group starts.',
    },
    {
        title: 'Track every cycle',
        description: 'Log contributions as they come in and watch the group move through each cycle automatically.',
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
                        <p class="mt-4 max-w-lg text-lg text-muted-foreground">From a blank slate to a running group
                            in about a minute.</p>
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

                <ScrollReveal :delay="200" class="mx-auto lg:mx-0 lg:mt-24">
                    <img :src="PuyoHowItWorksImage" class="w-100" />
                </ScrollReveal>
            </div>
        </div>
    </section>
</template>