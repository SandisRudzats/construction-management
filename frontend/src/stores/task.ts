import { defineStore } from 'pinia';
import api from '@/services/api';

export interface WorkTask {
  id: number;
  employee_id: number;
  description: string;
  start_date: string;
  end_date: string;
  construction_site_id: number;
}

interface TaskState {
  tasks: WorkTask[];
  loading: boolean;
  error: string | null;
}

export const useTaskStore = defineStore('task', {
  state: (): TaskState => ({
    tasks: [],
    loading: false,
    error: null,
  }),
  actions: {

    async addTask(newTask: WorkTask) {
      this.loading = true;
      this.error = null;

      try {
        const response = await api.post('v1/work-task/create', newTask);
        if (response.status === 201) {
          this.tasks.push(response.data);
        } else {
          this.error = 'Failed to add work task.';
        }
      } catch (err: any) {
        this.error = err.response?.data?.message || 'An error occurred.';
      } finally {
        this.loading = false;
      }
    },

    async updateTask(taskId: number, updatedTask: WorkTask) {
      this.loading = true;
      this.error = null;

      try {
        const response = await api.put(`v1/work-task/${taskId}`, updatedTask);
        if (response.status === 200) {
          // Update the task in the array
          const index = this.tasks.findIndex((task) => task.id === taskId);
          if (index !== -1) {
            this.tasks[index] = { ...this.tasks[index], ...response.data };
          }
        } else {
          this.error = 'Failed to update work task.';
        }
      } catch (err: any) {
        this.error = err.response?.data?.message || 'An error occurred.';
      } finally {
        this.loading = false;
      }
    },

    async deleteTask(taskId: number) {
      this.loading = true;
      this.error = null;

      try {
        const response = await api.delete(`v1/work-task/${taskId}`);
        if (response.status === 204) {
          // Remove the task from the array
          this.tasks = this.tasks.filter((task) => task.id !== taskId);
        } else {
          this.error = 'Failed to delete work task.';
        }
      } catch (err: any) {
        this.error = err.response?.data?.message || 'An error occurred.';
      } finally {
        this.loading = false;
      }
    },
  },
});
