<template>
  <div class="view-construction-sites">
    <h2>Construction Sites and Work Tasks</h2>
    <div v-if="loading || employeeStore.isLoading">Loading...</div>
    <div v-else-if="error || employeeStore.getError">
      Error: {{ error || employeeStore.getError }}
    </div>
    <div v-else-if="constructionSites.length > 0">
      <div v-for="site in constructionSites" :key="site.id" class="site-container">
        <h3>{{ site.name }} (ID: {{ site.id }})</h3>

        <div class="site-details" v-if="editingSiteId !== site.id">
          <div class="detail-row"><strong>Manager ID:</strong> {{ site.manager_id }}</div>
          <div class="detail-row"><strong>Location:</strong> {{ site.location }}</div>
          <div class="detail-row"><strong>Area:</strong> {{ site.area }}</div>
          <div class="detail-row">
            <strong>Required Access Level:</strong> {{ site.required_access_level }}
          </div>
          <div v-if="userStore.user.role === 'admin'" class="detail-row">
            <button @click="editSite(site)" class="action-button">Edit Site</button>
            <button @click="deleteSite(site.id)" class="action-button delete-button">
              Delete Site
            </button>
          </div>
        </div>
        <div class="site-edit" v-else>
          <div class="detail-row">
            <strong>Manager ID:</strong> <input type="number" v-model="editedSite.manager_id" />
          </div>
          <div class="detail-row">
            <strong>Location:</strong> <input type="text" v-model="editedSite.location" />
          </div>
          <div class="detail-row">
            <strong>Area:</strong> <input type="number" v-model="editedSite.area" />
          </div>
          <div class="detail-row">
            <strong>Required Access Level:</strong>
            <input type="number" v-model="editedSite.required_access_level" />
          </div>
          <button @click="updateSite(site)" class="action-button">Update Site</button>
        </div>

        <h4>Work Tasks</h4>
        <button
          v-if="userStore.user.role === 'admin' || userStore.user.id === site.manager_id"
          @click="toggleAddTask(site)"
          class="action-button add-button"
        >
          {{ site.showAddTask ? 'Cancel Add Task' : 'Add Work Task' }}
        </button>

        <div v-if="site.showAddTask">
          <div class="detail-row">
            <strong>Employee:</strong>
            <select v-model="newTask.employee_id">
              <option
                v-for="employee in getFilteredEmployees(
                  site.manager_id,
                  site.required_access_level,
                )"
                :key="employee.id"
                :value="employee.id"
              >
                {{ employee.first_name }} {{ employee.last_name }}
              </option>
              <option
                v-if="
                  getFilteredEmployees(site.manager_id, site.required_access_level).length === 0
                "
                disabled
              >
                No suitable employees
              </option>
            </select>
          </div>
          <div class="detail-row">
            <strong>Description:</strong> <input type="text" v-model="newTask.description" />
          </div>
          <div class="detail-row">
            <strong>Start Date:</strong> <input type="date" v-model="newTask.start_date" />
          </div>
          <div class="detail-row">
            <strong>End Date:</strong> <input type="date" v-model="newTask.end_date" />
          </div>
          <button @click="addTask(site)" class="action-button save-button">Save Task</button>
        </div>

        <table class="work-tasks-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Employee</th>
              <th>Description</th>
              <th>Start Date</th>
              <th>End Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="task in site.workTasks" :key="task.id">
              <td v-if="editingTaskId !== task.id">{{ task.id }}</td>
              <td v-else>
                <select v-model="editedTask.employee_id">
                  <option
                    v-for="employee in getFilteredEmployees(
                      site.manager_id,
                      site.required_access_level,
                    )"
                    :key="employee.id"
                    :value="employee.id"
                  >
                    {{ employee.first_name }} {{ employee.last_name }}
                  </option>
                  <option
                    v-if="
                      getFilteredEmployees(site.manager_id, site.required_access_level).length === 0
                    "
                    disabled
                  >
                    No suitable employees
                  </option>
                </select>
              </td>
              <td v-if="editingTaskId !== task.id">{{ task.description }}</td>
              <td v-else><input type="text" v-model="editedTask.description" /></td>
              <td v-if="editingTaskId !== task.id">{{ task.start_date }}</td>
              <td v-else><input type="date" v-model="editedTask.start_date" /></td>
              <td v-if="editingTaskId !== task.id">{{ task.end_date }}</td>
              <td v-else><input type="date" v-model="editedTask.end_date" /></td>
              <td>
                <button
                  v-if="
                    editingTaskId !== task.id &&
                    (userStore.user.role === 'admin' || isTaskManager(site, task.employee_id))
                  "
                  @click="editTask(task)"
                  class="action-button"
                >
                  Edit
                </button>
                <button
                  v-else-if="editingTaskId === task.id"
                  @click="updateTask(site.id, task)"
                  class="action-button save-button"
                >
                  Save
                </button>
                <button @click="deleteTask(site.id, task.id)" class="action-button delete-button">
                  Delete Task
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div v-else>No construction sites found.</div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted, reactive, computed } from 'vue'
import api from '@/services/api'
import { useUserStore } from '@/stores/user'
import { useEmployeeStore } from '@/stores/employee'

