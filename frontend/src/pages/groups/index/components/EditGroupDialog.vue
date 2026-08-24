<script setup lang="ts">
import GroupForm from '@/components/groups/GroupForm.vue';
import AppResponsiveDialog from '@/components/app/AppResponsiveDialog.vue';
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
    <AppResponsiveDialog v-model:open="dialog" title="Edit Group"
        description="Make necessary changes and click update to save changes.">
        <template #body>
            <GroupForm @submit="handleSubmit" :initial-values="{
                name: group.name,
                contribution_amount: parseFloat(group.contribution_amount),
                frequency_interval: group.frequency_interval,
                frequency_unit: group.frequency_unit,
                start_date: group.start_date.slice(0, 10)
            }" v-if="status === 'draft'" :loading="isPending" />
        </template>
    </AppResponsiveDialog>
</template>


<style lang="scss" scoped></style>