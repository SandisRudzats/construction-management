<template>
  <div class="view-employees-view">
    <h2>All Employees</h2>
    <div v-if="loading" class="loading-indicator">Loading employees...</div>
    <div v-if="error" class="error-message">{{ error }}</div>
    <div v-if="employees && employees.length > 0" class="employee-list">
      <table class="employee-table">
        <thead>
        <tr>
          <th>ID</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Birth Date</th>
          <th>Username</th>
          <th>Role</th>
          <th>Access Level</th>
          <th>Manager ID</th>
          <th>Created At</th>
          <th>Updated At</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="employee in employees" :key="employee.id">
          <td>{{ employee.id }}</td>
          <td>{{ employee.first_name }}</td>
          <td>{{ employee.last_name }}</td>
          <td>{{ employee.birth_date || 'Not set' }}</td>
          <td>{{ employee.username }}</td>
          <td>{{ employee.role }}</td>
          <td>{{ employee.access_level }}</td>
          <td>{{ employee.manager_id || 'Not set' }}</td>
          <td>{{ employee.created_at }}</td>
          <td>{{ employee.updated_at }}</td>
        </tr>
        </tbody>
      </table>
    </div>
    <div v-else-if="!loading && !error" class="no-employees-message">
      No employees found.
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue';
import { defineStore } from 'pinia';
import { createPinia } from 'pinia'; // Import createPinia
import api from '@/services/api';

interface Employee {
  id: number;
  first_name: string;
  last_name: string;
  birth_date: string | null;
  username: string;
  role: string;
  access_level: number;
  manager_id: number | null;
  created_at: string;
  updated_at: string;
}

// Define a Pinia store for employees
export const useEmployeeStore = defineStore('employee', {
  state: () => ({
    employees: [] as Employee[],
    loading: false,
    error: null as string | null,
  }),
  actions: {
    async fetchEmployees() {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.get('v1/employee');
        if (response.status === 200) {
          this.employees = response.data;
        } else {
          this.error = 'Failed to fetch employees.';
        }
      } catch (err: any) {
        this.error = err.response?.data?.message || 'An error occurred.';
      } finally {
        this.loading = false;
      }
    },
  },
});

export default defineComponent({
  name: 'ViewEmployeesView',
  setup() {
    // Use the employee store
    const employeeStore = useEmployeeStore();

    onMounted(() => {
      // Fetch employees when the component is mounted
      employeeStore.fetchEmployees();
    });

    // Access state from the store
    const { employees, loading, error } = employeeStore;

    return {
      employees,
      loading,
      error,
    };
  },
  pinia: createPinia(), // Add this line to make Pinia available in the component
});
</script>

<style scoped>
.view-employees-view {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 2rem;
  background-color: var(--card-bg);
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  overflow-x: auto;
}

.loading-indicator {
  text-align: center;
  margin-top: 1rem;
  color: var(--primary-text);
}

.error-message {
  color: var(--accent-red);
  margin-top: 1rem;
  padding: 0.75rem;
  background-color: rgba(244, 67, 54, 0.1);
  border: 1px solid var(--accent-red);
  border-radius: 4px;
  text-align: center;
}

.employee-list {
  margin-top: 1rem;
  overflow-x: auto;
}

.employee-table {
  width: 100%;
  border-collapse: collapse;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  min-width: 800px;
}

.employee-table thead tr {
  background-color: var(--header-bg);
  color: var(--header-text);
}

.employee-table th,
.employee-table td {
  padding: 0.75rem;
  text-align: left;
  border-bottom: 1px solid var(--border-color);
}

.employee-table th {
  font-weight: bold;
}

.employee-table tbody tr:hover {
  background-color: rgba(0, 0, 0, 0.05);
}

.no-employees-message {
  text-align: center;
  margin-top: 1rem;
  color: var(--primary-text);
}
</style>
