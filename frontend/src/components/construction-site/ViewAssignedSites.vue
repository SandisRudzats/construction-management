<template>
  <div class="view-assigned-sites">
    <h2>Assigned Construction Sites and Work Tasks</h2>
    <div v-if="loading">Loading...</div>
    <div v-else-if="error">Error: {{ error }}</div>
    <div v-else-if="assignedSites.length > 0">
      <div v-for="site in assignedSites" :key="site.id" class="site-container">
        <h3>{{ site.name }} (ID: {{ site.id }})</h3>
        <div class="site-details">
          <div class="detail-row">
            <strong>Manager ID:</strong> {{ site.manager_id }}
          </div>
          <div class="detail-row">
            <strong>Location:</strong> {{ site.location }}
          </div>
          <div class="detail-row">
            <strong>Area:</strong> {{ site.area }}
          </div>
          <div class="detail-row">
            <strong>Required Access Level:</strong> {{ site.required_access_level }}
          </div>
        </div>

        <h4>Work Tasks</h4>
        <table class="work-tasks-table">
          <thead>
          <tr>
            <th>ID</th>
            <th>Description</th>
            <th>Start Date</th>
            <th>End Date</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="task in site.workTasks" :key="task.id">
            <td>{{ task.id }}</td>
            <td>{{ task.description }}</td>
            <td>{{ task.start_date }}</td>
            <td>{{ task.end_date }}</td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div v-else>No construction sites assigned to you.</div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue';
import api from '@/services/api';
import { useUserStore } from '@/stores/user';

interface ConstructionSite {
  id: number;
  name: string;
  manager_id: number;
  location: string;
  area: number;
  required_access_level: number;
  workTasks: WorkTask[];
}

interface WorkTask {
  id: number;
  employee_id: number;
  description: string;
  start_date: string;
  end_date: string;
  construction_site_id: number;
}

export default defineComponent({
  name: 'ViewAssignedSites',
  setup() {
    const assignedSites = ref<ConstructionSite[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);
    const userStore = useUserStore();

    const fetchAssignedSites = async () => {
      loading.value = true;
      error.value = null;
      try {
        //  Get the employee ID from the userStore.
        // Fetch work tasks for the logged-in employee.  Clean URL.
        const taskResponse = await api.get(`v1/work-task/employee`);

        const siteIds = new Set(taskResponse.data.map((task: WorkTask) => task.construction_site_id));

        const sitesResponse = await api.get('v1/construction-site');
        const allSites: ConstructionSite[] = sitesResponse.data;

        assignedSites.value = allSites.filter((site) => siteIds.has(site.id));

        for (const site of assignedSites.value) {
          site.workTasks = taskResponse.data.filter(
            (task: WorkTask) => task.construction_site_id === site.id
          );
        }
      } catch (err: any) {
        error.value = err.response?.data?.message || 'An error occurred.';
      } finally {
        loading.value = false;
      }
    };

    onMounted(async () => {
      fetchAssignedSites();
    });

    return {
      assignedSites,
      loading,
      error,
      userStore,
    };
  },
});
</script>

<style scoped>
.site-container {
  margin-bottom: 2rem;
  border: 1px solid #ddd;
  padding: 1rem;
}

.site-details {
  margin-bottom: 1rem;
}

.detail-row {
  margin-bottom: 0.5rem;
}

.work-tasks-table {
  width: 100%;
  border-collapse: collapse;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  min-width: 800px;
}

.work-tasks-table thead tr {
  background-color: var(--header-bg);
  color: var(--header-text);
}

.work-tasks-table th,
.work-tasks-table td {
  padding: 0.75rem;
  text-align: left;
  border-bottom: 1px solid var(--border-color);
}

.work-tasks-table th {
  font-weight: bold;
}

.work-tasks-table tbody tr:hover {
  background-color: rgba(0, 0, 0, 0.05);
}
</style>
