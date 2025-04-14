import { defineStore } from 'pinia';
import api from '@/services/api.ts';

export interface EmployeeUser {
  id: number;
  first_name: string;
  last_name: string;
  birth_date: string;
  username: string;
  manager_id: number;
  role: string;
  active: boolean;
  access_level: number;
  created_at: string;
  updated_at: string;
  roles: string[];
  permissions: string[];
}

export interface NewEmployee {
  first_name: string;
  last_name: string;
  birth_date: string;
  username: string;
  password: string;
  access_level: number;
  role: string;
  manager_id: number | null;
}

interface EmployeeUserState {
  employeeUser: EmployeeUser | null;
  employees: EmployeeUser[];
  loading: boolean;
  error: string | null;
  hasFetched: boolean;
  hasFetchedEmployees: boolean;
}

export const useEmployeeUserStore = defineStore('employeeUser', {
  state: (): EmployeeUserState => ({
    employeeUser: null,
    employees: [],
    loading: false,
    error: null,
    hasFetched: false,
    hasFetchedEmployees: false,
  }),

  getters: {
    getEmployeeUser: (state) => state.employeeUser,
    isLoading: (state) => state.loading,
    getError: (state) => state.error,
    hasRole: (state) => (roleName: string) => state.employeeUser?.roles.includes(roleName) || false,
    hasPermission: (state) => (permissionName: string) => state.employeeUser?.permissions.includes(permissionName) || false,
    getManagers: (state) => {
      return state.employees.filter(e => e.role === 'manager');
    },
  },

  actions: {
    setEmployeeUser(employeeUserData: EmployeeUser | null) {
      this.employeeUser = employeeUserData;
    },

    async fetchAllEmployees() {
      if (this.hasFetchedEmployees) return;

      this.loading = true;
      this.error = null;

      try {
        const response = await api.get('v1/employee');
        if (response.status === 200) {
          this.employees = response.data;
          this.hasFetchedEmployees = true; // <-- Mark fetched
        } else {
          this.error = 'Failed to fetch employees.';
        }
      } catch (err: any) {
        this.error = err.message || 'Unexpected error occurred.';
      } finally {
        this.loading = false;
      }
    },

    clearEmployeeUser() {
      this.employeeUser = null;
      this.hasFetched = false;
    },

    initializeEmployeeUser() {
      const storedData = localStorage.getItem('employeeUser');
      if (storedData) {
        try {
          const employeeUserData = JSON.parse(storedData);
          this.setEmployeeUser(employeeUserData);
        } catch (error) {
          this.clearEmployeeUser();
        }
      }
    },

    async fetchEmployeeUserData() {
      if (this.hasFetched) return;

      this.loading = true;
      this.error = null;
      try {
        const response = await api.get('v1/employee/active-employees');
        if (response.status === 200 && response?.data) {
          const employeeUserData = response.data;
          this.setEmployeeUser(employeeUserData);
          this.hasFetched = true;
        } else {
          this.error = 'No employee/user data found.';
          this.hasFetched = false;
        }
      } catch (err: unknown) {
        if (err instanceof Error) {
          this.error = err.message;
        } else {
          this.error = 'An unexpected error occurred.';
        }
      } finally {
        this.loading = false;
      }
    },

    async updateEmployeeUser(id: number, updatedData: EmployeeUser) {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.put(`v1/employee/${id}`, updatedData);
        if (response.status === 200) {
          if (this.employeeUser?.id === id) {
            this.employeeUser = { ...this.employeeUser, ...response.data };
          }
        } else {
          this.error = 'Failed to update user/employee.';
        }
      } catch (err: unknown) {
        if (err instanceof Error) {
          this.error = err.message;
        } else {
          this.error = 'An unexpected error occurred.';
        }
      } finally {
        this.loading = false;
      }
    },

    async addEmployeeUser(newEmployeeData: NewEmployee): Promise<void> {
      try {
        await api.post('v1/employee/create', newEmployeeData);
      } catch (error) {
        this.error = 'Failed to add employee.';
      }
    },

    async createEmployeeAndStore(newEmployeeData: Omit<EmployeeUser, 'id' | 'created_at' | 'updated_at' | 'active' | 'roles' | 'permissions'>) {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.post('v1/employee/create', newEmployeeData);
        if (response.status === 200 || response.status === 201) {
          return response.data;
        } else {
          this.error = 'Failed to create employee.';
          return null;
        }
      } catch (err: any) {
        this.error = err.response?.data?.message || 'An unexpected error occurred.';
        return null;
      } finally {
        this.loading = false;
      }
    },

    clearEmployeeUserData() {
      this.employeeUser = null;
      this.hasFetched = false;
    },
  },

  persist: {
    key: 'employeeUser',
    storage: localStorage,
  },
});
