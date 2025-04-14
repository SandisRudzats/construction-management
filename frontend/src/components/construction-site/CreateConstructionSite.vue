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
        <input
          type="number"
          id="manager_id"
          v-model="constructionSiteData.manager_id"
          placeholder="Manager ID"
          required
          class="form-control"
        />
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
          <option value="" disabled hidden>Required Access Level</option>
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
import { defineComponent, ref, reactive } from 'vue';
import api from '@/services/api';
import { useVuelidate } from '@vuelidate/core';
import { required } from '@vuelidate/validators';

interface ConstructionSiteData {
  name: string;
  manager_id: number | null;
  location: string;
  area: number | null;
  required_access_level: number | null;
}

export default defineComponent({
  name: 'CreateConstructionSite',
  setup() {
    const constructionSiteData: ConstructionSiteData = reactive({
      name: '',
      manager_id: null,
      location: '',
      area: null,
      required_access_level: 1, // Default access level
    });

    const error = ref<string | null>(null);
    const successMessage = ref<string | null>(null);
    const isSubmitting = ref(false);

    const rules = {
      name: { required },
      manager_id: { required },
      location: { required },
      area: { required },
      required_access_level: { required },
    };

    const v$ = useVuelidate(rules, constructionSiteData);

    const handleSubmit = async () => {
      isSubmitting.value = true;
      error.value = null;
      successMessage.value = null;

      const validationResult = await v$.value.$validate();
      if (!validationResult) {
        isSubmitting.value = false;
        return;
      }

      try {
        const response = await api.post('v1/construction-site/create', constructionSiteData);
        if (response.status === 201) {
          successMessage.value = 'Construction site created successfully!';
          constructionSiteData.name = '';
          constructionSiteData.manager_id = null;
          constructionSiteData.location = '';
          constructionSiteData.area = null;
          constructionSiteData.required_access_level = 1;
          v$.value.$reset();
        } else {
          error.value = 'Failed to create construction site.';
        }
      } catch (err: any) {
        error.value = err.response?.data?.message || 'An error occurred.';
      } finally {
        isSubmitting.value = false;
      }
    };

    return {
      constructionSiteData,
      error,
      handleSubmit,
      successMessage,
      v$,
      isSubmitting,
    };
  },
});
</script>

<style scoped>
/* ... (your existing styles) ... */
</style>
