import { keepPreviousData, QueryClient } from '@tanstack/vue-query'

export const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      staleTime: 1000 * 60 * 60, // 1 hour
      placeholderData: keepPreviousData,
    },
  },
})

