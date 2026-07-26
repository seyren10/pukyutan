<script setup lang="ts">
import { Loader2Icon } from '@lucide/vue';

const { loading } = defineProps<{
    loading?: boolean
}>()
</script>
<template>
    <Loader2Icon class="animate-spin" v-if="loading" />
    <slot v-else />
</template>



<style scoped></style>