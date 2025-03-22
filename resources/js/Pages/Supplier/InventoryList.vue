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
import CustomPagination from "@/Components/CustomPagination.vue";

const currentPage = ref(1);
const perPage = ref(10);
const lastPage = ref(1);

const showingModal = ref(false);
const page = usePage();
const user = page.props.auth.user;
const supplier_uuid = page.props.auth.user.company.uuid;

const inventories = ref([]);
const products = ref([]);
const warehouses = ref([]);
const loading = ref(true);
const editingInventory = ref(false);
const editingInventoryUuid = ref(null);

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

const editInventoryItem = (inventory) => {
  editingInventory.value = true;
  editingInventoryUuid.value = inventory.uuid;

  form.product_id = inventory.product_id;
  form.warehouse_id = inventory.warehouse_id;
  form.cost_price = inventory.cost_price;
  form.selling_price = inventory.selling_price;
  form.stock_quantity = inventory.stock_quantity;
  form.min_order = inventory.min_order;
  form.max_order = inventory.max_order;
  form.promo_amount = inventory.promo_amount;
  form.promo_start_date = inventory.promo_start_date;
  form.promo_end_date = inventory.promo_end_date;
  form.status = inventory.status;

  showingModal.value = true;
};

const createInventory = () => {
  if (editingInventory.value) {
    form.put(
      route("supplier.inventories.update", {
        uuid: editingInventoryUuid.value,
      }),
      {
        preserveScroll: true,
        onSuccess: () => {
          closeModal();
          form.reset();
          fetchInventories();
        },
      }
    );
  } else {
    form.post(route("supplier.inventories.store"), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal();
        form.reset();
        fetchInventories();
      },
    });
  }
};

const handlePerPageChange = (newPerPage) => {
  perPage.value = newPerPage;
  fetchInventories(1);
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
      route("supplier.inventories.list", {
        uuid: supplier_uuid,
        page: currentPage.value,
        per_page: perPage.value,
      })
    );
    inventories.value = response.data.data;
    currentPage.value = response.data.current_page;
    lastPage.value = response.data.last_page;
    perPage.value = response.data.per_page;
  } catch (error) {
    console.error("Error fetching inventories:", error);
  } finally {
    loading.value = false;
  }
};

// Add this method to handle page changes
const handlePageChange = (page) => {
  currentPage.value = page;
  fetchInventories(page);
};

