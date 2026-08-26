<script setup lang="ts">
import ScrollReveal from '@/components/app/AppScrollReveal.vue'
import { Users, Mail, Eye, Activity } from '@lucide/vue'

const listFeatures = [
    {
        icon: Users,
        title: 'Member management',
        description: 'Add members by name, no accounts required, and reorder the payout line whenever you need to.',
    },
    {
        icon: Mail,
        title: 'Automatic email reminders',
        description: "As a due date nears, members with an email on file get reminded automatically — no app, no login, on their end.",
    },
    {
        icon: Eye,
        title: 'Read-only sharing',
        description: 'Approve a view request from your invite code and someone gets a live look at the books — never edit access.',
    },
    {
        icon: Activity,
        title: 'Activity log & PDF ledgers',
        description: 'Every action is timestamped and logged, and each member gets a downloadable, printable ledger.',
    },
]

const wheel = [
    { initials: 'AN', label: 'Aling Nena', x: 100, y: 28 },
    { initials: 'KJ', label: 'Kuya Jun', x: 162, y: 64 },
    { initials: 'AR', label: 'Ate Rose', x: 162, y: 136, next: true },
    { initials: 'TB', label: 'Tito Boy', x: 100, y: 172 },
    { initials: 'MD', label: 'Mang Dodong', x: 38, y: 136 },
    { initials: '+3', label: 'More members', x: 38, y: 64 },
]
</script>

<template>
    <section id="features" class="py-16 sm:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <ScrollReveal class="mx-auto max-w-2xl text-center">
                <h2 class="font-heading text-3xl font-semibold text-foreground sm:text-4xl">Everything the organizer
                    needs, nothing anyone else can mess with</h2>
                <p class="mt-4 text-lg text-muted-foreground">Built around how paluwagan groups actually run —
                    not a generic budgeting app bent into shape.</p>
            </ScrollReveal>

            <div class="mt-12 grid gap-6 lg:grid-cols-5 lg:items-stretch">
                <!-- Spotlight: the mechanic that actually differentiates Puyo -->
                <ScrollReveal class="lg:col-span-3">
                    <div
                        class="flex h-full flex-col gap-6 rounded-2xl border border-border bg-card p-6 sm:p-8 lg:flex-row lg:items-center">
                        <svg viewBox="0 0 200 200" class="h-48 w-48 shrink-0 sm:h-56 sm:w-56" aria-hidden="true">
                            <circle cx="100" cy="100" r="72" fill="none" class="stroke-border" stroke-width="1.5"
                                stroke-dasharray="3 5" />
                            <path d="M162 64 Q198 100 162 136" fill="none" class="stroke-primary" stroke-width="2.5"
                                stroke-linecap="round" marker-end="url(#arrow)" />
                            <defs>
                                <marker id="arrow" markerWidth="8" markerHeight="8" refX="4" refY="4"
                                    orient="auto-start-reverse">
                                    <path d="M0 0 L8 4 L0 8 Z" class="fill-primary" />
                                </marker>
                            </defs>
                            <g v-for="node in wheel" :key="node.initials">
                                <circle :cx="node.x" :cy="node.y" r="18"
                                    :class="node.next ? 'fill-primary' : 'fill-muted'" />
                                <circle v-if="node.next" :cx="node.x" :cy="node.y" r="22" fill="none"
                                    class="stroke-primary/40" stroke-width="2" />
                                <text :x="node.x" :y="node.y + 4" text-anchor="middle"
                                    class="font-heading text-[11px] font-semibold"
                                    :class="node.next ? 'fill-primary-foreground' : 'fill-muted-foreground'">
                                    {{ node.initials }}
                                </text>
                            </g>
                            <text x="100" y="98" text-anchor="middle"
                                class="fill-muted-foreground text-[9px] uppercase tracking-wide">Cycle</text>
                            <text x="100" y="112" text-anchor="middle"
                                class="font-heading fill-foreground text-[13px] font-semibold">4 of 8</text>
                        </svg>

                        <div>
                            <h3 class="font-heading text-xl font-semibold text-foreground">Automatic payout order
                            </h3>
                            <p class="mt-2 max-w-sm text-sm text-muted-foreground">
                                Set the order once — Puyo tracks whose turn is next, so nobody has to remember,
                                screenshot the list, or argue about it two cycles from now.
                            </p>
                            <p class="mt-3 text-xs font-medium text-primary">
                                Next up: Ate Rose, this Friday
                            </p>
                        </div>
                    </div>
                </ScrollReveal>

                <!-- Everything else: a compact list, not a wall of matching cards -->
                <ScrollReveal :delay="100" class="lg:col-span-2">
                    <div class="h-full divide-y divide-border rounded-2xl border border-border bg-card px-2">
                        <div v-for="feature in listFeatures" :key="feature.title"
                            class="flex items-start gap-3.5 px-4 py-4 transition-colors hover:bg-muted/40">
                            <div
                                class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-lg bg-primary/15 text-primary">
                                <component :is="feature.icon" class="size-4" />
                            </div>
                            <div>
                                <h3 class="font-heading text-sm font-semibold text-foreground">{{ feature.title }}
                                </h3>
                                <p class="mt-1 text-sm text-muted-foreground">{{ feature.description }}</p>
                            </div>
                        </div>
                    </div>
                </ScrollReveal>
            </div>
        </div>
    </section>
</template>
