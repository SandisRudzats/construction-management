import { createRouter, createWebHistory} from 'vue-router';
import type { NavigationGuardNext, RouteLocationNormalized, RouteRecordRaw } from 'vue-router';
import LoginForm from '@/components/auth/LoginForm.vue';
import Dashboard from '@/views/Dashboard.vue';
// import AccessControlWrapper from '@/components/auth/AccessControlWrapper.vue'; // You can use this, but I'll show a more common approach

// Construction Site Components
import CreateConstructionSite from '@/components/construction-site/CreateConstructionSite.vue';
import DeleteConstructionSite from '@/components/construction-site/DeleteConstructionSite.vue';
import EditConstructionSite from '@/components/construction-site/EditConstructionSite.vue';
import SiteWorkTasks from '@/components/construction-site/SiteWorkTasks.vue';
import ViewConstructionSites from '@/components/construction-site/ViewConstructionSites.vue';

// Employee Components
import CreateEmployee from '@/components/employee/CreateEmployee.vue';
import EditEmployee from '@/components/employee/EditEmployee.vue';
import EmployeeProfile from '@/components/employee/EmployeeProfile.vue';
import ManagerSubordinates from '@/components/employee/ManagerSubordinates.vue';
import ViewEmployees from '@/components/employee/ViewEmployees.vue';

// Work Task Components
import CreateWorkTask from '@/components/work-task/CreateWorkTask.vue';
import DeleteWorkTask from '@/components/work-task/DeleteWorkTask.vue';
import EditWorkTask from '@/components/work-task/EditWorkTask.vue';
import EmployeeWorkTasks from '@/components/work-task/EmployeeWorkTasks.vue';
import ViewWorkTasks from '@/components/work-task/ViewWorkTasks.vue';

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
  {
    path: '/',
    redirect: '/dashboard',
  },
  // Employee Routes
  {
    path: '/employees',
    name: 'Employees',
    component: ViewEmployees,
    meta: { requiresAuth: true },
  },
  {
    path: '/employees/create',
    name: 'CreateEmployee',
    component: CreateEmployee,
    meta: { requiresAuth: true },
  },
  {
    path: '/employees/edit/:id',
    name: 'EditEmployee',
    component: EditEmployee,
    meta: { requiresAuth: true },
  },
  {
    path: '/employees/profile/:id',
    name: 'EmployeeProfile',
    component: EmployeeProfile,
    meta: { requiresAuth: true },
  },
  {
    path: '/employees/subordinates/:id',
    name: 'ManagerSubordinates',
    component: ManagerSubordinates,
    meta: { requiresAuth: true },
  },
  // Construction Site Routes
  {
    path: '/construction-sites',
    name: 'ConstructionSites',
    component: ViewConstructionSites,
    meta: { requiresAuth: true },
  },
  {
    path: '/construction-sites/create',
    name: 'CreateConstructionSite',
    component: CreateConstructionSite,
    meta: { requiresAuth: true },
  },
  {
    path: '/construction-sites/delete/:id',
    name: 'DeleteConstructionSite',
    component: DeleteConstructionSite,
    meta: { requiresAuth: true },
  },
  {
    path: '/construction-sites/edit/:id',
    name: 'EditConstructionSite',
    component: EditConstructionSite,
    meta: { requiresAuth: true },
  },
  {
    path: '/construction-sites/tasks/:id',
    name: 'SiteWorkTasks',
    component: SiteWorkTasks,
    meta: { requiresAuth: true },
  },
  // Work Task Routes
  {
    path: '/work-tasks',
    name: 'WorkTasks',
    component: ViewWorkTasks,
    meta: { requiresAuth: true },
  },
  {
    path: '/work-tasks/create',
    name: 'CreateWorkTask',
    component: CreateWorkTask,
    meta: { requiresAuth: true },
  },
  {
    path: '/work-tasks/delete/:id',
    name: 'DeleteWorkTask',
    component: DeleteWorkTask,
    meta: { requiresAuth: true },
  },
  {
    path: '/work-tasks/edit/:id',
    name: 'EditWorkTask',
    component: EditWorkTask,
    meta: { requiresAuth: true },
  },
  {
    path: '/work-tasks/employee/:id',
    name: 'EmployeeWorkTasks',
    component: EmployeeWorkTasks,
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
