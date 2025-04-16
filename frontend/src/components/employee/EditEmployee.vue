<template>
  <div class="view-employees-view">
    <h2>Edit Employees</h2>

    <div v-if="loading" class="loading-indicator">Loading employees...</div>
    <div v-if="error" class="error-message">{{ error }}</div>

    <div v-if="canViewEmployees">
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
            <th v-if="canEditEmployees">Actions</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="employee in employees" :key="employee.id">
            <td>{{ employee.id }}</td>
            <td>
              <input v-model="employee.first_name" type="text" :disabled="!canEditEmployees" />
              <span v-if="employeeVuelidate(employee.id)?.first_name.$error" class="error-message">
                  {{ employeeVuelidate(employee.id)?.first_name.$errors[0].$message }}
                </span>
            </td>
            <td>
              <input v-model="employee.last_name" type="text" :disabled="!canEditEmployees" />
              <span v-if="employeeVuelidate(employee.id)?.last_name.$error" class="error-message">
                  {{ employeeVuelidate(employee.id)?.last_name.$errors[0].$message }}
                </span>
            </td>
            <td>
              <input v-model="employee.birth_date" type="date" :disabled="!canEditEmployees" />
              <span v-if="employeeVuelidate(employee.id)?.birth_date.$error" class="error-message">
                  {{ employeeVuelidate(employee.id)?.birth_date.$errors[0].$message }}
                </span>
            </td>
            <td>
              <input v-model="employee.username" type="text" :disabled="!canEditEmployees" />
              <span v-if="employeeVuelidate(employee.id)?.username.$error" class="error-message">
                  {{ employeeVuelidate(employee.id)?.username.$errors[0].$message }}
                </span>
            </td>
            <td>
              <select v-model="employee.role" :disabled="!canEditEmployees">
                <option value="admin">Admin</option>
                <option value="manager">Manager</option>
                <option value="employee">Employee</option>
              </select>
              <span v-if="employeeVuelidate(employee.id)?.role.$error" class="error-message">
                  {{ employeeVuelidate(employee.id)?.role.$errors[0].$message }}
                </span>
            </td>
            <td>
              <input v-model="employee.access_level" type="number" :disabled="!canEditEmployees" />
              <span v-if="employeeVuelidate(employee.id)?.access_level.$error" class="error-message">
                  {{ employeeVuelidate(employee.id)?.access_level.$errors[0].$message }}
                </span>
            </td>
            <td>
              <select v-model="employee.manager_id" :disabled="!canEditEmployees">
                <option value="">Select Manager</option>
                <option v-for="manager in managers" :key="manager.id" :value="manager.id">
                  {{ manager.first_name }} {{ manager.last_name }}
                </option>
              </select>
              <span v-if="employeeVuelidate(employee.id)?.manager_id.$error" class="error-message">
                  {{ employeeVuelidate(employee.id)?.manager_id.$errors[0].$message }}
                </span>
            </td>
            <td><input v-model="employee.active" type="checkbox" :disabled="!canEditEmployees" /></td>
            <td v-if="canEditEmployees">
              <button @click="updateEmployee(employee.id, employee)" class="update-button">Update</button>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
      <div v-else-if="!loading && !error" class="no-employees-message">No employees found.</div>
    </div>

    <div v-else>
      <p>You do not have permission to view employees.</p>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, onMounted, computed, ref } from 'vue';
import { useEmployeeStore, type Employee } from '@/stores/employee.ts';
import { useUserStore } from '@/stores/user.ts';
import useVuelidate from '@vuelidate/core';
import {required, numeric, minValue} from '@vuelidate/validators';

interface EmployeeValidationRules {
  first_name: { required: typeof required };
  last_name: { required: typeof required };
  birth_date: { required: typeof required };
  username: { required: typeof required };
  role: { required: typeof required };
  access_level: { required: typeof required; numeric: typeof numeric };
  manager_id: { required: typeof required };
}

export default defineComponent({
  name: 'EditEmployeesView',
  setup() {
    const employeeStore = useEmployeeStore();
    const userStore = useUserStore();
    const canEditEmployees = computed(() => userStore.user?.role === 'admin');
    const canViewEmployees = computed(() => {
      return ['admin', 'manager'].includes(userStore.user?.role ?? '');
    });

    onMounted(() => {
      if (!employeeStore.hasFetched) {
        employeeStore.fetchEmployees();
      }
    });

    const employees = computed(() => {
      const user = userStore.user;
      if (user?.role === 'manager' && user.id !== null && user.id !== undefined) {
        return employeeStore.getEmployeesByManagerId(user.id);
      }
      return employeeStore.employees;
    });

    const { loading, error } = employeeStore;

    const managers = computed(() => employeeStore.getManagers);

    const employeeValidations = ref<{ [key: number]: any }>({});

    const employeeVuelidate = (employeeId: number) => {
      if (!employeeValidations.value[employeeId]) {
        const rules = {
          first_name: { required },
          last_name: { required },
          birth_date: { required },
          username: { required },
          role: { required },
          access_level: { required, numeric, minValue: minValue(1) },
          manager_id: { required },
        };

        const employee = employees.value.find((e) => e.id === employeeId);
        const state = employee
          ? employee
          : {
            first_name: '',
            last_name: '',
            birth_date: '',
            username: '',
            role: '',
            access_level: 0,
            manager_id: '',
          };

        employeeValidations.value[employeeId] = useVuelidate(rules, state);
      }
      return employeeValidations.value[employeeId];
    };

    const updateEmployee = async (id: number, updatedData: Employee) => {
      try {
        const v$ = employeeVuelidate(id);
        if (!v$) return;
        const validationResult = await v$.$validate();
        if (!validationResult) return;

        if (!updatedData.birth_date) {
          updatedData.birth_date = '';
        }

        await employeeStore.updateEmployee(id, updatedData);
        console.log('Employee updated successfully.');
      } catch (err) {
        console.error('Failed to update employee:', err);
      }
    };

    return {
      employees,
      loading,
      error,
      managers,
      updateEmployee,
      canEditEmployees,
      canViewEmployees,
      employeeVuelidate,
    };
  },
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
