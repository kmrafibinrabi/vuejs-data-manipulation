<template>
  <DashboardLayout>
    <NoteForm @add-note="addNote" />
    <NoteList :notes="notes" @delete-note="deleteNote" />
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from './components/DashboardLayout.vue'
import NoteForm from './components/NoteForm.vue'
import NoteList from './components/NoteList.vue'
import { ref, onMounted, watch } from 'vue'

// 1. reactive notes array
const notes = ref([])

// 2. on page load, fetch from localStorage
onMounted(() => {
  const savedNotes = localStorage.getItem('notes')
  if (savedNotes) {
    notes.value = JSON.parse(savedNotes)
  }
})

// 3. add new note to the array
function addNote(note) {
  notes.value.push(note)
}

function deleteNote(index) {
  notes.value.splice(index, 1)
}

// 4. watch notes & save to localStorage whenever it changes
watch(notes, (newVal) => {
  localStorage.setItem('notes', JSON.stringify(newVal))
}, { deep: true })

</script>

