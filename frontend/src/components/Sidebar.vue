<template>
  <div class="sidebar">
    <router-link
      to="/dashboard"
      class="sidebar-option gray-button"
      @click="emitSection('dashboard')"
    >
      Dashboard
    </router-link>
    <router-link
      to="/employees"
      class="sidebar-option gray-button"
      @click="emitSection('employees')"
    >
      Employees
    </router-link>
    <router-link
      to="/construction-sites"
      class="sidebar-option gray-button"
      @click="emitSection('construction-sites')"
    >
      Sites
    </router-link>
    <router-link
      to="/work-tasks"
      class="sidebar-option gray-button"
      @click="emitSection('work-tasks')"
    >
      Tasks
    </router-link>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';

export default defineComponent({
  name: 'Sidebar',
  emits: ['section-selected'], // Declare emitted event
  setup(props, { emit }) { // Access emit here
    const router = useRouter();

    const handleLogout = async () => {
      try {
        await api.post('/auth/logout');
        localStorage.removeItem('user');
        await router.push('/login');
      } catch (err: any) {
        console.error('Logout failed', err);
      }
    };

    const emitSection = (sectionName: string) => {
      emit('section-selected', sectionName);
    };

    return {
      handleLogout,
      emitSection,
    };
  },
});
</script>

<style scoped>
.sidebar {
  display: flex;
  flex-direction: column;
  background-color: var(--primary-bg);
  padding: 1rem;
  width: 150px;
}

.sidebar-option {
  padding: 0.75rem 1rem;
  text-decoration: none;
  color: white;
  cursor: pointer;
  border-bottom: 1px solid var(--border-color);
  text-align: left;
  font-size: 1rem;
  transition: background-color 0.3s ease, color 0.3s ease;
  margin-bottom: 0.5rem;
}

.sidebar-option:last-child {
  border-bottom: none;
  margin-bottom: 0;
}

.sidebar-option:hover {
  background-color: #444;
}

.sidebar-option.router-link-exact-active {
  background-color: #444;
}

.gray-button {
  background-color: var(--dark-gray-button);
  border: none;
}
</style>
