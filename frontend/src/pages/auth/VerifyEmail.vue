<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card'
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle, EmptyDescription, EmptyContent } from '@/components/ui/empty'
import { Button } from '@/components/ui/button'
import { ArrowRight, MailCheck, RefreshCw } from '@lucide/vue'
import UnverifiedAlert from '../groups/index/components/UnverifiedAlert.vue';
import { useUserStore } from '@/stores/user.ts';
import { storeToRefs } from 'pinia';
import { onMounted, useTemplateRef } from 'vue';

const userStore = useUserStore()
const { isEmailVerified } = storeToRefs(userStore)
const unverifiedRef = useTemplateRef('unverifiedRef')

onMounted(() => {
    if (!unverifiedRef.value) return;


    if (
        unverifiedRef.value.cooldown <= 0
    ) {
        unverifiedRef.value.handleResend()
    }
})
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
                    <div class="flex flex-col justify-center gap-4">
                        <UnverifiedAlert :verified="isEmailVerified" ref="unverifiedRef" #="{ cooldown, handleResend }">
                            <Button variant="outline" :disabled="cooldown" @click="handleResend()">
                                <RefreshCw data-icon="inline-start" />
                                <template v-if="cooldown <= 0">Resend email</template>
                                <template v-else> Resend after {{ cooldown }}s</template>
                            </Button>
                        </UnverifiedAlert>
                        <Button variant="link" as-child size="xs">
                            <RouterLink :to="{ name: 'app' }">
                                Dashboard
                                <ArrowRight data-icon="inline-start" />
                            </RouterLink>
                        </Button>
                    </div>
                </EmptyContent>
            </Empty>
        </CardContent>
    </Card>
</template>