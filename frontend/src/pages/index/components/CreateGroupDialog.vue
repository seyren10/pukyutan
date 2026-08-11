<script setup lang="ts">
import GroupForm from '@/components/groups/GroupForm.vue';
import { Dialog, DialogDescription, DialogHeader, DialogScrollContent, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { createGroup } from '@/features/group/api';
import type { Group, GroupSchema } from '@/features/group/type';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { ref } from 'vue';

const dialog = defineModel({
    default: false
})
const emit = defineEmits<{
    (e: 'addMembers'): void
}>()
const queryClient = useQueryClient()
const addMembers = ref(false)
const selectedGroup = ref<Group | null>(null)

const { mutate: createGroupMutate, isPending: isCrateGroupPending } = useMutation({
    mutationFn: createGroup,
})

const handleSubmit = (payload: GroupSchema) => {
    createGroupMutate(payload, {
        onSuccess: (group) => {
            selectedGroup.value = group.data;
            dialog.value = false;

        },
        onSettled: () => queryClient.invalidateQueries({ queryKey: ['groups'] })
    })

}

</script>


<template>
    <Dialog v-model:open="dialog" unmount-on-hide>
        <DialogTrigger as-child>
            <slot :add-members="addMembers" :group="selectedGroup" />
        </DialogTrigger>

        <DialogScrollContent>
            <DialogHeader>
                <DialogTitle>Create a new group</DialogTitle>
                <DialogDescription>Fill out the form to create a new group</DialogDescription>
            </DialogHeader>

            <GroupForm @submit="handleSubmit" @add-members="emit('addMembers')" :loading="isCrateGroupPending" />
        </DialogScrollContent>
    </Dialog>
</template>


<style lang="scss" scoped></style>