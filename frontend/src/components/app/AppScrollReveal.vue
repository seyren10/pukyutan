<script setup lang="ts">
import { onMounted, onUnmounted, ref, useTemplateRef } from 'vue'

const { delay = 0, y = 24 } = defineProps<{
    /** Extra delay in ms before the reveal starts — used to stagger siblings. */
    delay?: number
    /** Starting vertical offset in px. */
    y?: number
}>()

const el = useTemplateRef<HTMLDivElement>('el')
const visible = ref(false)
let observer: IntersectionObserver | null = null

onMounted(() => {
    if (!el.value) return

    observer = new IntersectionObserver(([entry]) => {
        if (!entry.isIntersecting) return
        setTimeout(() => (visible.value = true), delay)
        observer?.disconnect()
    }, { threshold: 0.15 })

    observer.observe(el.value)
})

onUnmounted(() => observer?.disconnect())
</script>

<template>
    <div ref="el" class="transition-all duration-700 ease-out"
        :class="visible ? 'translate-y-0 opacity-100' : 'opacity-0'"
        :style="!visible ? { transform: `translateY(${y}px)` } : undefined">
        <slot />
    </div>
</template>