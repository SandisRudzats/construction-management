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
            <strong>Manager:</strong>
            <select v-model="editedSite.manager_id">
              <option v-for="manager in managers" :key="manager.id" :value="manager.id">
                {{ manager.first_name }} {{ manager.last_name }}
              </option>
            </select>
            <span v-if="v$.editedSite.manager_id.$error" class="error-message">
              {{ v$.editedSite.manager_id.$errors[0].$message }}
            </span>
          </div>
          <div class="detail-row">
            <strong>Location:</strong> <input type="text" v-model="editedSite.location" />
            <span v-if="v$.editedSite.location.$error" class="error-message">
              {{ v$.editedSite.location.$errors[0].$message }}
            </span>
          </div>
          <div class="detail-row">
            <strong>Area:</strong> <input type="number" v-model="editedSite.area" />
            <span v-if="v$.editedSite.area.$error" class="error-message">
              {{ v$.editedSite.area.$errors[0].$message }}
            </span>
          </div>
          <div class="detail-row">
            <strong>Required Access Level:</strong>
            <input type="number" v-model="editedSite.required_access_level" />
            <span v-if="v$.editedSite.required_access_level.$error" class="error-message">
              {{ v$.editedSite.required_access_level.$errors[0].$message }}
            </span>
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
                  getFilteredEmployees(site?.manager_id, site.required_access_level).length === 0
                "
                disabled
              >
                No suitable employees
              </option>
            </select>
            <span v-if="v$.newTask.employee_id.$error" class="error-message">
              Choose an employee
            </span>
          </div>
          <div class="detail-row">
            <strong>Description:</strong> <input type="text" v-model="newTask.description" />
            <span v-if="v$.newTask.description.$error" class="error-message">
              {{ v$.newTask.description.$errors[0].$message }}
            </span>
          </div>
          <div class="detail-row">
            <strong>Start Date:</strong> <input type="date" v-model="newTask.start_date" />
            <span v-if="v$.newTask.start_date.$error" class="error-message">
              {{ v$.newTask.start_date.$errors[0].$message }}
            </span>
          </div>
          <div class="detail-row">
            <strong>End Date:</strong> <input type="date" v-model="newTask.end_date" />
            <span v-if="v$.newTask.end_date.$error" class="error-message">
              {{ v$.newTask.end_date.$errors[0].$message }}
            </span>
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
              <td>{{ task.id }}</td>
              <td v-if="editingTaskId !== task.id">
                {{ employeeStore.getEmployees.find((e) => e.id === task.employee_id)?.first_name }}
              </td>
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
                    {{ employee.first_name }} {{ employee.last_name }} | access level :
                    {{ employee.access_level }}
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
                <span v-if="v$.editedTask.employee_id.$error" class="error-message">
                  {{ v$.editedTask.employee_id.$errors[0].$message }}
                </span>
              </td>
              <td v-if="editingTaskId !== task.id">{{ task.description }}</td>
              <td v-else>
                <input type="text" v-model="editedTask.description" />
                <span v-if="v$.editedTask.description.$error" class="error-message">
                  {{ v$.editedTask.description.$errors[0].$message }}
                </span>
              </td>
              <td v-if="editingTaskId !== task.id">{{ task.start_date }}</td>
              <td v-else>
                <input type="date" v-model="editedTask.start_date" />
                <span v-if="v$.editedTask.start_date.$error" class="error-message">
                  {{ v$.editedTask.start_date.$errors[0].$message }}
                </span>
              </td>
              <td v-if="editingTaskId !== task.id">{{ task.end_date }}</td>
              <td v-else>
                <input type="date" v-model="editedTask.end_date" />
                <span v-if="v$.editedTask.end_date.$error" class="error-message">
                  {{ v$.editedTask.end_date.$errors[0].$message }}
                </span>
              </td>
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
import { computed, defineComponent, onMounted, reactive, ref } from 'vue'
import { useUserStore } from '@/stores/user'
import { useEmployeeStore } from '@/stores/employee'
import { type ConstructionSite, useConstructionSiteStore } from '@/stores/construction-site'
import { useTaskStore, type WorkTask } from '@/stores/task'
import { useAccessPassStore } from '@/stores/access-pass.ts'
import useVuelidate from '@vuelidate/core'
import { minValue, numeric, required } from '@vuelidate/validators'

