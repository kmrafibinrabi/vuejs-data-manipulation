import axios from 'axios'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json'
  }
})

// Add token to all requests if it exists
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export const notesApi = {
  getAllNotes() {
    return api.get('/notes')
  },
  getNote(id) {
    return api.get(`/notes/${id}`)
  },
  createNote(noteData) {
    return api.post('/notes', noteData)
  },
  updateNote(id, noteData) {
    return api.put(`/notes/${id}`, noteData)
  },
  deleteNote(id) {
    return api.delete(`/notes/${id}`)
  },
  searchNotes(query) {
    return api.post('/notes/search', { query })
  }
}

export default api
