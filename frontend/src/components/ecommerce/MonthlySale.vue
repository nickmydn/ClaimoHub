<template>
  <div
    class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6"
  >
    <div class="flex items-center justify-between">
      <div>
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
          Transaction Amount
        </h3>

        <p class="mt-1 text-gray-500 text-theme-sm dark:text-gray-400">
          Transaction amount for the last 7 days
        </p>
      </div>
    </div>

    <div class="max-w-full overflow-x-auto custom-scrollbar">
      <div id="chartOne" class="-ml-5 min-w-[650px] xl:min-w-full pl-2">
        <VueApexCharts
          type="bar"
          height="180"
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

const amountData = computed(() => {
  return props.chartData.map((item) => item.total_amount)
})

const series = computed(() => [
  {
    name: 'Transaction Amount',
    data: amountData.value,
  },
])

const chartOptions = computed(() => ({
  colors: ['#465FFF'],

  chart: {
    fontFamily: 'Outfit, sans-serif',
    type: 'bar',
    toolbar: {
      show: false,
    },
  },

  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: '45%',
      borderRadius: 5,
      borderRadiusApplication: 'end' as const,
    },
  },

  dataLabels: {
    enabled: false,
  },

  stroke: {
    show: true,
    width: 4,
    colors: ['transparent'],
  },

  xaxis: {
    categories: categories.value,

    axisBorder: {
      show: false,
    },

    axisTicks: {
      show: false,
    },
  },

  legend: {
    show: false,
  },

  yaxis: {
    labels: {
      formatter: (value: number) => {
        return new Intl.NumberFormat('id-ID', {
          notation: 'compact',
          maximumFractionDigits: 1,
        }).format(value)
      },
    },
  },

  grid: {
    yaxis: {
      lines: {
        show: true,
      },
    },
  },

  fill: {
    opacity: 1,
  },

  tooltip: {
    y: {
      formatter: (value: number) => {
        return new Intl.NumberFormat('id-ID', {
          style: 'currency',
          currency: 'IDR',
          maximumFractionDigits: 0,
        }).format(value)
      },
    },
  },
}))
</script>
