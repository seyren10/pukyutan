<script setup lang="ts">
import { Avatar as DiceAvatar, Style } from '@dicebear/core'
import thumbs from '@dicebear/styles/big-smile.json' with { type: 'json' };

import { computed, ref, useAttrs, useTemplateRef } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '../ui/avatar';
import { useColorMode, useIntersectionObserver } from '@vueuse/core';
import { cn } from '@/lib/utils';
import { getInitials } from '@/lib/helpers';
import AppButtonLoaderSwap from './AppButtonLoaderSwap.vue';
import { Shuffle, X } from '@lucide/vue';
import { Button } from '../ui/button/index.ts';


defineOptions({
    inheritAttrs: false
})

const emits = defineEmits<{
    (e: 'seed', cb: () => void): void;
    (e: 'unseed', cb: () => void): void;
}>()
const { seed = null, fallback, googleAvatar = null, editable = false } = defineProps<{
    seed: string | null,
    fallback: string,
    googleAvatar?: string | null,
    editable?: boolean,
}>()

const { class: className, ...rest } = useAttrs();
const { state } = useColorMode()
const isPending = ref(false);
const isDeletingSeed = ref(false)
const style = new Style(thumbs);
const img = useTemplateRef('img')
const showImage = ref(false);

useIntersectionObserver(img, ([{ isIntersecting }]) => {
    if (isIntersecting)
        showImage.value = true
}, {
    rootMargin: '0px'
})
const hasDicebearSeed = computed(() => seed !== null)
const userInitials = computed(() => getInitials(fallback));
const avatar = computed(() => {
    if (!showImage.value) return '';

    return new DiceAvatar(style, {
        seed: seed || 'Roy',
        size: 16,
        backgroundColor: state.value === 'light' ? '#f3d9ba' : '#442d15',
    }).toDataUri()
});



const handleSeed = () => {
    if (!editable) return;

    isPending.value = true;
    emits('seed', () => {
        isPending.value = false
    })
}

const handleUnseed = () => {
    if (!editable) return;

    isDeletingSeed.value = true;

    emits('unseed', () => isDeletingSeed.value = false)
}

</script>

<template>
    <div class="group relative rounded-full" v-bind="rest">
        <Avatar :class="cn('size-16', className)" ref="img">
            <AvatarImage :src="hasDicebearSeed ? avatar : (googleAvatar || '')" :alt="fallback" v-if="showImage" />
            <AvatarFallback>{{ userInitials }}</AvatarFallback>
        </Avatar>

        <button v-if="editable" @click.prevent="handleSeed()" :disabled="isPending"
            class="rounded-full absolute transition-opacity duration-200 inset-0 bg-background/50 backdrop-blur-sm opacity-0 group-hover:opacity-100 grid place-content-center">
            <AppButtonLoaderSwap :loading="isPending || isDeletingSeed">
                <Shuffle class="size-4" />
            </AppButtonLoaderSwap>
            <Button @click.stop="handleUnseed()" :disabled="isDeletingSeed || isPending" variant="destructive"
                size="icon-xs" class="absolute left-0 top-0" v-if="hasDicebearSeed">
                <X />
            </Button>
        </button>
    </div>
</template>

<style scoped></style>