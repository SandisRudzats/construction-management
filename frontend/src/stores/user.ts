// import { defineStore } from 'pinia'
//
// export interface User {
//   id: number | null | undefined
//   username: string | null
//   firstName: string | null
//   lastName: string | null
//   active: boolean | null
//   role: string | null
//   birthDate: string | null
//   managerId: number | null
//   createdAt: string | null
//   roles: string[]
//   permissions: string[]
// }
//
// export const useUserStore = defineStore('user', {
//   state: (): User => ({
//     id: null,
//     username: null,
//     firstName: null,
//     lastName: null,
//     active: null,
//     role: null,
//     birthDate: null,
//     managerId: null,
//     createdAt: null,
//     roles: [],
//     permissions: [],
//   }),
//   actions: {
//     setUser(
//       userData: {
//         id: number
//         username: string
//         firstName: string
//         lastName: string
//         active: boolean
//         role: string
//         birthDate: string
//         managerId: number
//         createdAt: string
//         roles: string[]
//         permissions: string[]
//       } | null,
//     ) {
//       this.id = userData?.id
//       this.username = userData?.username || null
//       this.firstName = userData?.firstName || null
//       this.lastName = userData?.lastName || null
//       this.active = userData?.active || null
//       this.role = userData?.role || null
//       this.birthDate = userData?.birthDate || null
//       this.managerId = userData?.managerId || null
//       this.createdAt = userData?.createdAt || null
//       this.roles = userData?.roles || []
//       this.permissions = userData?.permissions || []
//     },
//     clearUser() {
//       this.id = null
//       this.username = null
//       this.firstName = null
//       this.lastName = null
//       this.role = null
//       this.birthDate = null
//       this.managerId = null
//       this.createdAt = null
//       this.roles = []
//       this.permissions = []
//     },
//     initializeUser() {
//       const storedUser = localStorage.getItem('user')
//       if (storedUser) {
//         try {
//           const userData = JSON.parse(storedUser)
//           this.setUser(userData)
//         } catch (error) {
//           console.error('Failed to parse user data from localStorage', error)
//           this.clearUser()
//         }
//       }
//     },
//
//     hasPermission(permissionName: string): boolean {
//       return this.permissions.includes(permissionName)
//     },
//   },
//   getters: {
//     user: (state) => ({
//       // Define a getter for the user object
//       id: state.id,
//       username: state.username,
//       firstName: state.firstName,
//       lastName: state.lastName,
//       active: state.active,
//       role: state.role,
//       birthDate: state.birthDate,
//       managerId: state.managerId,
//       createdAt: state.createdAt,
//       roles: state.roles,
//       permissions: state.permissions,
//     }),
//   },
//   persist: {
//     key: 'user',
//     storage: localStorage,
//   },
// })

import { defineStore } from 'pinia'

export interface User {
  id: number | null | undefined
  username: string | null
  firstName: string | null
  lastName: string | null
  active: boolean | null
  role: string | null
  birthDate: string | null
  managerId: number | null
  createdAt: string | null
  roles: string[]
  permissions: string[]
}

export const useUserStore = defineStore('user', {
  state: (): User => ({
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
    setUser(userData: Partial<User> | null) {
      this.id = userData?.id ?? null
      this.username = userData?.username ?? null
      this.firstName = userData?.firstName ?? null
      this.lastName = userData?.lastName ?? null
      this.active = userData?.active ?? null
      this.role = userData?.role ?? null
      this.birthDate = userData?.birthDate ?? null
      this.managerId = userData?.managerId ?? null
      this.createdAt = userData?.createdAt ?? null
      this.roles = userData?.roles ?? []
      this.permissions = userData?.permissions ?? []
    },

    clearUser() {
      this.setUser(null)
    },

    initializeUser() {
      const storedUser = localStorage.getItem('user')
      if (storedUser) {
        try {
          this.setUser(JSON.parse(storedUser))
        } catch (error) {
          console.error('Failed to parse user data from localStorage', error)
          this.clearUser()
        }
      }
    },

    hasPermission(permissionName: string): boolean {
      return this.permissions.includes(permissionName)
    },
  },

  getters: {
    user: (state): User => ({
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
      permissions: state.permissions,
    }),
  },

  persist: {
    key: 'user',
    storage: localStorage,
  },
})

