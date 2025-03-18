<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ref, onMounted } from "vue";
import axios from "axios";

const showingModal = ref(false);
const page = usePage();
const user = page.props.auth.user;
const supplier_uuid = "a0405292-dc2c-44ad-88c4-302caad40239";

const inventories = ref([]);
const products = ref([]);
const warehouses = ref([]);
const loading = ref(true);

const form = useForm({
  company_id: user.company_id,
  product_id: "",
  warehouse_id: "",
  cost_price: "",
  selling_price: "",
  stock_quantity: "",
  min_order: "",
  max_order: "",
  promo_amount: "",
  promo_start_date: "",
  promo_end_date: "",
  created_by: user.id,
  status: "active",
});

const createInventory = () => {
  form.post(route("supplier.inventories.store"), {
    preserveScroll: true,
    onSuccess: () => {
      closeModal();
      form.reset();
      fetchInventories();
    },
  });
};

const fetchProducts = async () => {
  try {
    const response = await axios.get(
      route("supplier.products.list", {
        uuid: supplier_uuid,
      })
    );
    products.value = response.data;
  } catch (error) {
    console.error("Error fetching products:", error);
  }
};

const fetchWarehouses = async () => {
  try {
    const response = await axios.get(
      route("supplier.warehouses.list", {
        uuid: supplier_uuid,
      })
    );
    warehouses.value = response.data;
  } catch (error) {
    console.error("Error fetching warehouses:", error);
  }
};

const fetchInventories = async () => {
  loading.value = true;
  try {
    const response = await axios.get(
      route("supplier.inventories", {
        uuid: supplier_uuid,
      })
    );
    inventories.value = response.data;
  } catch (error) {
    console.error("Error fetching inventories:", error);
  } finally {
    loading.value = false;
  }
};

const closeModal = () => {
  showingModal.value = false;
  form.reset();
};

