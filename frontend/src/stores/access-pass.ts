import { defineStore } from 'pinia'
import api from '@/services/api'
import type { ApiResponse } from '@/interfaces/ApiResponse.ts'

export interface AccessPass {
  id: number
  construction_site_id: number
  employee_id: number
  work_task_id: number
  valid_from: string
  valid_to: string
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
    handleError(err: Error | unknown, defaultMessage: string) {
      if (err instanceof Error) {
        this.error = err.message
      } else {
        this.error = defaultMessage
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
        const response = await api.post('v1/access-passes/validate-access', {
          employeeId,
          constructionSiteId,
          workTaskId,
          checkDate,
        })
        if (response.data.success) {
          return response.data.data
        } else {
          this.error = response.data.message
          return null
        }
      } catch (err: Error | unknown) {
        this.handleError(err, 'An unknown error occurred while validating access pass')
        return null
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
        const response = await api.post<ApiResponse<AccessPass>>(
          '/v1/access-passes/create',
          payload,
        )
        if (response.data.success) {
          const accessPass = response.data.data
          if (accessPass) {
            return accessPass
          }
        } else {
          this.error = response.data.message
          return null
        }
      } catch (err: Error | unknown) {
        this.handleError(err, 'An unknown error occurred while creating access pass')
        return null
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
        const response = await api.put<ApiResponse<AccessPass>>(
          '/v1/access-passes/update-from-task',
          {
            construction_site_id: task.construction_site_id,
            employee_id: task.employee_id,
            work_task_id: task.id,
            valid_from: task.start_date,
            valid_to: task.end_date,
          },
        )
        if (response.data.success) {
          const accessPass = response.data.data
          if (accessPass) {
            return accessPass
          }
        } else {
          this.error = response.data.message
          return null
        }
      } catch (err: Error | unknown) {
        this.handleError(
          err,
          'An unknown error occurred while updating access pass dates from task',
        )
        return null
      } finally {
        this.loading = false
      }
    },
  },
})
