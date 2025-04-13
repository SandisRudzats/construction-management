<template>
  <div v-if="employee && !loading">
    <h2>Employee Details</h2>
    <p><strong>ID:</strong> {{ employee.id }}</p>
    <p><strong>Name:</strong> {{ employee.first_name }} {{ employee.last_name }}</p>
    <p><strong>Username:</strong> {{ employee.username }}</p>
    <p><strong>Role:</strong> {{ employee.role }}</p>
    <p><strong>Status:</strong> {{ employee.active ? 'Active' : 'Inactive' }}</p>
    <p><strong>Birth Date:</strong> {{ employee.birth_date || 'Not Provided' }}</p>
    <p><strong>Manager ID:</strong> {{ employee.manager_id }}</p>
    <p><strong>Member Since:</strong> {{ employee.created_at }}</p>
  </div>
  <div v-else-if="loading">
    <p>Loading...</p>
  </div>
  <div v-else-if="error">
    <p>Error loading employee: {{ error }}</p>
  </div>
</template>

<script lang="ts">
import { defineComponent, onMounted } from 'vue';
import { defineStore } from 'pinia';
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
  active: boolean; // Add active property
}

// Define a Pinia store for employees
export const useEmployeeStore = defineStore('employee', {
  state: () => ({
    employee: {
      id: 0,
      first_name: '',
      last_name: '',
      birth_date: null,
      username: '',
      role: '',
      access_level: 0,
      manager_id: null,
      created_at: '',
      updated_at: '',
      active: false, // Add active property to initial state
    } as Employee,
    loading: false,
    error: null as string | null,
  }),
  actions: {
    async fetchEmployee() {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.get('v1/employee/me');
        if (response.status === 200) {
          this.employee = response.data as Employee; // Type assertion here
        } else {
          this.error = 'Failed to fetch employee.';
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
  name: 'EmployeeProfile',
  setup() {
    const employeeStore = useEmployeeStore();

    onMounted(() => {
      employeeStore.fetchEmployee();
    });

    const { employee, loading, error } = employeeStore;

    return {
      employee,
      loading,
      error,
    };
  },
});
</script>

<style scoped>
/* ... (your existing styles) ... */
</style>
