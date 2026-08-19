<script setup lang="ts">
import AddMemberForm from '@/components/groups/AddMemberForm.vue';
import { Dialog, DialogDescription, DialogHeader, DialogScrollContent, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
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
    <Dialog v-model:open="dialog">
        <DialogTrigger as-child>
            <slot />
        </DialogTrigger>

        <DialogScrollContent>
            <DialogHeader>
                <DialogTitle>Add Members to {{ group.name }}</DialogTitle>
                <DialogDescription> Add everyone in this paluwagan. You can reorder who receives each payout below.
                </DialogDescription>
            </DialogHeader>

            <!-- Each add/remove/reorder mutation invalidates the groups cache itself
                 (see features/members/composables), so this dialog no longer needs to
                 track "was something changed" and reconcile on close. -->
            <AddMemberForm :group-id="group.id" #="{ membersCount }">
                <ActivateGroupDialog :group="group" :members-count="membersCount" :loading="isPending"
                    @confirm="handleActivateGroup" v-if="membersCount > 0" />
            </AddMemberForm>
        </DialogScrollContent>
    </Dialog>
</template>


<style lang="scss" scoped></style>
