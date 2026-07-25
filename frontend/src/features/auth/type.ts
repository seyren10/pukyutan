import type z from 'zod'
import type { loginCredentialSchema, userRole } from './schema'
import type { GroupPivot } from '../groups/type'

export type LoginCredentials = {
  email: string
  password: string
}

export type RegistrationPayload = {
  name: string
  email: string
  password: string
  password_confirmation: string
}

export type User = {
  id: number
  name: string
  email: string
  email_verified_at: string
  google_id: null | string
  avatar: null | string
}

export type UserId = User['id']
export type UserWithPivot = User & {
  pivot: GroupPivot
}

export type UserRole = (typeof userRole)[number]

export type LoginCredential = z.infer<typeof loginCredentialSchema>
