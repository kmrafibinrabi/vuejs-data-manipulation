<template>
  <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">🧠 Image Research Module</h2>

    <form @submit.prevent="handleSubmit" class="space-y-4">
      <input type="file" accept="image/*" @change="handleFile" class="block w-full text-sm" />
      
      <textarea
        v-model="question"
        maxlength="200"
        rows="3"
        placeholder="Ask a question about the image..."
        class="w-full border p-3 rounded"
      ></textarea>
      <div class="text-right text-xs text-gray-500">{{ question.length }}/200 characters</div>

      <button
        type="submit"
        :disabled="loading || !imageFile || !question"
        class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700"
      >
        {{ loading ? 'Analyzing...' : 'Ask AI' }}
      </button>
    </form>

    <div v-if="result" class="mt-6 p-4 bg-gray-100 border rounded">
      <h3 class="font-semibold text-gray-700 mb-2">🧠 AI Feedback:</h3>
      <p class="text-gray-800 whitespace-pre-line">{{ result }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const imageFile = ref(null)
const question = ref('')
const result = ref('')
const loading = ref(false)

const handleFile = (e) => {
  imageFile.value = e.target.files[0]
}

const handleSubmit = async () => {
  if (!imageFile.value || !question.value) return

  loading.value = true
  result.value = ''

  const formData = new FormData()
  formData.append('image', imageFile.value)
  formData.append('question', question.value)

  try {
    const res = await axios.post('http://127.0.0.1:8000/api/image-research', formData)
    result.value = res.data.answer
  } catch (err) {
    console.error(err)
    result.value = '❌ Something went wrong.'
  } finally {
    loading.value = false
  }
}
</script>
