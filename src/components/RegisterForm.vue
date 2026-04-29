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
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
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
    await auth.register(name.value, email.value, password.value)
    // Registration successful, switch to login
    emit('switch-to-login')
  } catch (err) {
    const errorMessage = err.response?.data?.message || 
                        err.response?.data?.errors?.email?.[0] || 
                        'Something went wrong!'
    error.value = errorMessage
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