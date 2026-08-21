<script setup lang="ts">
import { Button } from '@/components/ui/button'
import BlurText from '@/components/bits/BlurText.vue'
import { ArrowRight } from '@lucide/vue'
import PuyoHeroImage from '@/assets/puyo_splash.webp'
import GroupShotLightImage from '@/assets/group_shot_light.webp'
import GroupShotDarkImage from '@/assets/group_shot_dark.webp'
import { useColorMode } from '@vueuse/core'

const { state } = useColorMode()
</script>

<template>
    <section class="relative overflow-hidden">
        <!-- Soft honey-toned glow, not a generic blue SaaS gradient -->
        <div class="pointer-events-none absolute inset-x-0 -top-40 -z-10 flex justify-center blur-3xl"
            aria-hidden="true">
            <div class="aspect-square w-160 rounded-full bg-primary/25" />
        </div>

        <div class="mx-auto grid max-w-6xl gap-12 px-4 py-16 sm:px-6 md:py-24 lg:grid-cols-2 lg:items-center lg:px-8">
            <div class="flex flex-col items-start gap-6">
                <img :src="PuyoHeroImage" class="w-40 pointer-events-none select-none" />

                <BlurText text="Your paluwagan, finally organized." :delay="80" tag="h1"
                    class="font-heading text-4xl leading-tight font-semibold text-balance text-foreground sm:text-5xl" />

                <p class="max-w-lg text-lg text-muted-foreground text-balance">
                    Puyo keeps your group's contributions, payout order, and cycles in one place — no more spreadsheets,
                    group chats, or arguments over whose turn it is.
                </p>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <Button as-child size="lg">
                        <RouterLink :to="{ name: 'register' }">
                            Start your first group
                            <ArrowRight data-icon="inline-end" />
                        </RouterLink>
                    </Button>
                    <Button as-child size="lg" variant="outline">
                        <a href="#how-it-works">See how it works</a>
                    </Button>
                </div>
            </div>

            <div class="flex justify-center lg:justify-end">
                <img :src="state === 'light' ? GroupShotLightImage : GroupShotDarkImage"
                    class="pointer-events-none select-none" />
            </div>
        </div>
    </section>
</template>