<script setup lang="ts">
import GroupForm from '@/components/groups/GroupForm.vue';
import { Dialog, DialogDescription, DialogHeader, DialogScrollContent, DialogTitle, } from '@/components/ui/dialog';
import { useGroupEditMutation } from '@/features/group/composables/use-group-edit-mutation';
import type { GroupLike, GroupSchema } from '@/features/group/type';
import { computed } from 'vue';

const { group } = defineProps<{
    group: GroupLike
}>()
const dialog = defineModel({
    default: false
})
const { mutate, isPending } = useGroupEditMutation()
const status = computed(() => group.status)

const handleSubmit = (payload: GroupSchema) => {
    mutate({
        groupId: group.id,
        payload
    }, {
        onSuccess: () => {
            dialog.value = false
        }
    })
}

</script>


<template>
    <Dialog v-model:open="dialog" unmount-on-hide>

        <DialogScrollContent>
            <DialogHeader>
                <DialogTitle>Edit Group</DialogTitle>
                <DialogDescription>Make necessary changes and click update to save changes.</DialogDescription>
            </DialogHeader>

            <GroupForm @submit="handleSubmit" :initial-values="{
                name: group.name,
                contribution_amount: parseFloat(group.contribution_amount),
                frequency_interval: group.frequency_interval,
                frequency_unit: group.frequency_unit,
                start_date: group.start_date.slice(0, 10)
            }" v-if="status === 'draft'" :loading="isPending" />
        </DialogScrollContent>
    </Dialog>
</template>


<style lang="scss" scoped></style>