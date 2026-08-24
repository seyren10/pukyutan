<script setup lang="ts">
import GroupForm from '@/components/groups/GroupForm.vue';
import AppResponsiveDialog from '@/components/app/AppResponsiveDialog.vue';
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
    <AppResponsiveDialog v-model:open="dialog" title="Create a new group"
        description="Fill out the form to create a new group">
        <slot />

        <template #body>
            <GroupForm @submit="handleSubmit" :loading="isCreateGroupPending" />
        </template>
    </AppResponsiveDialog>
</template>


<style lang="scss" scoped></style>