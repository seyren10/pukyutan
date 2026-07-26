import 'vue-router'

declare module 'vue-router' {
  interface RouteMeta {
    // Make properties optional or required as needed
    requiresAuth?: boolean
    requiresGuest?: boolean
  }
}