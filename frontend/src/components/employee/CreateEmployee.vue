<template>
  <div class="create-employee-view">
    <h2>Create Employee</h2>
    <form @submit.prevent="handleSubmit" class="employee-form">
      <div class="form-group">
        <input
          type="text"
          id="first_name"
          v-model="employeeData.first_name"
          placeholder="First Name"
          required
          class="form-control"
        />
      </div>
      <div class="form-group">
        <input
          type="text"
          id="last_name"
          v-model="employeeData.last_name"
          placeholder="Last Name"
          required
          class="form-control"
        />
      </div>
      <div class="form-group">
        <input
          type="date"
          id="birth_date"
          v-model="employeeData.birth_date"
          placeholder="Birth Date"
          class="form-control"
        />
      </div>
      <div class="form-group">
        <input
          type="text"
          id="username"
          v-model="employeeData.username"
          placeholder="Username"
          required
          class="form-control"
        />
      </div>
      <div class="form-group">
        <input
          type="password"
          id="password"
          v-model="employeeData.password"
          placeholder="Password"
          required
          class="form-control"
        />
      </div>
      <div class="form-group">
        <select
          id="access_level"
          v-model="employeeData.access_level"
          required
          class="form-control"
        >
          <option :value="1">1</option>
          <option :value="2">2</option>
          <option :value="3">3</option>
          <option :value="4">4</option>
          <option :value="5">5</option>
        </select>
      </div>
      <div class="form-group">
        <select
          id="role"
          v-model="employeeData.role"
          required
          class="form-control"
        >
          <option value="admin">admin</option>
          <option value="manager">manager</option>
          <option value="employee">employee</option>
        </select>
      </div>
      <button type="submit" class="btn-dark-gray-confirm">Create Employee</button>
      <div v-if="error" class="error-message">{{ error }}</div>
      <div v-if="successMessage" class="success-message">{{ successMessage }}</div>
    </form>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue'
import api from '@/services/api.ts'

interface EmployeeData {
  first_name: string
  last_name: string
  birth_date: string
  username: string
  password: string
  access_level: number | null
  role: string
}

export default defineComponent({
  name: 'CreateEmployeeView',
  setup() {
    const employeeData: EmployeeData = {
      first_name: '',
      last_name: '',
      birth_date: '',
      username: '',
      password: '',
      access_level: 1, // Default access level
      role: 'Employee',
    }

    const error = ref<string | null>(null)
    const successMessage = ref<string | null>(null)

    const handleSubmit = async () => {
      error.value = null
      successMessage.value = null
      try {
        if (
          !employeeData.first_name ||
          !employeeData.last_name ||
          !employeeData.username ||
          !employeeData.password ||
          !employeeData.role ||
          employeeData.access_level === null
        ) {
          error.value = 'Please fill in all required fields.'
          return
        }

        const response = await api.post('/v1/employee/create', employeeData)

        if (response.status === 200) {
          successMessage.value = response.data.message || 'Employee created successfully!'
          employeeData.first_name = ''
          employeeData.last_name = ''
          employeeData.birth_date = ''
          employeeData.username = ''
          employeeData.password = ''
          employeeData.access_level = 1
          employeeData.role = 'employee'
        } else {
          error.value = 'Failed to create employee.'
        }
      } catch (err: any) {
        error.value = err.response?.data?.message || 'An error occurred.'
      }
    }

    return {
      employeeData,
      error,
      handleSubmit,
      successMessage,
    }
  },
})
</script>

<style scoped>
.create-employee-view {
  max-width: 600px;
  margin: 2rem auto;
  padding: 2rem;
  background-color: var(--card-bg);
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  align-items: center;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  color: var(--primary-text);
  font-weight: bold;
}

input[type='text'],
input[type='date'],
input[type='number'],
input[type='password'],
select {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.2s ease;
  background-color: var(--input-bg);
  color: var(--input-text);
}

input[type='text']::placeholder,
input[type='date']::placeholder,
input[type='number']::placeholder,
input[type='password']::placeholder,
select::placeholder {
  color: rgba(var(--input-text-rgb), 0.6);
}

input[type='text']:focus,
input[type='date']:focus,
input[type='number']:focus,
input[type='password']:focus,
select:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 2px rgba(var(--primary-rgb), 0.2);
}

.error-message {
  color: #dc3545;
  margin-top: 1rem;
  padding: 0.75rem;
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
  border-radius: 4px;
  width: 100%;
  text-align: center;
}

.success-message {
  color: #155724;
  margin-top: 1rem;
  padding: 0.75rem;
  background-color: #d4edda;
  border: 1px solid #c3e6cb;
  border-radius: 4px;
  width: 100%;
  text-align: center;
}

/* Remove this style */
.select-placeholder option:first-child {
  color: rgba(var(--input-text-rgb), 0.6);
}
</style>