defineProps({
  //   inventories: {
  //     type: Array
  //   },
});
onMounted(() => {
  fetchInventories();
  fetchProducts();
  fetchWarehouses();
});
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Inventory Management" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold text-gray-800">
                Inventory Items
              </h2>
              <button
                @click="showingModal = true"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
              >
                Add Inventory
              </button>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                    >
                      Product
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                    >
                      Warehouse
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                    >
                      Stock Quantity
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                    >
                      Cost Price
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                    >
                      Selling Price
                    </th>
                
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                    >
                      Status
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                    >
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="inventory in inventories" :key="inventory.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ inventory.product?.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ inventory.warehouse?.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ inventory.stock_quantity }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      KES {{ inventory.cost_price }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      KES {{ inventory.selling_price }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        :class="{
                          'px-2 py-1 text-xs rounded-full': true,
                          'bg-green-100 text-green-800':
                            inventory.status === 'active',
                          'bg-red-100 text-red-800':
                            inventory.status === 'inactive',
                        }"
                      >
                        {{ inventory.status }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <button
                        class="text-indigo-600 hover:text-indigo-900 mr-3"
                      >
                        Edit
                      </button>
                      <button class="text-red-600 hover:text-red-900">
                        Delete
                      </button>
                    </td>
                  </tr>
                  <tr v-if="inventories.length === 0">
                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                      No inventory items found
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Add Inventory Modal -->
            <Modal :show="showingModal" @close="closeModal">
              <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                  Add New Inventory Item
                </h2>

                <form @submit.prevent="createInventory">
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <InputLabel for="product_id" value="Product" />
                      <select
                        id="product_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        v-model="form.product_id"
                        required
                      >
                        <option value="">Select Product</option>
                        <option
                          v-for="product in products"
                          :key="product.id"
                          :value="product.id"
                        >
                          {{ product.name }}
                        </option>
                        <!-- Add product options dynamically -->
                      </select>
                      <InputError
                        class="mt-2"
                        :message="form.errors.product_id"
                      />
                    </div>

                    <div>
                      <InputLabel for="warehouse_id" value="Warehouse" />
                      <select
                        id="warehouse_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        v-model="form.warehouse_id"
                        required
                      >
                        <option value="">Select Warehouse</option>
                        <option
                          v-for="warehouse in warehouses"
                          :key="warehouse.id"
                          :value="warehouse.id"
                        >
                          {{ warehouse.name }}
                        </option>
                        <!-- Add warehouse options dynamically -->
                      </select>
                      <InputError
                        class="mt-2"
                        :message="form.errors.warehouse_id"
                      />
                    </div>

                    <div>
                      <InputLabel for="cost_price" value="Cost Price (KES)" />
                      <TextInput
                        id="cost_price"
                        type="number"
                        step="0.01"
                        class="mt-1 block w-full"
                        v-model="form.cost_price"
                        required
                      />
                      <InputError
                        class="mt-2"
                        :message="form.errors.cost_price"
                      />
                    </div>

                    <div>
                      <InputLabel
                        for="selling_price"
                        value="Selling Price (KES)"
                      />
                      <TextInput
                        id="selling_price"
                        type="number"
                        step="0.01"
                        class="mt-1 block w-full"
                        v-model="form.selling_price"
                        required
                      />
                      <InputError
                        class="mt-2"
                        :message="form.errors.selling_price"
                      />
                    </div>

                    <div>
                      <InputLabel for="stock_quantity" value="Beginning Stock" />
                      <TextInput
                        id="stock_quantity"
                        type="number"
                        step="0.01"
                        class="mt-1 block w-full"
                        v-model="form.stock_quantity"
                        required
                      />
                      <InputError
                        class="mt-2"
                        :message="form.errors.stock_quantity"
                      />
                    </div>

                    <!-- <div>
                      <InputLabel for="batch_number" value="Batch Number" />
                      <TextInput
                        id="batch_number"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.batch_number"
                      />
                      <InputError
                        class="mt-2"
                        :message="form.errors.batch_number"
                      />
                    </div> -->

                    <div>
                      <InputLabel for="min_order" value="Minimum Order" />
                      <TextInput
                        id="min_order"
                        type="number"
                        step="0.01"
                        class="mt-1 block w-full"
                        v-model="form.min_order"
                        required
                      />
                      <InputError
                        class="mt-2"
                        :message="form.errors.min_order"
                      />
                    </div>

                    <div>
                      <InputLabel for="max_order" value="Maximum Order" />
                      <TextInput
                        id="max_order"
                        type="number"
                        step="0.01"
                        class="mt-1 block w-full"
                        v-model="form.max_order"
                        required
                      />
                    <InputError
                        class="mt-2"
                        :message="form.errors.max_order"
                      />
                    </div>

                    <div>
                      <InputLabel for="promo_amount" value="Promo Amount" />
                      <TextInput
                        id="promo_amount"
                        type="number"
                        step="0.01"
                        class="mt-1 block w-full"
                        v-model="form.promo_amount"
                      />
                      <InputError
                        class="mt-2"
                        :message="form.errors.promo_amount"
                      />
                    </div>

                    <div>
                      <InputLabel
                        for="promo_start_date"
                        value="Promo Start Date"
                      />
                      <TextInput
                        id="promo_start_date"
                        type="date"
                        class="mt-1 block w-full"
                        v-model="form.promo_start_date"
                      />
                      <InputError
                        class="mt-2"
                        :message="form.errors.promo_start_date"
                      />
                    </div>

                    <div>
                      <InputLabel for="promo_end_date" value="Promo End Date" />
                      <TextInput
                        id="promo_end_date"
                        type="date"
                        class="mt-1 block w-full"
                        v-model="form.promo_end_date"
                      />
                      <InputError
                        class="mt-2"
                        :message="form.errors.promo_end_date"
                      />
                    </div>
                  </div>

                  <div class="mt-6 flex justify-end">
                    <button
                      type="button"
                      class="mr-3 px-4 py-2 text-sm text-gray-700 hover:text-gray-900"
                      @click="closeModal"
                    >
                      Cancel
                    </button>
                    <PrimaryButton :disabled="form.processing">
                      Create Inventory Item
                    </PrimaryButton>
                  </div>
                </form>
              </div>
            </Modal>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>