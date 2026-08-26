<script setup lang="ts">
import ScrollReveal from '@/components/app/AppScrollReveal.vue'
import { Badge } from '@/components/ui/badge'
import { Card, CardHeader, CardContent } from '@/components/ui/card'
import { Crown, Eye, Check, Lock, Mail } from '@lucide/vue'

const ownerPowers = [
    'Add, remove, and reorder members',
    'Log or undo a contribution',
    'Activate, disburse, and complete cycles',
    'Approve, reject, or revoke view requests',
]

const viewerPowers = [
    'See members, contributions, and cycle status',
    'Watch the full activity log in real time',
    'Leave the shared group anytime',
]
</script>

<template>
    <section id="roles" class="py-16 sm:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <ScrollReveal class="mx-auto max-w-2xl text-center">
                <h2 class="font-heading text-3xl font-semibold text-balance text-foreground sm:text-4xl">One owner in
                    control. Total transparency for everyone else.</h2>
                <p class="mt-4 text-lg text-muted-foreground text-balance">
                    Puyo isn't a group chat where everyone pokes at the same data. It's your ledger — with an
                    optional, read-only window for anyone you trust.
                </p>
            </ScrollReveal>

            <div class="mt-12 grid gap-6 md:grid-cols-2">
                <ScrollReveal>
                    <Card class="h-full border-primary/25 bg-primary/5">
                        <CardHeader class="flex-row items-center gap-3 space-y-0">
                            <div
                                class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/15 text-primary">
                                <Crown class="size-5" />
                            </div>
                            <div class="flex min-w-0 flex-col gap-1">
                                <span class="font-heading text-lg font-semibold text-foreground">Group Owner</span>
                                <span class="text-xs text-muted-foreground">That's you — the organizer</span>
                            </div>
                            <Badge class="ml-auto shrink-0">Full control</Badge>
                        </CardHeader>
                        <CardContent>
                            <ul class="space-y-2.5">
                                <li v-for="power in ownerPowers" :key="power"
                                    class="flex items-start gap-2 text-sm text-foreground/90">
                                    <Check class="mt-0.5 size-4 shrink-0 text-primary" />
                                    {{ power }}
                                </li>
                            </ul>
                        </CardContent>
                    </Card>
                </ScrollReveal>

                <ScrollReveal :delay="120">
                    <Card class="h-full">
                        <CardHeader class="flex-row items-center gap-3 space-y-0">
                            <div
                                class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-muted text-muted-foreground">
                                <Eye class="size-5" />
                            </div>
                            <div class="flex min-w-0 flex-col gap-1">
                                <span class="font-heading text-lg font-semibold text-foreground">Invited Viewer</span>
                                <span class="text-xs text-muted-foreground">Optional — approved via invite code</span>
                            </div>
                            <Badge variant="secondary" class="ml-auto shrink-0">Read-only</Badge>
                        </CardHeader>
                        <CardContent class="flex flex-col gap-4">
                            <ul class="space-y-2.5">
                                <li v-for="power in viewerPowers" :key="power"
                                    class="flex items-start gap-2 text-sm text-foreground/90">
                                    <Check class="mt-0.5 size-4 shrink-0 text-primary" />
                                    {{ power }}
                                </li>
                            </ul>
                            <span
                                class="inline-flex w-fit items-center gap-1.5 rounded-full border border-border bg-muted/60 px-2.5 py-1 text-[11px] font-medium text-muted-foreground">
                                <Lock class="size-3" />
                                Can never add, edit, or delete anything
                            </span>
                        </CardContent>
                    </Card>
                </ScrollReveal>
            </div>

            <ScrollReveal :delay="200"
                class="mt-6 flex flex-col items-start gap-3 rounded-2xl border border-dashed border-border bg-muted/30 p-5 sm:flex-row sm:items-center">
                <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-primary/15 text-primary">
                    <Mail class="size-4.5" />
                </div>
                <p class="text-sm text-muted-foreground">
                    <span class="font-medium text-foreground">Members don't need accounts.</span> — Just add names and
                    optional emails. Puyo handles payment reminders automatically so you don't have to chase anyone or
                    force them to download an app.
                </p>
            </ScrollReveal>
        </div>
    </section>
</template>
