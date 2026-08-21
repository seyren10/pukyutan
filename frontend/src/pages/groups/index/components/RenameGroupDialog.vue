<script setup lang="ts">
import GroupRenameForm from '@/components/groups/GroupRenameForm.vue';
import { Dialog, DialogDescription, DialogHeader, DialogScrollContent, DialogTitle, } from '@/components/ui/dialog';
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
    <Dialog v-model:open="dialog" unmount-on-hide>
        <DialogScrollContent>
            <DialogHeader>
                <DialogTitle>Rename Group</DialogTitle>
                <DialogDescription> The amount, frequency, and start date are locked in once a group is active — only
                    the name can
                    still be changed..</DialogDescription>
            </DialogHeader>

            <GroupRenameForm :group-name="group.name" @submit="handleSubmit" v-if="status !== 'draft'"
                :loading="isPending" />

        </DialogScrollContent>
    </Dialog>
</template>


<style lang="scss" scoped></style>