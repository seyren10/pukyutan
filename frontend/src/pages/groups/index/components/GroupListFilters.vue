<script setup lang="ts">
import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { GROUP_STATUS_META, GROUP_SORT_OPTIONS } from '@/features/group/constant'
import { ArrowUpDown } from '@lucide/vue'

const status = defineModel<string>('status', { default: 'all' })
const sort = defineModel<string>('sort', { default: GROUP_SORT_OPTIONS[0].value })

const statusOptions = [
    { value: 'all', label: 'All' },
    ...Object.entries(GROUP_STATUS_META).map(([value, meta]) => ({ value, label: meta.label })),
]

// ToggleGroup's single mode deselects to an empty string if the active item
// is clicked again — fall back to "all" rather than leaving the filter in a
// blank, ambiguous state with nothing visibly selected.
const handleStatusUpdate = (value: unknown) => {
    if (typeof value !== 'string') return
    status.value = value || 'all'
}
</script>

<template>
    <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center">
        <ToggleGroup type="single" variant="outline" :model-value="status" @update:model-value="handleStatusUpdate"
            class="w-full overflow-x-auto [scrollbar-width:none] sm:w-fit sm:overflow-visible [&::-webkit-scrollbar]:hidden">
            <ToggleGroupItem v-for="option in statusOptions" :key="option.value" :value="option.value"
                class="text-xs">
                {{ option.label }}
            </ToggleGroupItem>
        </ToggleGroup>

        <Select v-model="sort">
            <SelectTrigger size="sm" class="w-full sm:ml-auto sm:w-[190px]">
                <ArrowUpDown data-icon="inline-start" />
                <SelectValue />
            </SelectTrigger>
            <SelectContent>
                <SelectGroup>
                    <SelectItem v-for="option in GROUP_SORT_OPTIONS" :key="option.value" :value="option.value">
                        {{ option.label }}
                    </SelectItem>
                </SelectGroup>
            </SelectContent>
        </Select>
    </div>
</template>