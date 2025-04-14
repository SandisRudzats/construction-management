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
          <th>Manager</th>
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
            <select v-model="employee.role">
              <option value="admin">Admin</option>
              <option value="manager">Manager</option>
              <option value="employee">Employee</option>
            </select>
          </td>
          <td>
            <input v-model="employee.access_level" type="number" />
          </td>
          <td>
            <select v-model="employee.manager_id">
              <option value="">Select Manager</option>
              <option v-for="manager in managers" :key="manager.id" :value="manager.id">
                {{ manager.first_name }} {{ manager.last_name }}
              </option>
            </select>
          </td>
          <td>
            <input v-model="employee.active" type="checkbox" />
          </td>
          <td>
            <button @click="updateEmployee(employee.id, employee)" class="update-button">Update</button>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
    <div v-else-if="!loading && !error" class="no-employees-message">No employees found.</div>
  </div>
</template>

<script lang="ts">
import { defineComponent, onMounted, computed } from 'vue'
import { useEmployeeStore } from '@/stores/employee.ts'
import { useUserStore } from '@/stores/user.ts'
import type { Employee } from '@/stores/employee.ts'

export default defineComponent({
  name: 'EditEmployeesView',
  setup() {
    const employeeStore = useEmployeeStore()
    const userStore = useUserStore()

    // Fetch employees when the component is mounted
    onMounted(async () => {
      if (!employeeStore.hasFetched) {
        await employeeStore.fetchEmployees()
      }
    })

    const employees = computed(() => {
      const user = userStore.user
      // If the user is a manager, filter employees based on their manager_id
      if (user?.role === 'manager' && user.id !== null) {
        return employeeStore.getEmployeesByManagerId(user.id)
      }
      // Otherwise return all employees
      return employeeStore.employees
    })

    const { loading, error } = employeeStore

    // Managers are those with the "manager" role
    const managers = computed(() => {
      return employeeStore.getEmployeesByRole('manager')
    })

    // Refactor updateEmployee to use store's updateEmployee method
    const updateEmployee = async (id: number, updatedData: Employee) => {
      try {
        await employeeStore.updateEmployee(id, updatedData)
        console.log('Employee updated successfully.')
      } catch (err) {
        console.error('Failed to update employee:', err)
      }
    }

    return {
      employees,
      loading,
      error,
      managers,
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
