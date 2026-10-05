<template>
  <AdminDashboard v-if="authStore.role === 'admin'" />

  <CustomerDashboard v-else-if="authStore.role === 'customer'" />

  <div
    v-else
    class="flex items-center justify-center min-h-[400px]"
  >
    <p class="text-gray-500">
      Dashboard is not available for your role.
    </p>
  </div>
</template>

<script setup lang="ts">
import AdminDashboard from './AdminDashboard.vue'
import CustomerDashboard from './CustomerDashboard.vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
</script>
