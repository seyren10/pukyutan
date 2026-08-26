<script setup lang="ts">
import { Button } from '@/components/ui/button'
import ScrollReveal from '@/components/app/AppScrollReveal.vue'
import { ArrowRight } from '@lucide/vue'
</script>

<template>
    <section class="py-16 sm:py-24">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <ScrollReveal>
                <div
                    class="relative overflow-hidden rounded-3xl border border-primary/20 bg-primary/10 px-6 py-12 sm:px-12">
                    <svg class="pointer-events-none absolute inset-0 h-full w-full text-primary/10" aria-hidden="true">
                        <defs>
                            <pattern id="honeycomb" width="32" height="18.5" patternUnits="userSpaceOnUse"
                                patternTransform="scale(1.4)">
                                <path d="M8 0 L24 0 L32 9.25 L24 18.5 L8 18.5 L0 9.25 Z" fill="none"
                                    stroke="currentColor" stroke-width="1" />
                            </pattern>
                        </defs>
                        <rect width="100%" height="100%" fill="url(#honeycomb)" />
                    </svg>

                    <div class="relative">
                        <h2 class="font-heading text-3xl font-semibold text-foreground sm:text-4xl">Ready to
                            organize your group?</h2>
                        <p class="mx-auto mt-3 max-w-md text-muted-foreground">Set it up in a minute. Zero sign-ups
                            required for your members.</p>
                        <Button as-child size="lg" class="mt-6">
                            <RouterLink :to="{ name: 'register' }">
                                Create your group
                                <ArrowRight data-icon="inline-end" />
                            </RouterLink>
                        </Button>
                    </div>
                </div>
            </ScrollReveal>
        </div>
    </section>
</template>