<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Edit3Icon, EyeIcon, MoreVertical, Ticket, Trash2Icon, UserRound } from '@lucide/vue';
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import type { GroupCardDropdownEvent } from '.';
import type { GroupLike } from '@/features/group/type';
import { useCopyInviteCode } from '@/features/group/composables/use-copy-invite-code';

const { group, isOwner = true } = defineProps<{
    group: GroupLike
    /** Owner-only actions (manage members, edit/rename, delete) are hidden for view-only collaborators. */
    isOwner?: boolean
}>()
const emit = defineEmits<{
    (e: 'select', type: GroupCardDropdownEvent): void
}>()
const router = useRouter()
const { copyInviteCode } = useCopyInviteCode()

// Membership can only be managed while the group is still a draft — once
// active the member list locks, so the owner-only action set is trimmed
// down to just renaming from that point on.
const isDraft = computed(() => group.status === 'draft')
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <slot>
                <Button size="icon" variant="ghost">
                    <MoreVertical />
                </Button>
            </slot>
        </DropdownMenuTrigger>

        <DropdownMenuContent align="end">
            <DropdownMenuItem @select="router.push({ name: 'groups.detail', params: { id: group.id } })">
                <EyeIcon /> View Group
            </DropdownMenuItem>
            <template v-if="isOwner">
                <DropdownMenuItem @select="emit('select', 'add-member')" v-if="isDraft">
                    <UserRound /> Manage Members
                </DropdownMenuItem>
                <DropdownMenuItem @select="emit('select', 'edit-group')" v-if="group.status === 'draft'">
                    <Edit3Icon /> Edit Group
                </DropdownMenuItem>
                <DropdownMenuItem @select="emit('select', 'rename-group')" v-else>
                    <Edit3Icon /> Rename Group
                </DropdownMenuItem>
                <DropdownMenuItem @select="copyInviteCode(group.invite_code)" v-if="group.invite_code">
                    <Ticket /> Copy Invitation Code
                </DropdownMenuItem>

                <DropdownMenuSeparator />
                <DropdownMenuItem variant="destructive" @select="emit('select', 'delete-group')">
                    <Trash2Icon /> Delete Group
                </DropdownMenuItem>
            </template>
        </DropdownMenuContent>
    </DropdownMenu>
</template>


<style scoped></style>
