import { defineStore } from 'pinia'
import api from '@/services/api'

export interface WorkTask {
  id: number
  employee_id: number
  description: string
  start_date: string
  end_date: string
  construction_site_id: number
}

interface TaskState {
  tasks: WorkTask[]
  loading: boolean
  error: string | null
}

export const useTaskStore = defineStore('task', {
  state: (): TaskState => ({
    tasks: [],
    loading: false,
    error: null,
  }),
  actions: {
    handleError(err: Error | unknown, defaultMessage: string) {
      if (err instanceof Error) {
        this.error = err.message
      } else {
        this.error = defaultMessage
      }
    },

    async updateTask(taskId: number, updatedTask: WorkTask) {
      this.loading = true
      this.error = null

      try {
        const response = await api.put(`v1/work-task/${taskId}`, updatedTask)
        if (response.status === 200) {
          // Update the task in the array
          const index = this.tasks.findIndex((task) => task.id === taskId)
          if (index !== -1) {
            this.tasks[index] = { ...this.tasks[index], ...response.data.data }
          }

          return response.data
        } else {
          this.error = 'Failed to update work task.'
        }
      } catch (err: Error | unknown) {
        this.handleError(err, 'An error occurred while updating the work task.')
      } finally {
        this.loading = false
      }
    },
  },
})
