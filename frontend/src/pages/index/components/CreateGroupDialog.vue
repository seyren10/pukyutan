<script setup lang="ts">
import GroupForm from '@/components/groups/GroupForm.vue';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';

const dialog = defineModel({
    default: false
})
</script>

<template>
    <Dialog v-model:open="dialog">
        <DialogTrigger as-child>
            <slot />
        </DialogTrigger>

        <DialogContent>
            <DialogHeader>
                <DialogTitle>Create a new group</DialogTitle>
                <DialogDescription>Fill out the form to create a new group</DialogDescription>
            </DialogHeader>

            <GroupForm />
        </DialogContent>
    </Dialog>
</template>


<style lang="scss" scoped></style>