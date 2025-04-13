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
        <div v-if="v$.first_name.$error" class="error-message">
          {{ v$.first_name.$errors[0].$message }}
        </div>
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
        <div v-if="v$.last_name.$error" class="error-message">
          {{ v$.last_name.$errors[0].$message }}
        </div>
      </div>
      <div class="form-group">
        <input
          type="date"
          id="birth_date"
          v-model="employeeData.birth_date"
          placeholder="Birth Date"
          class="form-control"
        />
        <div v-if="v$.birth_date.$error" class="error-message">
          {{ v$.birth_date.$errors[0].$message }}
        </div>
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
        <div v-if="v$.username.$error" class="error-message">
          {{ v$.username.$errors[0].$message }}
        </div>
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
        <div v-if="v$.password.$error" class="error-message">
          {{ v$.password.$errors[0].$message }}
        </div>
      </div>
      <div class="form-group">
        <select
          id="access_level"
          v-model="employeeData.access_level"
          required
          class="form-control select-placeholder"
        >
          <option value="" disabled hidden>
            Access Level
          </option>
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
        <select
          id="role"
          v-model="employeeData.role"
          required
          class="form-control select-placeholder"
        >
          <option value="" disabled hidden>role</option>
          <option value="admin">admin</option>
          <option value="manager">manager</option>
          <option value="employee">employee</option>
        </select>
        <div v-if="v$.role.$error" class="error-message">
          {{ v$.role.$errors[0].$message }}
        </div>
      </div>
      <div class="button-group">
        <button type="submit" class="btn-dark-gray-confirm" :disabled="isSubmitting">
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
import { defineComponent, ref, reactive, onMounted } from 'vue';
import api from '@/services/api';
import { useVuelidate } from '@vuelidate/core';
import { required, email, minLength, helpers } from '@vuelidate/validators';
import { useRouter } from 'vue-router'; // Import useRouter

// Custom validator for date format (YYYY-MM-DD)
const dateFormat = helpers.regex(/^\d{4}-\d{2}-\d{2}$/);

interface EmployeeData {
  first_name: string;
  last_name: string;
  birth_date: string;
  username: string;
  password: string;
  access_level: number | null;
  role: string;
}

export default defineComponent({
  name: 'CreateEmployeeView',
  setup() {
    const employeeData: EmployeeData = reactive({
      first_name: '',
      last_name: '',
      birth_date: '',
      username: '',
      password: '',
      access_level: 1, // Default access level
      role: 'employee',
    });

    const error = ref<string | null>(null);
    const successMessage = ref<string | null>(null);
    const isSubmitting = ref(false);
    const router = useRouter(); // Use useRouter

    const rules = {
      first_name: { required },
      last_name: { required },
      birth_date: { dateFormat }, // Use the custom validator
      username: { required, minLength: minLength(4) },
      password: { required, minLength: minLength(8) },
      access_level: { required },
      role: { required },
    };

    const v$ = useVuelidate(rules, employeeData);

    const handleSubmit = async () => {
      isSubmitting.value = true;
      error.value = null;
      successMessage.value = null;

      const validationResult = await v$.value.$validate(); // Await validation
      if (!validationResult) {
        isSubmitting.value = false;
        return; // Stop if the form is not valid
      }

      try {
        const response = await api.post('v1/employee/create', employeeData);
        const validStatusCodes = [200,201];
        if (validStatusCodes.includes(response.status)) {
          successMessage.value =
            response.data.message || 'Employee created successfully!';
          employeeData.first_name = '';
          employeeData.last_name = '';
          employeeData.birth_date = '';
          employeeData.username = '';
          employeeData.password = '';
          employeeData.access_level = 1;
          employeeData.role = 'employee';
          v$.value.$reset();
          // Redirect to the employees list page after successful creation
          await router.push('/employees');
        } else {
          error.value = 'Failed to create employee.';
        }
      } catch (err: any) {
        error.value = err.response?.data?.message || 'An error occurred.';
      } finally {
        isSubmitting.value = false;
      }
    };

    // onMounted hook to redirect if the user is not on the employees route.
    onMounted(() => {
      if (router.currentRoute.value.path !== '/employees') {
        router.push('/employees');
      }
    });

    return {
      employeeData,
      error,
      handleSubmit,
      successMessage,
      v$,
      isSubmitting,
      router
    };
  },
});
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

input[type="text"],
input[type="date"],
input[type="number"],
input[type="password"],
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

input[type="text"]::placeholder,
input[type="date"]::placeholder,
input[type="number"]::placeholder,
input[type="password"]::placeholder,
select::placeholder {
  color: rgba(var(--input-text-rgb), 0.6);
}

input[type="text"]:focus,
input[type="date"]:focus,
input[type="number"]:focus,
input[type="password"]:focus,
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