interface ConstructionSite {
  id: number
  name: string
  manager_id: number
  location: string
  area: number
  required_access_level: number
  workTasks: WorkTask[]
  showAddTask?: boolean
}

interface WorkTask {
  id: number
  employee_id: number
  description: string
  start_date: string
  end_date: string
  construction_site_id: number
}

export default defineComponent({
  name: 'ViewConstructionSites',
  setup() {
    const constructionSites = ref<ConstructionSite[]>([])
    const loading = ref(false)
    const error = ref<string | null>(null)
    const userStore = useUserStore()
    const employeeStore = useEmployeeStore()
    const editingSiteId = ref<number | null>(null)
    const editedSite = reactive<ConstructionSite>({
      id: 0,
      name: '',
      manager_id: 0,
      location: '',
      area: 0,
      required_access_level: 0,
      workTasks: [],
    })

    const editingTaskId = ref<number | null>(null)
    const editedTask = reactive<WorkTask>({
      id: 0,
      employee_id: 0,
      description: '',
      start_date: '',
      end_date: '',
      construction_site_id: 0,
    })

    const newTask = reactive<WorkTask>({
      id: 0,
      employee_id: 0,
      description: '',
      start_date: new Date().toISOString().slice(0, 10),
      end_date: new Date().toISOString().slice(0, 10),
      construction_site_id: 0,
    })

    const fetchConstructionSites = async () => {
      loading.value = true
      error.value = null

      try {
        let response
        if (userStore.user.role === 'admin' || userStore.user.role === 'manager') {
          response = await api.get('v1/construction-site')
        } else {
          // Fetch sites for employees, including work tasks
          const taskResponse = await api.get('v1/work-task/my-tasks')
          const siteIds = new Set(
            taskResponse.data.map((task: WorkTask) => task.construction_site_id),
          )

          const sitesResponse = await api.get('v1/construction-site')
          const allSites: ConstructionSite[] = sitesResponse.data

          // Filter sites based on the employee's assigned tasks
          constructionSites.value = allSites.filter((site) => siteIds.has(site.id))

          // Populate workTasks for each site
          for (const site of constructionSites.value) {
            site.workTasks = taskResponse.data.filter(
              (task) => task.construction_site_id === site.id,
            )
          }
          loading.value = false
          return // Exit early to avoid redundant site.map
        }

        constructionSites.value = response.data // Assign sites for admin/manager

        // Fetch work tasks for each site.  This is done *after* fetching the sites.
        for (const site of constructionSites.value) {
          const taskResponse = await api.get(`v1/construction-site/${site.id}/work-tasks`)
          site.workTasks = taskResponse.data
          site.showAddTask = false // Initialize showAddTask
        }
      } catch (err: any) {
        error.value = err.response?.data?.message || 'An error occurred.'
      } finally {
        loading.value = false
      }
    }

    const editSite = (site: ConstructionSite) => {
      editingSiteId.value = site.id
      Object.assign(editedSite, site)
    }

    const updateSite = async (site: ConstructionSite) => {
      loading.value = true
      error.value = null

      try {
        const response = await api.put(`v1/construction-site/${site.id}`, editedSite)
        if (response.status === 200) {
          await fetchConstructionSites()
          editingSiteId.value = null
        } else {
          error.value = 'Failed to update construction site.'
        }
      } catch (err: any) {
        error.value = err.response?.data?.message || 'An error occurred.'
      } finally {
        loading.value = false
      }
    }

    const deleteSite = async (id: number) => {
      if (window.confirm('Are you sure you want to delete this site?')) {
        loading.value = true
        error.value = null

        try {
          const response = await api.delete(`v1/construction-site/${id}`)
          if (response.status === 204) {
            await fetchConstructionSites()
          } else {
            error.value = 'Failed to delete construction site.'
          }
        } catch (err: any) {
          error.value = err.response?.data?.message || 'An error occurred.'
        } finally {
          loading.value = false
        }
      }
    }

    const toggleAddTask = (site: ConstructionSite) => {
      site.showAddTask = !site.showAddTask
      Object.assign(newTask, {
        construction_site_id: site.id,
        employee_id: 0,
        description: '',
        start_date: new Date().toISOString().slice(0, 10),
        end_date: new Date().toISOString().slice(0, 10),
      })
    }

    const addTask = async (site: ConstructionSite) => {
      loading.value = true
      error.value = null
      newTask.construction_site_id = site.id
      try {
        const response = await api.post('v1/work-task/create', newTask)
        if (response.status === 201) {
          await fetchConstructionSites()
          site.showAddTask = false
          Object.assign(newTask, {
            employee_id: 0,
            description: '',
            start_date: new Date().toISOString().slice(0, 10),
            end_date: new Date().toISOString().slice(0, 10),
          })
        } else {
          error.value = 'Failed to add work task.'
        }
      } catch (err: any) {
        error.value = err.response?.data?.message || 'An error occurred.'
      } finally {
        loading.value = false
      }
    }

    const editTask = (task: WorkTask) => {
      editingTaskId.value = task.id
      Object.assign(editedTask, task)
    }

    const updateTask = async (siteId: number, task: WorkTask) => {
      loading.value = true
      error.value = null
      try {
        const response = await api.put(`v1/work-task/${task.id}`, editedTask)
        if (response.status === 200) {
          await fetchConstructionSites()
          editingTaskId.value = null
        } else {
          error.value = 'Failed to update work task.'
        }
      } catch (err: any) {
        error.value = err.response?.data?.message || 'An error occurred.'
      } finally {
        loading.value = false
      }
    }

    const deleteTask = async (siteId: number, taskId: number) => {
      if (window.confirm('Are you sure you want to delete this task?')) {
        // added confirm
        loading.value = true
        error.value = null
        try {
          const response = await api.delete(`v1/work-task/${taskId}`)
          if (response.status === 204) {
            await fetchConstructionSites()
          } else {
            error.value = 'Failed to delete work task.'
          }
        } catch (err: any) {
          error.value = err.response?.data?.message || 'An error occurred.'
        } finally {
          loading.value = false
        }
      }
    }

    const getFilteredEmployees = (managerId: number, accessLevel: number) => {
      if (!employeeStore.getEmployees) {
        return []
      }

      console.log('this');
      return employeeStore.getEmployees.filter(
        (e) => e.manager_id === managerId && e.access_level >= accessLevel,
      )
    }

    const isTaskManager = (site: ConstructionSite, employeeId: number) => {
      const employee = employeeStore.getEmployees.find((e) => e.id === employeeId)
      if (!employee) {
        return false
      }
      return userStore.user.id === site.manager_id
    }

    onMounted(async () => {
      if (!employeeStore.hasFetched) {
        await employeeStore.fetchEmployees()
      }
      try {
        await fetchConstructionSites()
      } catch (e) {
        console.log(e)
      }
    })

    return {
      constructionSites,
      loading,
      error,
      userStore,
      employeeStore,
      editingSiteId,
      editedSite,
      editingTaskId,
      editedTask,
      editSite,
      updateSite,
      deleteSite,
      toggleAddTask,
      addTask,
      editTask,
      updateTask,
      deleteTask,
      newTask,
      getFilteredEmployees,
      isTaskManager,
    }
  },
})
</script>

<style scoped>
.site-container {
  margin-bottom: 2rem;
  border: 1px solid #ddd;
  padding: 1rem;
}

.site-details,
.site-edit {
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

.action-button {
  padding: 0.5rem 1rem;
  margin-right: 0.5rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.action-button.delete-button {
  background-color: #dc3545;
  color: white;
}

.action-button.add-button {
  background-color: #28a745;
  color: white;
}

.action-button.save-button {
  background-color: #1e7e34;
  color: white;
}

.site-edit input[type='text'],
.site-edit input[type='number'],
.work-tasks-table input[type='text'],
.work-tasks-table input[type='number'],
.work-tasks-table input[type='date'] {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  box-sizing: border-box;
}

.work-tasks-table select {
  width: auto;
}
</style>
