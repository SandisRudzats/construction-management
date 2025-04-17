import { defineStore } from 'pinia'
import api from '@/services/api'
import { type ApiResponse } from '@/interfaces/ApiResponse.ts'

export interface Employee {
  id: number
  first_name: string
  last_name: string
  birth_date: string
  username: string
  manager_id: number
  role: string
  active: boolean | true
  access_level: number
  created_at: string | null
  updated_at: string | null
}

export interface NewEmployee {
  first_name: string
  last_name: string
  birth_date: string
  username: string
  password: string
  access_level: number
  role: string
  manager_id: number | null
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
    getEmployees: (state) => state.employees,
    isLoading: (state) => state.loading,
    getError: (state) => state.error,

    getEmployeesByRole: (state) => (role: string) => state.employees.filter((e) => e.role === role),

    getEmployeesByAccessLevel: (state) => (minLevel: number) =>
      state.employees.filter((e) => e.access_level >= minLevel),

    getEmployeesByManagerId: (state) => (managerId: number) =>
      state.employees.filter((e) => e.manager_id === managerId),

    getEmployeeById: (state) => (id: number) => state.employees.find((e) => e.id === id) || null,

    getEmployeesByManagerAndAccess: (state) => (managerId: number, minAccess: number) =>
      state.employees.filter((e) => e.manager_id === managerId && e.access_level >= minAccess),

    getManagers: (state) => state.employees.filter((e) => e.role === 'manager'),
  },

  actions: {
    async fetchEmployees() {
      if (this.hasFetched) return

      await this._withLoading(async () => {
        const response = await api.get<ApiResponse<Employee[]>>('v1/employee/active-employees')
        if (response.data.success) {
          this.employees = response.data.data || []
          this.hasFetched = true
        } else {
          this.error = response.data.message
          this.hasFetched = false
        }
      }, 'An unknown error occurred while fetching employees')
    },

    async updateEmployee(id: number, updatedData: Employee) {
      await this._withLoading(async () => {
        const response = await api.put(`v1/employee/${id}`, updatedData)
        if (response.data.success) {
          const index = this.employees.findIndex((e) => e.id === id)
          if (index !== -1) {
            this.employees[index] = {
              ...this.employees[index],
              ...response.data.data,
            }
          }
        } else {
          this.error = response.data.message
        }
      }, 'An unknown error occurred while updating Employee')
    },

    async createEmployee(newEmployeeData: NewEmployee): Promise<Employee> {
      return (await this._withLoading(async () => {
        const response = await api.post<ApiResponse<Employee>>(
          'v1/employee/create',
          newEmployeeData,
        )
        if (response.data.success && response.data.data) {
          this.employees.push(response.data.data)
          this.hasFetched = true
          return response.data.data
        } else {
          this.error = response.data.message
          this.hasFetched = false
          throw new Error(this.error || 'Failed to create employee')
        }
      }, 'An unknown error occurred while creating Employee')) as Employee
    },

    async _withLoading<T>(
      callback: () => Promise<T>,
      fallbackErrorMessage = 'An error occurred',
    ): Promise<T | void> {
      this.loading = true
      this.error = null
      try {
        return await callback()
      } catch (err: Error | unknown) {
        this.error = err instanceof Error ? err.message : fallbackErrorMessage
        throw new Error(this.error)
      } finally {
        this.loading = false
      }
    },
  },
})
