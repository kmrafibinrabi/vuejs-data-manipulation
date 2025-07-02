<template>
  <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Share Your Bold Idea</h2>

    <form @submit.prevent="generateOutcome">
      <textarea
        v-model="idea"
        class="w-full border p-3 rounded"
        rows="4"
        placeholder="Type your future vision..."
      ></textarea>
      <button
        :disabled="loading"
        class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
      >
        {{ loading ? 'Thinking...' : 'Generate Future' }}
      </button>
    </form>

    <div v-if="result" class="mt-6 p-4 bg-gray-100 border rounded">
      <h3 class="font-semibold text-gray-700 mb-2">100% Determined Outcome:</h3>
      <p class="text-gray-800 whitespace-pre-line">{{ result }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const idea = ref('')
const result = ref('')
const loading = ref(false)

const API_KEY = import.meta.env.VITE_GOOGLE_API_KEY
const ENDPOINT =
  'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent'

const generateOutcome = async () => {
  if (!idea.value.trim()) return
  result.value = ''
  loading.value = true

  try {
    const payload = {
      contents: [
        {
          parts: [
            {
              text: `You are a future-predicting assistant. For the idea: "${idea.value}", respond with a single confident and deterministic future outcome. Never give multiple options.`
            }
          ]
        }
      ]
    }

    const response = await axios.post(ENDPOINT, payload, {
      headers: {
        'Content-Type': 'application/json',
        'X-goog-api-key': API_KEY
      }
    })

    console.log('Full API response:', response.data)

    const candidate = response.data.candidates?.[0]

    console.log('Candidate content:', candidate?.content)

    let outputText = ''

    if (
      candidate &&
      candidate.content &&
      Array.isArray(candidate.content.parts) &&
      candidate.content.parts.length > 0
    ) {
      // Join all parts' text (usually just one part)
      outputText = candidate.content.parts.map(p => p.text).join(' ')
    }

    if (outputText) {
      result.value = outputText.trim()
    } else {
      result.value = 'No prediction received.'
      console.warn('Could not extract text from candidate content parts:', candidate.content)
    }
  } catch (err) {
    console.error(err)
    result.value = '❌ Something went wrong while generating the prediction.'
  } finally {
    loading.value = false
  }
}
</script>