const closeModal = () => {
  showingModal.value = false;
  editingInventory.value = false;
  editingInventoryUuid.value = null;
  form.reset();
  form.clearErrors();
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

<style scoped>
@import "../Supplier/SupplierComponents/SupplierSharedStyles.css";
</style> 

<template>
  <AuthenticatedLayout>
    <Head title="Inventory Management" />

    <div class="page-container">
      <div class="content-container">
        <div class="header-container">
          <h1 class="page-title">Inventory</h1>
        </div>

        <div class="table-controls">
          <div class="search-container">
            <div class="search-icon">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                width="20"
                height="20"
                color="#000000"
                fill="none"
              >
                <path
                  opacity="0.4"
                  fill-rule="evenodd"
                  clip-rule="evenodd"
                  d="M16.7929 16.7929C17.1834 16.4024 17.8166 16.4024 18.2071 16.7929L22.7071 21.2929C23.0976 21.6834 23.0976 22.3166 22.7071 22.7071C22.3166 23.0976 21.6834 23.0976 21.2929 22.7071L16.7929 18.2071C16.4024 17.8166 16.4024 17.1834 16.7929 16.7929Z"
                  fill="currentColor"
                />
                <path
                  fill-rule="evenodd"
                  clip-rule="evenodd"
                  d="M1 11C1 5.47715 5.47715 1 11 1C16.5228 1 21 5.47715 21 11C21 16.5228 16.5228 21 11 21C5.47715 21 1 16.5228 1 11ZM11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3Z"
                  fill="currentColor"
                />
              </svg>
            </div>
            <input
              type="text"
              class="search-input"
              placeholder="Search inventory..."
              v-model="searchQuery"
            />
          </div>
          <button @click="showingModal = true" class="add-btn">
            <span class="plus-icon">+</span>
            Add Product
          </button>
        </div>

        <div class="table-container">
          <div class="table-wrapper">
            <table class="data-table">
              <thead class="bg-gray-50">
                <tr>
                  <th class="table-header table-header-sortable">Product</th>
                  <th class="table-header table-header-sortable">Warehouse</th>
                  <th class="table-header table-header-text">Stock Quantity</th>
                  <th class="table-header table-header-text">Cost Price</th>
                  <th class="table-header table-header-text">Selling Price</th>
                  <th class="table-header table-header-text">Status</th>
                  <th class="table-header actions-column">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="inventory in inventories"
                  :key="inventory.id"
                  class="data-row hover:bg-[#f8fafc]"
                >
                  <td class="table-cell">{{ inventory.product?.name }}</td>
                  <td class="table-cell">{{ inventory.warehouse?.name }}</td>
                  <td class="table-cell">{{ inventory.stock_quantity }}</td>
                  <td class="table-cell">KES {{ inventory.cost_price }}</td>
                  <td class="table-cell">KES {{ inventory.selling_price }}</td>
                  <td class="table-cell whitespace-nowrap">
                    <span
                      :class="
                        inventory.status === 'active'
                          ? 'status-pill-active'
                          : 'status-pill-inactive'
                      "
                    >
                      {{ inventory.status }}
                    </span>
                  </td>
                  <td class="table-cell">
                    <button
                      class="action-btn edit-btn"
                      @click="editInventoryItem(inventory)"
                    >
                      Edit
                    </button>
                    <button class="action-btn delete-btn">Delete</button>
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
        </div>
        <CustomPagination
          :current-page="currentPage"
          :last-page="lastPage"
          :per-page="perPage"
          @page-changed="handlePageChange"
          @update:per-page="handlePerPageChange"
        />

        <!-- Add Inventory Modal -->
        <div v-if="showingModal" class="modal-overlay" @click.self="closeModal">
          <div class="modal-container">
            <div class="modal-header">
              <h2>
                {{
                  editingInventory
                    ? "Edit Inventory Item"
                    : "Add New Inventory Item"
                }}
              </h2>
              <button class="close-btn" @click="closeModal">&times;</button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="createInventory">
                <!-- Product Dropdown -->
                <div class="form-group">
                  <label>Product Name <span class="required">*</span></label>
                  <select
                    class="custom-select"
                    v-model="form.product_id"
                    required
                    :disabled="editingInventory"
                  >
                    <option value="">Select Product</option>
                    <option
                      v-for="product in products"
                      :key="product.id"
                      :value="product.id"
                    >
                      {{ product.name }}
                    </option>
                  </select>
                  <InputError class="mt-2" :message="form.errors.product_id" />
                </div>

                <!-- Warehouse Dropdown -->
                <div class="form-group">
                  <label>Warehouse <span class="required">*</span></label>
                  <select
                    class="custom-select"
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
                  </select>
                  <InputError
                    class="mt-2"
                    :message="form.errors.warehouse_id"
                  />
                </div>

                <!-- Cost Price -->
                <div class="form-group">
                  <label
                    >Cost Price (KES) <span class="required">*</span></label
                  >
                  <input
                    type="number"
                    step="0.01"
                    v-model="form.cost_price"
                    required
                    class="form-input"
                  />
                  <InputError class="mt-2" :message="form.errors.cost_price" />
                </div>

                <!-- Selling Price -->
                <div class="form-group">
                  <label
                    >Selling Price (KES) <span class="required">*</span></label
                  >
                  <input
                    type="number"
                    step="0.01"
                    v-model="form.selling_price"
                    required
                    class="form-input"
                  />
                  <InputError
                    class="mt-2"
                    :message="form.errors.selling_price"
                  />
                </div>

                <!-- Stock Quantity -->
                <div class="form-group">
                  <label>Beginning Stock <span class="required">*</span></label>
                  <input
                    type="number"
                    v-model="form.stock_quantity"
                    required
                    class="form-input"
                  />
                  <InputError
                    class="mt-2"
                    :message="form.errors.stock_quantity"
                  />
                </div>

                <!-- Min Order -->
                <div class="form-group">
                  <label>Minimum Order <span class="required">*</span></label>
                  <input
                    type="number"
                    v-model="form.min_order"
                    required
                    class="form-input"
                  />
                  <InputError class="mt-2" :message="form.errors.min_order" />
                </div>

                <!-- Max Order -->
                <div class="form-group">
                  <label>Maximum Order <span class="required">*</span></label>
                  <input
                    type="number"
                    v-model="form.max_order"
                    required
                    class="form-input"
                  />
                  <InputError class="mt-2" :message="form.errors.max_order" />
                </div>

                <div class="form-group">
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

                <div class="form-group">
                  <InputLabel for="promo_start_date" value="Promo Start Date" />
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

                <div class="form-group">
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

                <div class="form-actions">
                  <button type="button" class="cancel-btn" @click="closeModal">
                    Cancel
                  </button>
                  <button
                    type="submit"
                    class="submit-btn"
                    :disabled="form.processing"
                  >
                    {{ editingInventory ? "Update" : "Create" }} Inventory Item
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>