<script setup lang="ts">
import { computed } from 'vue'
import { useStorage, useNow } from '@vueuse/core'
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
import { Button } from '@/components/ui/button'
import { useAuthMutations } from '@/features/auth/mutations'
import { FlagIcon } from '@lucide/vue'

const { verified } = defineProps<{
    verified: boolean
}>()

const { verifyEmailMutation } = useAuthMutations()
const { mutate, isPending } = verifyEmailMutation

const COOLDOWN_SECONDS = 60

// Persisted target timestamp, not a countdown — survives refresh and
// even a closed tab, since it's an absolute point in time, not a tick count.
const resendAvailableAt = useStorage<number>('verify-email:resend-available-at', 0)

// A reactive "now" ticking every second, used only to derive the display.
const now = useNow({ interval: 1000 })

const cooldown = computed(() => {
    const remainingMs = resendAvailableAt.value - now.value.getTime()
    return Math.max(0, Math.ceil(remainingMs / 1000))
})

function handleResend() {
    mutate(undefined, {
        onSuccess: () => {
            resendAvailableAt.value = Date.now() + COOLDOWN_SECONDS * 1000
        },
    })
}
</script>

<template>
    <Alert v-if="!verified" variant="warning">
        <FlagIcon />
        <AlertTitle>Verify your email to create groups</AlertTitle>
        <AlertDescription class="flex items-center justify-between gap-4">
            <span>Until then, you can only view groups that have been shared with you.</span>
            <Button size="sm" variant="outline" :disabled="isPending || cooldown > 0" @click="handleResend">
                {{ cooldown > 0 ? `Resend email (${cooldown}s)` : 'Resend email' }}
            </Button>
        </AlertDescription>
    </Alert>
</template>

<style scoped></style>