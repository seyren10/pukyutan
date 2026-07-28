<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Empty, EmptyContent, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { useUserStore } from '@/stores/user';
import { PackageOpen } from '@lucide/vue';
import { storeToRefs } from 'pinia';


const userStore = useUserStore()
const { isEmailVerified } = storeToRefs(userStore)
</script>
<template>
    <Empty>
        <EmptyHeader>
            <EmptyMedia variant="icon">
                <PackageOpen />
            </EmptyMedia>
            <EmptyTitle>No groups yet</EmptyTitle>
            <EmptyDescription>Create your first paluwagan group to get started.</EmptyDescription>
        </EmptyHeader>
        <EmptyContent>
            <Button v-if="isEmailVerified">
                <Plus data-icon="inline-start" />
                Create a group
            </Button>
        </EmptyContent>
    </Empty>
</template>


<style scoped></style>