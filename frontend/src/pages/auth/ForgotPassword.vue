<script setup lang="ts">
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card'
import { FieldGroup, Field, FieldLabel } from '@/components/ui/field'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { ArrowLeft, Send } from '@lucide/vue'
</script>

<template>

    <Card>
        <CardHeader>
            <CardTitle class="font-heading text-xl">Reset your password</CardTitle>
            <CardDescription>
                Enter your email and we'll send you a link to reset your password.
            </CardDescription>
        </CardHeader>

        <CardContent>
            <FieldGroup>
                <Field>
                    <FieldLabel for="email">Email</FieldLabel>
                    <Input id="email" type="email" placeholder="name@example.com" autocomplete="email" />
                </Field>

                <Button type="submit" class="w-full">
                    <Send data-icon="inline-start" />
                    Send reset link
                </Button>
            </FieldGroup>
        </CardContent>

        <CardFooter class="justify-center text-sm text-muted-foreground">
            <RouterLink :to="{ name: 'login' }" class="flex items-center gap-1 text-foreground hover:underline">
                <ArrowLeft class="size-4" />
                Back to log in
            </RouterLink>
        </CardFooter>
    </Card>
</template>