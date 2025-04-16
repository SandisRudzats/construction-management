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
        if (response.data.success) {
          this.constructionSites = response.data.data
          // Fetch work tasks for each site
          for (const site of this.constructionSites) {
            const taskResponse = await api.get(`v1/construction-site/${site.id}/site-work-tasks`)
            site.workTasks = taskResponse.data.data
            site.showAddTask = false
          }
        } else {
          this.error = response.data.message
          return null
        }
      } catch (err: Error | unknown) {
        if (err instanceof Error) {
          this.error = err.message
        } else {
          this.error = 'An unknown error occurred while fetching construction sites'
          return null
        }
      } finally {
        this.loading = false
      }
    },

    async updateSite(id: number, updatedSite: ConstructionSite) {
      this.loading = true
      this.error = null

      try {
        const response = await api.put(`v1/construction-site/${id}`, updatedSite)
        if (response.data.success) {
          // Update the site in the array
          const index = this.constructionSites.findIndex((site) => site.id === id)
          if (index !== -1) {
            this.constructionSites[index] = {
              ...this.constructionSites[index],
              ...response.data.data,
            }
          }
        } else {
          this.error = response.data.message
        }
      } catch (err: Error | unknown) {
        if (err instanceof Error) {
          this.error = err.message
        } else {
          this.error = 'An unknown error occurred while updating construction site'
          return null
        }
      } finally {
        this.loading = false
      }
    },

    async deleteSite(id: number) {
      this.loading = true
      this.error = null

      try {
        const response = await api.delete(`v1/construction-site/${id}`)
        if (response.data.success) {
          // Remove the site from the array
          this.constructionSites = this.constructionSites.filter((site) => site.id !== id)
        } else {
          this.error = response.data.message
        }
      } catch (err: Error | unknown) {
        if (err instanceof Error) {
          this.error = err.message
        } else {
          this.error = 'An unknown error occurred while deleting construction site'
          return null
        }
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

        if (response.data.success) {
          const createdTask: WorkTask = response.data.data

          const siteIndex = this.constructionSites.findIndex(
            (site) => site.id === newTask.construction_site_id,
          )

          if (siteIndex !== -1) {
            this.constructionSites[siteIndex].workTasks.push(createdTask)
          }

          return createdTask
        } else {
          this.error = response.data.message
          return null
        }
      } catch (err: Error | unknown) {
        if (err instanceof Error) {
          this.error = err.message
        } else {
          this.error = 'An unknown error occurred while adding task for Construction site'
          return null
        }
      } finally {
        this.loading = false
      }
    },

    async deleteTask(taskId: number) {
      this.loading = true
      this.error = null

      try {
        const response = await api.delete(`v1/work-task/${taskId}`)
        if (response.data.success) {
          this.constructionSites.forEach((site) => {
            site.workTasks = site.workTasks.filter((task) => task.id !== taskId)
          })
        } else {
          this.error = response.data.message
        }
      } catch (err: Error | unknown) {
        if (err instanceof Error) {
          this.error = err.message
        } else {
          this.error = 'An unknown error occurred while deleting Task'
          return null
        }
      } finally {
        this.loading = false
      }
    },

    async fetchUserAssignedSites(user: User) {
      this.loading = true
      this.error = null

      try {
        const response = await api.get('v1/construction-site')
        if (response.data.success) {
          this.constructionSites = response.data.data
          // Fetch work tasks only for the current user
          for (const site of this.constructionSites) {
            const taskResponse = await api.get(`v1/work-task/employee-tasks`)
            site.workTasks = taskResponse.data.data.filter(
              (task: WorkTask) => task.construction_site_id === site.id,
            )
            site.showAddTask = false
          }
        } else {
          this.error = response.data.message
          return null
        }
      } catch (err: Error | unknown) {
        if (err instanceof Error) {
          this.error = err.message
        } else {
          this.error = 'An unknown error occurred while fetching Employee assigned sites'
          return null
        }
      } finally {
        this.loading = false
      }
    },

    async createConstructionSite(constructionSiteData: Omit<ConstructionSite, 'id' | 'workTasks'>) {
      this.loading = true
      this.error = null

      try {
        const response = await api.post('v1/construction-site/create', constructionSiteData)
        if (response.data.success) {
          this.constructionSites.push(response.data.data)
        } else {
          this.error = response.data.message
        }
      } catch (err: Error | unknown) {
        if (err instanceof Error) {
          this.error = err.message
        } else {
          this.error = 'An unknown error occurred while creating Construction site'
          return null
        }
      } finally {
        this.loading = false
      }
    },
  },
})
