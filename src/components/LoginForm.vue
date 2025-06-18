<template>
  <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Login</h2>
    <form @submit.prevent="handleLogin">
      <input v-model="username" placeholder="Username" class="input mb-2" />
      <input v-model="password" type="password" placeholder="Password" class="input mb-4" />

      <!-- Error message -->
      <p v-if="error" class="text-red-500 text-sm mt-2">{{ error }}</p>

      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Login</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const username = ref('')
const password = ref('')
const error = ref('')

const handleLogin = async () => {
  error.value = ''

  if (!username.value) {
    error.value = 'Username is required'
    return
  }

  if (!password.value) {
    error.value = 'Password is required'
    return
  }

  const success = await auth.login(username.value, password.value)

  if (!success) {
    error.value = 'Invalid credentials'
  }
}
</script>
