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
    <div class="max-w-md mx-auto mt-8 bg-white shadow-lg rounded-lg p-6">
        <form @submit.prevent="handlesubmit">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input
                    v-model="title"
                    type="text"
                    placeholder="Enter note title"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                />
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea
                    v-model="description"
                    placeholder="Write your note here"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                ></textarea>
            </div>
            <button
                type="submit"
                class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors"
            >
                Add Note
            </button>
        </form>
    </div>
</template>