import type { NavigationGuardNext, RouteLocation } from 'vue-router'
import { useUserStore } from '@/stores/user'
import { queryClient } from '../services/tanstack-query/query-client'
import { getUserQueryOptions } from '@/features/groups/query-options'

export const ensureAuthenticated = async (
  to: RouteLocation,
  from: RouteLocation,
  next: NavigationGuardNext,
) => {
  const userStore = useUserStore()
  try {
    const user = await queryClient.fetchQuery(getUserQueryOptions)
    userStore.setUser(user)
    next()
  } catch {
    userStore.setUser(null)
    next({ name: 'login', replace: true, query: { ref: to.fullPath, session_expired: 'true' } })
  }
}
