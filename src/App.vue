<template>

  <LoginForm
    v-if="!auth.isLoggedIn && showForm === 'login'"
    @switch-to-register="showForm = 'register'"
  />
  <RegisterForm
    v-if="!auth.isLoggedIn && showForm === 'register'"
    @switch-to-login="showForm = 'login'"
  />
  <DashboardLayout v-if="auth.isLoggedIn">
    <NoteForm @add-note="addNote" />
    <NoteList :notes="notes" @delete-note="deleteNote" />
  </DashboardLayout>
  <!-- <LoginForm v-else /> -->
</template>

<script setup>
import DashboardLayout from './components/DashboardLayout.vue'
import NoteForm from './components/NoteForm.vue'
import NoteList from './components/NoteList.vue'
import { ref, onMounted, watch } from 'vue'
import { useAuthStore } from './stores/auth'
import LoginForm from './components/LoginForm.vue'
import RegisterForm from './components/RegisterForm.vue'
import { notesApi } from './services/api'

const auth = useAuthStore()
const showForm = ref('login')

// 1. reactive notes array
const notes = ref([])
const loading = ref(false)
const error = ref(null)

// 2. on page load, fetch notes from API
onMounted(async () => {
  if (auth.isLoggedIn) {
    await fetchNotes()
  }
})

// Fetch notes from Laravel API
async function fetchNotes() {
  loading.value = true
  error.value = null
  try {
    const response = await notesApi.getAllNotes()
    notes.value = response.data
  } catch (err) {
    error.value = 'Failed to fetch notes'
    console.error('Fetch error:', err)
    // Fallback to localStorage if API fails
    const savedNotes = localStorage.getItem('notes')
    if (savedNotes) {
      notes.value = JSON.parse(savedNotes)
    }
  } finally {
    loading.value = false
  }
}

// 3. add new note through API
async function addNote(note) {
  try {
    const response = await notesApi.createNote(note)
    notes.value.push(response.data)
  } catch (err) {
    error.value = 'Failed to add note'
    console.error('Add error:', err)
  }
}

async function deleteNote(index) {
  const note = notes.value[index]
  if (!note) return

  try {
    await notesApi.deleteNote(note.id)
    notes.value.splice(index, 1)
  } catch (err) {
    error.value = 'Failed to delete note'
    console.error('Delete error:', err)
  }
}

// Watch for auth changes and reload notes
watch(() => auth.isLoggedIn, (newVal) => {
  if (newVal) {
    fetchNotes()
  }
})
</script>

