// stores/access-pass.ts
import { defineStore } from 'pinia'
import axios from 'axios'
import api from '@/services/api'

export interface AccessPass {
  id: number
  construction_site_id: number
  employee_id: number
  work_task_id: number
  valid_from: string
  valid_to: string
}

interface ValidateAccessPayload {
  employeeId: number
  constructionSiteId: number
  workTaskId: number
  checkDate: string
}

export const useAccessPassStore = defineStore('accessPass', {
  state: () => ({
    accessPasses: [] as AccessPass[],
    loading: false,
    error: null as string | null,
    validationResult: null as { success: boolean; message: string; data?: any } | null,
  }),

  getters: {
    getAccessPasses: (state) => state.accessPasses,
  },

  actions: {
    async fetchAccessPasses() {
      this.loading = true
      this.error = null
      try {
        const response = await axios.get('/api/v1/access-passes')
        this.accessPasses = response.data
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Failed to fetch access passes.'
      } finally {
        this.loading = false
      }
    },

    async deleteAccessPass(id: number) {
      this.loading = true
      this.error = null
      try {
        await axios.delete(`/api/v1/access-passes/${id}`)
        this.accessPasses = this.accessPasses.filter((p) => p.id !== id)
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Failed to delete access pass.'
      } finally {
        this.loading = false
      }
    },

    async validateAccessPass(
      employeeId: number,
      constructionSiteId: number,
      workTaskId: number,
      checkDate: string,
    ) {
      this.loading = true
      this.error = null

      try {
        const response = await api.post('v1/access-pass/validate-access', {
          employeeId,
          constructionSiteId,
          workTaskId,
          checkDate,
        })

        return response.data
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Failed to validate access.'
        throw error
      } finally {
        this.loading = false
      }
    },

    async createAccessPass(payload: {
      construction_site_id: number
      employee_id: number
      work_task_id: number
      valid_from: string
      valid_to: string
    }) {
      this.loading = true
      this.error = null
      try {
        const response = await api.post('/v1/access-passes/create', payload)
        return response.data
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Failed to create access pass.'
        console.error('Access pass creation error:', this.error)
      } finally {
        this.loading = false
      }
    },

    async updateAccessPassDatesFromTask(task: {
      id: number
      construction_site_id: number
      employee_id: number
      start_date: string
      end_date: string
    }) {
      this.loading = true
      this.error = null

      try {
        const response = await api.post('/v1/access-passes/update-from-task', {
          construction_site_id: task.construction_site_id,
          employee_id: task.employee_id,
          work_task_id: task.id,
          valid_from: task.start_date,
          valid_to: task.end_date,
        })

        return response.data
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Failed to update access pass from task.'
        throw err
      } finally {
        this.loading = false
      }
    }
  },
})
