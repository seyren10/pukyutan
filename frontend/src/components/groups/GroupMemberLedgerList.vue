<script setup lang="ts">
import { ref, watchEffect } from 'vue'
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip'
import { Mail, CheckCircle2, CirclePlus, Pencil } from '@lucide/vue'
import { getInitials } from '@/lib/helpers'
import type { Member, MemberWithLedger } from '@/features/members/type'
import EditMemberDialog from './EditMemberDialog.vue'

type LedgerEntry = Pick<MemberWithLedger, 'expected_total' | 'paid_total' | 'balance'>

const { members, ledgerByMemberId, isOwner = false } = defineProps<{
    members: Member[]
    /** Cycle-scoped status, keyed by member id — omitted while the group is still a draft (no cycle to be scoped to yet). */
    ledgerByMemberId?: Record<number, LedgerEntry>
    /** Contact-info editing is owner-only — view-only collaborators just see the list. */
    isOwner?: boolean
}>()

// Editing a member's name/email doesn't touch payout order or ledger data,
// so it's allowed here regardless of group status — unlike adding/removing/
// reordering, which are locked to draft groups.
const editingMember = ref<Member | null>(null)

const showEditMemberDialog = ref(false)
const handleEditMember = (member: Member) => {
    editingMember.value = member
    showEditMemberDialog.value = true
}

watchEffect(() => { if (!showEditMemberDialog.value) editingMember.value = null })
</script>

<template>
    <div class="flex flex-col divide-y divide-border">
        <div v-for="(member, index) in members" :key="member.id"
            class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">
            <div class="flex size-7 shrink-0 items-center justify-center rounded-full font-mono text-xs"
                :class="index === 0 ? 'bg-primary text-primary-foreground' : 'bg-muted text-muted-foreground'">
                {{ index + 1 }}
            </div>

            <Avatar class="size-8 shrink-0">
                <AvatarFallback class="bg-accent text-xs text-accent-foreground">
                    {{ getInitials(member.name) }}
                </AvatarFallback>
            </Avatar>

            <div class="flex min-w-0 flex-1 flex-col">
                <div class="flex items-center gap-2">
                    <span class="truncate text-sm font-medium text-foreground">{{ member.name }}</span>

                </div>
                <span class="flex items-center gap-1 text-xs text-muted-foreground">
                    <Mail class="size-3 shrink-0" />
                    <span class="truncate">{{ member.email || "No email on file" }}</span>
                </span>
            </div>

            <div v-if="ledgerByMemberId?.[member.id]" class="shrink-0 text-right text-xs">
                <div v-if="ledgerByMemberId[member.id].balance <= 0"
                    class="flex items-center justify-end gap-1 text-success">
                    <CheckCircle2 class="size-3" />
                    All caught up
                </div>

                <Tooltip v-if="ledgerByMemberId[member.id].balance < 0">
                    <TooltipTrigger as-child>
                        <span class="flex items-center justify-end gap-1 text-muted-foreground">
                            <CirclePlus class="size-3" />
                            ₱{{ Math.abs(ledgerByMemberId[member.id].balance).toLocaleString('en-PH') }} credit
                        </span>
                    </TooltipTrigger>
                    <TooltipContent class="max-w-xs">
                        <p>Automatically applied to whatever they owe next.</p>
                    </TooltipContent>
                </Tooltip>

                <span v-else-if="ledgerByMemberId[member.id].balance > 0" class="text-accent-foreground">
                    Owes ₱{{ ledgerByMemberId[member.id].balance.toLocaleString('en-PH') }}
                </span>
            </div>

            <Button v-if="isOwner" variant="ghost" size="icon" type="button"
                class="shrink-0 text-muted-foreground hover:text-foreground" @click="handleEditMember(member)">
                <Pencil class="size-3.5" />
            </Button>
        </div>
    </div>

    <EditMemberDialog :member="editingMember" v-model="showEditMemberDialog" v-if="editingMember" />
</template>