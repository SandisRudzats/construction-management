import { defineStore } from 'pinia';
import api from "@/services/api.ts";

interface UserState {
  id: number | null;
  username: string | null;
  firstName: string | null;
  lastName: string | null;
  active: boolean | null;
  role: string | null;
  birthDate: string | null;
  managerId: number | null;
  createdAt: string | null;
  roles: string[];
  permissions: string[];
}

export const useUserStore = defineStore('user', {
  state: (): UserState => ({
    id: null,
    username: null,
    firstName: null,
    lastName: null,
    active: null,
    role: null,
    birthDate: null,
    managerId: null,
    createdAt: null,
    roles: [],
    permissions: [],
  }),
  actions: {
    setUser(userData: {
      id: number;
      username: string;
      firstName: string;
      lastName: string;
      active: boolean;
      role: string;
      birthDate: string;
      managerId: number;
      createdAt: string;
      roles: string[];
      permissions: string[];
    } | null) { // Allow null for logout
      this.id = userData?.id || null; // Use optional chaining and nullish coalescing
      this.username = userData?.username || null;
      this.firstName = userData?.firstName || null;
      this.lastName = userData?.lastName || null;
      this.active = userData?.active || null;
      this.role = userData?.role || null;
      this.birthDate = userData?.birthDate || null;
      this.managerId = userData?.managerId || null;
      this.createdAt = userData?.createdAt || null;
      this.roles = userData?.roles || [];
      this.permissions = userData?.permissions || [];
    },
    clearUser() { // Added clearUser for consistency
      this.id = null;
      this.username = null;
      this.firstName = null;
      this.lastName = null;
      this.role = null;
      this.birthDate = null;
      this.managerId = null;
      this.createdAt = null;
      this.roles = [];
      this.permissions = [];
    },
    initializeUser() {
      const storedUser = localStorage.getItem('user');
      if (storedUser) {
        try {
          const userData = JSON.parse(storedUser);
          this.setUser(userData); // Use setUser action
        } catch (error) {
          console.error('Failed to parse user data from localStorage', error);
          this.clearUser();
        }
      }
      // If no user data, do nothing.  The user is not logged in.
    },
    hasRole(roleName: string): boolean {
      return this.roles.includes(roleName);
    },

    async fetchEmployeeData() {
      try {
        const response = await api.get('/v1/employee')  // GET request to the /v1/employee endpoint
        this.user = response.data  // Store the fetched data into user state
      } catch (err) {
        console.error('Error fetching employee data:', err)  // Log error if the request fails
      }
    },

    hasPermission(permissionName: string): boolean {
      return this.permissions.includes(permissionName);
    },
  },
  getters: {
    user: (state) => ({ // Define a getter for the user object
      id: state.id,
      username: state.username,
      firstName: state.firstName,
      lastName: state.lastName,
      active: state.active,
      role: state.role,
      birthDate: state.birthDate,
      managerId: state.managerId,
      createdAt: state.createdAt,
      roles: state.roles,
      permissions: state.permissions
    }),
  },
  persist: { // Add the persist option here
    key: 'user', // Optional:  You can change this key
    storage: localStorage, // Optional:  Use sessionStorage if you prefer
    // Optional:  If you only want to persist certain state properties, specify them here.
    // paths: ['id', 'username', 'roles', 'permissions'], // Example: Only persist these
  },
});
