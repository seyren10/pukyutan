<script setup lang="ts">
import { computed } from 'vue'
import { Plus, Eye } from '@lucide/vue'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import CycleTracker, { type CycleState } from '@/components/groups/CycleTracker.vue'

// TODO: replace with `useQuery({ queryKey: ['groups'], queryFn: fetchGroups })`
// once the groups endpoint exists. Shape matches what that query will return.
interface OwnedGroup {
  id: string
  name: string
  status: 'active' | 'draft' | 'completed'
  amount?: number
  frequency?: 'weekly' | 'monthly'
  cycleNumber?: number
  dueDate?: string
  membersAdded?: number
  cycles?: { state: CycleState }[]
}

interface SharedGroup {
  id: string
  name: string
  ownerName: string
  round: number
  cycle: number
}

const ownedGroups: OwnedGroup[] = [
  {
    id: 'pamilya-fund',
    name: 'pamilya fund',
    status: 'active',
    amount: 500,
    frequency: 'weekly',
    cycleNumber: 4,
    dueDate: 'jul 28',
    cycles: [
      { state: 'disbursed' },
      { state: 'disbursed' },
      { state: 'disbursed' },
      { state: 'current' },
      { state: 'future' },
    ],
  },
  {
    id: 'office-squad',
    name: 'office squad',
    status: 'active',
    amount: 1000,
    frequency: 'monthly',
    cycleNumber: 2,
    dueDate: 'aug 1',
    cycles: [
      { state: 'disbursed' },
      { state: 'current' },
      { state: 'future' },
      { state: 'future' },
    ],
  },
  {
    id: 'barkada-savings',
    name: 'barkada savings',
    status: 'draft',
    membersAdded: 4,
  },
]

const sharedGroups: SharedGroup[] = [
  {
    id: 'tita-corine',
    name: "tita corine's paluwagan",
    ownerName: 'corine s.',
    round: 1,
    cycle: 3,
  },
]

const activeCount = computed(
  () => ownedGroups.filter((g) => g.status === 'active').length,
)
const draftCount = computed(
  () => ownedGroups.filter((g) => g.status === 'draft').length,
)

const statusVariant: Record<OwnedGroup['status'], 'success' | 'accent' | 'secondary'> = {
  active: 'success',
  draft: 'accent',
  completed: 'secondary',
}

function formatAmount(amount: number) {
  return `₱${amount.toLocaleString('en-PH')}`
}
</script>

<template>
  <div class="flex flex-col gap-8">
    <div class="flex items-start justify-between gap-4">
      <div class="flex flex-col gap-1">
        <p class="text-sm text-primary">your groups</p>
        <h1 class="font-heading text-2xl font-semibold text-foreground">
          {{ activeCount }} active, {{ draftCount }} draft
        </h1>
      </div>
      <Button>
        <Plus data-icon="inline-start" />
        new group
      </Button>
    </div>

    <div class="grid gap-4" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr))">
      <Card
        v-for="group in ownedGroups"
        :key="group.id"
        :class="group.status === 'draft' ? 'border-dashed' : undefined"
      >
        <CardHeader class="flex-row items-start justify-between">
          <CardTitle class="font-heading">{{ group.name }}</CardTitle>
          <Badge :variant="statusVariant[group.status]">{{ group.status }}</Badge>
        </CardHeader>

        <CardContent class="flex flex-col gap-4">
          <template v-if="group.status === 'draft'">
            <p class="text-sm text-muted-foreground">
              {{ group.membersAdded }} members added · not yet activated
            </p>
            <Button variant="outline" class="w-fit">activate group</Button>
          </template>

          <template v-else>
            <CycleTracker :cycles="group.cycles ?? []" size="mini" />
            <div class="flex justify-between font-mono text-xs text-muted-foreground">
              <span>{{ formatAmount(group.amount!) }} · {{ group.frequency }}</span>
              <span>cycle {{ group.cycleNumber }} due {{ group.dueDate }}</span>
            </div>
          </template>
        </CardContent>
      </Card>
    </div>

    <div v-if="sharedGroups.length" class="flex flex-col gap-3">
      <p class="text-sm text-primary">shared with you</p>

      <Card v-for="group in sharedGroups" :key="group.id">
        <CardContent class="flex items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <Eye class="text-muted-foreground" />
            <div class="flex flex-col">
              <span class="font-heading text-base font-medium text-foreground">
                {{ group.name }}
              </span>
              <span class="text-sm text-muted-foreground">
                view only · owned by {{ group.ownerName }}
              </span>
            </div>
          </div>
          <span class="font-mono text-sm text-muted-foreground">
            round {{ group.round }} · cycle {{ group.cycle }}
          </span>
        </CardContent>
      </Card>
    </div>
  </div>
</template>