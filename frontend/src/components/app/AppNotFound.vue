<script setup lang="ts">
import NotfoundImage from "@/assets/404.webp";
import { Button } from "../ui/button";
import { Home } from "@lucide/vue";
</script>
<template>
    <div class="grid h-dvh place-content-center">
        <div class="text-center flex flex-col gap-6">
            <img :src="NotfoundImage" class="w-32 md:w-md mx-auto" />
            <div class="space-y-2">
                <h1 class="text-xl font-semibold text-foreground md:text-2xl">
                    Oops! Page not found.
                </h1>
                <p class="text-sm md:text-base text-muted-foreground max-w-[50ch]">We couldn't find the page you're
                    looking
                    for. It might
                    have been moved or doesn't exist anymore. </p>
            </div>

            <Button class="w-fit mx-auto" as-child>
                <RouterLink :to="{ name: 'dashboard' }" replace>
                    <Home />
                    Back to home page
                </RouterLink>
            </Button>
        </div>
    </div>
</template>



<style scoped></style>