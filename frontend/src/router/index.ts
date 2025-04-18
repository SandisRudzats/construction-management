import { createRouter, createWebHistory} from 'vue-router';
import type { NavigationGuardNext, RouteLocationNormalized, RouteRecordRaw } from 'vue-router';
import LoginForm from '@/components/auth/LoginForm.vue';
import Dashboard from '@/views/Dashboard.vue';

// Construction Site Components
import CreateConstructionSite from '@/components/construction-site/CreateConstructionSite.vue';
import ManageConstructionSites from '@/components/construction-site/ManageConstructionSites.vue';

// Employee Components
import CreateEmployee from '@/components/employee/CreateEmployee.vue';
import EditEmployee from '@/components/employee/EditEmployee.vue';
import EmployeeProfile from '@/components/employee/EmployeeProfile.vue';

const routes: RouteRecordRaw[] = [
  {
    path: '/login',
    name: 'Login',
    component: LoginForm,
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true },
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

router.beforeEach(
  (to: RouteLocationNormalized, from: RouteLocationNormalized, next: NavigationGuardNext) => {
    const requiresAuth = to.matched.some((record) => record.meta.requiresAuth);
    const isLoggedIn = !!localStorage.getItem('user'); // Or use your auth mechanism

    if (requiresAuth && !isLoggedIn) {
      next('/login');
    } else if (to.path === '/' && isLoggedIn) {
      next('/dashboard');
    } else {
      next();
    }
  },
);

export default router;
