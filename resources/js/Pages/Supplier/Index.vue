<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref } from "vue";

defineProps({
  suppliers: {
    type: Array,
    required: true,
  },
});
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Suppliers" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold text-gray-800">Suppliers</h2>
              <Link
                :href="route('suppliers.create')"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
              >
                Add Supplier
              </Link>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Company Name
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Contact Person
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Email
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Phone
                    </th>
                    <!-- <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Actions
                    </th> -->
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="supplier in suppliers" :key="supplier.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ supplier.company_name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ supplier.contact_person }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ supplier.email }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ supplier.phone_number }}
                    </td>
                    <!-- <td class="px-6 py-4 whitespace-nowrap text-sm">
                      <Link
                        :href="route('suppliers.edit', supplier.id)"
                        class="text-indigo-600 hover:text-indigo-900 mr-3"
                      >
                        Edit
                      </Link>
                      <button
                        @click="deleteSupplier(supplier.id)"
                        class="text-red-600 hover:text-red-900"
                      >
                        Delete
                      </button>
                    </td> -->
                  </tr>
                  <tr v-if="suppliers.length === 0">
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                      No suppliers found
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>