<script setup lang="ts">
import AddMemberForm from '@/components/groups/AddMemberForm.vue';
import AppResponsiveDialog from '@/components/app/AppResponsiveDialog.vue';
import type { GroupLike } from '@/features/group/type';
import ActivateGroupDialog from './ActivateGroupDialog.vue';
import { useGroupActivateMutation } from '@/features/group/composables/use-group-activate-mutation.ts';

const { group } = defineProps<{
    group: GroupLike
}>()

const dialog = defineModel({
    default: false
})

const { isPending, mutate } = useGroupActivateMutation()

const handleActivateGroup = () => {
    mutate(group.id, {
        onSuccess: () => {
            dialog.value = false
        }
    })
}
</script>


<template>
    <AppResponsiveDialog v-model:open="dialog" :title="`Add Members to ${group.name}`"
        description="Add everyone in this paluwagan. You can reorder who receives each payout below.">
        <slot />

        <template #body>
            <!-- Each add/remove/reorder mutation invalidates the groups cache itself
                 (see features/members/composables), so this dialog no longer needs to
                 track "was something changed" and reconcile on close. -->
            <AddMemberForm :group-id="group.id" #="{ membersCount }">
                <ActivateGroupDialog :group="group" :members-count="membersCount" :loading="isPending"
                    @confirm="handleActivateGroup" v-if="membersCount > 0" />
            </AddMemberForm>
        </template>
    </AppResponsiveDialog>
</template>


<style lang="scss" scoped></style>