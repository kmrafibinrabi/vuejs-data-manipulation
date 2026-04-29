<template>
  <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <div
      v-for="(note, index) in notes"
      :key="note.id || index"
      class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition"
      :style="{ borderLeft: `4px solid ${note.color || '#FFD700'}` }"
    >
      <div class="flex justify-between items-start mb-2">
        <div>
          <h3 class="text-lg font-semibold text-gray-900">
            {{ note.title }}
          </h3>
          <p v-if="note.category" class="text-xs text-gray-500">
            {{ note.category }}
          </p>
        </div>
        <button
          @click="$emit('delete-note', index)"
          class="text-red-500 text-sm hover:underline ml-4"
          title="Delete"
        >
          ✖
        </button>
      </div>
      <p class="text-gray-700 text-sm">
        {{ note.content }}
      </p>
      <p v-if="note.created_at" class="text-xs text-gray-400 mt-2">
        {{ formatDate(note.created_at) }}
      </p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  notes: {
    type: Array,
    required: true
  }
})
defineEmits(['delete-note'])

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}
</script>
