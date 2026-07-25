<script setup lang="ts">
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card'
import { FieldGroup, Field, FieldLabel } from '@/components/ui/field'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Lock } from '@lucide/vue'
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="font-heading text-xl">Log in</CardTitle>
            <CardDescription>Enter your email and password to continue.</CardDescription>
        </CardHeader>

        <CardContent>
            <FieldGroup>
                <Field>
                    <FieldLabel for="email">Email</FieldLabel>
                    <Input id="email" type="email" placeholder="name@example.com" autocomplete="email" />
                </Field>

                <Field>
                    <div class="flex items-center justify-between">
                        <FieldLabel for="password">Password</FieldLabel>
                        <RouterLink :to="{ name: 'forgot-password' }"
                            class="text-sm text-muted-foreground hover:text-foreground">
                            Forgot password?
                        </RouterLink>
                    </div>
                    <Input id="password" type="password" autocomplete="current-password" />
                </Field>

                <Button type="submit" class="w-full">
                    <Lock data-icon="inline-start" />
                    Log in
                </Button>
            </FieldGroup>
        </CardContent>

        <CardFooter class="justify-center text-sm text-muted-foreground">
            Don't have an account?
            <RouterLink :to="{ name: 'register' }" class="ml-1 text-foreground hover:underline">
                Create one
            </RouterLink>
        </CardFooter>
    </Card>
</template>