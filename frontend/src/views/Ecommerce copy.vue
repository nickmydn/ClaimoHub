<template>
  <AdminLayout>
    <div class="grid grid-cols-12 gap-4 md:gap-6">
      <div class="col-span-12 space-y-6 xl:col-span-7">
        <EcommerceMetrics :statistics="statistics" />
        <MonthlyTarget :statistics="statistics" />
      </div>
      <div class="col-span-12 xl:col-span-5">
        <MonthlySale :chart-data="transactionChart" />
      </div>

      <div class="col-span-12">
        <StatisticsChart :chart-data="transactionChart" />
      </div>

      <div class="col-span-12 xl:col-span-7">
        <RecentOrders :transactions="recentTransactions"/>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'

import AdminLayout from '../components/layout/AdminLayout.vue'
import EcommerceMetrics from '../components/ecommerce/EcommerceMetrics.vue'
import MonthlyTarget from '../components/ecommerce/MonthlyTarget.vue'
import MonthlySale from '../components/ecommerce/MonthlySale.vue'
import StatisticsChart from '../components/ecommerce/StatisticsChart.vue'
import RecentOrders from '../components/ecommerce/RecentOrders.vue'

import { getAdminDashboard } from '@/api/dashboard'

const loading = ref(true)
const errorMessage = ref('')

const statistics = ref({
  total_distributors: 0,
  total_merchants: 0,
  total_transactions: 0,
  total_voucher_stock: 0,
  total_rewards_claimed: 0,
})

const recentTransactions = ref([])

const transactionChart = ref([])

const loadDashboard = async () => {
  loading.value = true
  errorMessage.value = ''

  try {
    const response = await getAdminDashboard()

    const data = response.data.data

    statistics.value = data.statistics
    recentTransactions.value = data.recent_transactions
    transactionChart.value = data.transaction_chart
  } catch (error: any) {
    console.error('Dashboard error:', error)

    errorMessage.value =
      error.response?.data?.message ||
      'Failed to load dashboard data.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadDashboard()
})
</script>
