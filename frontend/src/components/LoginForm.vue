<template>
  <div class="login-container">
    <form @submit.prevent="handleLogin">
      <input
          type="text"
          v-model="form.username"
          class="form-control input-large"
          placeholder="Username"
      />
      <input
          type="password"
          v-model="form.password"
          class="form-control input-large"
          placeholder="Password"
      />
      <button type="submit" class="btn-primary">Login</button>
      <div v-if="error" class="error-message">{{ error }}</div>
    </form>
  </div>
</template>

<script lang="ts">
import { defineComponent, reactive, ref } from 'vue';
import api from '@/services/api';
import { useRouter } from 'vue-router';

interface ErrorResponse {
  response: {
    data: {
      message: string;
    };
  };
}

export default defineComponent({
  name: 'LoginForm',
  setup() {
    const form = reactive({
      username: '',
      password: '',
    });
    const error = ref<string | null>(null);
    const router = useRouter();

    const handleLogin = async () => {
      error.value = null;
      try {
        const response = await api.post('/auth/login', {
          username: form.username,
          password: form.password,
        });
        localStorage.setItem('user', JSON.stringify(response.data));
        await router.push('/dashboard');
      } catch (err: any) {
        if ((err as ErrorResponse).response) {
          error.value = err.response.data.message || 'Login failed';
        } else {
          error.value = 'An unexpected error occurred.';
        }
      }
    };

    return {
      form,
      error,
      handleLogin,
    };
  },
});
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background-color: var(--primary-bg);
}

form {
  width: 300px;
  padding: 3rem;
  background-color: var(--card-bg);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  align-items: center;
}

.form-control {
  width: 100%;
  color: var(--primary-text);
  margin-bottom: 1.5rem;
  padding: 1rem;
  text-align: center;
}

.form-control::placeholder {
  opacity: 0.3;
}

.btn-primary {
  width: 100%;
  align-self: center;
}

.input-large {
  font-size: 1.1rem;
}
</style>
