<script setup lang="ts">
import ScrollReveal from '@/components/app/AppScrollReveal.vue'
import LandingFaqItem from './LandingFaqItem.vue'
import PuyoFaqImage from '@/assets/puyo_faq.webp'

const faqs = [
    {
        question: 'Do my group members need to use the app or create an account?',
        answer: "No. Members are just records you manage — names on your list. Only you, the organizer, need an account, unless someone wants to log in purely to view the group's records.",
    },
    {
        question: 'Can a member log in and see their own contributions?',
        answer: "Not as a \"member\" account directly — but if they register or sign in as a Puyo user and you approve their view-access request using your group's invite code, they'll be able to see the full group ledger, including their own contribution history.",
    },
    {
        question: 'Can someone with view access edit anything — add a contribution, change the order?',
        answer: 'No. View access is strictly read-only. Only the group owner can add, edit, or delete members, log or undo contributions, reorder the payout line, or disburse a cycle.',
    },
    {
        question: "Can I revoke someone's access later?",
        answer: 'Yes, anytime — from your dashboard you can see and manage every pending and accepted view request across all your groups.',
    },
    {
        question: 'Can I remove a contribution I entered by mistake?',
        answer: "Yes, but only for the current, still-open cycle — once a cycle is disbursed or a new one starts, past entries lock so the ledger stays reliable.",
    },
    {
        question: 'Will members get reminded to pay without signing in?',
        answer: "Yes. If you've added an email for a member, Puyo automatically emails them a reminder as their contribution due date approaches — no account or login required on their end.",
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