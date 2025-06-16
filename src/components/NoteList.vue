<template>
  <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <div
      v-for="(note, index) in notes"
      :key="index"
      class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition"
    >
      <div class="flex justify-between items-start mb-2">
        <h3 class="text-lg font-semibold text-gray-900">
          {{ note.title }}
        </h3>
        <button
          @click="$emit('delete-note', index)"
          class="text-red-500 text-sm hover:underline ml-4"
          title="Delete"
        >
          ✖
        </button>
      </div>
      <p class="text-gray-700 text-sm">
        {{ note.description }}
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
</script>
