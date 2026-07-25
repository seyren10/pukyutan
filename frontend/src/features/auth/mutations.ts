import { useMutation } from '@tanstack/vue-query'
import type { LoginCredentials } from './type'
import { login } from './api'
import { toast } from 'vue-sonner'

export const useAuthMutations = () => {
  const loginMutation = useMutation({
    mutationFn: (payload: LoginCredentials) => login(payload),
    onSuccess: () => {
      toast.info('Successfully logged in', {
        position: 'top-center',
      })
    },
  })

  return {
    loginMutation,
  }
}
