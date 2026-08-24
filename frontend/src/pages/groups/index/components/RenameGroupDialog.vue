<script setup lang="ts">
import GroupRenameForm from '@/components/groups/GroupRenameForm.vue';
import AppResponsiveDialog from '@/components/app/AppResponsiveDialog.vue';
import { useGroupEditMutation } from '@/features/group/composables/use-group-edit-mutation';
import type { GroupLike, RenameGroupSchema } from '@/features/group/type';
import { computed } from 'vue';

const { group } = defineProps<{
    group: GroupLike
}>()
const dialog = defineModel({
    default: false
})
const { mutate, isPending } = useGroupEditMutation()
const status = computed(() => group.status)

const handleSubmit = (payload: RenameGroupSchema) => {
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
    <AppResponsiveDialog v-model:open="dialog" title="Rename Group"
        description="The amount, frequency, and start date are locked in once a group is active — only the name can still be changed.">
        <template #body>
            <GroupRenameForm :group-name="group.name" @submit="handleSubmit" v-if="status !== 'draft'"
                :loading="isPending" />
        </template>
    </AppResponsiveDialog>
</template>


<style lang="scss" scoped></style>