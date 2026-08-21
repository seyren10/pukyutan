<script setup lang="ts">
import ScrollReveal from '@/components/app/AppScrollReveal.vue'
import { MessageSquareWarning, ShuffleIcon, EyeOff } from '@lucide/vue'

const messages = [
    {
        sender: 'Kuya Jun',
        time: '11:42 PM',
        text: "anyone have the updated list? di ko na masundan dito sa chat 😅",
        icon: MessageSquareWarning,
        tag: 'Buried in the scroll',
    },
    {
        sender: 'Tito Boy',
        time: '11:47 PM',
        text: "sabi ni Ate Rose sya daw sunod, tapos si Mang Dodong naman? alin na ba talaga",
        icon: ShuffleIcon,
        tag: 'Order disputed',
    },
    {
        sender: 'Aling Nena',
        time: '11:51 PM',
        text: "so who's actually paid this week na?? 👀",
        icon: EyeOff,
        tag: "No one's sure",
    },
]
</script>

<template>
    <section class="bg-secondary/40 py-16 sm:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-[1.1fr_1fr] lg:items-center lg:gap-16">
                <ScrollReveal>
                    <h2 class="font-heading text-3xl font-semibold text-balance text-foreground sm:text-4xl">
                        A paluwagan runs on trust. Puyo runs on the trust you already have.
                    </h2>
                    <p class="mt-4 max-w-lg text-lg text-muted-foreground text-balance">
                        A paluwagan (or ROSCA — rotating savings and credit association) is one of the oldest ways
                        communities save together: everyone contributes on a schedule, and each cycle one member
                        takes the full pot. It works because people trust each other — right up until the group
                        chat scrolls past the thing everyone needed to see.
                    </p>
                </ScrollReveal>

                <ScrollReveal :delay="120">
                    <div class="mx-auto w-full max-w-sm overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
                        <div class="flex items-center gap-2.5 border-b border-border bg-muted/50 px-4 py-3">
                            <div
                                class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary/15 text-xs font-semibold text-primary">
                                BS
                            </div>
                            <div class="flex min-w-0 flex-col">
                                <span class="truncate text-sm font-medium text-foreground">Barangay Savers</span>
                                <span class="text-[11px] text-muted-foreground">12 members · group chat</span>
                            </div>
                        </div>

                        <div class="divide-y divide-border/70">
                            <div v-for="msg in messages" :key="msg.sender + msg.time"
                                class="flex flex-col gap-1.5 px-4 py-3">
                                <div class="flex items-baseline justify-between gap-2">
                                    <span class="text-xs font-medium text-foreground">{{ msg.sender }}</span>
                                    <span class="shrink-0 text-[11px] text-muted-foreground">{{ msg.time }}</span>
                                </div>
                                <p class="text-sm text-muted-foreground">{{ msg.text }}</p>
                                <span
                                    class="inline-flex w-fit items-center gap-1 rounded-full bg-destructive/10 px-2 py-0.5 text-[11px] font-medium text-destructive">
                                    <component :is="msg.icon" class="size-3" />
                                    {{ msg.tag }}
                                </span>
                            </div>
                        </div>

                        <div class="border-t border-border bg-muted/30 px-4 py-2.5 text-[11px] text-muted-foreground italic">
                            Someone is typing…
                        </div>
                    </div>
                </ScrollReveal>
            </div>
        </div>
    </section>
</template>
