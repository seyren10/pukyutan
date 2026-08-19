<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogContent,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogCancel,
    AlertDialogAction,
} from '@/components/ui/alert-dialog'
import { buttonVariants } from '@/components/ui/button'
import { Trash2 } from '@lucide/vue'
import type { GroupLike } from '@/features/group/type'
import { useGroupDeleteMutation } from '@/features/group/composables/use-group-delete-mutation'

const { group } = defineProps<{
    group: GroupLike
}>()
const emit = defineEmits<{
    // Lets consumers react beyond the cache invalidation the mutation already
    // does — e.g. the group-detail page needs to navigate away since the
    // group it's showing no longer exists.
    (e: 'deleted'): void
}>()
const dialog = defineModel<boolean>({ default: false })

const { mutate, isPending } = useGroupDeleteMutation()

const handleDelete = () => {
    mutate(group.id, {
        onSuccess: () => {
            dialog.value = false
            emit('deleted')
        },
    })
}
</script>

<template>
    <AlertDialog v-model:open="dialog">
        <AlertDialogContent>
            <AlertDialogHeader>
                <div class="flex size-10 items-center justify-center rounded-full bg-destructive/10 text-destructive">
                    <Trash2 class="size-5" />
                </div>
                <AlertDialogTitle class="font-heading">Delete "{{ group.name }}"?</AlertDialogTitle>
                <AlertDialogDescription>
                    This permanently deletes the group along with its members and payout order, cycles, contributions and history. This can't be
                    undone.
                </AlertDialogDescription>
            </AlertDialogHeader>

            <AlertDialogFooter>
                <AlertDialogCancel>Cancel</AlertDialogCancel>
                <AlertDialogAction :disabled="isPending" :class="buttonVariants({ variant: 'destructive' })"
                    @click="handleDelete">
                    {{ isPending ? 'Deleting...' : 'Delete group' }}
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
