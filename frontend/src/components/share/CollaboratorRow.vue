<script setup lang="ts">
import { computed } from 'vue'
import { format } from 'date-fns'
import {
    AlertDialog,
    AlertDialogTrigger,
    AlertDialogContent,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogCancel,
    AlertDialogAction,
} from '@/components/ui/alert-dialog'
import { Button } from '@/components/ui/button'
import { UserMinus } from '@lucide/vue'
import { useRevokeShareMutation } from '@/features/share/composables/use-revoke-share-mutation'
import type { GroupShare } from '@/features/share/type'
import AppAvatar from '../app/AppAvatar.vue'

const { share } = defineProps<{
    share: GroupShare
}>()

const grantedLabel = computed(() =>
    share.responded_at ? format(new Date(share.responded_at), 'MMM d, yyyy') : null,
)

const { mutate: revokeMutate, isPending } = useRevokeShareMutation()
</script>

<template>
    <div class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">
        <AppAvatar class="size-9" :fallback="share.user.name" :google-avatar="share.user.avatar"
            :seed="share.user.dicebear_seed" />

        <div class="flex min-w-0 flex-1 flex-col gap-0.5">
            <span class="truncate text-sm font-medium text-foreground">{{ share.user.name }}</span>
            <span class="truncate text-xs text-muted-foreground">
                {{ share.user.email }}
                <template v-if="grantedLabel"> · access since {{ grantedLabel }}</template>
            </span>
        </div>

        <AlertDialog>
            <AlertDialogTrigger as-child>
                <Button size="sm" variant="ghost" :disabled="isPending"
                    class="shrink-0 text-muted-foreground hover:text-destructive">
                    <UserMinus data-icon="inline-start" />
                    Remove
                </Button>
            </AlertDialogTrigger>

            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle class="font-heading">Remove {{ share.user.name }}'s access?</AlertDialogTitle>
                    <AlertDialogDescription>
                        They'll immediately lose the ability to view this group. They can request access again
                        later if you change your mind.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction :disabled="isPending" @click="revokeMutate(share.id)">
                        {{ isPending ? 'Removing...' : 'Remove access' }}
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </div>
</template>
