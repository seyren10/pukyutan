<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogTrigger } from '@/components/ui/dialog'
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetDescription, SheetTrigger } from '@/components/ui/sheet'
import { useIsMobile } from '@/composables/use-is-mobile'
import { cn } from '@/lib/utils'

/**
 * A form/detail dialog that adapts to touch context instead of just scaling
 * down: a centered modal on desktop, a bottom sheet on mobile. Bottom
 * sheets keep the drag handle and primary content within thumb reach and
 * feel native on a phone, where a small centered modal doesn't — see
 * adapt.md's "bottom sheets instead of dropdowns/dialogs" guidance.
 *
 * Usage mirrors the plain `Dialog` it replaces: pass trigger content as the
 * default slot (unnamed, so `<AppResponsiveDialog><Button>...</Button></AppResponsiveDialog>`
 * keeps working like the old `<Dialog>` call sites did — omit it entirely
 * when the dialog is opened programmatically instead), the dialog's body
 * via `#body`, and an optional icon badge above the title via `#icon`.
 */
const { title, description, contentClass, titleClass, unmountOnHide = true } = defineProps<{
    title: string
    description?: string
    /** Extra classes for the desktop modal only (e.g. a narrower `sm:max-w-md`) — the mobile sheet keeps a consistent full-width layout. */
    contentClass?: HTMLAttributes['class']
    titleClass?: HTMLAttributes['class']
    /** Reset the body's internal state (e.g. a form) each time the dialog closes. */
    unmountOnHide?: boolean
}>()

const open = defineModel<boolean>('open', { default: false })

const isMobile = useIsMobile()
</script>

<template>
    <Dialog v-if="!isMobile" v-model:open="open" :unmount-on-hide="unmountOnHide">
        <DialogTrigger v-if="$slots.default" as-child>
            <slot />
        </DialogTrigger>

        <DialogContent :class="cn('max-h-[85vh] overflow-y-auto', contentClass)">
            <DialogHeader>
                <slot name="icon" />
                <DialogTitle :class="titleClass">{{ title }}</DialogTitle>
                <DialogDescription v-if="description">{{ description }}</DialogDescription>
            </DialogHeader>

            <slot name="body" />
        </DialogContent>
    </Dialog>

    <Sheet v-else v-model:open="open" :unmount-on-hide="unmountOnHide">
        <SheetTrigger v-if="$slots.default" as-child>
            <slot />
        </SheetTrigger>

        <SheetContent side="bottom"
            class="flex max-h-[88dvh] flex-col gap-0 overflow-hidden rounded-t-2xl p-0">
            <div class="mx-auto mt-2.5 h-1.5 w-10 shrink-0 rounded-full bg-muted" />

            <SheetHeader class="shrink-0 border-b border-border px-4 pb-4 pt-3 text-left">
                <slot name="icon" />
                <SheetTitle :class="titleClass">{{ title }}</SheetTitle>
                <SheetDescription v-if="description">{{ description }}</SheetDescription>
            </SheetHeader>

            <div class="flex flex-1 flex-col gap-4 overflow-y-auto px-4 py-4"
                style="padding-bottom: max(1rem, env(safe-area-inset-bottom))">
                <slot name="body" />
            </div>
        </SheetContent>
    </Sheet>
</template>