<template>
  <AdminLayout>
    <div class="space-y-6">

      <!-- Header -->
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">
            Distributors
          </h1>

          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Manage distributor data.
          </p>
        </div>

        <button
          @click="openCreateModal"
          class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600"
        >
          + Add Distributor
        </button>
      </div>

      <!-- Alert Success -->
      <div
        v-if="successMessage"
        class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"
      >
        {{ successMessage }}
      </div>

      <!-- Alert Error -->
      <div
        v-if="errorMessage"
        class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
      >
        {{ errorMessage }}
      </div>

      <!-- Table Card -->
      <div
        class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
      >

        <!-- Search -->
        <div class="border-b border-gray-200 p-5 dark:border-gray-800">
          <input
            v-model="search"
            type="text"
            placeholder="Search distributor..."
            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white"
          />
        </div>

        <!-- Table -->
        <div class="max-w-full overflow-x-auto">
          <table class="min-w-full">

            <thead>
              <tr class="border-b border-gray-200 dark:border-gray-800">
                <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">
                  Code
                </th>

                <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">
                  Name
                </th>

                <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">
                  Email
                </th>

                <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">
                  Phone
                </th>

                <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">
                  Status
                </th>

                <th class="px-6 py-4 text-right text-sm font-medium text-gray-500">
                  Action
                </th>
              </tr>
            </thead>

            <tbody>

              <!-- Loading -->
              <tr v-if="loading">
                <td
                  colspan="6"
                  class="px-6 py-10 text-center text-sm text-gray-500"
                >
                  Loading...
                </td>
              </tr>

              <!-- Data -->
              <tr
                v-for="distributor in filteredDistributors"
                :key="distributor.id"
                class="border-b border-gray-100 dark:border-gray-800"
              >
                <td class="px-6 py-4 text-sm text-gray-800 dark:text-white">
                  {{ distributor.md_code }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-800 dark:text-white">
                  {{ distributor.md_name }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-500">
                  {{ distributor.md_email || '-' }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-500">
                  {{ distributor.md_phone || '-' }}
                </td>

                <td class="px-6 py-4">
                  <span
                    :class="
                      distributor.is_active
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-700'
                    "
                    class="rounded-full px-3 py-1 text-xs font-medium"
                  >
                    {{ distributor.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>

                <td class="px-6 py-4 text-right">
                  <button
                    @click="openEditModal(distributor)"
                    class="text-sm font-medium text-brand-500 hover:text-brand-600"
                  >
                    Edit
                  </button>

                  <button
                    @click="confirmDelete(distributor)"
                    class="ml-4 text-sm font-medium text-red-500 hover:text-red-600"
                  >
                    Delete
                  </button>
                </td>
              </tr>

              <!-- Empty -->
              <tr
                v-if="
                  !loading &&
                  filteredDistributors.length === 0
                "
              >
                <td
                  colspan="6"
                  class="px-6 py-10 text-center text-sm text-gray-500"
                >
                  No distributor data found.
                </td>
              </tr>

            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- ================================================= -->
    <!-- CREATE / EDIT MODAL -->
    <!-- ================================================= -->

    <div
      v-if="showModal"
      class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 px-4"
    >
      <div
        class="w-full max-w-lg rounded-2xl bg-white p-6 dark:bg-gray-900"
      >

        <!-- Modal Header -->
        <div class="mb-6 flex items-center justify-between">
          <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
              {{ isEdit ? 'Edit Distributor' : 'Add Distributor' }}
            </h2>

            <p class="mt-1 text-sm text-gray-500">
              {{
                isEdit
                  ? 'Update distributor information.'
                  : 'Create a new distributor.'
              }}
            </p>
          </div>

          <button
            @click="closeModal"
            class="text-gray-400 hover:text-gray-600"
          >
            ✕
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="space-y-4">

          <!-- Code -->
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
              Distributor Code
            </label>

            <input
              v-model="form.md_code"
              type="text"
              required
              placeholder="e.g. DIST001"
              class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            />

            <p
              v-if="validationErrors.md_code"
              class="mt-1 text-xs text-red-500"
            >
              {{ validationErrors.md_code[0] }}
            </p>
          </div>

          <!-- Name -->
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
              Distributor Name
            </label>

            <input
              v-model="form.md_name"
              type="text"
              required
              placeholder="Distributor name"
              class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            />

            <p
              v-if="validationErrors.md_name"
              class="mt-1 text-xs text-red-500"
            >
              {{ validationErrors.md_name[0] }}
            </p>
          </div>

          <!-- Email -->
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
              Email
            </label>

            <input
              v-model="form.md_email"
              type="email"
              placeholder="email@example.com"
              class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            />

            <p
              v-if="validationErrors.md_email"
              class="mt-1 text-xs text-red-500"
            >
              {{ validationErrors.md_email[0] }}
            </p>
          </div>

          <!-- Phone -->
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
              Phone
            </label>

            <input
              v-model="form.md_phone"
              type="text"
              placeholder="08123456789"
              class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            />
          </div>

          <!-- Address -->
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
              Address
            </label>

            <textarea
              v-model="form.md_address"
              rows="3"
              placeholder="Distributor address"
              class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            />
          </div>

          <!-- Status -->
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
              Status
            </label>

            <select
              v-model="form.is_active"
              class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            >
              <option :value="true">Active</option>
              <option :value="false">Inactive</option>
            </select>
          </div>

          <!-- Buttons -->
          <div class="flex justify-end gap-3 pt-4">

            <button
              type="button"
              @click="closeModal"
              class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300"
            >
              Cancel
            </button>

            <button
              type="submit"
              :disabled="saving"
              class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-50"
            >
              {{ saving ? 'Saving...' : isEdit ? 'Update' : 'Save' }}
            </button>

          </div>

        </form>
      </div>
    </div>

    <!-- ================================================= -->
    <!-- DELETE MODAL -->
    <!-- ================================================= -->

    <div
      v-if="showDeleteModal"
      class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 px-4"
    >
      <div
        class="w-full max-w-md rounded-2xl bg-white p-6 dark:bg-gray-900"
      >

        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
          Delete Distributor?
        </h2>

        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
          Are you sure you want to delete
          <strong>{{ selectedDistributor?.md_name }}</strong>?
        </p>

        <div class="mt-6 flex justify-end gap-3">

          <button
            @click="showDeleteModal = false"
            class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 dark:border-gray-700 dark:text-gray-300"
          >
            Cancel
          </button>

          <button
            @click="handleDelete"
            :disabled="deleting"
            class="rounded-lg bg-red-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-red-600 disabled:opacity-50"
          >
            {{ deleting ? 'Deleting...' : 'Delete' }}
          </button>

        </div>
      </div>
    </div>

  </AdminLayout>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'

import {
  getDistributors,
  createDistributor,
  updateDistributor,
  deleteDistributor,
} from '@/api/distributor'

interface Distributor {
  id: number
  md_code: string
  md_name: string
  md_email: string | null
  md_phone: string | null
  md_address: string | null
  is_active: boolean
}

interface DistributorForm {
  md_code: string
  md_name: string
  md_email: string
  md_phone: string
  md_address: string
  is_active: boolean
}

const distributors = ref<Distributor[]>([])

const loading = ref(false)
const saving = ref(false)
const deleting = ref(false)

const showModal = ref(false)
const showDeleteModal = ref(false)

const isEdit = ref(false)
const selectedDistributor = ref<Distributor | null>(null)

const search = ref('')
const successMessage = ref('')
const errorMessage = ref('')

const validationErrors = ref<Record<string, string[]>>({})

const form = ref<DistributorForm>({
  md_code: '',
  md_name: '',
  md_email: '',
  md_phone: '',
  md_address: '',
  is_active: true,
})

const filteredDistributors = computed(() => {
  const keyword = search.value.toLowerCase().trim()

  if (!keyword) {
    return distributors.value
  }

  return distributors.value.filter((distributor) => {
    return (
      distributor.md_code?.toLowerCase().includes(keyword) ||
      distributor.md_name?.toLowerCase().includes(keyword) ||
      distributor.md_email?.toLowerCase().includes(keyword) ||
      distributor.md_phone?.toLowerCase().includes(keyword)
    )
  })
})

const resetForm = () => {
  form.value = {
    md_code: '',
    md_name: '',
    md_email: '',
    md_phone: '',
    md_address: '',
    is_active: true,
  }

  validationErrors.value = {}
}

const clearMessages = () => {
  successMessage.value = ''
  errorMessage.value = ''
}

const loadDistributors = async () => {
  loading.value = true
  clearMessages()

  try {
    const response = await getDistributors()

    distributors.value = response.data.data
  } catch (error: any) {
    console.error('Failed to load distributors:', error)

    errorMessage.value =
      error.response?.data?.message ||
      'Failed to load distributor data.'
  } finally {
    loading.value = false
  }
}

const openCreateModal = () => {
  clearMessages()
  resetForm()

  isEdit.value = false
  selectedDistributor.value = null

  showModal.value = true
}

const openEditModal = (distributor: Distributor) => {
  clearMessages()
  validationErrors.value = {}

  isEdit.value = true
  selectedDistributor.value = distributor

  form.value = {
    md_code: distributor.md_code,
    md_name: distributor.md_name,
    md_email: distributor.md_email || '',
    md_phone: distributor.md_phone || '',
    md_address: distributor.md_address || '',
    is_active: distributor.is_active,
  }

  showModal.value = true
}

const closeModal = () => {
  if (saving.value) return

  showModal.value = false
  resetForm()
}

const submitForm = async () => {
  saving.value = true
  validationErrors.value = {}
  clearMessages()

  try {
    if (isEdit.value && selectedDistributor.value) {
      await updateDistributor(
        selectedDistributor.value.id,
        form.value,
      )

      successMessage.value = 'Distributor updated successfully.'
    } else {
      await createDistributor(form.value)

      successMessage.value = 'Distributor created successfully.'
    }

    showModal.value = false

    resetForm()

    await loadDistributors()

    successMessage.value = isEdit.value
      ? 'Distributor updated successfully.'
      : 'Distributor created successfully.'
  } catch (error: any) {
    console.error('Failed to save distributor:', error)

    if (error.response?.status === 422) {
      validationErrors.value =
        error.response.data.errors || {}

      errorMessage.value =
        error.response.data.message ||
        'Please check the form.'
    } else {
      errorMessage.value =
        error.response?.data?.message ||
        'Failed to save distributor.'
    }
  } finally {
    saving.value = false
  }
}

const confirmDelete = (distributor: Distributor) => {
  clearMessages()

  selectedDistributor.value = distributor
  showDeleteModal.value = true
}

const handleDelete = async () => {
  if (!selectedDistributor.value) return

  deleting.value = true
  clearMessages()

  try {
    await deleteDistributor(
      selectedDistributor.value.id,
    )

    showDeleteModal.value = false

    successMessage.value =
      'Distributor deleted successfully.'

    selectedDistributor.value = null

    await loadDistributors()

    successMessage.value =
      'Distributor deleted successfully.'
  } catch (error: any) {
    console.error('Failed to delete distributor:', error)

    errorMessage.value =
      error.response?.data?.message ||
      'Failed to delete distributor.'
  } finally {
    deleting.value = false
  }
}

onMounted(() => {
  loadDistributors()
})
</script>
