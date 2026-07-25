<script setup lang="ts">
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card'
import { FieldGroup, Field, FieldLabel, FieldDescription } from '@/components/ui/field'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { UserPlus } from '@lucide/vue'
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="font-heading text-xl">Create an account</CardTitle>
            <CardDescription>Start tracking your paluwagan groups.</CardDescription>
        </CardHeader>

        <CardContent>
            <FieldGroup>
                <Field>
                    <FieldLabel for="name">Name</FieldLabel>
                    <Input id="name" placeholder="Juan Dela Cruz" autocomplete="name" />
                </Field>

                <Field>
                    <FieldLabel for="email">Email</FieldLabel>
                    <Input id="email" type="email" placeholder="name@example.com" autocomplete="email" />
                </Field>

                <Field>
                    <FieldLabel for="password">Password</FieldLabel>
                    <Input id="password" type="password" autocomplete="new-password" />
                    <FieldDescription>At least 8 characters.</FieldDescription>
                </Field>

                <Field>
                    <FieldLabel for="password_confirmation">Confirm password</FieldLabel>
                    <Input id="password_confirmation" type="password" autocomplete="new-password" />
                </Field>

                <Button type="submit" class="w-full">
                    <UserPlus data-icon="inline-start" />
                    Create account
                </Button>
            </FieldGroup>
        </CardContent>

        <CardFooter class="justify-center text-sm text-muted-foreground">
            Already have an account?
            <RouterLink :to="{ name: 'login' }" class="ml-1 text-foreground hover:underline">
                Log in
            </RouterLink>
        </CardFooter>
    </Card>
</template>