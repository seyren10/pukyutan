<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import { CalendarIcon, ChevronDown, Plus, PlusIcon } from '@lucide/vue'
import { DateFormatter, getLocalTimeZone, parseDate, today, type DateValue } from '@internationalized/date'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { InputGroup, InputGroupAddon, InputGroupInput, InputGroupText } from '@/components/ui/input-group'
import {
    FormField,
    FormItem,
    FormLabel,
    FormControl,
    FormDescription,
    FormMessage,
} from '@/components/ui/form'
import { FieldLabel, FieldDescription, FieldError } from '@/components/ui/field'
import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group'
import { NativeSelect, NativeSelectOption } from '@/components/ui/native-select'
import { Calendar } from '@/components/ui/calendar'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import type { GROUP_FREQUENCY_UNIT } from '@/features/group/constant'
import { createGroupSchema } from '@/features/group/schema'
import type { GroupFrequencyUnit, GroupSchema } from '@/features/group/type'
import AppButtonLoaderSwap from '../app/AppButtonLoaderSwap.vue'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '../ui/dropdown-menu/index.ts'
import { ButtonGroup, ButtonGroupSeparator } from '../ui/button-group/index.ts'

const emit = defineEmits<{
    (e: 'submit', payload: GroupSchema): void
    (e: 'addMembers'): void
}>()
const { initialValues, loading } = defineProps<{
    loading?: boolean,
    initialValues?: GroupSchema
}>()
const frequencyUnitOptions: { value: (typeof GROUP_FREQUENCY_UNIT)[number]; label: string }[] = [
    { value: 'day', label: 'days' },
    { value: 'week', label: 'weeks' },
    { value: 'month', label: 'months' },
]

const { handleSubmit, setFieldValue, errors, values } = useForm({
    validationSchema: toTypedSchema(createGroupSchema),
    initialValues: {
        name: '',
        frequency_unit: 'week',
        frequency_interval: 1,
        start_date: today(getLocalTimeZone()).toString(),
        ...initialValues
    },
})

// --- Contribution schedule presets (UI-only, maps down to the two real fields) ---
type SchedulePreset = 'weekly' | 'biweekly' | 'monthly' | 'yearly' | 'custom'

const schedulePreset = ref<SchedulePreset>('weekly')

const presetMap: Record<Exclude<SchedulePreset, 'custom'>, { unit: GroupFrequencyUnit; interval: number }> = {
    weekly: { unit: 'week', interval: 1 },
    biweekly: { unit: 'week', interval: 2 },
    monthly: { unit: 'month', interval: 1 },
    yearly: { unit: 'month', interval: 12 }
}

watch(schedulePreset, (preset) => {
    if (preset === 'custom') return

    setFieldValue('frequency_unit', presetMap[preset].unit)
    setFieldValue('frequency_interval', presetMap[preset].interval)
}, {
    immediate: true
})

const scheduleDescription = computed(() => {
    if (schedulePreset.value === 'weekly') return 'Members contribute every week.'
    if (schedulePreset.value === 'biweekly') return 'Members contribute every 2 weeks.'
    if (schedulePreset.value === 'monthly') return 'Members contribute every month.'
    if (schedulePreset.value === 'yearly') return 'Members contribute every year.'
    return null
})

const scheduleError = computed(() => errors.value.frequency_unit ?? errors.value.frequency_interval)

// --- Start date ---
const df = new DateFormatter('en-PH', { dateStyle: 'long' })



const startDate = computed<DateValue | undefined>({
    get: () =>
        values.start_date ? parseDate(values.start_date) : undefined,
    set: (value) => {
        setFieldValue('start_date', value ? value.toString() : undefined)
    },
})



const onSubmit = handleSubmit((values) => {
    emit('submit', values)
    emit('addMembers')
})

const handleCreateGroupOnly = async () => {
    handleSubmit((values) => {
        emit('submit', values)
    })()
}
</script>

