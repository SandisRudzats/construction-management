<template>
  <div class="dashboard-container">
    <div class="header dark-header">
      <h1>Construction Management System</h1>
    </div>
    <div class="content-wrapper">
      <div class="sidebar">
        <div
          v-if="hasPermission('viewOwnProfile')"
          @click="handleSectionSelect('dashboard')"
          class="sidebar-option gray-button"
        >
          Dashboard
        </div>
        <div class="sidebar-option gray-button group-title" @click="toggleEmployeesSection">
          Employees
          <span :class="{ 'arrow-icon': true, open: showEmployeesSection }">▶</span>
        </div>
        <div v-if="showEmployeesSection" class="sidebar-sub-options">
          <div
            v-if="hasPermission('manageEmployees')"
            @click.stop="handleSectionSelect('employees')"
            class="sidebar-option sub-option"
          >
            View Employees
          </div>
          <div
            v-if="hasPermission('manageEmployees')"
            @click.stop="handleSectionSelect('create-employee')"
            class="sidebar-option sub-option"
          >
            Create Employee
          </div>
          <div
            v-if="hasPermission('manageEmployees')"
            @click.stop="handleSectionSelect('edit-employee')"
            class="sidebar-option sub-option"
          >
            Edit Employees
          </div>
          <div
            v-if="hasPermission('manageEmployees')"
            @click.stop="handleSectionSelect('deactivate-employee')"
            class="sidebar-option sub-option"
          >
            Deactivate Employees
          </div>
          <div
            v-if="hasPermission('viewOwnProfile')"
            @click.stop="handleSectionSelect('view-employee-profile')"
            class="sidebar-option sub-option"
          >
            View Employee Profile
          </div>
          <div
            v-if="hasPermission('viewTeam')"
            @click.stop="handleSectionSelect('view-manager-subordinates')"
            class="sidebar-option sub-option"
          >
            View Manager Subordinates
          </div>
        </div>

        <div class="sidebar-option gray-button group-title" @click="toggleConstructionSitesSection">
          Construction Sites
          <span :class="{ 'arrow-icon': true, open: showConstructionSitesSection }">▶</span>
        </div>
        <div v-if="showConstructionSitesSection" class="sidebar-sub-options">
          <div
            v-if="hasPermission('manageSites')"
            @click.stop="handleSectionSelect('construction-sites')"
            class="sidebar-option sub-option"
          >
            View Sites
          </div>
          <div
            v-if="hasPermission('viewAssignedSites')"
            @click.stop="handleSectionSelect('view-assigned-sites')"
            class="sidebar-option sub-option"
          >
            View assigned Sites
          </div>
          <div
            v-if="hasPermission('manageSites')"
            @click.stop="handleSectionSelect('create-construction-site')"
            class="sidebar-option sub-option"
          >
            Create Site
          </div>
          <div
            v-if="hasPermission('manageSites')"
            @click.stop="handleSectionSelect('edit-construction-site')"
            class="sidebar-option sub-option"
          >
            Edit Sites
          </div>
          <div
            v-if="hasPermission('manageSites')"
            @click.stop="handleSectionSelect('delete-construction-site')"
            class="sidebar-option sub-option"
          >
            Delete Sites
          </div>
          <div
            v-if="hasPermission('manageOwnSites')"
            @click.stop="handleSectionSelect('view-site-work-tasks')"
            class="sidebar-option sub-option"
          >
            View Site Work Tasks
          </div>
        </div>

        <div class="sidebar-option gray-button group-title" @click="toggleWorkTasksSection">
          Work Tasks
          <span :class="{ 'arrow-icon': true, open: showWorkTasksSection }">▶</span>
        </div>
        <div v-if="showWorkTasksSection" class="sidebar-sub-options">
          <div
            v-if="hasPermission('manageAllTasks')"
            @click.stop="handleSectionSelect('work-tasks')"
            class="sidebar-option sub-option"
          >
            View Tasks
          </div>
          <div
            v-if="hasPermission('manageAllTasks')"
            @click.stop="handleSectionSelect('create-work-task')"
            class="sidebar-option sub-option"
          >
            Create Task
          </div>
          <div
            v-if="hasPermission('manageAllTasks')"
            @click.stop="handleSectionSelect('edit-work-task')"
            class="sidebar-option sub-option"
          >
            Edit Tasks
          </div>
          <div
            v-if="hasPermission('manageAllTasks')"
            @click.stop="handleSectionSelect('delete-work-task')"
            class="sidebar-option sub-option"
          >
            Delete Tasks
          </div>
          <div
            v-if="hasPermission('viewOwnTasks')"
            @click.stop="handleSectionSelect('view-employee-work-tasks')"
            class="sidebar-option sub-option"
          >
            View Employee Work Tasks
          </div>
        </div>
        <button class="sidebar-option gray-button" @click="handleLogout">Logout</button>
      </div>

      <div class="main-content">
        <div v-if="selectedSection === 'dashboard'">
          <h1>Welcome to the dashboard!</h1>
          <p>This is the main dashboard area. You can add general information and widgets here.</p>
        </div>
        <div v-else-if="selectedSection === 'employees'">
          <ViewEmployees />
        </div>
        <div v-else-if="selectedSection === 'create-employee'">
          <CreateEmployee />
        </div>
        <div v-else-if="selectedSection === 'edit-employee'">
          <EditEmployee />
        </div>
        <div v-else-if="selectedSection === 'deactivate-employee'">
          <DeactivateEmployee />
        </div>
        <div v-else-if="selectedSection === 'view-employee-profile'">
          <EmployeeProfile />
        </div>
        <div v-else-if="selectedSection === 'view-manager-subordinates'">
          <ManagerSubordinates />
        </div>
        <div v-else-if="selectedSection === 'construction-sites'">
          <ViewConstructionSites />
        </div>
        <div v-else-if="selectedSection === 'view-assigned-sites'">
          <ViewConstructionSites />
        </div>
        <div v-else-if="selectedSection === 'create-construction-site'">
          <CreateConstructionSite />
        </div>
        <div v-else-if="selectedSection === 'edit-construction-site'">
          <EditConstructionSite />
        </div>
        <div v-else-if="selectedSection === 'delete-construction-site'">
          <DeleteConstructionSite />
        </div>
        <div v-else-if="selectedSection === 'view-site-work-tasks'">
          <SiteWorkTasks />
        </div>
        <div v-else-if="selectedSection === 'work-tasks'">
          <ViewWorkTasks />
        </div>
        <div v-else-if="selectedSection === 'create-work-task'">
          <CreateWorkTask />
        </div>
        <div v-else-if="selectedSection === 'edit-work-task'">
          <EditWorkTask />
        </div>
        <div v-else-if="selectedSection === 'delete-work-task'">
          <DeleteWorkTask />
        </div>
        <div v-else-if="selectedSection === 'view-employee-work-tasks'">
          <EmployeeWorkTasks />
        </div>
        <div v-else>
          <h1>Welcome to the dashboard!</h1>
          <p>Select a section from the sidebar to view its content.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'
