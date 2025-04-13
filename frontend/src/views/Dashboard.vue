<template>
  <div class="dashboard-container">
    <header class="dashboard-header">
      <div class="header-title">Construction Site Management Dashboard</div>
      <button class="btn-dark-gray" @click="handleLogout">Logout</button>
    </header>
    <div class="main-content">
      <Sidebar @section-selected="handleSectionSelected" />
      <div class="dashboard-content">
        <CreateEmployeeView
          v-if="selectedSection === 'employees'"
          class="side-by-side"
        />
        <ViewEmployeesView
          v-if="selectedSection === 'employees'"
          class="side-by-side"
        />
        <div v-else>
          <p>Welcome to the dashboard!</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import api from '@/services/api';
import { useRouter } from 'vue-router';
import Sidebar from '@/components/Sidebar.vue';
import CreateEmployeeView from '@/components/employee/CreateEmployee.vue';
import ViewViewEmployeesView from '@/components/employee/ViewEmployees.vue';

export default defineComponent({
  name: 'Dashboard',
  components: {
    Sidebar,
    CreateEmployeeView: CreateEmployeeView,
    ViewEmployeesView: ViewViewEmployeesView, // Register the component
  },
  setup() {
    const error = ref<string | null>(null);
    const router = useRouter();
    const selectedSection = ref<string | null>('dashboard');

    const handleLogout = async () => {
      error.value = null;
      try {
        await api.post('/auth/logout');
        localStorage.removeItem('user');
        await router.push('/login');
      } catch (err: any) {
        error.value = err.response?.data?.message || 'Logout failed';
      }
    };

    const handleSectionSelected = (section: string) => {
      console.log('Selected section:', section);
      selectedSection.value = section;
    };

    return {
      error,
      handleLogout,
      selectedSection,
      handleSectionSelected,
    };
  },
});
</script>

<style scoped>
.dashboard-container {
  display: grid;
  grid-template-rows: auto 1fr;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 2rem;
  background-color: var(--card-bg);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  grid-column: 1 / -1;
}

.header-title {
  color: var(--primary-text);
  font-size: 1.5rem;
  font-weight: bold;
}

.btn-dark-gray {
  background-color: var(--dark-gray-button);
  color: white;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.btn-dark-gray:hover {
  background-color: #444;
}

.main-content {
  display: grid;
  grid-template-columns: auto 1fr;
}

.dashboard-content {
  flex-grow: 1;
  display: flex;
  align-items: flex-start;
  justify-content: flex-start;
  padding: 2rem;
  color: var(--primary-text);
  flex-direction: row; /* Arrange children in a row */
  gap: 2rem; /* Add some gap between the two components */
}

.side-by-side {
  width: 50%; /* Each component takes up 50% of the width */
  transform: scale(0.8);
  transform-origin: top left;
  padding-left: 2rem;
}
</style>