<template>
    <form class="flex w-full flex-col gap-6" @submit="onSubmit">
        <FormField v-slot="{ componentField }" name="name">
            <FormItem>
                <FormLabel>Group name</FormLabel>
                <FormControl>
                    <Input placeholder="Pamilya fund" autocomplete="off" v-bind="componentField" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="contribution_amount">
            <FormItem>
                <FormLabel>Contribution amount</FormLabel>
                <FormControl>
                    <InputGroup>
                        <InputGroupAddon>
                            <InputGroupText>₱</InputGroupText>
                        </InputGroupAddon>
                        <InputGroupInput type="number" step="0.01" min="0.01" inputmode="decimal" placeholder="500"
                            class="font-mono" v-bind="componentField" />
                    </InputGroup>
                </FormControl>
                <FormDescription>What each member sends every cycle.</FormDescription>
                <FormMessage />
            </FormItem>
        </FormField>

        <!-- Composite field: one visible label, two underlying schema keys -->
        <div class="flex flex-col gap-3">
            <FieldLabel>Contribution schedule</FieldLabel>
            <ToggleGroup v-model="schedulePreset" type="single" class="flex flex-wrap">
                <ToggleGroupItem value="weekly" :disabled="schedulePreset === 'weekly'">Weekly</ToggleGroupItem>
                <ToggleGroupItem value="biweekly" :disabled="schedulePreset === 'biweekly'">Every 2 weeks
                </ToggleGroupItem>
                <ToggleGroupItem value="monthly" :disabled="schedulePreset === 'monthly'">Monthly</ToggleGroupItem>
                <ToggleGroupItem value="yearly" :disabled="schedulePreset === 'yearly'">Yearly</ToggleGroupItem>
                <ToggleGroupItem value="custom" :disabled="schedulePreset === 'custom'">Custom</ToggleGroupItem>
            </ToggleGroup>

            <div v-show="schedulePreset === 'custom'" class="flex items-center gap-2">
                <span class="text-sm text-muted-foreground">Every</span>
                <FormField v-slot="{ componentField }" name="frequency_interval">
                    <Input type="number" min="1" step="1" inputmode="numeric" aria-label="Number of units"
                        class="w-16 font-mono" v-bind="componentField" />
                </FormField>
                <FormField v-slot="{ componentField }" name="frequency_unit">
                    <NativeSelect aria-label="Unit" class="flex-1" v-bind="componentField as any">
                        <NativeSelectOption v-for="option in frequencyUnitOptions" :key="option.value"
                            :value="option.value">
                            {{ option.label }}
                        </NativeSelectOption>
                    </NativeSelect>
                </FormField>
            </div>

            <FieldDescription v-show="scheduleDescription">{{ scheduleDescription }}</FieldDescription>
            <FieldError v-if="scheduleError" :errors="[scheduleError]" />
        </div>

        <FormField v-slot="{ value }" name="start_date">
            <FormItem class="flex flex-col gap-2">
                <FormLabel>Start date</FormLabel>
                <Popover v-slot="{ close }">
                    <PopoverTrigger as-child>
                        <FormControl>
                            <Button type="button" variant="outline" class="w-full justify-start font-mono font-normal"
                                :class="!value && 'text-muted-foreground'">
                                <CalendarIcon data-icon="inline-start" />
                                {{ value ? df.format(parseDate(value).toDate(getLocalTimeZone())) : 'Pick a start date'
                                }}
                            </Button>
                        </FormControl>
                    </PopoverTrigger>
                    <PopoverContent class="w-auto max-h-[70vh] overflow-y-auto p-0" align="start">
                        <Calendar v-model="startDate" :min-value="today(getLocalTimeZone())" layout="month-and-year"
                            @update:model-value="close" />
                    </PopoverContent>
                </Popover>
                <FormDescription>The first cycle starts collecting on this date.</FormDescription>
                <FormMessage />
            </FormItem>
        </FormField>



        <ButtonGroup class="w-full">
            <Button type="submit" :disabled="loading" class="grow">
                <AppButtonLoaderSwap :loading="loading">
                    <Plus data-icon="inline-start" />
                </AppButtonLoaderSwap>
                <span class="truncate max-w-full">
                    {{ initialValues ? 'Update' : 'Create and add Members' }}
                </span>
            </Button>
            <template v-if="true">
                <ButtonGroupSeparator />
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button size="icon" :disabled="loading" aria-label="more options">
                            <ChevronDown />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem @select="handleCreateGroupOnly">
                            <PlusIcon />
                            <div class="flex flex-col">
                                <span class="font-medium">Create group</span>
                                <span class="text-muted-foreground">Create only the group and add members
                                    later.</span>
                            </div>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </template>
        </ButtonGroup>
    </form>
</template>