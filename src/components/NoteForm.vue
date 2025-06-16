<script setup>
import { ref } from 'vue';

const title = ref('');
const description = ref('');

const emit = defineEmits(['add-note']);

function handlesubmit(){
    if (title.value.trim() === '' || description.value.trim() === '') {
        alert('Please fill in both fields.');
        return;
        
    }

    emit('add-note', {
        title: title.value,
        description: description.value
    });

    title.value = ''
  description.value = ''
}
</script>


<template>
  <form @submit.prevent="handleSubmit">
    <div class="mb-2">
      <label class="block text-sm font-medium text-gray-700">Title</label>
      <input
        v-model="title"
        type="text"
        placeholder="Enter note title"
        class="w-full px-3 py-2 border rounded"
      />
    </div>
    <div class="mb-2">
      <label class="block text-sm font-medium text-gray-700">Description</label>
      <textarea
        v-model="description"
        placeholder="Write your note here"
        class="w-full px-3 py-2 border rounded"
      ></textarea>
    </div>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
      Add Note
    </button>
  </form>
</template>