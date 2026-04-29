import { defineStore } from 'pinia'
import axios from 'axios'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    isLoggedIn: false,
    token: localStorage.getItem('auth_token') || null
  }),

  actions: {
    async register(name, email, password) {
      try {
        const response = await axios.post(`${API_URL}/register`, {
          name,
          email,
          password
        })
        
        return true
      } catch (error) {
        console.error('Registration error:', error.response?.data || error)
        throw error
      }
    },

    async login(email, password) {
      try {
        const response = await axios.post(`${API_URL}/login`, {
          email,
          password
        })

        const { user, token } = response.data
        
        this.user = user
        this.token = token
        this.isLoggedIn = true
        
        // Save token to localStorage
        localStorage.setItem('auth_token', token)
        
        // Set default auth header for future requests
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        return true
      } catch (error) {
        console.error('Login error:', error.response?.data || error)
        return false
      }
    },

    async logout() {
      try {
        // Call logout endpoint if token exists
        if (this.token) {
          axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
          await axios.post(`${API_URL}/logout`)
        }
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.user = null
        this.isLoggedIn = false
        this.token = null
        localStorage.removeItem('auth_token')
        delete axios.defaults.headers.common['Authorization']
      }
    },

    // Initialize auth from stored token
    initializeAuth() {
      if (this.token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
      }
    }
  }
})
