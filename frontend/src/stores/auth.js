import { defineStore } from 'pinia'
import * as authApi from '../api/auth'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('token'),
    user: JSON.parse(localStorage.getItem('user') || 'null'),
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,

    role: (state) => state.user?.role_user || null,
  },

  actions: {
    async login(credentials) {
      const response = await authApi.login(credentials)

      const data = response.data.data

      this.token = data.token
      this.user = data.user

      localStorage.setItem('token', data.token)
      localStorage.setItem(
        'user',
        JSON.stringify(data.user),
      )

      return data
    },

    async getMe() {
      const response = await authApi.getMe()

      this.user = response.data.data

      localStorage.setItem(
        'user',
        JSON.stringify(this.user),
      )

      return this.user
    },

    async logout() {
      try {
        if (this.token) {
          await authApi.logout()
        }
      } finally {
        this.token = null
        this.user = null

        localStorage.removeItem('token')
        localStorage.removeItem('user')
      }
    },
  },
})
