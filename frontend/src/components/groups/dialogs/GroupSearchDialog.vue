<script setup lang="ts">
import { Command, CommandGroup, CommandList } from '@/components/ui/command'
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Spinner } from '@/components/ui/spinner'
import { getGroupSearchQueryOptions } from '@/features/group/query'
import { useQuery } from '@tanstack/vue-query'
import { refDebounced, useEventListener } from '@vueuse/core'
import { Search, SearchX } from '@lucide/vue'
import { ListboxFilter } from 'reka-ui'
import { computed, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import GroupSearchResultItem from '@/components/groups/GroupSearchResultItem.vue'

const dialog = defineModel<boolean>({ default: false })
const router = useRouter()
const route = useRoute()

const search = ref('')
// Debounced independently of the dashboard's own query — this dialog only
// ever talks to getGroupSearchQueryOptions (its own ["groups","search",...]
// cache branch), so typing here can never trigger or race with the
// dashboard's paginated ["groups","list",...] query underneath it.
const debouncedSearch = refDebounced(search, 500)

const { data, isFetching } = useQuery(getGroupSearchQueryOptions(debouncedSearch))
const results = computed(() => data.value?.data ?? [])
const hasSearched = computed(() => debouncedSearch.value.trim().length > 0)

// Reset on close so re-opening always starts from a clean slate.
watch(dialog, (open) => {
    if (!open) search.value = ''
})

const handleSelect = (groupId: number) => {
    dialog.value = false

    if (route.matched.some(m => m.name === 'groups.detail'))
        router.replace({ name: 'groups.detail', params: { id: groupId } })
    else
        router.push({ name: 'groups.detail', params: { id: groupId } })
}

// Global ⌘K / Ctrl+K to open, mirroring the spotlight convention — works
// from anywhere the dialog is mounted, not just when the header trigger is
// focused.
useEventListener(window, 'keydown', (e: KeyboardEvent) => {
    if (e.key.toLowerCase() === 'k' && (e.metaKey || e.ctrlKey)) {
        e.preventDefault()
        dialog.value = !dialog.value
    }
    if (e.key === 'Escape') {
        dialog.value = false
    }
})
</script>

<template>
    <Dialog v-model:open="dialog">
        <DialogContent class="gap-0 overflow-hidden p-0 sm:max-w-lg" :show-close-button="false">
            <DialogHeader class="sr-only">
                <DialogTitle>Search groups</DialogTitle>
                <DialogDescription>Search your groups by name</DialogDescription>
            </DialogHeader>

            <!-- Note: this Command's own local text-filtering never engages here —
                 the search box below binds to our own `search` ref instead of
                 Command's internal filterState.search, so `results` (already
                 server-filtered by getGroupSearchQueryOptions) render as-is. -->
            <Command>
                <div data-slot="command-input-wrapper" class="flex h-12 items-center gap-2 border-b px-4">
                    <Search class="size-4 shrink-0 text-muted-foreground" />
                    <ListboxFilter v-model="search" auto-focus placeholder="Search groups by name..."
                        class="flex h-10 w-full rounded-md bg-transparent py-3 text-sm outline-hidden placeholder:text-muted-foreground disabled:cursor-not-allowed disabled:opacity-50" />
                    <Spinner v-if="isFetching" class="size-4 shrink-0 text-muted-foreground" />
                </div>

                <CommandList class="max-h-80 p-1.5">
                    <template v-if="!hasSearched">
                        <div class="flex flex-col items-center gap-1.5 px-4 py-10 text-center">
                            <Search class="size-5 text-muted-foreground/60" />
                            <p class="text-sm text-muted-foreground">Start typing to search your groups</p>
                        </div>
                    </template>
                    <template v-else-if="!isFetching && results.length === 0">
                        <div class="flex flex-col items-center gap-1.5 px-4 py-10 text-center">
                            <SearchX class="size-5 text-muted-foreground/60" />
                            <p class="text-sm text-muted-foreground">No groups match "{{ debouncedSearch }}"</p>
                        </div>
                    </template>
                    <template v-else>
                        <CommandGroup>
                            <GroupSearchResultItem v-for="group in results" :key="group.id" :group="group"
                                @select="handleSelect(group.id)" />
                        </CommandGroup>
                    </template>
                </CommandList>
            </Command>
        </DialogContent>
    </Dialog>
</template>