<!-- src/components/Calendar.vue -->
<template>
  <div class="bg-white p-6 rounded shadow-md">
    <h2 class="text-xl font-semibold mb-4">📅 June 2025</h2>

    <div class="grid grid-cols-7 gap-2 text-center font-medium text-gray-700">
      <div>Sun</div>
      <div>Mon</div>
      <div>Tue</div>
      <div>Wed</div>
      <div>Thu</div>
      <div>Fri</div>
      <div>Sat</div>
    </div>

    <div class="grid grid-cols-7 gap-2 mt-2 text-center">
      <!-- Starting blanks (June 1 is Sunday, so 0 blanks) -->
      <template v-for="blank in startBlankDays" :key="'b'+blank">
        <div></div>
      </template>

      <!-- Dates -->
      <template v-for="date in totalDays" :key="date">
        <div class="py-2 rounded hover:bg-blue-100 cursor-pointer">
          {{ date }}
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
const totalDays = 30  // June has 30 days
const startBlankDays = 6 // June 1, 2025 is Sunday (6 blanks before)
</script>
