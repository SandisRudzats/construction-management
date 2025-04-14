import { defineStore } from 'pinia'
import api from '@/services/api'

interface Employee {
  id: number
  first_name: string
  last_name: string
  birth_date: string
  username: string
  manager_id: number
  role: string
  access_level: number
  created_at: string
  updated_at: string
}

interface EmployeeState {
  employees: Employee[]
  loading: boolean
  error: string | null
  hasFetched: boolean
}

export const useEmployeeStore = defineStore('employee', {
  state: (): EmployeeState => ({
    employees: [],
    loading: false,
    error: null,
    hasFetched: false,
  }),

  getters: {
    getEmployees: (state) => state.employees || [],
    isLoading: (state) => state.loading,
    getError: (state) => state.error,

    getEmployeesByRole: (state) => {
      return (role: string) =>
        state.employees.filter((employee) => employee.role === role)
    },

    getEmployeesByAccessLevel: (state) => {
      return (minLevel: number) =>
        state.employees.filter((employee) => employee.access_level >= minLevel)
    },

    getEmployeesByManagerId: (state) => {
      return (managerId: number) =>
        state.employees.filter((employee) => employee.manager_id === managerId)
    },

    getEmployeeById: (state) => {
      return (id: number) =>
        state.employees.find((employee) => employee.id === id) || null
    },
    getEmployeesByManagerAndAccess: (state) => {
      return (managerId: number, minAccess: number) =>
        state.employees.filter(
          (e) => e.manager_id === managerId && e.access_level >= minAccess
        )
    },
  },

  actions: {
    async fetchEmployees() {
      if (this.hasFetched) return // Prevent redundant calls

      this.loading = true
      this.error = null
      try {
        const response = await api.get('v1/employee/active-employees')
        if (response.status === 200 && response?.data) {
          this.employees = response.data;
          this.hasFetched = true
        } else {
          this.error = 'No employees data found.'
          this.hasFetched = false
        }
      } catch (err: any) {
        this.error = err.message || 'An error occurred.'
      } finally {
        this.loading = false
      }
    },

    clearEmployees() {
      this.employees = []
      this.hasFetched = false
    }
  },
})
