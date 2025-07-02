<!-- src/components/DashboardLayout.vue -->
<template>
  <div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-900 text-white flex flex-col">
      <div class="h-16 flex items-center justify-center text-lg font-bold">
        <svg class="h-6 w-6 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 7l6 6-6 6M9 7l6 6-6 6" />
        </svg>
      </div>

      <nav class="flex-1 px-4">
        <ul class="space-y-2">
          <li @click="activeModule = 'tasks'" :class="linkClass('tasks')">🏠 Task List</li>
          <li @click="activeModule = 'calendar'" :class="linkClass('calendar')">📅 Time and Date</li>
          <li @click="activeModule = 'products'" :class="linkClass('products')">🛒 Products</li>
          <li
  @click="activeModule = 'ideas'"
  :class="linkClass('ideas')"
>
  🧠 Future Ideas
</li>

        </ul>
      </nav>

      <div class="p-4 mt-auto">
        <a href="#" class="flex items-center text-sm hover:text-gray-300">
          ⚙️ Settings
        </a>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <!-- Header -->
      <header class="h-16 bg-white border-b flex items-center justify-between px-6">
        <input type="text" placeholder="Search" class="w-1/3 px-4 py-2 border rounded focus:outline-none focus:ring" />

        <div class="flex items-center space-x-4">
          <button class="text-gray-500 hover:text-gray-700">🔔</button>
          <div class="flex items-center">
            <img src="/rafi-01.png" class="w-8 h-8 rounded-full" />
            <span class="ml-2 text-sm font-medium text-gray-700">{{ auth.user.name }}</span>
          </div>
          <button @click="auth.logout()" class="text-sm text-red-600 hover:underline">Logout</button>
        </div>
      </header>

      <!-- Hero Area -->
      <main class="p-6 flex-1 bg-gray-100">
        <NoteForm v-if="activeModule === 'tasks'" @add-note="addNote" />
        <NoteList v-if="activeModule === 'tasks'" :notes="notes" @delete-note="deleteNote" />
        <Calendar v-if="activeModule === 'calendar'" />
        <ProductList v-if="activeModule === 'products'" />
        <FutureIdeas v-if="activeModule === 'ideas'" />
        
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import NoteForm from './NoteForm.vue'
import NoteList from './NoteList.vue'
import Calendar from './Calendar.vue'
import ProductList from './ProductList.vue'
import { useAuthStore } from '../stores/auth'
import FutureIdeas from './FutureIdeas.vue'

const auth = useAuthStore()
const activeModule = ref('tasks')
const notes = ref([])

function addNote(note) {
  notes.value.push(note)
}

function deleteNote(index) {
  notes.value.splice(index, 1)
}

function linkClass(module) {
  return [
    'cursor-pointer px-4 py-2 block rounded transition',
    activeModule.value === module
      ? 'bg-gray-800 text-white'
      : 'text-gray-300 hover:bg-gray-700 hover:text-white'
  ]
}
</script>
