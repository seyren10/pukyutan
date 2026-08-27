<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogTrigger,
    AlertDialogContent,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogCancel,
    AlertDialogAction,
} from '@/components/ui/alert-dialog'
import { Button, buttonVariants } from '@/components/ui/button'
import { CheckCircle2, Lock, Archive } from '@lucide/vue'
import type { GroupLike } from '@/features/group/type'
import { useGroup } from '@/features/group/composables/use-group'
import { useMarkascompleteMutation } from '@/features/group/composables/use-markascomplete-mutation'

const { group } = defineProps<{
    group: GroupLike
}>()

const dialog = defineModel<boolean>({ default: false })

const { name: groupName } = useGroup(() => group)
const { isPending: isMarkingComplete, mutate: markAsCompleteMutate } = useMarkascompleteMutation()

const rules = [
    {
        icon: Lock,
        text: "No new rounds can be started once the group is marked complete.",
    },
    {
        icon: Archive,
        text: "The group becomes a closed, read-only record — members can still view its history.",
    },
]

const handleConfirm = () => {
    markAsCompleteMutate(group.id, {
        onSuccess: () => {
            dialog.value = false
        },
    })
}
</script>

<template>
    <AlertDialog v-model:open="dialog">
        <AlertDialogTrigger as-child>
            <slot>
                <Button variant="outline" size="sm">
                    <CheckCircle2 data-icon="inline-start" />
                    Mark as Complete
                </Button>
            </slot>
        </AlertDialogTrigger>

        <AlertDialogContent>
            <AlertDialogHeader>
                <div class="flex size-10 items-center justify-center rounded-full bg-secondary text-secondary-foreground">
                    <CheckCircle2 class="size-5" />
                </div>
                <AlertDialogTitle class="font-heading">Mark "{{ groupName }}" as complete?</AlertDialogTitle>
                <AlertDialogDescription>
                    Every member has received their payout for this round. Marking the group as complete closes it
                    out for good instead of starting another round.
                </AlertDialogDescription>
            </AlertDialogHeader>

            <ul class="flex flex-col gap-3 py-2">
                <li v-for="rule in rules" :key="rule.text" class="flex items-start gap-3 text-sm text-muted-foreground">
                    <component :is="rule.icon" class="mt-0.5 size-4 shrink-0 text-foreground" />
                    <span>{{ rule.text }}</span>
                </li>
            </ul>

            <AlertDialogFooter>
                <AlertDialogCancel>Not yet</AlertDialogCancel>
                <AlertDialogAction :disabled="isMarkingComplete" :class="buttonVariants({ variant: 'secondary' })"
                    @click="handleConfirm">
                    {{ isMarkingComplete ? 'Marking...' : 'Mark as Complete' }}
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
