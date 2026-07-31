<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { EyeIcon, MoreVertical, Users2Icon } from '@lucide/vue';


</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <slot>
                <Button size="icon" variant="ghost">
                    <MoreVertical />
                </Button>
            </slot>
        </DropdownMenuTrigger>

        <DropdownMenuContent align="end">
            <DropdownMenuItem>
                <EyeIcon /> View Group
            </DropdownMenuItem>
            <DropdownMenuItem>
                <Users2Icon /> Add Members
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>


<style scoped></style>