<script setup lang="ts">
import { renameGroupSchema } from '@/features/group/schema';
import type { RenameGroupSchema } from '@/features/group/type';
import { toTypedSchema } from '@vee-validate/zod';
import { useForm } from 'vee-validate';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '../ui/form';
import { Input } from '../ui/input';
import { Button } from '../ui/button';
import { Check } from '@lucide/vue';

const { groupName } = defineProps<{
    groupName: string
}>()
const emit = defineEmits<{
    (e: 'submit', payload: RenameGroupSchema): void;
}>()
const { handleSubmit } = useForm({
    validationSchema: toTypedSchema(renameGroupSchema),
    initialValues: {
        name: groupName
    }
})

const submit = handleSubmit((values) => emit('submit', values))
</script>

<template>
    <form @submit="submit" class="space-y-4">
        <FormField v-slot="{ componentField }" name="name">
            <FormItem>
                <FormLabel>Group name</FormLabel>
                <FormControl>
                    <Input placeholder="Pamilya fund" autocomplete="off" v-bind="componentField" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <Button>
            <Check />
            Update Group
        </Button>
    </form>
</template>


<style scoped></style>