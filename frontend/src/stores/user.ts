import { defineStore } from 'pinia';

interface UserState {
  id: number | null;
  username: string | null;
  firstName: string | null;
  lastName: string | null;
  role: string | null;
  roles: string[];
  permissions: string[];
}

export const useUserStore = defineStore('user', {
  state: (): UserState => ({
    id: null,
    username: null,
    firstName: null,
    lastName: null,
    role: null,
    roles: [],
    permissions: [],
  }),
  actions: {
    setUser(userData: {
      id: number;
      username: string;
      firstName: string;
      lastName: string;
      role: string;
      roles: string[];
      permissions: string[];
    } | null) { // Allow null for logout
      this.id = userData?.id || null; // Use optional chaining and nullish coalescing
      this.username = userData?.username || null;
      this.firstName = userData?.firstName || null;
      this.lastName = userData?.lastName || null;
      this.role = userData?.role || null;
      this.roles = userData?.roles || [];
      this.permissions = userData?.permissions || [];
    },
    clearUser() { // Added clearUser for consistency
      this.id = null;
      this.username = null;
      this.firstName = null;
      this.lastName = null;
      this.role = null;
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
      role: state.role,
      roles: state.roles,
      permissions: state.permissions
    }),
  }
});

