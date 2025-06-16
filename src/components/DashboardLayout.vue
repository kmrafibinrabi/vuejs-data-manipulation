<!-- src/components/DashboardLayout.vue -->
<template>
  <div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-900 text-white flex flex-col">
      <div class="h-16 flex items-center justify-center text-lg font-bold">
        <svg class="h-6 w-6 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2"
          viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M3 7l6 6-6 6M9 7l6 6-6 6" />
        </svg>
      </div>

      <nav class="flex-1 px-4">
        <ul class="space-y-2">
          <li><a href="#" class="flex items-center p-2 bg-gray-800 rounded">🏠 Dashboard</a></li>
          <li><a href="#" class="flex items-center p-2 hover:bg-gray-800 rounded">👥 Team</a></li>
          <li><a href="#" class="flex items-center p-2 hover:bg-gray-800 rounded">📁 Projects</a></li>
          <li><a href="#" class="flex items-center p-2 hover:bg-gray-800 rounded">📅 Calendar</a></li>
          <li><a href="#" class="flex items-center p-2 hover:bg-gray-800 rounded">📄 Documents</a></li>
          <li><a href="#" class="flex items-center p-2 hover:bg-gray-800 rounded">📊 Reports</a></li>
        </ul>

        <div class="mt-6 text-xs text-gray-400 uppercase">Your Teams</div>
        <ul class="mt-2 space-y-2">
          <li><a href="#" class="flex items-center p-2 hover:bg-gray-800 rounded"><span
                class="mr-3 w-6 h-6 flex items-center justify-center bg-gray-700 rounded-full">H</span>
              Heroicons</a></li>
          <li><a href="#" class="flex items-center p-2 hover:bg-gray-800 rounded"><span
                class="mr-3 w-6 h-6 flex items-center justify-center bg-gray-700 rounded-full">T</span>
              Tailwind Labs</a></li>
          <li><a href="#" class="flex items-center p-2 hover:bg-gray-800 rounded"><span
                class="mr-3 w-6 h-6 flex items-center justify-center bg-gray-700 rounded-full">W</span>
              Workcation</a></li>
        </ul>
      </nav>

      <div class="p-4 mt-auto">
        <a href="#" class="flex items-center text-sm hover:text-gray-300">
          ⚙️ Settings
        </a>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <!-- Header -->
      <header class="h-16 bg-white border-b flex items-center justify-between px-6">
        <input type="text" placeholder="Search"
          class="w-1/3 px-4 py-2 border rounded focus:outline-none focus:ring" />

        <div class="flex items-center space-x-4">
          <button class="text-gray-500 hover:text-gray-700">🔔</button>
          <div class="flex items-center">
            <img src="https://i.pravatar.cc/32" class="w-8 h-8 rounded-full" />
            <span class="ml-2 text-sm font-medium text-gray-700">Tom Cook ▾</span>
          </div>
        </div>
      </header>

      <!-- Hero Slot -->
      <main class="p-6 flex-1 bg-gray-100">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
// No logic needed here right now
</script>