import { useUserStore } from '@/stores/user'
// Import the components
import ViewEmployees from '@/components/employee/ViewEmployees.vue'
import CreateEmployee from '@/components/employee/CreateEmployee.vue'
import EditEmployee from '@/components/employee/EditEmployee.vue'
import DeactivateEmployee from '@/components/employee/DeactivateEmployee.vue'
import EmployeeProfile from '@/components/employee/EmployeeProfile.vue'
import ManagerSubordinates from '@/components/employee/ManagerSubordinates.vue'
import ViewConstructionSites from '@/components/construction-site/ViewConstructionSites.vue'
import CreateConstructionSite from '@/components/construction-site/CreateConstructionSite.vue'
import EditConstructionSite from '@/components/construction-site/EditConstructionSite.vue'
import DeleteConstructionSite from '@/components/construction-site/DeleteConstructionSite.vue' // Corrected import
import SiteWorkTasks from '@/components/construction-site/SiteWorkTasks.vue'
import ViewWorkTasks from '@/components/work-task/ViewWorkTasks.vue'
import CreateWorkTask from '@/components/work-task/CreateWorkTask.vue'
import EditWorkTask from '@/components/work-task/EditWorkTask.vue'
import DeleteWorkTask from '@/components/work-task/DeleteWorkTask.vue'
import EmployeeWorkTasks from '@/components/work-task/EmployeeWorkTasks.vue'

