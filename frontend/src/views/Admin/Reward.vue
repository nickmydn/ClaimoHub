<template>
  <AdminLayout>
    <div class="space-y-6">

      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold dark:text-white">
            Rewards
          </h1>
          <p class="mt-1 text-sm text-gray-500">
            Manage reward campaigns.
          </p>
        </div>

        <button
          @click="openCreate"
          class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm text-white"
        >
          + Add Reward
        </button>
      </div>

      <div
        v-if="message"
        class="rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700"
      >
        {{ message }}
      </div>

      <div
        v-if="errorMessage"
        class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700"
      >
        {{ errorMessage }}
      </div>

      <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

        <div class="border-b p-5">
          <input
            v-model="search"
            placeholder="Search reward..."
            class="w-full rounded-lg border px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
          />
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full">

            <thead>
              <tr class="border-b">
                <th class="px-6 py-4 text-left text-sm text-gray-500">Code</th>
                <th class="px-6 py-4 text-left text-sm text-gray-500">Name</th>
                <th class="px-6 py-4 text-left text-sm text-gray-500">Voucher</th>
                <th class="px-6 py-4 text-left text-sm text-gray-500">Min. Transaction</th>
                <th class="px-6 py-4 text-left text-sm text-gray-500">Quota</th>
                <th class="px-6 py-4 text-left text-sm text-gray-500">Period</th>
                <th class="px-6 py-4 text-left text-sm text-gray-500">Status</th>
                <th class="px-6 py-4 text-right text-sm text-gray-500">Action</th>
              </tr>
            </thead>

            <tbody>

              <tr v-if="loading">
                <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-500">
                  Loading...
                </td>
              </tr>

              <tr
                v-for="item in filteredData"
                :key="item.id"
                class="border-b"
              >
                <td class="px-6 py-4 text-sm dark:text-white">
                  {{ item.mr_code }}
                </td>

                <td class="px-6 py-4 text-sm dark:text-white">
                  {{ item.mr_name }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-500">
                  {{ item.voucher_product?.mvp_name || '-' }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-500">
                  {{ formatCurrency(item.mr_min_transaction) }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-500">
                  {{ item.mr_quota }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-500">
                  {{ formatDate(item.mr_start_date) }}
                  -
                  {{ formatDate(item.mr_end_date) }}
                </td>

                <td class="px-6 py-4">
                  <span
                    class="rounded-full px-3 py-1 text-xs"
                    :class="
                      item.is_active
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-700'
                    "
                  >
                    {{ item.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>

                <td class="px-6 py-4 text-right">
                  <button
                    @click="openEdit(item)"
                    class="text-sm text-brand-500"
                  >
                    Edit
                  </button>

                  <button
                    @click="remove(item)"
                    class="ml-4 text-sm text-red-500"
                  >
                    Delete
                  </button>
                </td>
              </tr>

              <tr v-if="!loading && filteredData.length === 0">
                <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-500">
                  No reward data found.
                </td>
              </tr>

            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 px-4"
    >
      <div class="w-full max-w-lg rounded-2xl bg-white p-6 dark:bg-gray-900">

        <h2 class="mb-5 text-xl font-semibold dark:text-white">
          {{ editing ? 'Edit Reward' : 'Add Reward' }}
        </h2>

        <form @submit.prevent="submit" class="space-y-4">

          <select
            v-model="form.mr_voucher_product_id"
            required
            class="w-full rounded-lg border px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
          >
            <option value="">
              Select Voucher Product
            </option>

            <option
              v-for="item in voucherProducts"
              :key="item.id"
              :value="item.id"
            >
              {{ item.mvp_code }} - {{ item.mvp_name }}
            </option>
          </select>

          <input
            v-model="form.mr_code"
            required
            placeholder="Reward Code"
            class="w-full rounded-lg border px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
          />

          <input
            v-model="form.mr_name"
            required
            placeholder="Reward Name"
            class="w-full rounded-lg border px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
          />

          <input
            v-model="form.mr_min_transaction"
            required
            type="number"
            min="0"
            placeholder="Minimum Transaction"
            class="w-full rounded-lg border px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
          />

          <input
            v-model="form.mr_quota"
            required
            type="number"
            min="0"
            placeholder="Quota"
            class="w-full rounded-lg border px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
          />

          <div class="grid grid-cols-2 gap-3">

            <div>
              <label class="mb-1 block text-sm text-gray-500">
                Start Date
              </label>

              <input
                v-model="form.mr_start_date"
                required
                type="date"
                class="w-full rounded-lg border px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
              />
            </div>

            <div>
              <label class="mb-1 block text-sm text-gray-500">
                End Date
              </label>

              <input
                v-model="form.mr_end_date"
                required
                type="date"
                class="w-full rounded-lg border px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
              />
            </div>

          </div>

          <select
            v-model="form.is_active"
            class="w-full rounded-lg border px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
          >
            <option :value="true">Active</option>
            <option :value="false">Inactive</option>
          </select>

          <div class="flex justify-end gap-3">
            <button
              type="button"
              @click="showModal = false"
              class="rounded-lg border px-4 py-2.5"
            >
              Cancel
            </button>

            <button
              :disabled="saving"
              class="rounded-lg bg-brand-500 px-5 py-2.5 text-white"
            >
              {{ saving ? 'Saving...' : editing ? 'Update' : 'Save' }}
            </button>
          </div>

        </form>
      </div>
    </div>

  </AdminLayout>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'

import {
  getRewards,
  createReward,
  updateReward,
  deleteReward,
} from '@/api/reward'

import { getVoucherProducts } from '@/api/voucherproduct'

const rewards = ref<any[]>([])
const voucherProducts = ref<any[]>([])

const loading = ref(false)
const saving = ref(false)

const showModal = ref(false)
const editing = ref(false)
const selectedId = ref<number | null>(null)

const search = ref('')
const message = ref('')
const errorMessage = ref('')

const form = ref({
  mr_voucher_product_id: '',
  mr_code: '',
  mr_name: '',
  mr_min_transaction: 0,
  mr_quota: 0,
  mr_start_date: '',
  mr_end_date: '',
  is_active: true,
})

const filteredData = computed(() => {
  const q = search.value.toLowerCase().trim()

  if (!q) return rewards.value

  return rewards.value.filter((item) =>
    [
      item.mr_code,
      item.mr_name,
      item.voucher_product?.mvp_name,
    ]
      .filter(Boolean)
      .some((v) =>
        String(v).toLowerCase().includes(q),
      ),
  )
})

const formatCurrency = (value: number) =>
  new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(value)

const formatDate = (date: string) => {
  if (!date) return '-'

  return new Intl.DateTimeFormat('id-ID').format(
    new Date(date),
  )
}

const loadData = async () => {
  loading.value = true

  try {
    const [rewardResponse, voucherResponse] =
      await Promise.all([
        getRewards(),
        getVoucherProducts(),
      ])

    rewards.value = rewardResponse.data.data
    voucherProducts.value = voucherResponse.data.data
  } catch (error: any) {
    errorMessage.value =
      error.response?.data?.message ||
      'Failed to load reward data.'
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  form.value = {
    mr_voucher_product_id: '',
    mr_code: '',
    mr_name: '',
    mr_min_transaction: 0,
    mr_quota: 0,
    mr_start_date: '',
    mr_end_date: '',
    is_active: true,
  }
}

const openCreate = () => {
  resetForm()
  editing.value = false
  selectedId.value = null
  showModal.value = true
}

const openEdit = (item: any) => {
  editing.value = true
  selectedId.value = item.id

  form.value = {
    mr_voucher_product_id: item.mr_voucher_product_id,
    mr_code: item.mr_code,
    mr_name: item.mr_name,
    mr_min_transaction: item.mr_min_transaction,
    mr_quota: item.mr_quota,
    mr_start_date: item.mr_start_date?.substring(0, 10) || '',
    mr_end_date: item.mr_end_date?.substring(0, 10) || '',
    is_active: item.is_active,
  }

  showModal.value = true
}

const submit = async () => {
  saving.value = true

  try {
    if (editing.value && selectedId.value) {
      await updateReward(
        selectedId.value,
        form.value,
      )

      message.value =
        'Reward updated successfully.'
    } else {
      await createReward(form.value)

      message.value =
        'Reward created successfully.'
    }

    showModal.value = false
    await loadData()
  } catch (error: any) {
    errorMessage.value =
      error.response?.data?.message ||
      'Failed to save reward.'
  } finally {
    saving.value = false
  }
}

const remove = async (item: any) => {
  if (!confirm(`Delete ${item.mr_name}?`)) return

  try {
    await deleteReward(item.id)

    message.value =
      'Reward deleted successfully.'

    await loadData()
  } catch (error: any) {
    errorMessage.value =
      error.response?.data?.message ||
      'Failed to delete reward.'
  }
}

onMounted(loadData)
</script>
