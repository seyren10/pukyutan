<script setup lang="ts">
import { Undo } from '@lucide/vue';
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from '../ui/alert-dialog';
import { Button } from '../ui/button';
import type { Contribution } from '@/features/contribution/type';
import { computed } from 'vue';
import { useDeleteContributionMutation } from '@/features/contribution/composables/use-delete-contribution-mutation';
import { useRouteParams } from '@vueuse/router';

const { contribution } = defineProps<{
    contribution: Contribution
}>()
const emit = defineEmits<{
    (e: 'confirm'): void
}>()
const dialog = defineModel<boolean>({ default: false })

const groupId = useRouteParams('id', null, {
    transform: Number
});
const { mutate, isPending } = useDeleteContributionMutation(groupId.value, contribution.cycle_id)

const memberName = computed(() => contribution.member?.name || 'unknown member')

const handleDelete = () => {
    mutate(contribution.id, {
        onSuccess: () => {
            emit('confirm')
            dialog.value = false
        }
    })
}
</script>
<template>
    <AlertDialog v-model:open="dialog">
        <AlertDialogTrigger as-child>
            <slot>
                <Button variant="destructive" size="icon-xs">
                    <Undo />
                </Button>
            </slot>
        </AlertDialogTrigger>
        <AlertDialogContent>
            <AlertDialogHeader>
                <div class="flex size-10 items-center justify-center rounded-full bg-destructive/10 text-destructive">
                    <Undo class="size-5" />
                </div>
                <AlertDialogTitle class="font-heading">Undo "{{ memberName }}
                    contribution"?</AlertDialogTitle>
                <AlertDialogDescription>
                    This permanently deletes {{ memberName }}'s contribution of ₱{{ contribution.amount }} for this
                    cycle.
                </AlertDialogDescription>
            </AlertDialogHeader>

            <AlertDialogFooter>
                <AlertDialogCancel>Cancel</AlertDialogCancel>
                <AlertDialogAction :disabled="isPending" @click="handleDelete">
                    {{ isPending ? 'Deleting...' : 'Delete' }}
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>



<style scoped></style>