export default defineComponent({
  name: 'Dashboard',
  emits: ['section-selected'],
  components: {
    ViewEmployees,
    CreateEmployee,
    EditEmployee,
    DeactivateEmployee,
    EmployeeProfile,
    ManagerSubordinates,
    ViewConstructionSites,
    CreateConstructionSite,
    EditConstructionSite,
    DeleteConstructionSite, // Corrected component name
    SiteWorkTasks,
    ViewWorkTasks,
    CreateWorkTask,
    EditWorkTask,
    DeleteWorkTask,
    EmployeeWorkTasks,
  },
  setup() {
    const router = useRouter()
    const showEmployeesSection = ref(false)
    const showConstructionSitesSection = ref(false)
    const showWorkTasksSection = ref(false)
    const userStore = useUserStore()
    const userRole = computed(() => userStore.user?.role || null)
    const selectedSection = ref<string>('dashboard')

    const handleLogout = async () => {
      try {
        await api.post('/auth/logout')
        localStorage.removeItem('user')
        userStore.clearUser()
        await router.push('/login')
      } catch (err: any) {
        console.error('Logout failed', err)
      }
    }

    const handleSectionSelect = (sectionName: string) => {
      selectedSection.value = sectionName
      console.log(
        'Selected section:',
        sectionName,
        'Current selectedSection:',
        selectedSection.value,
      )
      // Emit the event to notify the parent component (if needed)
      // emit('section-selected', sectionName);
    }

    const toggleEmployeesSection = () => {
      showEmployeesSection.value = !showEmployeesSection.value
    }

    const toggleConstructionSitesSection = () => {
      showConstructionSitesSection.value = !showConstructionSitesSection.value
    }

    const toggleWorkTasksSection = () => {
      showWorkTasksSection.value = !showWorkTasksSection.value
    }

    type Permission =
      | 'viewOwnProfile'
      | 'manageEmployees'
      | 'manageSites'
      | 'manageAllTasks'
      | 'viewTeam'
      | 'manageOwnSites'
      | 'viewOwnTasks'
      | 'viewAssignedSites'
      | 'manageOwnTasks'

    const hasPermission = (permission: Permission): boolean => {
      const user = computed(() => userStore.user).value // Access user reactively

      // console.log(user.permissions);

      // console.log('Checking permission:', permission, 'for user:', user.permissions)
      if (!user || !user.role) return false // Ensure user and role are defined

      // Role-based permissions
      const rolePermissions: Record<string, Permission[]> = {
        admin: [
          'viewOwnProfile',
          'manageEmployees',
          'manageSites',
          'manageAllTasks',
          'viewTeam',
          'manageOwnSites',
          'viewOwnTasks',
        ],
        manager: ['viewOwnProfile', 'viewTeam', 'manageOwnSites', 'viewOwnTasks', 'manageOwnTasks'],
        employee: ['viewOwnProfile', 'viewOwnTasks', 'viewAssignedSites'],
      }

      if (rolePermissions[user.role] && rolePermissions[user.role].includes(permission)) {
        console.log('returns true for', permission)
        return true
      }

      // Special permissions
      const specialPermissions: Record<Permission, boolean> = {
        manageEmployees: user.role === 'admin',
        manageSites: user.role === 'admin',
        manageAllTasks: user.role === 'admin',
        manageOwnTasks: user.role === 'manager',
        viewTeam: user.role === 'admin' || user.role === 'manager',
        manageOwnSites: user.role === 'admin' || user.role === 'manager',
        viewOwnTasks: true, // All users can view their own tasks
        viewOwnProfile: true, //all users can view their own profile.
        viewAssignedSites: user.role === 'employee',
      }

      // console.log('Permission check for:', permission, 'Result:', hasPermission(permission));

      console.log(specialPermissions[permission] || false)
      return specialPermissions[permission] || false
    }

    onMounted(() => {
      userStore.initializeUser()
      console.log('User initialized:', userStore.user)
    })

    return {
      handleLogout,
      handleSectionSelect,
      showEmployeesSection,
      toggleEmployeesSection,
      hasPermission,
      userRole,
      showConstructionSitesSection,
      toggleConstructionSitesSection,
      showWorkTasksSection,
      toggleWorkTasksSection,
      selectedSection,
    }
  },
})
</script>

<style scoped>
.dashboard-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.header {
  background-color: var(--primary-bg);
  color: white;
  padding: 1rem;
  text-align: center;
  border-bottom: 1px solid var(--border-color);
}

.header.dark-header {
  background-color: #333;
}

.content-wrapper {
  /* New wrapper styles */
  display: flex;
  flex: 1; /* Allow it to take up remaining space */
  flex-direction: row; /* Arrange sidebar and main content in a row */
}

.sidebar {
  display: flex;
  flex-direction: column;
  background-color: var(--primary-bg);
  padding: 1rem;
  width: 200px;
  /* height: 100%;  Remove the fixed height */
}

.sidebar-option {
  padding: 0.75rem 1rem;
  text-decoration: none;
  color: white;
  cursor: pointer;
  border-bottom: 1px solid var(--border-color);
  text-align: left;
  font-size: 1rem;
  transition:
    background-color 0.3s ease,
    color 0.3s ease;
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
  margin-bottom: 1rem;
}

.group-title {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-right: 1rem;
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
  padding-left: 1rem;
  border-left: 1px solid var(--border-color);
}

.sub-option {
  padding: 0.75rem 1rem;
  text-decoration: none;
  color: white;
  background-color: darkgray;
  cursor: pointer;
  text-align: left;
  font-size: 1rem;
  transition:
    background-color 0.3s ease,
    color 0.3s ease;
  margin-bottom: 0.5rem;
}

.sub-option:hover {
  background-color: #444;
}

.main-content {
  flex: 1;
  padding: 2rem;
  background-color: lightgray;
  height: 100%;
}
</style>
