<script setup lang="ts">
import GroupForm from '@/components/groups/GroupForm.vue';
import { Dialog, DialogDescription, DialogHeader, DialogScrollContent, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { createGroup } from '@/features/group/api';
import type { GroupSchema } from '@/features/group/type';
import { useMutation } from '@tanstack/vue-query';
import { ref } from 'vue';

const dialog = defineModel({
    default: false
})
const addMembers = ref(false)
const showAddMembersDialog = ref(false);
const selectedGroupId = ref<number | null>(null)

const { mutate: createGroupMutate, isPending: isCrateGroupPending } = useMutation({
    mutationFn: createGroup,
})
const handleSubmit = (payload: GroupSchema) => {
    if (addMembers.value) {
        createGroupMutate(payload, {
            onSuccess: (group) => {
                selectedGroupId.value = group.data.id
                showAddMembersDialog.value = true
            }
        })
    }
}
</script>


<template>
    <Dialog v-model:open="dialog">
        <DialogTrigger as-child>
            <slot />
        </DialogTrigger>

        <DialogScrollContent>
            <DialogHeader>
                <DialogTitle>Create a new group</DialogTitle>
                <DialogDescription>Fill out the form to create a new group</DialogDescription>
            </DialogHeader>

            <GroupForm @submit="handleSubmit" v-model:add-members="addMembers" :loading="isCrateGroupPending" />

        </DialogScrollContent>
    </Dialog>
</template>


<style lang="scss" scoped></style>