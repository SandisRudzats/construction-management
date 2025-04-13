<template>
  <div class="sidebar">
    <router-link
      v-if="hasPermission('dashboard')"
      to="/dashboard"
      class="sidebar-option gray-button"
      @click="emitSection('dashboard')"
    >
      Dashboard
    </router-link>
    <div v-if="hasPermission('employees')" class="sidebar-group">
      <div
        class="sidebar-option gray-button group-title"
        @click="toggleEmployeesSection"
      >
        Employees
        <span :class="{ 'arrow-icon': true, 'open': showEmployeesSection }">▶</span>
      </div>
      <div
        v-if="showEmployeesSection"
        class="sidebar-sub-options"
      >
        <router-link
          v-if="hasPermission('viewEmployees')"
          to="/employees"
          class="sidebar-option sub-option"
          @click="emitSection('employees')"
        >
          View Employees
        </router-link>
        <router-link
          v-if="hasPermission('createEmployee')"
          to="/employees/create"
          class="sidebar-option sub-option"
          @click="emitSection('create-employee')"
        >
          Create Employee
        </router-link>
      </div>
    </div>
    <router-link
      v-if="hasPermission('constructionSites')"
      to="/construction-sites"
      class="sidebar-option gray-button"
      @click="emitSection('construction-sites')"
    >
      Sites
    </router-link>
    <router-link
      v-if="hasPermission('workTasks')"
      to="/work-tasks"
      class="sidebar-option gray-button"
      @click="emitSection('work-tasks')"
    >
      Tasks
    </router-link>
    <router-link
      v-if="hasPermission('settings')"
      to="/settings"
      class="sidebar-option gray-button"
      @click="emitSection('settings')"
    >
      Settings
    </router-link>
    <button class="sidebar-option gray-button" @click="handleLogout">Logout</button>
    <div v-if="!userRole">
      <p>User Role is not set. Please log in.</p>
    </div>
    <div v-else>
      <p>User Role: {{ userRole }}</p>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';
import { useUserStore } from '@/stores/user'; // Import the user store

export default defineComponent({
  name: 'Sidebar',
  emits: ['section-selected'],
  setup(props, { emit }) {
    const router = useRouter();
    const showEmployeesSection = ref(false);
    const userStore = useUserStore();  // Get user data from store
    const userRole = computed(() => userStore.user?.role || null);

    interface PermissionMapping {
      [key: string]: string[];
    }
    // Define a mapping of roles to permissions
    const permissions: PermissionMapping = {
      'Administrator': ['dashboard', 'employees', 'viewEmployees', 'createEmployee', 'constructionSites', 'workTasks', 'settings'],
      'Manager': ['dashboard', 'employees', 'viewEmployees', 'createEmployee', 'constructionSites', 'workTasks'],
      'Employee': ['dashboard', 'employees', 'viewEmployees', 'workTasks'],
    };

    const hasPermission = (permission: string) => {
      if (!userRole.value) {
        console.warn(`Permission check for ${permission} failed: userRole is null`);
        return false;
      }
      const rolePermissions = permissions[userRole.value] || [];
      const hasPerm = rolePermissions.includes(permission);
      if (!hasPerm) {
        console.warn(`Permission check for ${permission} failed.  Role: ${userRole.value}, Permissions: ${rolePermissions.join(', ')}`);
      }
      return hasPerm;
    };

    const handleLogout = async () => {
      try {
        await api.post('/auth/logout');
        localStorage.removeItem('user');
        userStore.clearUser(); // Clear user data in the store
        await router.push('/login');
      } catch (err: any) {
        console.error('Logout failed', err);
      }
    };

    const emitSection = (sectionName: string) => {
      emit('section-selected', sectionName);
    };

    const toggleEmployeesSection = () => {
      showEmployeesSection.value = !showEmployeesSection.value;
    };

    // Fetch user data on component mount (if it's not already in the store)
    onMounted(() => {
      if (!userStore.user) {
        //  you might need to adjust this, depending on how you initialize the user in your store.
        //  The important thing is that the user data, including the role, is in the store *before* Sidebar is rendered.
        userStore.initializeUser();
      }
    });

    return {
      handleLogout,
      emitSection,
      showEmployeesSection,
      toggleEmployeesSection,
      hasPermission,
      userRole,
    };
  },
});
</script>

<style scoped>
.sidebar {
  display: flex;
  flex-direction: column;
  background-color: var(--primary-bg);
  padding: 1rem;
  width: 150px;
}

.sidebar-option {
  padding: 0.75rem 1rem;
  text-decoration: none;
  color: white;
  cursor: pointer;
  border-bottom: 1px solid var(--border-color);
  text-align: left;
  font-size: 1rem;
  transition: background-color 0.3s ease, color 0.3s ease;
  margin-bottom: 0.5rem;
}

.sidebar-option:last-child {
  border-bottom: none;
  margin-bottom: 0;
}

.sidebar-option:hover {
  background-color: #444;
}

.sidebar-option.router-link-exact-active {
  background-color: #444;
}

.gray-button {
  background-color: var(--dark-gray-button);
  border: none;
}

.sidebar-group {
  display: flex;
  flex-direction: column;
}

.group-title {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.arrow-icon {
  transition: transform 0.3s ease;
  display: inline-block;
}

.open {
  transform: rotate(90deg);
}

.sidebar-sub-options {
  display: flex;
  flex-direction: column;
  margin-left: 1rem;
}

.sub-option {
  padding: 0.75rem 1rem;
  text-decoration: none;
  color: white;
  cursor: pointer;
  text-align: left;
  font-size: 1rem;
  transition: background-color 0.3s ease, color 0.3s ease;
  margin-bottom: 0.5rem;
}

.sub-option:hover {
  background-color: #444;
}
</style>
