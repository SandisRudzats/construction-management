<template>
  <div v-if="employeeUser">
    <h2>Employee Details</h2>
    <p><strong>ID:</strong> {{ employeeUser.id }}</p>
    <p><strong>Name:</strong> {{ employeeUser.first_name }} {{ employeeUser.last_name }}</p>
    <p><strong>Username:</strong> {{ employeeUser.username }}</p>
    <p><strong>Role:</strong> {{ employeeUser.role }}</p>
    <p><strong>Status:</strong> {{ employeeUser.active ? 'Active' : 'Inactive' }}</p>
    <p><strong>Birth Date:</strong> {{ employeeUser.birth_date || 'Not Provided' }}</p>
    <p><strong>Manager ID:</strong> {{ employeeUser.manager_id }}</p>
    <p><strong>Member Since:</strong> {{ employeeUser.created_at }}</p>
  </div>
  <div v-else>
    <p>Loading...</p>
  </div>
</template>

<script lang="ts">
import {computed, defineComponent} from 'vue'
import {useEmployeeStore} from "@/stores/employee.ts";
import {useUserStore} from "@/stores/user.ts";

export default defineComponent({
  name: 'EmployeeProfile',
  setup() {
    const employeeStore = useEmployeeStore()
    const userStore = useUserStore()

    const employeeUser = computed(() => {
      const userId = userStore.user?.id; // Access id safely
      if (userId) {
        console.log(employeeStore.getEmployeeById(userId))
        return employeeStore.getEmployeeById(userId);
      }
      return null;
    });

    console.log(employeeUser);
    return {
      employeeUser,
    }
  },
})
</script>

<style scoped>
</style>
