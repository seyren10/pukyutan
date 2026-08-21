<script setup lang="ts">
import GroupForm from '@/components/groups/GroupForm.vue';
import { Dialog, DialogDescription, DialogHeader, DialogScrollContent, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { createGroup } from '@/features/group/api';
import type { Group, GroupSchema } from '@/features/group/type';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

const dialog = defineModel({
    default: false
})
const emit = defineEmits<{
    (e: 'addMembers', group: Group): void
}>()
const queryClient = useQueryClient()

const { mutate: createGroupMutate, isPending: isCreateGroupPending } = useMutation({
    mutationFn: createGroup,
})

const handleSubmit = (payload: GroupSchema, { addMembers }: { addMembers: boolean }) => {
    createGroupMutate(payload, {
        // Only close the dialog and (optionally) hand the new group off once the
        // create request actually resolves — emitting eagerly here raced ahead
        // of the mutation and left the add-members dialog with no group to show.
        onSuccess: (group) => {
            dialog.value = false;

            if (addMembers) emit('addMembers', group.data)
        },
        onSettled: () => queryClient.invalidateQueries({ queryKey: ['groups'] })
    })
}

</script>


<template>
    <Dialog v-model:open="dialog" unmount-on-hide>
        <DialogTrigger as-child>
            <slot />
        </DialogTrigger>

        <DialogScrollContent>
            <DialogHeader>
                <DialogTitle>Create a new group</DialogTitle>
                <DialogDescription>Fill out the form to create a new group</DialogDescription>
            </DialogHeader>

            <GroupForm @submit="handleSubmit" :loading="isCreateGroupPending" />
        </DialogScrollContent>
    </Dialog>
</template>


<style lang="scss" scoped></style>
