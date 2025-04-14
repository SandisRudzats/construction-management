<template>
  <div v-if="userStore.user">
    <h2>Employee Details</h2>
    <p><strong>ID:</strong> {{ userStore.user.id }}</p>
    <p><strong>Name:</strong> {{ userStore.user.firstName }} {{ userStore.user.lastName }}</p>
    <p><strong>Username:</strong> {{ userStore.user.username }}</p>
    <p><strong>Role:</strong> {{ userStore.user.role }}</p>
    <p><strong>Status:</strong> {{ userStore.user.active ? 'Active' : 'Inactive' }}</p>
    <p><strong>Birth Date:</strong> {{ userStore.user.birthDate || 'Not Provided' }}</p>
    <p><strong>Manager ID:</strong> {{ userStore.user.managerId }}</p>
    <p><strong>Member Since:</strong> {{ userStore.user.createdAt }}</p>
  </div>
  <div v-else>
    <p>Loading...</p>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import { useUserStore } from '@/stores/user.ts'

export default defineComponent({
  name: 'EmployeeProfile',
  setup() {
    const userStore = useUserStore()

    return {
      userStore,
    }
  },
})
</script>

<style scoped>
/* ... (your existing styles) ... */
</style>
