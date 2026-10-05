<template>
  <div
    class="rounded-2xl border border-gray-200 bg-gray-100 dark:border-gray-800 dark:bg-white/[0.03]"
  >
    <div
      class="px-5 pt-5 bg-white shadow-default rounded-2xl pb-11 dark:bg-gray-900 sm:px-6 sm:pt-6"
    >
      <div class="flex justify-between">
        <div>
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
            Reward Progress
          </h3>

          <p class="mt-1 text-gray-500 text-theme-sm dark:text-gray-400">
            Overview of claimed rewards
          </p>
        </div>
      </div>

      <div class="relative max-h-[195px]">
        <div id="chartTwo" class="h-full">
          <div class="radial-bar-chart">
            <VueApexCharts
              type="radialBar"
              height="330"
              :options="chartOptions"
              :series="series"
            />
          </div>
        </div>

        <span
          class="absolute left-1/2 top-[85%] -translate-x-1/2 -translate-y-[85%] rounded-full bg-success-50 px-3 py-1 text-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500"
        >
          {{ progress }}%
        </span>
      </div>

      <p
        class="mx-auto mt-1.5 w-full max-w-[380px] text-center text-sm text-gray-500 sm:text-base"
      >
        {{ statistics.total_rewards_claimed }}
        rewards have been claimed successfully.
      </p>
    </div>

    <div class="flex items-center justify-center gap-5 px-6 py-3.5 sm:gap-8 sm:py-5">
      <div>
        <p class="mb-1 text-center text-gray-500 text-theme-xs dark:text-gray-400 sm:text-sm">
          Rewards
        </p>

        <p
          class="flex items-center justify-center text-base font-semibold text-gray-800 dark:text-white/90 sm:text-lg"
        >
          {{ statistics.total_rewards_claimed }}
        </p>
      </div>

      <div class="w-px bg-gray-200 h-7 dark:bg-gray-800"></div>

      <div>
        <p class="mb-1 text-center text-gray-500 text-theme-xs dark:text-gray-400 sm:text-sm">
          Transactions
        </p>

        <p
          class="flex items-center justify-center text-base font-semibold text-gray-800 dark:text-white/90 sm:text-lg"
        >
          {{ statistics.total_transactions }}
        </p>
      </div>

      <div class="w-px bg-gray-200 h-7 dark:bg-gray-800"></div>

      <div>
        <p class="mb-1 text-center text-gray-500 text-theme-xs dark:text-gray-400 sm:text-sm">
          Voucher Stock
        </p>

        <p
          class="flex items-center justify-center text-base font-semibold text-gray-800 dark:text-white/90 sm:text-lg"
        >
          {{ statistics.total_voucher_stock }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import VueApexCharts from 'vue3-apexcharts'

interface Statistics {
  total_distributors: number
  total_merchants: number
  total_transactions: number
  total_voucher_stock: number
  total_rewards_claimed: number
}

const props = defineProps<{
  statistics: Statistics
}>()

const progress = computed(() => {
  if (props.statistics.total_transactions === 0) {
    return 0
  }

  return Math.min(
    Math.round(
      (props.statistics.total_rewards_claimed /
        props.statistics.total_transactions) *
        100,
    ),
    100,
  )
})

const series = computed(() => [progress.value])

const chartOptions = {
  colors: ['#465FFF'],

  chart: {
    fontFamily: 'Outfit, sans-serif',
    sparkline: {
      enabled: true,
    },
  },

  plotOptions: {
    radialBar: {
      startAngle: -90,
      endAngle: 90,

      hollow: {
        size: '80%',
      },

      track: {
        background: '#E4E7EC',
        strokeWidth: '100%',
        margin: 5,
      },

      dataLabels: {
        name: {
          show: false,
        },

        value: {
          fontSize: '36px',
          fontWeight: '600',
          offsetY: 60,
          color: '#1D2939',

          formatter: function (val: number) {
            return val.toFixed(0) + '%'
          },
        },
      },
    },
  },

  fill: {
    type: 'solid',
    colors: ['#465FFF'],
  },

  stroke: {
    lineCap: 'round' as const,
  },

  labels: ['Reward Progress'],
}
</script>

<style scoped>
.radial-bar-chart {
  width: 100%;
  max-width: 330px;
  margin: 0 auto;
}
</style>
