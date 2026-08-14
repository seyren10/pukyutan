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
import { Button } from '@/components/ui/button'
import { useGroup } from '@/features/group/composables/use-group';
import type { GroupLike } from '@/features/group/type'
import { Rocket, Lock, CalendarCheck, Ban } from '@lucide/vue'
import { computed } from 'vue';

const { group, loading, membersCount } = defineProps<{
    group: GroupLike,
    loading?: boolean,
    membersCount?: number
}>()
const emit = defineEmits<{
    (e: 'confirm'): void
}>()

const { name, membersCount: groupMembersCount, startDateLabel, frequencyLabel } = useGroup(() => group)

const finalMembersCount = computed(() => membersCount ?? groupMembersCount.value)

const rules = [
    {
        icon: Lock,
        text: 'The member list locks — no adding, removing, or reordering members until this round finishes.',
    },
    {
        icon: CalendarCheck,
        text: "Cycle dates are generated now, based on today's start date and frequency, and won't shift afterward.",
    },
    {
        icon: Ban,
        text: "This can't be undone — the group can't be sent back to draft once activated.",
    },
]
</script>

<template>
    <AlertDialog>
        <AlertDialogTrigger as-child>
            <Button>
                <Rocket data-icon="inline-start" />
                Activate group
            </Button>
        </AlertDialogTrigger>

        <AlertDialogContent>
            <AlertDialogHeader>
                <div class="flex size-10 items-center justify-center rounded-full bg-accent text-accent-foreground ">
                    <Rocket class="size-5" />
                </div>
                <AlertDialogTitle class="font-heading">Activate "{{ name }}"?</AlertDialogTitle>
                <AlertDialogDescription>
                    <strong>{{ finalMembersCount }}</strong> members will start their rotation on <strong>{{
                        startDateLabel }}</strong>, <strong> {{ frequencyLabel
                        }}</strong>.
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
                <AlertDialogAction :disabled="loading" @click="emit('confirm')">
                    {{ loading ? 'Activating...' : 'Activate group' }}
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>