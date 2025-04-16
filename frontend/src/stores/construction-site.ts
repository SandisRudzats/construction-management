import { defineStore } from 'pinia'
import api from '@/services/api'
import { type WorkTask } from '@/stores/task.ts'
import { type User } from '@/stores/user'

export interface ConstructionSite {
  id: number
  name: string
  manager_id: number | null
  location: string
  area: number | null
  required_access_level: number | null
  workTasks: WorkTask[]
  showAddTask?: boolean
}

interface ConstructionSiteState {
  constructionSites: ConstructionSite[]
  loading: boolean
  error: string | null
}

export const useConstructionSiteStore = defineStore('constructionSite', {
  state: (): ConstructionSiteState => ({
    constructionSites: [],
    loading: false,
    error: null,
  }),
  actions: {
    async fetchConstructionSites(user: User) {
      this.loading = true
      this.error = null

      try {
        const response = await api.get('v1/construction-site')

        this.constructionSites = response.data

        // Fetch work tasks for each site
        for (const site of this.constructionSites) {
          const taskResponse = await api.get(`v1/construction-site/${site.id}/work-tasks`)
          site.workTasks = taskResponse.data
          site.showAddTask = false // Initialize showAddTask
        }
      } catch (err: any) {
        this.error = err.response?.data?.message || 'An error occurred.'
      } finally {
        this.loading = false
      }
    },

    async updateSite(id: number, updatedSite: ConstructionSite) {
      this.loading = true
      this.error = null

      try {
        const response = await api.put(`v1/construction-site/${id}`, updatedSite)
        if (response.status === 200) {
          // Update the site in the array
          const index = this.constructionSites.findIndex((site) => site.id === id)
          if (index !== -1) {
            this.constructionSites[index] = { ...this.constructionSites[index], ...response.data }
          }
        } else {
          this.error = 'Failed to update construction site.'
        }
      } catch (err: any) {
        this.error = err.response?.data?.message || 'An error occurred.'
      } finally {
        this.loading = false
      }
    },

    async deleteSite(id: number) {
      this.loading = true
      this.error = null

      try {
        const response = await api.delete(`v1/construction-site/${id}`)
        if (response.status === 204) {
          // Remove the site from the array
          this.constructionSites = this.constructionSites.filter((site) => site.id !== id)
        } else {
          this.error = 'Failed to delete construction site.'
        }
      } catch (err: any) {
        this.error = err.response?.data?.message || 'An error occurred.'
      } finally {
        this.loading = false
      }
    },

    toggleAddTask(site: ConstructionSite) {
      const index = this.constructionSites.findIndex((s) => s.id === site.id)
      if (index !== -1) {
        this.constructionSites[index] = {
          ...this.constructionSites[index],
          showAddTask: !this.constructionSites[index].showAddTask,
        }
      }
    },

    async addTask(newTask: WorkTask) {
      this.loading = true
      this.error = null

      try {
        const response = await api.post('v1/work-task/create', newTask)

        if (response.status === 201) {
          const createdTask: WorkTask = response.data

          const siteIndex = this.constructionSites.findIndex(
            (site) => site.id === newTask.construction_site_id,
          )

          if (siteIndex !== -1) {
            // const taskResponse = await api.get(
            //   `v1/construction-site/${newTask.construction_site_id}/work-tasks`,
            // )
            // this.constructionSites[siteIndex].workTasks = taskResponse.data
            this.constructionSites[siteIndex].workTasks.push(createdTask)
          }

          return createdTask
        } else {
          this.error = 'Failed to add work task.'
          return null
        }
      } catch (err: any) {
        this.error = err.response?.data?.message || 'An error occurred.'
        return null
      } finally {
        this.loading = false
      }
    },

    async updateTask(taskId: number, updatedTask: WorkTask) {
      this.loading = true
      this.error = null

      try {
        const response = await api.put(`v1/work-task/${taskId}`, updatedTask)

        if (response.status === 200) {
          // Find the site and update the task in its workTasks array
          this.constructionSites.forEach((site) => {
            const taskIndex = site.workTasks.findIndex((task) => task.id === taskId)
            if (taskIndex !== -1) {
              site.workTasks[taskIndex] = { ...site.workTasks[taskIndex], ...response.data }
            }
          })
        } else {
          this.error = 'Failed to update work task.'
        }
      } catch (err: any) {
        this.error = err.response?.data?.message || 'An error occurred.'
      } finally {
        this.loading = false
      }
    },

    async deleteTask(taskId: number) {
      this.loading = true
      this.error = null

      try {
        const response = await api.delete(`v1/work-task/${taskId}`)
        if (response.status === 204) {
          // Remove the task from the workTasks array of the correct site
          this.constructionSites.forEach((site) => {
            site.workTasks = site.workTasks.filter((task) => task.id !== taskId)
          })
        } else {
          this.error = 'Failed to delete work task.'
        }
      } catch (err: any) {
        this.error = err.response?.data?.message || 'An error occurred.'
      } finally {
        this.loading = false
      }
    },

    // New function for fetching only tasks assigned to the current user
    async fetchUserAssignedSites(user: User) {
      this.loading = true
      this.error = null

      try {
        const response = await api.get('v1/construction-site')
        this.constructionSites = response.data

        // Fetch work tasks only for the current user
        for (const site of this.constructionSites) {
          const taskResponse = await api.get(`v1/work-task/employee`) // Fetch tasks assigned to this user
          site.workTasks = taskResponse.data.filter(
            (task: WorkTask) => task.construction_site_id === site.id,
          )
          site.showAddTask = false // Initialize showAddTask
        }
      } catch (err: any) {
        this.error = err.response?.data?.message || 'An error occurred.'
      } finally {
        this.loading = false
      }
    },
    async createConstructionSite(constructionSiteData: Omit<ConstructionSite, 'id' | 'workTasks'>) {
      this.loading = true
      this.error = null

      try {
        const response = await api.post('v1/construction-site/create', constructionSiteData)
        if (response.status === 201) {
          this.constructionSites.push(response.data)
        } else {
          this.error = 'Failed to create construction site.'
        }
      } catch (err: any) {
        this.error = err.response?.data?.message || 'An error occurred.'
      } finally {
        this.loading = false
      }
    },
  },
})
