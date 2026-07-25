import type { VariantProps } from "class-variance-authority"
import { cva } from "class-variance-authority"

export { default as Badge } from "./Badge.vue"

export const badgeVariants = cva(
  'inline-flex items-center rounded-md border px-2 py-0.5 text-xs font-medium w-fit whitespace-nowrap shrink-0 gap-1 [&>svg]:size-3 [&>svg]:pointer-events-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] transition-[color,box-shadow] overflow-hidden',
  {
    variants: {
      variant: {
        default: 'border-transparent bg-primary text-primary-foreground [a&]:hover:bg-primary/90',
        secondary: 'border-transparent bg-secondary text-secondary-foreground [a&]:hover:bg-secondary/90',
        destructive: 'border-transparent bg-destructive text-white [a&]:hover:bg-destructive/90',
        outline: 'text-foreground border-border [a&]:hover:bg-accent [a&]:hover:text-accent-foreground',
        // Custom: pale amber tint, used for "draft" status (section 8).
        accent: 'border-transparent bg-accent text-accent-foreground [a&]:hover:bg-accent/90',
        // Custom: added per design doc section 3, for "active"/healthy/collected states.
        success: 'border-transparent bg-success text-success-foreground [a&]:hover:bg-success/90',
        // Custom: shares the amber family with `accent` but reserved for cautionary
        // (not merely draft) states, e.g. "payment overdue".
        warning: 'border-transparent bg-accent/60 text-accent-foreground [a&]:hover:bg-accent/80',
      },
    },
    defaultVariants: {
      variant: 'default',
    },
  },
)
export type BadgeVariants = VariantProps<typeof badgeVariants>
