<template>
  <div class="login-container">
    <form @submit.prevent="handleLogin" class="login-form">
      <h2 class="login-heading">Login</h2>
      <input
        type="text"
        v-model="username"
        class="form-control input-large"
        placeholder="Username"
        autocomplete="username"
        required
      />
      <input
        type="password"
        v-model="password"
        class="form-control input-large"
        placeholder="Password"
        autocomplete="current-password"
        required
      />
      <button type="submit" class="btn-dark-gray-confirm">Login</button>
      <div v-if="error" class="error-message">{{ error }}</div>
    </form>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api' // Adjust the path if needed
import { useUserStore } from '@/stores/user' // Adjust the path if needed

export default defineComponent({
  name: 'LoginForm',
  setup() {
    const username = ref('')
    const password = ref('')
    const error = ref<string | null>(null)
    const router = useRouter()
    const userStore = useUserStore()

    const handleLogin = async (e: Event) => {
      e.preventDefault()
      error.value = null

      if (!username.value.trim() || !password.value.trim()) {
        error.value = 'Username and password are required.'
        return
      }

      try {
        const response = await api.post('/auth/login', { //  Use your API endpoint
          username: username.value,
          password: password.value,
        })

        userStore.setUser(response.data); // Store user data
        localStorage.setItem('user', JSON.stringify(response.data)); // Persist
        await router.push('/dashboard') //  your dashboard route
      } catch (err: any) {
        const message =
          err.response?.data?.message || 'Login failed. Please check your credentials.'
        error.value = message
        console.error('Login Error:', err)
      }
    }

    return {
      username,
      password,
      error,
      handleLogin,
    }
  },
})
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background-color: var(--primary-bg); /* Use your CSS variable */
}

.login-form {
  width: 90%;
  max-width: 350px;
  padding: 2rem;
  background-color: var(--card-bg); /* Use your CSS variable */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
  border-radius: 10px;
  display: flex;
  flex-direction: column;
  align-items: center;
  transition:
    transform 0.2s ease-in-out,
    box-shadow 0.2s ease-in-out;
  backdrop-filter: blur(5px);
}

.login-form:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
}

.login-heading {
  font-size: 2rem;
  font-weight: bold;
  color: var(--primary-text); /* Use your CSS variable */
  margin-bottom: 2rem;
  text-align: center;
}

.form-control {
  width: 100%;
  margin-bottom: 1.5rem;
  padding: 1rem;
  font-size: 1rem;
  border: 1px solid var(--border-color); /* Use your CSS variable */
  border-radius: 5px;
  transition:
    border-color 0.2s ease,
    box-shadow 0.2s ease;
  background-color: var(--input-bg); /* Use your CSS variable */
  color: var(--primary-text); /* Use your CSS variable */
}

.form-control::placeholder {
  color: rgba(var(--input-text-rgb), 0.3); /* Use your CSS variable */
  opacity: 1;
}

.form-control:focus {
  outline: none;
  border-color: var(--primary); /* Use your CSS variable */
  box-shadow: 0 2px 6px rgba(var(--primary-rgb), 0.3); /* Use your CSS variable */
}

.error-message {
  color: var(--accent-red); /* Use your CSS variable */
  margin-top: 1.5rem;
  text-align: center;
  font-size: 0.9rem;
  padding: 0.5rem;
  background-color: rgba(244, 67, 54, 0.1);
  border-radius: 5px;
  border: 1px solid var(--accent-red); /* Use your CSS variable */
}
</style>
