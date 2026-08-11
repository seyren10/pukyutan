<script setup lang="ts">
import AddMemberForm from '@/components/groups/AddMemberForm.vue';
import { Dialog, DialogDescription, DialogHeader, DialogScrollContent, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { getGroupsInfiniteQueryOptions } from '@/features/group/query';
import type { Group } from '@/features/group/type';
import { useQueryClient } from '@tanstack/vue-query';
import { ref, watch } from 'vue';
import ActivateGroupDialog from './ActivateGroupDialog.vue';
import { useGroupActivateMutation } from '@/features/group/composables/use-group-active-mutation.ts';

const { group } = defineProps<{
    group: Group
}>()

const dialog = defineModel({
    default: false
})

const queryClient = useQueryClient()
/**
 * invalidate the group query when the form is updated.
 */
const updateGroupsWhenUpdated = ref(false)
const { isPending, mutate } = useGroupActivateMutation()

const handleFormUpdated = () => {
    updateGroupsWhenUpdated.value = true;
}

const handleActivateGroup = () => {
    mutate(group.id, {
        onSuccess: () => {
            dialog.value = false
        }
    })
}


/**
 * Invalidate the group query when the dialog is closed and the form was updated.
 */
watch(dialog, () => {
    if (dialog.value)
        updateGroupsWhenUpdated.value = false;

    if (updateGroupsWhenUpdated.value && !dialog.value) {
        const queryKey = getGroupsInfiniteQueryOptions().queryKey
        queryClient.invalidateQueries({ queryKey })
    }
})
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


            <AddMemberForm :group-id="group.id" @updated="handleFormUpdated" #="{ membersCount }">
                <ActivateGroupDialog :group="group" :members-count="membersCount" :loading="isPending"
                    @confirm="handleActivateGroup" v-if="membersCount > 0" />
            </AddMemberForm>
        </DialogScrollContent>
    </Dialog>
</template>


<style lang="scss" scoped></style>