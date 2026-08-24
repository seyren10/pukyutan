import { useMediaQuery } from "@vueuse/core";

// Mirrors Tailwind's `md` breakpoint (768px) so "mobile" here means exactly
// what `md:` utilities elsewhere in the app already treat as "not mobile".
// Centralized so every component that needs to branch on viewport size
// (bottom nav, responsive dialogs, etc.) agrees on the same cutoff.
export const useIsMobile = () => useMediaQuery("(max-width: 767px)");