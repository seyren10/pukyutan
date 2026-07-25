import z from 'zod'

export const userRole = ['owner', 'member'] as const

export const loginCredentialSchema = z.object({
  email: z.string(),
  password: z.string(),
})
