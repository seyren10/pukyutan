<script setup lang="ts">
import { computed } from 'vue'
import { Button } from '@/components/ui/button'
import { ButtonGroup } from '@/components/ui/button-group'
import { ChevronLeft, ChevronRight, MoreHorizontal } from '@lucide/vue'
import type { PaginationMeta } from '@/types/paginate'

const props = defineProps<{
    meta: PaginationMeta
}>()

const page = defineModel<number | null>({
    default: 1
})

// Laravel's own links array already includes "&laquo; Previous" and
// "Next &raquo;" as the first/last entries — pull those out so they
// can be rendered as icon buttons instead of raw HTML-entity text,
// and keep the rest (numbers + "...") as the middle group.
const previousLink = computed(() => props.meta.links[0])
const nextLink = computed(() => props.meta.links[props.meta.links.length - 1])
const pageLinks = computed(() => props.meta.links.slice(1, -1))

</script>

<template>
    <div v-if="meta.last_page > 1" class="flex flex-col items-center gap-3 sm:flex-row sm:justify-between">
        <p v-if="meta.total > 0" class="text-sm text-muted-foreground">
            Showing {{ meta.from }}–{{ meta.to }} of {{ meta.total.toLocaleString() }}
        </p>

        <!-- Full control, sm and up -->
        <ButtonGroup class="hidden sm:inline-flex">
            <Button variant="outline" size="icon" :disabled="previousLink?.url === null"
                @click="page = previousLink?.page ?? null">
                <ChevronLeft />
            </Button>

            <template v-for="(link, index) in pageLinks" :key="index">
                <Button v-if="link.label === '...'" variant="outline" size="icon" disabled class="pointer-events-none">
                    <MoreHorizontal class="size-4" />
                </Button>
                <Button v-else :variant="link.active ? 'default' : 'outline'" size="icon" :disabled="link.url === null"
                    @click="page = link.page ?? null">
                    {{ link.label }}
                </Button>
            </template>

            <Button variant="outline" size="icon" :disabled="nextLink?.url === null"
                @click="page = nextLink?.page ?? null">
                <ChevronRight />
            </Button>
        </ButtonGroup>

        <!-- Compact fallback, below sm — full number strip doesn't fit -->
        <ButtonGroup class="sm:hidden">
            <Button variant="outline" size="icon" :disabled="previousLink?.url === null"
                @click="page = previousLink?.page ?? null">
                <ChevronLeft />
            </Button>
            <Button variant="outline" size="icon" disabled class="pointer-events-none font-mono">
                {{ meta.current_page }} / {{ meta.last_page }}
            </Button>
            <Button variant="outline" size="icon" :disabled="nextLink?.url === null"
                @click="page = nextLink?.page ?? null">
                <ChevronRight />
            </Button>
        </ButtonGroup>
    </div>
</template>