export default defineComponent({
  name: 'ManageConstructionSites',
  setup() {
    const userStore = useUserStore()
    const employeeStore = useEmployeeStore()
    const constructionSiteStore = useConstructionSiteStore()
    const taskStore = useTaskStore()
    const accessPassStore = useAccessPassStore()

    // Use computed properties to access data from the store
    const constructionSites = computed(() => constructionSiteStore.constructionSites)
    const loading = computed(() => constructionSiteStore.loading || taskStore.loading)
    const error = computed(() => constructionSiteStore.error || employeeStore.error)

    const editingSiteId = ref<number | null>(null)
    const editedSite = reactive({
      id: 0,
      name: '',
      manager_id: 0,
      location: '',
      area: 0,
      required_access_level: 0,
      workTasks: [],
    })

    const editingTaskId = ref<number | null>(null)
    const editedTask = reactive({
      id: 0,
      employee_id: 0,
      description: '',
      start_date: '',
      end_date: '',
      construction_site_id: 0,
    })

    const newTask = reactive({
      id: 0,
      employee_id: 0,
      description: '',
      start_date: new Date().toISOString().slice(0, 10),
      end_date: new Date().toISOString().slice(0, 10),
      construction_site_id: 0,
    })

    const rules = computed(() => ({
      editedSite: {
        manager_id: { required, numeric, minValue: minValue(1) },
        location: { required },
        area: { required, numeric, minValue: minValue(1) },
        required_access_level: { required, numeric, minValue: minValue(1) },
      },
      newTask: {
        employee_id: { required, numeric, minValue: minValue(1) },
        description: { required },
        start_date: { required },
        end_date: { required },
      },
      editedTask: {
        employee_id: { required, numeric, minValue: minValue(1) },
        description: { required },
        start_date: { required },
        end_date: { required },
      },
    }))

    const v$ = useVuelidate(rules, { editedSite, newTask, editedTask })

    // Use store actions instead of local functions
    const fetchConstructionSites = async () => {
      await constructionSiteStore.fetchConstructionSites(userStore.user)
    }

    const editSite = (site: ConstructionSite) => {
      editingSiteId.value = site.id
      Object.assign(editedSite, site)
      v$.value.$reset()
    }

    const updateSite = async (site: ConstructionSite) => {
      const result = await v$.value.editedSite.$validate()
      if (!result) return

      await constructionSiteStore.updateSite(site.id, editedSite)
      if (!constructionSiteStore.error) {
        editingSiteId.value = null
      }
    }

    const deleteSite = async (id: number) => {
      if (window.confirm('Are you sure you want to delete this site?')) {
        await constructionSiteStore.deleteSite(id)
      }
    }

    const toggleAddTask = (site: ConstructionSite) => {
      constructionSiteStore.toggleAddTask(site)
      Object.assign(newTask, {
        construction_site_id: site.id,
        employee_id: 0,
        description: '',
        start_date: new Date().toISOString().slice(0, 10),
        end_date: new Date().toISOString().slice(0, 10),
      })

      v$.value.$reset()
    }

    const addTask = async (site: ConstructionSite) => {
      const result = await v$.value.newTask.$validate()
      if (!result) return

      newTask.construction_site_id = site.id

      const createdTask = await constructionSiteStore.addTask(newTask)

      if (!(createdTask && createdTask.id)) return

      site.showAddTask = false

      Object.assign(newTask, {
        employee_id: 0,
        description: '',
        start_date: new Date().toISOString().slice(0, 10),
        end_date: new Date().toISOString().slice(0, 10),
      })

      const createdAccessPass = await accessPassStore.createAccessPass({
        construction_site_id: site.id,
        employee_id: createdTask.employee_id,
        work_task_id: createdTask.id,
        valid_from: createdTask.start_date,
        valid_to: createdTask.end_date,
      })

      v$.value.$reset()

      return {
        task: createdTask,
        accessPass: createdAccessPass,
      }
    }

    const editTask = (task: WorkTask) => {
      editingTaskId.value = task.id
      Object.assign(editedTask, task)

      v$.value.$reset()
    }

    const updateTask = async (siteId: number, task: WorkTask) => {
      const result = await v$.value.editedTask.$validate()
      if (!result) return

      const response = await taskStore.updateTask(task.id, editedTask)
      if (response) {
        await accessPassStore.updateAccessPassDatesFromTask({
          id: response.id,
          construction_site_id: response.construction_site_id,
          employee_id: response.employee_id,
          start_date: response.start_date,
          end_date: response.end_date,
        })

        editingTaskId.value = null

        const index = taskStore.tasks.findIndex((t) => t.id === response.id)
        if (index !== -1) {
          taskStore.tasks.splice(index, 1, response)
        } else {
          taskStore.tasks.push(response)
        }
      }
    }

    const deleteTask = async (siteId: number, taskId: number) => {
      if (window.confirm('Are you sure you want to delete this task?')) {
        await constructionSiteStore.deleteTask(taskId)
      }
    }

    const getFilteredEmployees = computed(
      () => (siteManagerId: number, requiredAccessLevel: number) => {
        return employeeStore.getEmployees.filter(
          (e) => e.manager_id === siteManagerId && e.access_level >= requiredAccessLevel,
        )
      },
    )

    const isTaskManager = (site: ConstructionSite, employeeId: number) => {
      const employee = employeeStore.getEmployees.find((e) => e.id === employeeId)
      if (!employee) {
        return false
      }
      return userStore.user.id === site.manager_id
    }

    const managers = computed(() => {
      return employeeStore.getEmployees.filter((e) => e.role === 'manager')
    })

    onMounted(async () => {
      if (!employeeStore.hasFetched) {
        await employeeStore.fetchEmployees()
      }
      await fetchConstructionSites()
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
      v$,
      managers,
    }
  },
})
</script>

<style scoped>
.site-details,
.site-edit {
  margin-bottom: 1rem;
}

.detail-row {
  margin-bottom: 0.5rem;
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

.error-message {
  color: red;
  font-size: 0.8rem;
}
</style>
