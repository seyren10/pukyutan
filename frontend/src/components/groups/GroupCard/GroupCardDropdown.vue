<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Edit3Icon, EyeIcon, MoreVertical, UserRound, Users2Icon } from '@lucide/vue';
import { useRouter } from 'vue-router';
import type { GroupCardDropdownEvent } from '.';
import type { GroupLike } from '@/features/group/type';

const { group } = defineProps<{
    group: GroupLike
}>()
const emit = defineEmits<{
    (e: 'select', type: GroupCardDropdownEvent): void
}>()
const router = useRouter()
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
            <DropdownMenuItem @select="emit('select', 'add-member')">
                <UserRound /> Manage Members
            </DropdownMenuItem>
            <DropdownMenuItem @select="emit('select', 'edit-group')" v-if="group.status === 'draft'">
                <Edit3Icon /> Edit Group
            </DropdownMenuItem>
            <DropdownMenuItem @select="emit('select', 'rename-group')" v-else>
                <Edit3Icon /> Rename Group
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>


<style scoped></style>