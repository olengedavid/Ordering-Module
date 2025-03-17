<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import BankAccountList from "@/Components/Supplier/bankAccountList.vue";
import WarehouseList from "@/Components/Supplier/warehouseList.vue";
import DeliveryRegionList from "@/Components/Supplier/DeliveryRegionList.vue";
import ProductList from "@/Components/Supplier/ProductList.vue";

defineProps({
  supplier: Object,
  bankAccounts: Array
});
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="`Supplier #${supplier.id}`" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Supplier Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6">
            <div class="flex justify-between items-start">
              <div>
                <h2 class="text-2xl font-semibold text-gray-800">
                  Supplier #{{ supplier.id }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                  {{ supplier.company_name }}
                </p>
              </div>
              <button
                class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600"
              >
                Activate
              </button>
            </div>

            <!-- Supplier Details Grid -->
            <div
              class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6"
            >
              <div>
                <h3 class="text-sm font-medium text-gray-500">Company Name:</h3>
                <p class="mt-1">{{ supplier.company_name }}</p>
              </div>
              <div>
                <h3 class="text-sm font-medium text-gray-500">
                  Office Address:
                </h3>
                <p class="mt-1">{{ supplier.office_address }}</p>
              </div>
              <div>
                <h3 class="text-sm font-medium text-gray-500">
                  Contact Person:
                </h3>
                <p class="mt-1">{{ supplier.contact_person }}</p>
              </div>
              <div>
                <h3 class="text-sm font-medium text-gray-500">
                  Email Address:
                </h3>
                <p class="mt-1">{{ supplier.email }}</p>
              </div>
              <div>
                <h3 class="text-sm font-medium text-gray-500">Phone Number:</h3>
                <p class="mt-1">{{ supplier.phone_number }}</p>
              </div>
              <div>
                <h3 class="text-sm font-medium text-gray-500">KRA PIN:</h3>
                <p class="mt-1">{{ supplier.kra_pin }}</p>
              </div>
              <div>
                <h3 class="text-sm font-medium text-gray-500">Industry:</h3>
                <p class="mt-1">{{ supplier.industry }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="mb-6">
          <nav class="flex space-x-4" aria-label="Tabs">
            <button
              v-for="tab in [
                'Bank Accounts',
                'Warehouses',
                'Delivery Regions',
                'Products',
              ]"
              :key="tab"
              class="px-3 py-2 text-sm font-medium rounded-md"
              :class="[
                activeTab === tab
                  ? 'bg-indigo-100 text-indigo-700'
                  : 'text-gray-500 hover:text-gray-700',
              ]"
              @click="activeTab = tab"
            >
              {{ tab }}
            </button>
          </nav>
        </div>

        <!-- Tab Content -->
        <div v-if="activeTab === 'Bank Accounts'">
          <BankAccountList
            :bank-accounts="bankAccounts"
            :supplier-uuid="supplier.uuid"
          />
        </div>
        <div v-else-if="activeTab === 'Warehouses'">
          <WarehouseList :supplier="supplier" />
        </div>
        <div v-else-if="activeTab === 'Delivery Regions'">
          <DeliveryRegionList :supplier="supplier" />
        </div>
        <div v-else-if="activeTab === 'Products'">
          <ProductList :supplier="supplier" />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script>
export default {
  data() {
    return {
      activeTab: "Bank Accounts",
    };
  },
};
</script>