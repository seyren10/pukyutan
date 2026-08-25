<script setup lang="ts">
import { computed } from 'vue'
import { formatDistanceToNow } from 'date-fns'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Check, X } from '@lucide/vue'
import { useAcceptShareMutation } from '@/features/share/composables/use-accept-share-mutation'
import { useRejectShareMutation } from '@/features/share/composables/use-reject-share-mutation'
import type { GroupShare } from '@/features/share/type'
import AppAvatar from '../app/AppAvatar.vue'

const { share, showGroup = false } = defineProps<{
    share: GroupShare
    showGroup?: boolean
}>()

const requestedLabel = computed(() =>
    formatDistanceToNow(new Date(share.requested_at), { addSuffix: true }),
)

const { mutate: acceptMutate, isPending: isAccepting } = useAcceptShareMutation()
const { mutate: rejectMutate, isPending: isRejecting } = useRejectShareMutation()

const isBusy = computed(() => isAccepting.value || isRejecting.value)
</script>

<template>
    <div class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">
        <AppAvatar class="size-9" :fallback="share.user.name" :google-avatar="share.user.avatar"
            :seed="share.user.dicebear_seed" />

        <div class="flex min-w-0 flex-1 flex-col gap-0.5">
            <div class="flex flex-wrap items-center gap-2">
                <span class="truncate text-sm font-medium text-foreground">{{ share.user.name }}</span>
                <Badge v-if="showGroup && share.group_name" variant="outline" class="font-normal">
                    {{ share.group_name }}
                </Badge>
            </div>
            <span class="truncate text-xs text-muted-foreground">
                {{ share.user.email }} · requested {{ requestedLabel }}
            </span>
        </div>

        <div class="flex shrink-0 items-center gap-1.5">
            <Button size="sm" variant="ghost" :disabled="isBusy" class="text-muted-foreground hover:text-destructive"
                @click="rejectMutate(share.id)">
                <X />
                <span class="hidden sm:inline">Decline</span>
            </Button>
            <Button size="sm" :disabled="isBusy" @click="acceptMutate(share.id)">
                <Check data-icon="inline-start" />
                {{ isAccepting ? 'Accepting...' : 'Accept' }}
            </Button>
        </div>
    </div>
</template>
