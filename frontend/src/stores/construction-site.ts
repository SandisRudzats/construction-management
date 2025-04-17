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
      await this._withLoading(async () => {
        const response = await api.get('v1/construction-site')
        if (response.data.success) {
          const sites = response.data.data
          for (const site of sites) {
            const taskResponse = await api.get(`v1/construction-site/${site.id}/site-work-tasks`)
            site.workTasks = taskResponse.data.data
            site.showAddTask = false
          }
          this.constructionSites = sites
        } else {
          this.error = response.data.message
        }
      }, 'An unknown error occurred while fetching construction sites')
    },

    async updateSite(id: number, updatedSite: ConstructionSite) {
      await this._withLoading(async () => {
        const response = await api.put(`v1/construction-site/${id}`, updatedSite)
        if (response.data.success) {
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
      }, 'An unknown error occurred while updating construction site')
    },

    async deleteSite(id: number) {
      await this._withLoading(async () => {
        const response = await api.delete(`v1/construction-site/${id}`)
        if (response.data.success) {
          this.constructionSites = this.constructionSites.filter((site) => site.id !== id)
        } else {
          this.error = response.data.message
        }
      }, 'An unknown error occurred while deleting construction site')
    },

    toggleAddTask(site: ConstructionSite) {
      const index = this.constructionSites.findIndex((s) => s.id === site.id)
      if (index !== -1) {
        this.constructionSites[index].showAddTask = !this.constructionSites[index].showAddTask
      }
    },

    async addTask(newTask: WorkTask) {
      return await this._withLoading(async () => {
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
      }, 'An unknown error occurred while adding task for Construction site')
    },

    async deleteTask(taskId: number) {
      await this._withLoading(async () => {
        const response = await api.delete(`v1/work-task/${taskId}`)
        if (response.data.success) {
          this.constructionSites.forEach((site) => {
            site.workTasks = site.workTasks.filter((task) => task.id !== taskId)
          })
        } else {
          this.error = response.data.message
        }
      }, 'An unknown error occurred while deleting Task')
    },

    async fetchUserAssignedSites(user: User) {
      await this._withLoading(async () => {
        const response = await api.get('v1/construction-site')
        if (response.data.success) {
          const sites = response.data.data
          const taskResponse = await api.get('v1/work-task/employee-tasks')
          for (const site of sites) {
            site.workTasks = taskResponse.data.data.filter(
              (task: WorkTask) => task.construction_site_id === site.id,
            )
            site.showAddTask = false
          }
          this.constructionSites = sites
        } else {
          this.error = response.data.message
        }
      }, 'An unknown error occurred while fetching Employee assigned sites')
    },

    async createConstructionSite(constructionSiteData: Omit<ConstructionSite, 'id' | 'workTasks'>) {
      await this._withLoading(async () => {
        const response = await api.post('v1/construction-site/create', constructionSiteData)
        if (response.data.success) {
          this.constructionSites.push({ ...response.data.data, workTasks: [], showAddTask: false })
        } else {
          this.error = response.data.message
        }
      }, 'An unknown error occurred while creating Construction site')
    },

    async _withLoading<T>(
      callback: () => Promise<T>,
      fallbackErrorMessage = 'An error occurred',
    ): Promise<T | void> {
      this.loading = true
      this.error = null
      try {
        return await callback()
      } catch (err: Error | unknown) {
        this.error = err instanceof Error ? err.message : fallbackErrorMessage
      } finally {
        this.loading = false
      }
    },
  },
})
