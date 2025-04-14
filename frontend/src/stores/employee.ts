import { defineStore } from 'pinia'
import api from '@/services/api'

export interface Employee {
  id: number
  first_name: string
  last_name: string
  birth_date: string
  username: string
  manager_id: number
  role: string
  active: boolean
  access_level: number
  created_at: string
  updated_at: string
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
    getEmployees: (state) => state.employees || [],
    isLoading: (state) => state.loading,
    getError: (state) => state.error,

    getEmployeesByRole: (state) => {
      return (role: string) => state.employees.filter((employee) => employee.role === role)
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
      return (id: number) => state.employees.find((employee) => employee.id === id) || null
    },
    getEmployeesByManagerAndAccess: (state) => {
      return (managerId: number, minAccess: number) =>
        state.employees.filter((e) => e.manager_id === managerId && e.access_level >= minAccess)
    },
    getManagers: (state) => state.employees.filter((employee) => employee.role === 'manager'),
  },

  actions: {
    async fetchEmployees() {
      if (this.hasFetched) return

      this.loading = true
      this.error = null
      try {
        const response = await api.get('v1/employee/active-employees')
        if (response.status === 200 && response?.data) {
          this.employees = response.data
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

    async updateEmployee(id: number, updatedData: Employee) {
      this.loading = true
      this.error = null
      try {
        const response = await api.put(`v1/employee/${id}`, updatedData)
        if (response.status === 200) {
          const index = this.employees.findIndex((e) => e.id === id)
          if (index !== -1) {
            this.employees[index] = {
              ...this.employees[index],
              ...response.data,
            }
          }
          console.log('Employee updated successfully:', response.data)
        } else {
          this.error = 'Failed to update employee.'
          console.error('Failed to update employee:', response.data)
        }
      } catch (err: any) {
        if (err instanceof Error) {
          this.error = err.message
        } else {
          this.error = 'An error occurred while updating employee.'
        }
        console.error(err)
      } finally {
        this.loading = false
      }
    },

    async addEmployeeUser(newEmployeeData: NewEmployee): Promise<void> {
      this.loading = true
      this.error = null

      try {
        await api.post('v1/employee/create', newEmployeeData)
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Failed to add employee.'
      } finally {
        this.loading = false
      }
    },

    clearEmployees() {
      this.employees = []
      this.hasFetched = false
    },

    addEmployee(employee: Employee) {
      this.employees.push(employee)
    },

    setEmployees(employees: Employee[]) {
      this.employees = employees
    },
  },
})
