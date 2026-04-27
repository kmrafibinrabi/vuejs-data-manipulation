import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    isLoggedIn: false
  }),
  actions: {
    async login(email, password) {
      try {
        const response = await axios.get(`http://localhost:3000/users?email=${email}`)

        const user = response.data[0]

        if (user && user.password === password) {
          this.user = user
          this.isLoggedIn = true
          return true
        } else {
          return false
        }
      } catch (error) {
        console.error('Login error:', error)
        return false
      }
    },
    logout() {
      this.user = null
      this.isLoggedIn = false
    }
  }
})
