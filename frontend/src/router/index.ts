import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw, NavigationGuardNext, RouteLocationNormalized } from 'vue-router'
import LoginForm from '@/components/LoginForm.vue'
import Dashboard from '@/views/Dashboard.vue';

const routes: Array<RouteRecordRaw> = [
  {
    path: '/login',
    name: 'Login',
    component: LoginForm,
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('@/views/Dashboard.vue'),
    meta: { requiresAuth: true }, // Add meta field
  },
  {
    path: '/',
    redirect: '/dashboard', // Redirect to dashboard if logged in
  },
  {
    path: '/employees',
    name: 'Employees',
    component: Dashboard, //  Use Dashboard, and the component will be shown conditionally
  },
  {
    path: '/construction-sites',
    name: 'ConstructionSites',
    component: Dashboard, //  Use Dashboard, and the component will be shown conditionally
  },
  {
    path: '/work-tasks',
    name: 'WorkTasks',
    component: Dashboard, //  Use Dashboard, and the component will be shown conditionally
  },
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
})

router.beforeEach(
  (to: RouteLocationNormalized, from: RouteLocationNormalized, next: NavigationGuardNext) => {
    const requiresAuth = to.matched.some((record) => record.meta.requiresAuth)
    const isLoggedIn = !!localStorage.getItem('user') // Check if user is logged in

    if (requiresAuth && !isLoggedIn) {
      next('/login') // Redirect to login if not logged in
    } else if (to.path === '/' && isLoggedIn) {
      next('/dashboard')
    } else {
      next() // Proceed to the route
    }
  },
)

export default router
