<template>
  <AdminLayout>
    <div class="space-y-6">

      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold dark:text-white">
            Voucher Products
          </h1>
          <p class="mt-1 text-sm text-gray-500">
            Manage external voucher products and stock.
          </p>
        </div>

        <button
          @click="openCreate"
          class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm text-white"
        >
          + Add Voucher Product
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
            placeholder="Search voucher product..."
            class="w-full rounded-lg border px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
          />
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead>
              <tr class="border-b">
                <th class="px-6 py-4 text-left text-sm text-gray-500">Provider</th>
                <th class="px-6 py-4 text-left text-sm text-gray-500">Code</th>
                <th class="px-6 py-4 text-left text-sm text-gray-500">Name</th>
                <th class="px-6 py-4 text-left text-sm text-gray-500">Denomination</th>
                <th class="px-6 py-4 text-left text-sm text-gray-500">Stock</th>
                <th class="px-6 py-4 text-left text-sm text-gray-500">Status</th>
                <th class="px-6 py-4 text-right text-sm text-gray-500">Action</th>
              </tr>
            </thead>

            <tbody>
              <tr v-if="loading">
                <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                  Loading...
                </td>
              </tr>

              <tr
                v-for="item in filteredData"
                :key="item.id"
                class="border-b"
              >
                <td class="px-6 py-4 text-sm dark:text-white">
                  {{ item.mvp_provider }}
                </td>

                <td class="px-6 py-4 text-sm dark:text-white">
                  {{ item.mvp_code }}
                </td>

                <td class="px-6 py-4 text-sm dark:text-white">
                  {{ item.mvp_name }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-500">
                  {{ formatCurrency(item.mvp_denomination) }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-500">
                  {{ item.mvp_stock }}
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
                <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                  No voucher product found.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <div
      v-if="showModal"
      class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 px-4"
    >
      <div class="w-full max-w-lg rounded-2xl bg-white p-6 dark:bg-gray-900">

        <h2 class="mb-5 text-xl font-semibold dark:text-white">
          {{ editing ? 'Edit Voucher Product' : 'Add Voucher Product' }}
        </h2>

        <form @submit.prevent="submit" class="space-y-4">

          <input
            v-model="form.mvp_provider"
            required
            placeholder="Provider"
            class="w-full rounded-lg border px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
          />

          <input
            v-model="form.mvp_code"
            required
            placeholder="Voucher Code"
            class="w-full rounded-lg border px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
          />

          <input
            v-model="form.mvp_name"
            required
            placeholder="Voucher Name"
            class="w-full rounded-lg border px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
          />

          <input
            v-model="form.mvp_denomination"
            required
            type="number"
            min="0"
            placeholder="Denomination"
            class="w-full rounded-lg border px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
          />

          <input
            v-model="form.mvp_stock"
            required
            type="number"
            min="0"
            placeholder="Stock"
            class="w-full rounded-lg border px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
          />

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
  getVoucherProducts,
  createVoucherProduct,
  updateVoucherProduct,
  deleteVoucherProduct,
} from '@/api/voucherproduct'

const data = ref<any[]>([])

const loading = ref(false)
const saving = ref(false)
const showModal = ref(false)
const editing = ref(false)
const selectedId = ref<number | null>(null)

const search = ref('')
const message = ref('')
const errorMessage = ref('')

const form = ref({
  mvp_provider: '',
  mvp_code: '',
  mvp_name: '',
  mvp_denomination: 0,
  mvp_stock: 0,
  is_active: true,
})

const filteredData = computed(() => {
  const q = search.value.toLowerCase().trim()

  if (!q) return data.value

  return data.value.filter((item) =>
    [
      item.mvp_provider,
      item.mvp_code,
      item.mvp_name,
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

const loadData = async () => {
  loading.value = true

  try {
    const response = await getVoucherProducts()
    data.value = response.data.data
  } catch (error: any) {
    errorMessage.value =
      error.response?.data?.message ||
      'Failed to load voucher products.'
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  form.value = {
    mvp_provider: '',
    mvp_code: '',
    mvp_name: '',
    mvp_denomination: 0,
    mvp_stock: 0,
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
    mvp_provider: item.mvp_provider,
    mvp_code: item.mvp_code,
    mvp_name: item.mvp_name,
    mvp_denomination: item.mvp_denomination,
    mvp_stock: item.mvp_stock,
    is_active: item.is_active,
  }

  showModal.value = true
}

const submit = async () => {
  saving.value = true

  try {
    if (editing.value && selectedId.value) {
      await updateVoucherProduct(
        selectedId.value,
        form.value,
      )

      message.value =
        'Voucher product updated successfully.'
    } else {
      await createVoucherProduct(form.value)

      message.value =
        'Voucher product created successfully.'
    }

    showModal.value = false
    await loadData()
  } catch (error: any) {
    errorMessage.value =
      error.response?.data?.message ||
      'Failed to save voucher product.'
  } finally {
    saving.value = false
  }
}

const remove = async (item: any) => {
  if (!confirm(`Delete ${item.mvp_name}?`)) return

  try {
    await deleteVoucherProduct(item.id)

    message.value =
      'Voucher product deleted successfully.'

    await loadData()
  } catch (error: any) {
    errorMessage.value =
      error.response?.data?.message ||
      'Failed to delete voucher product.'
  }
}

onMounted(loadData)
</script>
