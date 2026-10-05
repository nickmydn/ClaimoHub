<template>
  <AdminLayout>
    <div class="grid grid-cols-12 gap-4 md:gap-6">

      <div class="col-span-12">
        <div
          class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3"
        >
          <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
            Welcome to ClaimoHub
          </h2>

          <p class="mt-2 text-gray-500 dark:text-gray-400">
            Manage your transactions and claim available rewards.
          </p>
        </div>
      </div>

      <div class="col-span-12 md:col-span-4">
        <div
          class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3"
        >
          <p class="text-sm text-gray-500 dark:text-gray-400">
            My Transactions
          </p>

          <h3 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white">
            0
          </h3>
        </div>
      </div>

      <div class="col-span-12 md:col-span-4">
        <div
          class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3"
        >
          <p class="text-sm text-gray-500 dark:text-gray-400">
            Rewards Claimed
          </p>

          <h3 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white">
            0
          </h3>
        </div>
      </div>

      <div class="col-span-12 md:col-span-4">
        <div
          class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3"
        >
          <p class="text-sm text-gray-500 dark:text-gray-400">
            Available Rewards
          </p>

          <h3 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white">
            0
          </h3>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import AdminLayout from '../components/layout/AdminLayout.vue'
</script>
