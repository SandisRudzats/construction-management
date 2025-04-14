<template>
  <div class="view-employees-view">
    <h2>Edit Employees</h2>
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
            <th>Active</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="employee in employees" :key="employee.id">
            <td>{{ employee.id }}</td>
            <td>
              <input v-model="employee.first_name" type="text" />
            </td>
            <td>
              <input v-model="employee.last_name" type="text" />
            </td>
            <td>
              <input v-model="employee.birth_date" type="date" />
            </td>
            <td>
              <input v-model="employee.username" type="text" />
            </td>
            <td>
              <input v-model="employee.role" type="text" />
            </td>
            <td>
              <input v-model="employee.access_level" type="number" />
            </td>
            <td>
              <input v-model="employee.manager_id" type="number" />
            </td>
            <td>
              <input v-model="employee.active" type="checkbox" />
            </td>
            <td>
              <button @click="updateEmployee(employee.id)" class="update-button">Update</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-else-if="!loading && !error" class="no-employees-message">No employees found.</div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue'
import api from '@/services/api'

interface Employee {
  id: number
  first_name: string
  last_name: string
  birth_date: string | null
  username: string
  password_hash: string | null // Note:  We don't обычно edit this directly
  access_level: number
  manager_id: number
  role: string
  active: boolean
  created_at: string | null
  updated_at: string | null
}

export default defineComponent({
  name: 'EditEmployeesView',
  setup() {
    const employees = ref<Employee[]>([])
    const loading = ref(false)
    const error = ref<string | null>(null)

    const fetchEmployees = async () => {
      loading.value = true
      error.value = null
      try {
        const response = await api.get('v1/employee')
        if (response.status === 200) {
          employees.value = response.data
        } else {
          error.value = 'Failed to fetch employees.'
        }
      } catch (err: any) {
        error.value = err.message || 'An error occurred.'
      } finally {
        loading.value = false
      }
    }

    const updateEmployee = async (id: number) => {
      const employeeToUpdate = employees.value.find((emp) => emp.id === id)
      if (!employeeToUpdate) {
        console.error('Employee to update not found:', id)
        return
      }

      try {
        //  Don't send password_hash
        const { ...dataToUpdate } = employeeToUpdate

        const response = await api.put(`v1/employee/${id}`, dataToUpdate)
        if (response.status === 200) {
          const updatedEmployeeData = response.data
          const index = employees.value.findIndex((e) => e.id === id)
          if (index !== -1) {
            employees.value[index] = {
              ...employees.value[index],
              ...updatedEmployeeData,
            }
          }
          console.log('Employee updated successfully:', response.data)
        } else {
          error.value = 'Failed to update employee.'
          console.error('Failed to update employee:', response.data)
        }
      } catch (err: any) {
        const errors = err.response?.data?.errors

        if (errors) {
          error.value = Object.entries(errors)
            .map(([field, messages]) => `${field}: ${messages.join(', ')}`)
            .join('\n') ?? ['An error occurred.']
        }
      }
    }

    onMounted(() => {
      fetchEmployees('any')
    })

    return {
      employees,
      loading,
      error,
      updateEmployee,
    }
  },
})
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

.employee-table input,
.employee-table select {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid var(--border-color);
  border-radius: 4px;
  font-size: 1rem;
}

.employee-table input[type='checkbox'] {
  width: auto;
}

.employee-table .update-button {
  background-color: darkgray;
  color: white;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
  transition: background-color 0.3s ease;
}

.employee-table .update-button:hover {
  background-color: #444;
}

.no-employees-message {
  text-align: center;
  margin-top: 1rem;
  color: var(--primary-text);
}
</style>
