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
      <button type="submit" class="btn-dark-gray-confirm login-button">Login</button>
      <div v-if="error" class="error-message">{{ error }}</div>
    </form>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'
import { useUserStore } from '@/stores/user'

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
        const response = await api.post('/auth/login', {
          username: username.value,
          password: password.value,
        })

        userStore.setUser(response.data)
        localStorage.setItem('user', JSON.stringify(response.data))
        await router.push('/dashboard')
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
  background-color: var(--primary-bg);
}

.login-form {
  width: 90%;
  max-width: 350px;
  padding: 2rem;
  background-color: var(--card-bg);
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
  color: var(--primary-text);
  margin-bottom: 2rem;
  text-align: center;
}

.form-control {
  width: 100%;
  margin-bottom: 1.5rem;
  padding: 1rem;
  font-size: 1rem;
  border: 1px solid var(--border-color);
  border-radius: 5px;
  transition:
    border-color 0.2s ease,
    box-shadow 0.2s ease;
  background-color: var(--input-bg);
  color: var(--primary-text);
}

.form-control::placeholder {
  color: rgba(var(--input-text-rgb), 0.3);
  opacity: 1;
}

.form-control:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 2px 6px rgba(var(--primary-rgb), 0.3);
}

.btn-primary {
  width: 100%;
  padding: 1rem;
  font-size: 1.1rem;
  font-weight: bold;
  background-color: var(--primary);
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition:
    background-color 0.3s ease,
    transform 0.1s ease;
}

.btn-primary:hover {
  background-color: var(--primary-dark);
  transform: translateY(-2px);
}

.btn-primary:active {
  background-color: color-mix(in srgb, var(--primary-dark), #000000 20%);
  transform: translateY(0);
}

.error-message {
  color: var(--accent-red);
  margin-top: 1.5rem;
  text-align: center;
  font-size: 0.9rem;
  padding: 0.5rem;
  background-color: rgba(244, 67, 54, 0.1);
  border-radius: 5px;
  border: 1px solid var(--accent-red);
}
</style>
``` I've corrected the errors and adjusted the code to align with your project's CSS variabl
