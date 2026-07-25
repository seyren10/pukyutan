import { useUserStore } from '@/stores/user'
import { storeToRefs } from 'pinia'
import type { NavigationGuardNext, RouteLocation } from 'vue-router'

export const guestRouteGuard = async (
  to: RouteLocation,
  from: RouteLocation,
  next: NavigationGuardNext,
) => {
  const userStore = useUserStore()
  const { user } = storeToRefs(userStore)

  if (user.value) next({ name: 'home' })
  else next()
}
