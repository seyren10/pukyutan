<script setup lang="ts">
import { computed, ref, watchEffect } from 'vue'
import { useForm } from 'vee-validate'
import { VueDraggable } from 'vue-draggable-plus'
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card'
import { FormField, FormItem, FormLabel, FormControl, FormDescription, FormMessage } from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle, EmptyDescription } from '@/components/ui/empty'
import { GripVertical, X, Star, Mail, UserPlus, Users } from '@lucide/vue'
import { toTypedSchema } from '@vee-validate/zod'
import { memberSchema } from '@/features/members/schema'
import { useQuery } from '@tanstack/vue-query'
import { getGroupMembersQueryOptions } from '@/features/members/query'
import { useAddMemberMutation } from '@/features/members/composables/add-member-mutation'
import { isAxiosError } from 'axios'
import type { LaravelError } from '@/types/common'
import type { Member, MemberSchema } from '@/features/members/type'
import { useRemoveMemberMutation } from '@/features/members/composables/remove-member-mutation'
import { useReorderMembersMutation } from '@/features/members/composables/reorder-member-mutation'
import { useDebounceFn } from '@vueuse/core'

const { groupId } = defineProps<{
    groupId: number
}>()

const { data } = useQuery(getGroupMembersQueryOptions(() => groupId))
const membersData = computed(() => data.value?.data)

const { addMemberMutate } = useAddMemberMutation()
const { mutate: removeMemberMutate } = useRemoveMemberMutation(groupId)
const { mutate: reorderMembersMutate } = useReorderMembersMutation(groupId)

const members = ref<Member[]>([])


const { handleSubmit, resetForm, setErrors, resetField, values } = useForm({
    validationSchema: toTypedSchema(memberSchema),
})

const dragSnapshot = ref<Member[]>([])
const hasQueuedReorder = ref(false);

const onDragStart = () => {
    if (!hasQueuedReorder.value) {
        dragSnapshot.value = [...members.value]
    }
}

const onDragEnd = () => {
    hasQueuedReorder.value = true;
    commitReorder()
}
const onSubmit = handleSubmit((payload) => {
    addMemberMutate({ groupId, payload }, {
        onError: (error) => {
            if (isAxiosError(error)) {
                const formError = error as LaravelError<MemberSchema>
                if (formError.response?.data?.errors)
                    setErrors(formError.response?.data?.errors)
            }
        },
        onSuccess: () => {
            resetForm()
        }
    })
})

const commitReorder = useDebounceFn(() => {
    hasQueuedReorder.value = false

    const memberIds = members.value.map((m) => m.id)
    const previousIds = dragSnapshot.value.map((m) => m.id)

    // Dropped back in the same order — nothing to persist.
    if (memberIds.join() === previousIds.join()) return

    const snapshot = dragSnapshot
    reorderMembersMutate(memberIds, {
        onError: () => {
            members.value = snapshot.value
        },
    })
}, 1000)

watchEffect(() => {
    if (!membersData.value) return;
    const sortedMembers: Member[] = [...membersData.value].sort((a, b) => a.payout_order - b.payout_order)

    members.value = sortedMembers
})

watchEffect(() => {
    if (values.email === '') {
        resetField('email')
    }
})


</script>

<template>
    <div class="flex flex-col gap-4">
        <Card>
            <CardContent>
                <form class="space-y-4" @submit="onSubmit">
                    <FormField v-slot="{ componentField }" name="name">
                        <FormItem class="flex-1">
                            <FormLabel>Name</FormLabel>
                            <FormControl>
                                <Input placeholder="Juan Dela Cruz" v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <FormField v-slot="{ componentField }" name="email">
                        <FormItem class="flex-1">
                            <FormLabel>Email <span class="font-normal text-muted-foreground">(optional)</span>
                            </FormLabel>
                            <FormControl>
                                <Input type="email" placeholder="juan@example.com" v-bind="componentField" />
                            </FormControl>
                            <FormDescription>Used to send payment reminders. Leave blank if they don't have one.
                            </FormDescription>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <Button type="submit" class="sm:mt-6">
                        <UserPlus data-icon="inline-start" />
                        Add member
                    </Button>
                </form>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle class="font-heading text-lg">Payout order</CardTitle>
                <CardDescription>Drag to reorder. Whoever's first receives the very first payout.</CardDescription>
            </CardHeader>

            <CardContent>
                <Empty v-if="members.length === 0">
                    <EmptyHeader>
                        <EmptyMedia variant="icon">
                            <Users />
                        </EmptyMedia>
                        <EmptyTitle>No members yet</EmptyTitle>
                        <EmptyDescription>Add your first member above to start building the payout order.
                        </EmptyDescription>
                    </EmptyHeader>
                </Empty>

                <VueDraggable v-else v-model="members" :animation="150" handle=".drag-handle"
                    class="flex flex-col gap-2" @start="onDragStart" @end="onDragEnd">
                    <div v-for="(member, index) in members" :key="member.id"
                        class="flex items-center gap-3 rounded-lg border border-border bg-card p-3">
                        <GripVertical class="drag-handle size-4 shrink-0 cursor-grab text-muted-foreground" />

                        <div class="flex size-8 shrink-0 items-center justify-center rounded-full font-mono text-sm"
                            :class="index === 0 ? 'bg-primary text-primary-foreground' : 'bg-muted text-muted-foreground'">
                            {{ index + 1 }}
                        </div>

                        <div class="flex min-w-0 flex-1 flex-col">
                            <div class="flex items-center gap-2">
                                <span class="truncate text-sm font-medium text-foreground">{{ member.name }}</span>
                                <Badge v-if="index === 0" variant="accent" class="shrink-0 gap-1">
                                    <Star class="size-3" />
                                    Receives first
                                </Badge>
                            </div>
                            <span class="flex items-center gap-1 text-xs text-muted-foreground">
                                <Mail class="size-3 shrink-0" />
                                <span class="truncate">{{ member.email || "No email — reminders won't be sent" }}</span>
                            </span>
                        </div>

                        <Button variant="ghost" size="icon" type="button"
                            class="shrink-0 text-muted-foreground hover:text-destructive"
                            @click="removeMemberMutate(member.id)">
                            <X />
                        </Button>
                    </div>
                </VueDraggable>
            </CardContent>
        </Card>
    </div>
</template>