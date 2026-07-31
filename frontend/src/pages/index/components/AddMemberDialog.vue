<script setup lang="ts">
import AddMemberForm from '@/components/groups/AddMemberForm.vue';
import { Dialog, DialogDescription, DialogHeader, DialogScrollContent, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import type { Group } from '@/features/group/type';

const { group } = defineProps<{
    group: Group
}>()

const dialog = defineModel({
    default: false
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


            <AddMemberForm :group-id="group.id" />
        </DialogScrollContent>
    </Dialog>
</template>


<style lang="scss" scoped></style>