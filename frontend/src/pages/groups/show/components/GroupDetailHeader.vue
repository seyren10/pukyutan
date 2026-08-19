<script setup lang="ts">
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { Hexagon, Wallet, CalendarClock, Users, Eye, Ticket, Copy, Check, ShieldUser, LogOut } from '@lucide/vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
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
import { getInitials } from '@/lib/helpers'
import { useGroupDetail } from '@/features/group/composables/use-group'
import { useLeaveGroupMutation } from '@/features/share/composables/use-leave-group-mutation'
import type { GroupDetail } from '@/features/group/type'
import { toast } from 'vue-sonner'

const { group, isOwner } = defineProps<{
    group: GroupDetail
    isOwner: boolean
}>()

const router = useRouter()

const {
    name,
    user,
    status,
    cyclesCount,
    contributionAmount,
    frequencyUnit,
    frequencyInterval,
    frequencyLabel,
    startDateLabel,
} = useGroupDetail(() => group)

// `GroupDetail` carries the full `members` array rather than the list-view's
// `members_count`, so it's counted directly here instead of via the shared
// composable (which reads a field this shape doesn't have).
const membersCount = computed(() => group.members.length)

const justCopied = ref(false)

const copyInviteCode = async () => {
    if (!group.invite_code) return

    await navigator.clipboard.writeText(group.invite_code)
    justCopied.value = true
    toast.success('Invite code copied')

    setTimeout(() => (justCopied.value = false), 1500)
}

const { mutate: leaveGroupMutate, isPending: isLeaving } = useLeaveGroupMutation()

const handleLeave = () => {
    leaveGroupMutate(group.id, {
        onSuccess: () => {
            toast.success(`You've left "${name.value}"`)
            router.push({ name: 'dashboard' })
        },
    })
}
</script>

<template>
    <div class="flex flex-col gap-5 rounded-2xl border border-border bg-card p-5 shadow-sm sm:flex-row sm:items-start sm:justify-between sm:gap-6 sm:p-6">
        <div class="flex items-start gap-4">
            <div class="relative shrink-0">
                <Hexagon class="size-14 text-primary sm:size-16" fill="currentColor" :stroke-width="1.5" />
                <span
                    class="pointer-events-none absolute inset-0 flex items-center justify-center font-mono text-sm font-semibold text-primary-foreground sm:text-base">
                    {{ getInitials(name) }}
                </span>
            </div>

            <div class="flex flex-col gap-2">
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="font-heading text-2xl font-semibold capitalize text-foreground sm:text-3xl">
                        {{ name }}
                    </h1>
                    <Badge v-if="status === 'active'" variant="success">Active</Badge>
                    <Badge v-else-if="status === 'draft'" variant="accent">Draft</Badge>
                    <Badge v-else variant="secondary">Completed</Badge>
                    <Badge v-if="!isOwner" variant="outline" class="gap-1">
                        <Eye class="size-3" />
                        View only
                    </Badge>
                </div>

                <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-sm text-muted-foreground">
                    <span class="flex items-center gap-1.5" v-if="frequencyUnit && frequencyInterval">
                        <Wallet class="size-3.5" />
                        <span class="font-mono text-foreground">₱{{ contributionAmount }}</span>
                        <span>· {{ frequencyLabel }}</span>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <CalendarClock class="size-3.5" />
                        Started {{ startDateLabel }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <Users class="size-3.5" />
                        {{ membersCount }} {{ membersCount === 1 ? 'member' : 'members' }}
                    </span>
                    <span v-if="cyclesCount > 0" class="font-mono text-xs">
                        {{ cyclesCount }} {{ cyclesCount === 1 ? 'cycle' : 'cycles' }} total
                    </span>
                </div>

                <p v-if="!isOwner" class="text-xs text-muted-foreground">
                    Owned by {{ user?.name }}
                </p>
            </div>
        </div>

        <div class="flex shrink-0 flex-col items-end gap-2">
            <div v-if="isOwner && group.invite_code" class="flex items-center gap-2">
                <Button as-child variant="outline" size="sm">
                    <RouterLink :to="{ name: 'groups.detail.access.index' }">
                        <ShieldUser data-icon="inline-start" />
                        Manage access
                    </RouterLink>
                </Button>

                <button type="button" @click="copyInviteCode"
                    class="inline-flex items-center gap-1.5 rounded-full border border-dashed border-border bg-muted/40 px-3 py-1.5 font-mono text-xs text-muted-foreground transition-colors hover:bg-muted hover:text-foreground">
                    <Ticket class="size-3.5" />
                    {{ group.invite_code }}
                    <Check v-if="justCopied" class="size-3 text-success" />
                    <Copy v-else class="size-3" />
                </button>
            </div>

            <AlertDialog v-else-if="!isOwner">
                <AlertDialogTrigger as-child>
                    <Button variant="outline" size="sm" class="text-muted-foreground hover:text-destructive">
                        <LogOut data-icon="inline-start" />
                        Leave group
                    </Button>
                </AlertDialogTrigger>

                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle class="font-heading">Leave "{{ name }}"?</AlertDialogTitle>
                        <AlertDialogDescription>
                            You'll lose access to this group right away. You can ask the owner for a new invite
                            code if you want back in later.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>Stay</AlertDialogCancel>
                        <AlertDialogAction :disabled="isLeaving" @click="handleLeave">
                            {{ isLeaving ? 'Leaving...' : 'Leave group' }}
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </div>
    </div>
</template>