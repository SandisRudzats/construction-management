<template>
  <div class="view-assigned-sites">
    <h2>Assigned Construction Sites and Work Tasks</h2>
    <div v-if="loading">Loading...</div>
    <div v-else-if="error">Error: {{ error }}</div>
    <div v-else-if="assignedSites.length > 0">
      <div v-for="site in assignedSites" :key="site.id" class="site-container">
        <h3>{{ site.name }} (ID: {{ site.id }})</h3>
        <div class="site-details">
          <div class="detail-row"><strong>Manager ID:</strong> {{ site.manager_id }}</div>
          <div class="detail-row"><strong>Location:</strong> {{ site.location }}</div>
          <div class="detail-row"><strong>Area:</strong> {{ site.area }}</div>
          <div class="detail-row"><strong>Required Access Level:</strong> {{ site.required_access_level }}</div>
        </div>

        <h4>Work Tasks</h4>
        <table class="work-tasks-table">
          <thead>
          <tr>
            <th>ID</th>
            <th>Description</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Access Pass</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="task in site.workTasks" :key="task.id">
            <td>{{ task.id }}</td>
            <td>{{ task.description }}</td>
            <td>{{ task.start_date }}</td>
            <td>{{ task.end_date }}</td>
            <td>
              <button @click="validateAccess(site.id, task.id)" :disabled="checkingAccess">
                Gain Access
              </button>
              <span v-if="accessStatus[task.id]" :style="{ marginLeft: '10px' }">
                  {{ accessStatus[task.id] }}
                </span>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div v-else>No construction sites assigned to you.</div>
  </div>
</template>

<script lang="ts">
import { defineComponent, computed, onMounted, reactive, ref } from 'vue';
import { useConstructionSiteStore } from '@/stores/construction-site';
import { useUserStore } from '@/stores/user';
import { useAccessPassStore } from '@/stores/access-pass';
import dayjs from 'dayjs'; // make sure dayjs is installed

export default defineComponent({
  name: 'ViewAssignedSites',
  setup() {
    const constructionSiteStore = useConstructionSiteStore();
    const userStore = useUserStore();
    const checkingAccess = ref(false);
    const accessStatus = reactive<Record<number, string>>({});
    const accessPassStore = useAccessPassStore();

    const loading = computed(() => constructionSiteStore.loading);
    const error = computed(() => constructionSiteStore.error);

    const assignedSites = computed(() => {
      return constructionSiteStore.constructionSites
        .map(site => {
          const filteredTasks = site.workTasks.filter(
            task => task.employee_id === userStore.user.id
          );
          if (filteredTasks.length > 0) {
            return {
              ...site,
              workTasks: filteredTasks,
            };
          }
          return null;
        })
        .filter(site => site !== null);
    });

    const validateAccess = async (siteId?: number | null, taskId?: number | null) => {
      const employeeId = userStore.user?.id;

      if (!employeeId || !siteId || !taskId) {
        alert('Missing required data to validate access.');
        return;
      }

      try {
        const result = await accessPassStore.validateAccessPass(
          employeeId,
          siteId,
          taskId,
          dayjs().format('YYYY-MM-DD HH:mm:ss'),
        );

        if (result?.success) {
          alert(`Access Granted! Access Pass ID: ${result.data.accessPassId}`);
        } else {
          alert('Access Denied');
        }
      } catch (err: any) {
        alert(accessPassStore.error || 'An error occurred while validating access.');
      }
    };

    onMounted(async () => {
      if (!constructionSiteStore.constructionSites.length) {
        await constructionSiteStore.fetchUserAssignedSites(userStore.user);
      }
    });

    return {
      assignedSites,
      loading,
      error,
      validateAccess,
      accessStatus,
      checkingAccess,
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
