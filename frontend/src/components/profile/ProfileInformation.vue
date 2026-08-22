<script setup lang="ts">
import { UserIcon } from '@lucide/vue';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '../ui/card';


</script>
<template>
    <Card>
        <CardHeader>
            <CardTitle class="flex items-center gap-2">
                <UserIcon class="size-4 text-muted-foreground" />
                Profile information
            </CardTitle>
            <CardDescription>Update your name, email, and photo.</CardDescription>
        </CardHeader>

        <CardContent class="space-y-4">
            <slot />
        </CardContent>

        <CardFooter>
            <slot name="footer" />
        </CardFooter>
    </Card>
</template>


<style scoped></style>