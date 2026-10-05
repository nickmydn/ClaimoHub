<template>
  <div
    class="rounded-2xl border border-gray-200 bg-white px-5 pb-5 pt-5 dark:border-gray-800 dark:bg-white/3 sm:px-6 sm:pt-6"
  >
    <div class="flex flex-col gap-5 mb-6 sm:flex-row sm:justify-between">
      <div class="w-full">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
          Transaction Statistics
        </h3>

        <p class="mt-1 text-gray-500 text-theme-sm dark:text-gray-400">
          Transactions and transaction amount for the last 7 days
        </p>
      </div>
    </div>

    <div class="max-w-full overflow-x-auto custom-scrollbar">
      <div id="chartThree" class="-ms-4 min-w-[700px] ps-2">
        <VueApexCharts
          type="area"
          height="310"
          :options="chartOptions"
          :series="series"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import VueApexCharts from 'vue3-apexcharts'
import type { ApexOptions } from 'apexcharts'

interface ChartData {
  date: string
  total_transactions: number
  total_amount: number
}

const props = defineProps<{
  chartData: ChartData[]
}>()

const formatDate = (date: string) => {
  return new Intl.DateTimeFormat('en-US', {
    day: '2-digit',
    month: 'short',
  }).format(new Date(date))
}

const categories = computed(() => {
  return props.chartData.map((item) => formatDate(item.date))
})

const transactionData = computed(() => {
  return props.chartData.map((item) => item.total_transactions)
})

const amountData = computed(() => {
  return props.chartData.map((item) => item.total_amount)
})

const series = computed(() => [
  {
    name: 'Transactions',
    data: transactionData.value,
  },
  {
    name: 'Transaction Amount',
    data: amountData.value,
  },
])

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(value)
}

const chartOptions = computed<ApexOptions>(() => ({
  legend: {
    show: true,
    position: 'top',
    horizontalAlign: 'left',
  },

  colors: ['#465FFF', '#9CB9FF'],

  chart: {
    fontFamily: 'Outfit, sans-serif',
    type: 'area',
    toolbar: {
      show: false,
    },
  },

  fill: {
    type: 'gradient',
    gradient: {
      opacityFrom: 0.55,
      opacityTo: 0,
    },
  },

  stroke: {
    curve: 'straight',
    width: [2, 2],
  },

  markers: {
    size: 0,
  },

  grid: {
    xaxis: {
      lines: {
        show: false,
      },
    },

    yaxis: {
      lines: {
        show: true,
      },
    },
  },

  dataLabels: {
    enabled: false,
  },

  tooltip: {
    shared: true,
    intersect: false,

    y: {
      formatter: (value: number, { seriesIndex }) => {
        if (seriesIndex === 0) {
          return `${value} transactions`
        }

        return formatCurrency(value)
      },
    },
  },

  xaxis: {
    type: 'category',

    categories: categories.value,

    axisBorder: {
      show: false,
    },

    axisTicks: {
      show: false,
    },

    tooltip: {
      enabled: false,
    },
  },

  yaxis: [
    {
      seriesName: 'Transactions',

      title: {
        text: 'Transactions',
      },

      labels: {
        formatter: (value: number) => {
          return Math.round(value).toString()
        },
      },
    },

    {
      seriesName: 'Transaction Amount',

      opposite: true,

      title: {
        text: 'Amount',
      },

      labels: {
        formatter: (value: number) => {
          if (value >= 1_000_000) {
            return `Rp ${(value / 1_000_000).toFixed(1)}M`
          }

          if (value >= 1_000) {
            return `Rp ${(value / 1_000).toFixed(0)}K`
          }

          return `Rp ${value}`
        },
      },
    },
  ],
}))
</script>
