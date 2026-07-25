<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card'
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle, EmptyDescription, EmptyContent } from '@/components/ui/empty'
import { Button } from '@/components/ui/button'
import { MailCheck, RefreshCw } from '@lucide/vue'
</script>

<template>
    <Card>
        <CardContent>
            <Empty>
                <EmptyHeader>
                    <EmptyMedia variant="icon">
                        <MailCheck />
                    </EmptyMedia>
                    <EmptyTitle class="font-heading">Check your email</EmptyTitle>
                    <EmptyDescription>
                        We sent a verification link to your email address. Click the link to activate your account.
                    </EmptyDescription>
                </EmptyHeader>

                <EmptyContent>
                    <Button variant="outline">
                        <RefreshCw data-icon="inline-start" />
                        Resend email
                    </Button>
                </EmptyContent>
            </Empty>
        </CardContent>
    </Card>
</template>