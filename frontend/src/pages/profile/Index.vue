<script setup lang="ts">
import { Separator } from '@/components/ui/separator'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'

import UpdateProfileForm from '@/components/profile/UpdateProfileForm.vue'
import UpdatePasswordForm from '@/components/profile/UpdatePasswordForm.vue'

import ProfileInformation from '@/components/profile/ProfileInformation.vue'
import AvatarPicker from '@/components/profile/AvatarPicker.vue'

</script>

<template>
    <div class="mx-auto flex w-full max-w-2xl flex-col gap-6 px-4 py-10">
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-semibold tracking-tight">Account settings</h1>
            <p class="text-sm text-muted-foreground">
                Manage your profile details and account security.
            </p>
        </div>

        <Separator />

        <Tabs default-value="profile">
            <TabsList>
                <TabsTrigger value="profile">Profile</TabsTrigger>
                <TabsTrigger value="security">Security</TabsTrigger>
            </TabsList>

            <TabsContent value="profile" class="flex flex-col gap-6">
                <ProfileInformation>
                    <AvatarPicker />
                    <UpdateProfileForm />
                </ProfileInformation>
            </TabsContent>

            <TabsContent value="security" class="flex flex-col gap-6">
                <UpdatePasswordForm />
            </TabsContent>
        </Tabs>
    </div>
</template>