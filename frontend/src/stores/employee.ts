import { defineStore } from 'pinia';
import api from '@/services/api';

interface Employee {
  id: number;
  first_name: string;
  last_name: string;
  manager_id: number;
  role: string;
  access_level: number; // Add access_level
  // Add other employee properties as needed
}

interface EmployeeState {
  employees: Employee[];
  loading: boolean;
  error: string | null;
}

export const useEmployeeStore = defineStore('employee', {
  state: (): EmployeeState => ({
    employees: [],
    loading: false,
    error: null,
  }),
  getters: {
    getEmployees: (state) => state.employees,
    isLoading: (state) => state.loading,
    getError: (state) => state.error,
  },
  actions: {
    async fetchEmployees(siteManagerId: number | null = null, requiredAccessLevel: number | null = null) {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.get('v1/employee/active-employees');
        if (response.status === 200) {
          let filteredEmployees = response.data.filter((employee: Employee) => employee.role === 'employee');
          if (siteManagerId !== null) {
            filteredEmployees = filteredEmployees.filter(employee => employee.manager_id === siteManagerId);
          }
          if (requiredAccessLevel !== null) {
            filteredEmployees = filteredEmployees.filter(employee => employee.access_level >= requiredAccessLevel);
          }
          this.employees = filteredEmployees;
        } else {
          this.error = 'Failed to fetch employees.';
        }
      } catch (err: any) {
        this.error = err.message || 'An error occurred.';
      } finally {
        this.loading = false;
      }
    },
  },
});
