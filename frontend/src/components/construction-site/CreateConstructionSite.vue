<template>
  <div class="create-construction-site-view">
    <h2>Create Construction Site</h2>
    <form @submit.prevent="handleSubmit" class="construction-site-form">
      <div class="form-group">
        <input
          type="text"
          id="name"
          v-model="constructionSiteData.name"
          placeholder="Site Name"
          required
          class="form-control"
        />
        <div v-if="v$.name.$error" class="error-message">
          {{ v$.name.$errors[0].$message }}
        </div>
      </div>
      <div class="form-group">
        <select
          id="manager_id"
          v-model="constructionSiteData.manager_id"
          required
          class="form-control select-placeholder"
        >
          <option value="null" disabled>Select Manager</option>
          <option v-for="manager in managers" :key="manager.id" :value="manager.id">
            <span>{{ manager.first_name }} {{ manager.last_name}}</span>
          </option>
          <option v-if="!managers.length" disabled>No managers available</option>
        </select>
        <div v-if="v$.manager_id.$error" class="error-message">
          {{ v$.manager_id.$errors[0].$message }}
        </div>
      </div>
      <div class="form-group">
        <input
          type="text"
          id="location"
          v-model="constructionSiteData.location"
          placeholder="Location"
          required
          class="form-control"
        />
        <div v-if="v$.location.$error" class="error-message">
          {{ v$.location.$errors[0].$message }}
        </div>
      </div>
      <div class="form-group">
        <input
          type="number"
          id="area"
          v-model="constructionSiteData.area"
          placeholder="Area"
          required
          class="form-control"
        />
        <div v-if="v$.area.$error" class="error-message">
          {{ v$.area.$errors[0].$message }}
        </div>
      </div>
      <div class="form-group">
        <select
          id="required_access_level"
          v-model="constructionSiteData.required_access_level"
          required
          class="form-control select-placeholder"
        >
          <option value="null" disabled>Required Access Level</option>
          <option :value="1">1</option>
          <option :value="2">2</option>
          <option :value="3">3</option>
          <option :value="4">4</option>
          <option :value="5">5</option>
        </select>
        <div v-if="v$.required_access_level.$error" class="error-message">
          {{ v$.required_access_level.$errors[0].$message }}
        </div>
      </div>
      <div class="button-group">
        <button type="submit" class="btn-dark-gray-confirm" :disabled="isSubmitting">
          <span v-if="isSubmitting">Creating...</span>
          <span v-else>Create Site</span>
        </button>
      </div>
      <div v-if="error" class="error-message">{{ error }}</div>
      <div v-if="successMessage" class="success-message">{{ successMessage }}</div>
    </form>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, reactive, computed } from 'vue'
import { useConstructionSiteStore, type ConstructionSite } from '@/stores/construction-site'
import { useEmployeeStore } from '@/stores/employee'
import { useVuelidate } from '@vuelidate/core'
import { required } from '@vuelidate/validators'

export default defineComponent({
  name: 'CreateConstructionSite',
  setup() {
    const constructionSiteStore = useConstructionSiteStore()
    const employeeStore = useEmployeeStore()

    // Reactive data for the construction site form
    const constructionSiteData: ConstructionSite = reactive({
      id: 0,
      name: '',
      manager_id: null,
      location: '',
      area: null,
      required_access_level: null, // Default access level
      workTasks: [],
    })

    const error = ref<string | null>(null)
    const successMessage = ref<string | null>(null)
    const isSubmitting = ref(false)

    // Validation rules
    const rules = {
      name: { required },
      manager_id: { required },
      location: { required },
      area: { required },
      required_access_level: { required },
    }

    const v$ = useVuelidate(rules, constructionSiteData)

    const managers = computed(() => {
      return employeeStore.getManagers || []
    })

    // const managers = computed(() => employeeStore.mana)

    // Handle form submission
    const handleSubmit = async () => {
      isSubmitting.value = true
      error.value = null
      successMessage.value = null

      const validationResult = await v$.value.$validate()
      if (!validationResult) {
        isSubmitting.value = false
        return
      }

      try {
        await constructionSiteStore.createConstructionSite(constructionSiteData)
        successMessage.value = 'Construction site created successfully!'

        Object.assign(constructionSiteData, {
          name: '',
          manager_id: null,
          location: '',
          area: null,
          required_access_level: 1,
        })

        v$.value.$reset()
      } catch (err: any) {
        error.value = err.response?.data?.message || 'An error occurred.'
      } finally {
        isSubmitting.value = false
      }
    }

    return {
      constructionSiteData,
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
</style>
