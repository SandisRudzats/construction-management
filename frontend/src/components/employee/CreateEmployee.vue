<template>
  <div class="create-employee-view">
    <h2>Create Employee</h2>
    <form class="employee-form" @submit.prevent="handleSubmit">
      <div class="form-group">
        <label for="first_name">First Name</label>
        <input
          id="first_name"
          v-model="employeeData.first_name"
          class="form-control"
          placeholder="First Name"
          required
          type="text"
        />
        <div v-if="v$.first_name.$error" class="error-message">
          {{ v$.first_name.$errors[0].$message }}
        </div>
      </div>
      <div class="form-group">
        <label for="last_name">Last Name</label>
        <input
          id="last_name"
          v-model="employeeData.last_name"
          class="form-control"
          placeholder="Last Name"
          required
          type="text"
        />
        <div v-if="v$.last_name.$error" class="error-message">
          {{ v$.last_name.$errors[0].$message }}
        </div>
      </div>
      <div class="form-group">
        <label for="birth_date">Birth Date</label>
        <input
          id="birth_date"
          v-model="employeeData.birth_date"
          class="form-control"
          placeholder="Birth Date"
          type="date"
        />
        <div v-if="v$.birth_date.$error" class="error-message">
          {{ v$.birth_date.$errors[0].$message }}
        </div>
      </div>
      <div class="form-group">
        <label for="username">Username</label>
        <input
          id="username"
          v-model="employeeData.username"
          class="form-control"
          placeholder="Username"
          required
          type="text"
        />
        <div v-if="v$.username.$error" class="error-message">
          {{ v$.username.$errors[0].$message }}
        </div>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input
          id="password"
          v-model="employeeData.password"
          class="form-control"
          placeholder="Password"
          required
          type="password"
        />
        <div v-if="v$.password.$error" class="error-message">
          {{ v$.password.$errors[0].$message }}
        </div>
      </div>
      <div class="form-group">
        <label for="access_level">Access Level</label>
        <select
          id="access_level"
          v-model="employeeData.access_level"
          class="form-control select-placeholder"
          required
        >
          <option disabled hidden value="">Access Level</option>
          <option :value="1">1</option>
          <option :value="2">2</option>
          <option :value="3">3</option>
          <option :value="4">4</option>
          <option :value="5">5</option>
        </select>
        <div v-if="v$.access_level.$error" class="error-message">
          {{ v$.access_level.$errors[0].$message }}
        </div>
      </div>
      <div class="form-group">
        <label for="role">Role</label>
        <select
          id="role"
          v-model="employeeData.role"
          class="form-control select-placeholder"
          required
        >
          <option disabled hidden value="">role</option>
          <option value="admin">admin</option>
          <option value="manager">manager</option>
          <option value="employee">employee</option>
        </select>
        <div v-if="!employeeData.role"><span>Role selection is mandatory</span></div>
        <div v-if="v$.role.$error" class="error-message">
          {{ v$.role.$errors[0].$message }}
        </div>
      </div>
      <div class="form-group">
        <label for="manager_id">Manager</label>
        <select id="manager_id" v-model="employeeData.manager_id" required class="form-control">
          <option :value="null">None</option>
          <option v-for="manager in managers" :key="manager.id" :value="manager.id">
            {{ manager.first_name }} {{ manager.last_name }} (ID: {{ manager.id }})
          </option>
        </select>
        <div v-if="!employeeData.manager_id">Manager selection is mandatory</div>
      </div>
      <div class="button-group">
        <button :disabled="isSubmitting" class="btn-dark-gray-confirm" type="submit">
          <span v-if="isSubmitting">Creating...</span>
          <span v-else>Create Employee</span>
        </button>
      </div>
      <div v-if="error" class="error-message">{{ error }}</div>
      <div v-if="successMessage" class="success-message">{{ successMessage }}</div>
    </form>
  </div>
</template>

<script lang="ts">
import { computed, defineComponent, onMounted, reactive, ref } from 'vue'
import { useVuelidate } from '@vuelidate/core'
import { helpers, minLength, required } from '@vuelidate/validators'
import { type NewEmployee, useEmployeeStore } from '@/stores/employee.ts'

// Custom validator for date format (YYYY-MM-DD)
const dateFormat = helpers.regex(/^\d{4}-\d{2}-\d{2}$/)

export default defineComponent({
  name: 'CreateEmployee',
  setup() {
    const employeeData = reactive<NewEmployee>({
      first_name: '',
      last_name: '',
      birth_date: '',
      username: '',
      password: '',
      access_level: 1,
      role: 'employee',
      manager_id: null,
    })

    const error = ref<string | null>(null)
    const successMessage = ref<string | null>(null)
    const isSubmitting = ref(false)
    const employeeStore = useEmployeeStore()

    const rules = {
      first_name: { required },
      last_name: { required },
      birth_date: { dateFormat },
      username: { required, minLength: minLength(4) },
      password: { required, minLength: minLength(8) },
      access_level: { required },
      role: { required },
      manager_id: { required },
    }

    const v$ = useVuelidate(rules, employeeData)

    const handleSubmit = async () => {
      try {
        isSubmitting.value = true
        await employeeStore.addEmployeeUser(employeeData as NewEmployee)
        successMessage.value = 'Employee created successfully!'

        Object.assign(employeeData, {
          first_name: '',
          last_name: '',
          birth_date: '',
          username: '',
          password: '',
          access_level: 1,
          role: 'employee',
          manager_id: null,
        })

        employeeStore.hasFetched = false

        v$.value.$reset()
      } catch (err: any) {
        error.value = err.message || 'An error occurred.'
      } finally {
        isSubmitting.value = false
      }
    }

    const managers = computed(() => employeeStore.getManagers)

    onMounted(async () => {
      if (!employeeStore.hasFetched) {
        await employeeStore.fetchEmployees()
      }
    })

    return {
      employeeData,
      error,
      handleSubmit,
      successMessage,
      v$,
      isSubmitting,
      managers,
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

.form-group {
  margin-bottom: 1rem;
  width: 100%;
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
  box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.2);
}

.btn-primary {
  padding: 0.75rem 1.5rem;
  background-color: var(--header-bg);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.2s ease;
  margin-top: 1rem;
  width: fit-content;
}

.btn-primary:hover {
  background-color: var(--primary-dark);
}

.btn-primary:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}

.error-message {
  color: #dc3545;
  margin-top: 0.5rem;
  padding: 0.25rem 0.5rem;
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
  border-radius: 4px;
  width: 100%;
  text-align: left;
  font-size: 0.875rem;
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

.button-group {
  display: flex;
  justify-content: center;
  margin-top: 1rem;
  width: 100%;
}
</style>
