import type { User } from '@/features/auth/type'
import { StorageSerializers, useLocalStorage } from '@vueuse/core'
import { defineStore } from 'pinia'
import { readonly } from 'vue'

export const useUserStore = defineStore('user', () => {
  const user = useLocalStorage<User | null>('user', null, {
    serializer: StorageSerializers.object,
  })

  const setUser = (payload: User | null) => (user.value = payload)
  return {
    user: readonly(user),
    setUser,
  }
})
