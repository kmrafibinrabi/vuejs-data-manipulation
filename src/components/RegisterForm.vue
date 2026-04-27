<template>
  <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Register</h2>

    <form @submit.prevent="handleRegister">
      <input v-model="name" type="text" placeholder="Name" class="input mb-2" />
      <input v-model="email" type="email" placeholder="Email" class="input mb-2" />
      <input v-model="password" type="password" placeholder="Password" class="input mb-4" />
      <button class="bg-green-600 text-white  ">Register</button>

      <p v-if="error" class="text-red-500 text-sm mt-2">{{ error }}</p>
      <p class="text-sm mt-4">
        Already have an account?
        <span @click="goToLogin" class="text-blue-600 underline cursor-pointer">Login here</span>
      </p>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const name = ref('')
const email = ref('')
const password = ref('')
const error = ref('')
const emit = defineEmits(['switch-to-login'])

const handleRegister = async () => {
  if (!name.value || !email.value || !password.value) {
    error.value = 'All fields are required'
    return
  }

  try {
    // Check if user already exists
    const res = await axios.get(`http://localhost:3000/users?email=${email.value}`)
    if (res.data.length > 0) {
      error.value = 'Email already exists'
      return
    }

    // Register new user
    await axios.post('http://localhost:3000/users', {
      name: name.value,
      email: email.value,
      password: password.value
    })

    // Redirect to login
    emit('switch-to-login')
  } catch (err) {
    error.value = 'Something went wrong!'
  }
}

const goToLogin = () => {
  emit('switch-to-login')
}
</script>

<style scoped>
/* .input {
  @apply w-full   border rounded focus:outline-none focus:ring;
} */
</style>