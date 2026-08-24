<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { useColorMode } from '@vueuse/core'
import { Button } from '@/components/ui/button'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import { MoonIcon, SunIcon } from '@lucide/vue';
import { cn } from '@/lib/utils'

// Optional extra classes for the trigger button — e.g. a larger touch
// target on mobile headers — without every caller needing its own wrapper.
const { triggerClass } = defineProps<{ triggerClass?: HTMLAttributes['class'] }>()

// Pass { disableTransition: false } to enable transitions
const mode = useColorMode()
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button variant="ghost" size="icon" :class="cn('rounded-full', triggerClass)">
        <SunIcon class="h-[1.2rem] w-[1.2rem] rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0" />
        <MoonIcon class="absolute h-[1.2rem] w-[1.2rem] rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100" />
        <span class="sr-only">Toggle theme</span>
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end">
      <DropdownMenuItem @click="mode = 'light'">
        Light
      </DropdownMenuItem>
      <DropdownMenuItem @click="mode = 'dark'">
        Dark
      </DropdownMenuItem>
      <DropdownMenuItem @click="mode = 'auto'">
        System
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>