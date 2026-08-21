<script setup lang="ts">
import ScrollReveal from '@/components/app/AppScrollReveal.vue'
import LandingFaqItem from './LandingFaqItem.vue'
import PuyoFaqImage from '@/assets/puyo_faq.webp'

const faqs = [
    {
        question: 'Is Puyo free to use?',
        answer: 'Yes — Puyo is free for you and your group.',
    },
    {
        question: "What happens if someone misses a contribution?",
        answer: "The group's activity feed shows exactly who's contributed and who hasn't for the current cycle, so it's visible to everyone instead of being one person's word against another's.",
    },
    {
        question: 'Can all members see the group, or just the owner?',
        answer: "The group owner manages members and settings, but anyone you invite can view the group's status — contributions, cycle progress, and payout order — without needing edit access.",
    },
    {
        question: 'Is my data private?',
        answer: "Only people you've explicitly invited to a group can see it. Your other groups stay invisible to them.",
    },
    {
        question: 'Can I change the payout order after the group starts?',
        answer: "The order is meant to be set before the group goes active, since changing it mid-cycle is exactly the kind of dispute Puyo is built to avoid. You can freely reorder members while the group is still in draft.",
    },
]
</script>

<template>
    <section id="faq" class="bg-secondary/40 py-16 sm:py-24">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <ScrollReveal class="flex flex-col items-center gap-4 text-center">
                <img :src="PuyoFaqImage" class="w-28" />
                <h2 class="font-heading text-3xl font-semibold text-foreground sm:text-4xl">Frequently asked
                    questions</h2>
            </ScrollReveal>

            <div class="mt-10 space-y-3">
                <ScrollReveal v-for="(faq, i) in faqs" :key="faq.question" :delay="i * 60">
                    <LandingFaqItem :question="faq.question">
                        {{ faq.answer }}
                    </LandingFaqItem>
                </ScrollReveal>
            </div>
        </div>
    </section>
</template>