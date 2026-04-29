<template>
  <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">💡 Share Your Bold Idea</h2>

    <form @submit.prevent="generateOutcome" class="space-y-4">
      <textarea
        v-model="idea"
        class="w-full border p-3 rounded focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
        rows="4"
        placeholder="Type your future vision..."
      ></textarea>
      
      <button
        :disabled="loading"
        class="w-full bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
      >
        {{ loading ? 'Thinking...' : 'Generate Future' }}
      </button>
    </form>

    <!-- Result in landscape frame like Image Research module -->
    <div v-if="result" class="mt-6">
      <h3 class="font-semibold text-gray-700 mb-2">100% Determined Outcome:</h3>
      <div class="bg-gray-100 border rounded-lg overflow-hidden">
        <div class="p-4 min-h-[200px] max-h-[300px] overflow-y-auto">
          <div class="prose prose-sm max-w-none">
            <p class="text-gray-800 whitespace-pre-line break-words">{{ result }}</p>
          </div>
        </div>
      </div>

      <!-- Action buttons -->
      <div class="mt-4 flex gap-3">
        <button
          @click="generateOutcome"
          :disabled="loading"
          class="flex-1 px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          {{ loading ? 'Regenerating...' : 'Regenerate' }}
        </button>

        <button
          @click="clearFields"
          class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition-colors"
        >
          Clear
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const idea = ref('')
const result = ref('')
const loading = ref(false)

const generateOutcome = async () => {
  if (!idea.value.trim()) return
  result.value = ''
  loading.value = true

  try {
    const response = await axios.post('/api/future-ideas/generate', {
  idea: idea.value
})
    
    result.value = response.data.result || 'No prediction received.'
  } catch (err) {
    console.error(err)
    result.value = '❌ Something went wrong while generating the prediction.'
  } finally {
    loading.value = false
  }
}

const clearFields = () => {
  idea.value = ''
  result.value = ''
  loading.value = false
}
</script>

<style scoped>
/* Same landscape optimization as Image Research module */
.prose {
  max-width: 100%;
}

.prose p {
  margin-top: 0;
  margin-bottom: 0;
}

/* Custom scrollbar for better UX */
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