<template>
  <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">🧠 Image Research Module</h2>

    <form @submit.prevent="handleSubmit" class="space-y-4">
      <!-- Custom styled file upload -->
      <div class="relative">
        <input
          type="file"
          id="imageUpload"
          accept="image/*"
          @change="handleFile"
          class="hidden"
        />
        <label
          for="imageUpload"
          class="flex items-center justify-between w-full px-4 py-3 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-indigo-500 hover:bg-gray-100 transition-all duration-200 group"
        >
          <div class="flex items-center">
            <svg class="w-6 h-6 mr-2 text-gray-500 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span class="text-gray-700 group-hover:text-indigo-600 transition-colors">
              {{ imageFile ? imageFile.name : 'Choose an image' }}
            </span>
          </div>
          <span class="text-xs text-gray-400 group-hover:text-indigo-400 transition-colors">
            JPG, PNG, GIF, WEBP
          </span>
        </label>
      </div>

      <!-- Image preview -->
      <div v-if="imagePreview" class="relative border rounded-lg overflow-hidden bg-gray-50">
        <img :src="imagePreview" alt="Preview" class="w-full h-40 object-contain" />
        <button
          @click="removeImage"
          type="button"
          class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      
      <textarea
        v-model="question"
        maxlength="200"
        rows="3"
        placeholder="Ask a question about the image..."
        class="w-full border p-3 rounded focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
      ></textarea>
      <div class="text-right text-xs text-gray-500">{{ question.length }}/200 characters</div>

      <button
        type="submit"
        :disabled="loading || !imageFile || !question.trim()"
        class="w-full bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
      >
        {{ loading ? 'Analyzing...' : 'Ask AI' }}
      </button>
    </form>

    <!-- Result section -->
    <div v-if="result" class="mt-6">
      <h3 class="font-semibold text-gray-700 mb-2">🧠 AI Feedback:</h3>
      <div class="bg-gray-100 border rounded-lg overflow-hidden">
        <div class="p-4 min-h-[200px] max-h-[300px] overflow-y-auto">
          <div class="prose prose-sm max-w-none">
            <p class="text-gray-800 whitespace-pre-line break-words">{{ result }}</p>
          </div>
        </div>
      </div>
    </div>

    
    <!-- <div v-if="debug" class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded text-xs">
      <strong>Debug:</strong> Question: "{{ question }}" | Has file: {{ !!imageFile }}
    </div> -->
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const imageFile = ref(null)
const imagePreview = ref(null)
const question = ref('what is this')  // ← Set default value
const result = ref('')
const loading = ref(false)
  // Set to false in production

const handleFile = (e) => {
  const file = e.target.files[0]
  if (file) {
    console.log('File selected:', file.name, file.type, file.size)
    imageFile.value = file
    if (imagePreview.value) {
      URL.revokeObjectURL(imagePreview.value)
    }
    imagePreview.value = URL.createObjectURL(file)
  }
}

const removeImage = () => {
  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value)
  }
  imageFile.value = null
  imagePreview.value = null
  const fileInput = document.getElementById('imageUpload')
  if (fileInput) fileInput.value = ''
  // Reset question if needed
  // question.value = ''
}

const handleSubmit = async () => {
  // Trim question to avoid empty strings with spaces
  const trimmedQuestion = question.value.trim()
  
  if (!imageFile.value) {
    console.log('No file selected')
    result.value = 'Please select an image first.'
    return
  }
  
  if (!trimmedQuestion) {
    console.log('No question entered')
    result.value = 'Please enter a question about the image.'
    return
  }

  loading.value = true
  result.value = ''

  const formData = new FormData()
  formData.append('image', imageFile.value, imageFile.value.name)
  formData.append('question', trimmedQuestion)  // Use trimmed question

  try {
    console.log('Sending file:', imageFile.value.name)
    console.log('Question:', trimmedQuestion)
    
    // Log FormData contents for debugging
    for (let pair of formData.entries()) {
      console.log(pair[0], pair[1]);
    }
    
    const res = await axios.post('/api/image-research', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    
    console.log('Response:', res.data)
    
    if (res.data.success) {
      result.value = res.data.answer
    } else {
      result.value = '❌ Failed to analyze image: ' + (res.data.message || 'Unknown error')
    }
  } catch (err) {
    console.error('Error details:', err.response?.data || err.message)
    
    // Better error message
    if (err.response?.data?.errors) {
      const errors = err.response.data.errors
      result.value = '❌ Validation error: ' + JSON.stringify(errors)
    } else if (err.response?.data?.error) {
      result.value = '❌ Error: ' + err.response.data.error
    } else if (err.response?.data?.message) {
      result.value = '❌ Error: ' + err.response.data.message
    } else {
      result.value = '❌ Something went wrong. Please try again.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.prose {
  max-width: 100%;
}

.prose p {
  margin-top: 0;
  margin-bottom: 0;
}

.overflow-y-auto::-webkit-scrollbar {
  width: 8px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #e5e7eb;
  border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #9ca3af;
  border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
}
</style>