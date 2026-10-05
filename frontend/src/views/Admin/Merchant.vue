<template>
  <AdminLayout>
    <div class="space-y-6">

      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">
            Merchants
          </h1>
          <p class="mt-1 text-sm text-gray-500">
            Manage merchant data.
          </p>
        </div>

        <button
          @click="openCreate"
          class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600"
        >
          + Add Merchant
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

        <div class="border-b border-gray-200 p-5 dark:border-gray-800">
          <input
            v-model="search"
            placeholder="Search merchant..."
            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
          />
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead>
              <tr class="border-b border-gray-200 dark:border-gray-800">
                <th class="px-6 py-4 text-left text-sm text-gray-500">Code</th>
                <th class="px-6 py-4 text-left text-sm text-gray-500">Name</th>
                <th class="px-6 py-4 text-left text-sm text-gray-500">Distributor</th>
                <th class="px-6 py-4 text-left text-sm text-gray-500">Email</th>
                <th class="px-6 py-4 text-left text-sm text-gray-500">Status</th>
                <th class="px-6 py-4 text-right text-sm text-gray-500">Action</th>
              </tr>
            </thead>

            <tbody>
              <tr v-if="loading">
                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                  Loading...
                </td>
              </tr>

              <tr
                v-for="item in filteredData"
                :key="item.id"
                class="border-b border-gray-100 dark:border-gray-800"
              >
                <td class="px-6 py-4 text-sm dark:text-white">
                  {{ item.mm_code }}
                </td>

                <td class="px-6 py-4 text-sm dark:text-white">
                  {{ item.mm_name }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-500">
                  {{ item.distributor?.md_name || '-' }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-500">
                  {{ item.mm_email || '-' }}
                </td>

                <td class="px-6 py-4">
                  <span
                    class="rounded-full px-3 py-1 text-xs font-medium"
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
                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                  No merchant data found.
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
          {{ editing ? 'Edit Merchant' : 'Add Merchant' }}
        </h2>

        <form @submit.prevent="submit" class="space-y-4">

          <div>
            <label class="mb-1 block text-sm dark:text-gray-300">
              Distributor
            </label>

            <select
              v-model="form.mm_distributor_id"
              required
              class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            >
              <option value="">Select Distributor</option>
              <option
                v-for="d in distributors"
                :key="d.id"
                :value="d.id"
              >
                {{ d.md_code }} - {{ d.md_name }}
              </option>
            </select>
          </div>

          <div>
            <label class="mb-1 block text-sm dark:text-gray-300">
              Merchant Code
            </label>
            <input
              v-model="form.mm_code"
              required
              class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            />
          </div>

          <div>
            <label class="mb-1 block text-sm dark:text-gray-300">
              Merchant Name
            </label>
            <input
              v-model="form.mm_name"
              required
              class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            />
          </div>

          <div>
            <label class="mb-1 block text-sm dark:text-gray-300">
              Email
            </label>
            <input
              v-model="form.mm_email"
              type="email"
              class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            />
          </div>

          <div>
            <label class="mb-1 block text-sm dark:text-gray-300">
              Phone
            </label>
            <input
              v-model="form.mm_phone"
              class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            />
          </div>

          <div>
            <label class="mb-1 block text-sm dark:text-gray-300">
              Address
            </label>
            <textarea
              v-model="form.mm_address"
              rows="3"
              class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            />
          </div>

          <div>
            <label class="mb-1 block text-sm dark:text-gray-300">
              Status
            </label>

            <select
              v-model="form.is_active"
              class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            >
              <option :value="true">Active</option>
              <option :value="false">Inactive</option>
            </select>
          </div>

          <div class="flex justify-end gap-3 pt-3">
            <button
              type="button"
              @click="showModal = false"
              class="rounded-lg border px-4 py-2.5 text-sm"
            >
              Cancel
            </button>

            <button
              :disabled="saving"
              class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white disabled:opacity-50"
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
  getMerchants,
  createMerchant,
  updateMerchant,
  deleteMerchant,
} from '@/api/merchant'
import { getDistributors } from '@/api/distributor'

const merchants = ref<any[]>([])
const distributors = ref<any[]>([])

const loading = ref(false)
const saving = ref(false)
const showModal = ref(false)
const editing = ref(false)
const selectedId = ref<number | null>(null)

const search = ref('')
const message = ref('')
const errorMessage = ref('')

const form = ref({
  mm_distributor_id: '',
  mm_code: '',
  mm_name: '',
  mm_email: '',
  mm_phone: '',
  mm_address: '',
  is_active: true,
})

const filteredData = computed(() => {
  const q = search.value.toLowerCase().trim()

  if (!q) return merchants.value

  return merchants.value.filter((item) =>
    [
      item.mm_code,
      item.mm_name,
      item.mm_email,
      item.mm_phone,
      item.distributor?.md_name,
    ]
      .filter(Boolean)
      .some((value) =>
        String(value).toLowerCase().includes(q),
      ),
  )
})

const loadData = async () => {
  loading.value = true

  try {
    const [merchantResponse, distributorResponse] =
      await Promise.all([
        getMerchants(),
        getDistributors(),
      ])

    merchants.value = merchantResponse.data.data
    distributors.value = distributorResponse.data.data
  } catch (error: any) {
    errorMessage.value =
      error.response?.data?.message ||
      'Failed to load merchant data.'
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  form.value = {
    mm_distributor_id: '',
    mm_code: '',
    mm_name: '',
    mm_email: '',
    mm_phone: '',
    mm_address: '',
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
    mm_distributor_id: item.mm_distributor_id,
    mm_code: item.mm_code,
    mm_name: item.mm_name,
    mm_email: item.mm_email || '',
    mm_phone: item.mm_phone || '',
    mm_address: item.mm_address || '',
    is_active: item.is_active,
  }

  showModal.value = true
}

const submit = async () => {
  saving.value = true
  errorMessage.value = ''

  try {
    if (editing.value && selectedId.value) {
      await updateMerchant(selectedId.value, form.value)
      message.value = 'Merchant updated successfully.'
    } else {
      await createMerchant(form.value)
      message.value = 'Merchant created successfully.'
    }

    showModal.value = false
    await loadData()
  } catch (error: any) {
    errorMessage.value =
      error.response?.data?.message ||
      'Failed to save merchant.'
  } finally {
    saving.value = false
  }
}

const remove = async (item: any) => {
  if (!confirm(`Delete ${item.mm_name}?`)) return

  try {
    await deleteMerchant(item.id)

    message.value = 'Merchant deleted successfully.'

    await loadData()
  } catch (error: any) {
    errorMessage.value =
      error.response?.data?.message ||
      'Failed to delete merchant.'
  }
}

onMounted(loadData)
</script>
