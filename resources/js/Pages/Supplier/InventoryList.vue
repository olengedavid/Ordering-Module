<script setup>
import SupplierNavbar from "@/Components/SupplierNavbar.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import { ref, onMounted, onUnmounted } from "vue";
import axios from "axios";
import CustomPagination from "@/Components/CustomPagination.vue";
import SuccessMessage from "@/Components/SuccessMessage.vue";
import ErrorMessage from "@/Components/ErrorMessage.vue";
import { formatNumber } from "@/utils/formatters";

const currentPage = ref(1);
const perPage = ref(10);
const lastPage = ref(1);

// Add sorting state
const sortKey = ref("name");
const sortDir = ref("asc");

const showingModal = ref(false);
const page = usePage();
const user = page.props.auth.user;
const supplier_uuid = page.props.auth.user.company.uuid;

const inventories = ref([]);
const products = ref([]);
const warehouses = ref([]);
const loading = ref(true);
const successMessage = ref("");
const errorMessage = ref("");
const searchQuery = ref("");
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

const formErrors = ref({});
const isProductDropdownOpen = ref(false);
const isWarehouseDropdownOpen = ref(false);
const isStatusDropdownOpen = ref(false);
const productSearch = ref("");
const warehouseSearch = ref("");
const statusSearch = ref("");
const filteredProducts = ref([]);
const filteredWarehouses = ref([]);
const filteredStatuses = ref(["active", "inactive"]);

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
          // Flash
          successMessage.value = "Inventory Updated successfully";
          setTimeout(() => {
            successMessage.value = "";
          }, 3000);
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
        // Flash
        successMessage.value = "Inventory Created successfully";
        setTimeout(() => {
          successMessage.value = "";
        }, 3000);
      },
      onError: (errors) => {
        formErrors.value = errors;
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
    filteredProducts.value = response.data;
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
    filteredWarehouses.value = response.data;
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
        search: searchQuery.value,
        sort_key: sortKey.value,
        sort_direction: sortDir.value,
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

const getPrimaryImagePreviewPath = (product) => {
  if (!product.images) return "https://via.placeholder.com/50";

  try {
    let images;
    const parsedImages =
      typeof product.images === "string"
        ? JSON.parse(product.images)
        : product.images;

    // Handle both array and object formats
    images = Array.isArray(parsedImages)
      ? parsedImages
      : Object.values(parsedImages);

    const primaryImage = images.find((img) => img.type === "primary");

    if (primaryImage?.path) {
      return `/storage/${primaryImage.path}`;
    }
  } catch (error) {
    console.error("Error processing product images:", error);
  }

  return "https://via.placeholder.com/50";
};

const deleteInventory = async (inventoryUuid) => {
  if (!confirm("Are you sure you want to delete this inventory item?")) return;

  try {
    await axios.delete(route("supplier.inventories.destroy", inventoryUuid));

    successMessage.value = "Inventory deleted successfully";
    setTimeout(() => {
      successMessage.value = "";
    }, 3000);

    fetchInventories();
  } catch (error) {
    errorMessage.value = "Error deleting inventory";
    setTimeout(() => {
      errorMessage.value = "";
    }, 3000);
  }
};

const toggleProductDropdown = () => {
  isProductDropdownOpen.value = !isProductDropdownOpen.value;
};

const toggleWarehouseDropdown = () => {
  isWarehouseDropdownOpen.value = !isWarehouseDropdownOpen.value;
};

const toggleStatusDropdown = () => {
  isStatusDropdownOpen.value = !isStatusDropdownOpen.value;
};

const filterProducts = () => {
  filteredProducts.value = products.value.filter(product => 
    product.name.toLowerCase().includes(productSearch.value.toLowerCase())
  );
};

const filterWarehouses = () => {
  filteredWarehouses.value = warehouses.value.filter(warehouse => 
    warehouse.name.toLowerCase().includes(warehouseSearch.value.toLowerCase())
  );
};

const filterStatuses = () => {
  if (!statusSearch.value.trim()) {
    filteredStatuses.value = ["active", "inactive"];
  } else {
    const query = statusSearch.value.toLowerCase();
    filteredStatuses.value = ["active", "inactive"].filter(status => 
      status.toLowerCase().includes(query)
    );
  }
};

const selectProduct = (product) => {
  form.product_id = product.id;
  isProductDropdownOpen.value = false;
  productSearch.value = "";
};

const selectWarehouse = (warehouse) => {
  form.warehouse_id = warehouse.id;
  isWarehouseDropdownOpen.value = false;
  warehouseSearch.value = "";
};

const selectStatus = (status) => {
  form.status = status;
  isStatusDropdownOpen.value = false;
  statusSearch.value = "";
};

const closeDropdownsOutside = (event) => {
  const dropdowns = document.querySelectorAll('.custom-select-container');
  let clickedInside = false;
  
  dropdowns.forEach(dropdown => {
    if (dropdown.contains(event.target)) {
      clickedInside = true;
    }
  });

  if (!clickedInside) {
    isProductDropdownOpen.value = false;
    isWarehouseDropdownOpen.value = false;
    isStatusDropdownOpen.value = false;
  }
};

// Add sorting methods
const sortBy = (key) => {
  if (sortKey.value === key) {
    sortDir.value = sortDir.value === "asc" ? "desc" : "asc";
  } else {
    sortKey.value = key;
    sortDir.value = "asc";
  }
};

const getSortIcon = (key) => {
  if (sortKey.value !== key) return "sort-icon sort-none";
  return sortDir.value === "asc" ? "sort-icon sort-asc" : "sort-icon sort-desc";
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
  document.addEventListener('click', closeDropdownsOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', closeDropdownsOutside);
});
</script>

<template>
  <div>
    <Head title="Inventory Management" />
    <SupplierNavbar />

    <div class="page-container">
      <div class="content-container">
        <div class="message-container">
          <SuccessMessage
            v-if="successMessage"
            @close="successMessage = ''"
            v-slot="{}"
            >{{ successMessage }}</SuccessMessage
          >
          <ErrorMessage
            v-if="errorMessage"
            @close="errorMessage = ''"
            v-slot="{}"
            >{{ errorMessage }}</ErrorMessage
          >
        </div>

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
              placeholder="Search by product and warehouse name..."
              @keypress.enter="fetchInventories"
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
                  <th class="table-header table-header-sortable" @click="sortBy('product')">Product <i :class="getSortIcon('product')"></i></th>
                  <th class="table-header table-header-sortable" @click="sortBy('name')">Name <i :class="getSortIcon('name')"></i></th>
                  <th class="table-header table-header-sortable" @click="sortBy('warehouse')">Warehouse <i :class="getSortIcon('warehouse')"></i></th>
                  <th class="table-header table-header-sortable" @click="sortBy('stock_quantity')">Stock Quantity <i :class="getSortIcon('stock_quantity')"></i></th>
                  <th class="table-header table-header-sortable" @click="sortBy('cost_price')">Cost Price <i :class="getSortIcon('cost_price')"></i></th>
                  <th class="table-header table-header-sortable" @click="sortBy('selling_price')">Selling Price <i :class="getSortIcon('selling_price')"></i></th>
                  <th class="table-header table-header-sortable" @click="sortBy('status')">Status <i :class="getSortIcon('status')"></i></th>
                  <th class="table-header actions-column">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="inventory in inventories"
                  :key="inventory.id"
                  class="data-row hover:bg-[#f8fafc]"
                >
                  <td class="table-cell">
                    <div class="product-image-container">
                      <img
                        :src="getPrimaryImagePreviewPath(inventory?.product)"
                        class="product-thumbnail"
                        @error="
                          $event.target.src = 'https://via.placeholder.com/50'
                        "
                      />
                    </div>
                  </td>
                  <td class="table-cell">{{ inventory.product?.name }}</td>
                  <td class="table-cell">{{ inventory.warehouse?.name }}</td>
                  <td class="table-cell">
                    {{ formatNumber(inventory.stock_quantity) }}
                  </td>
                  <td class="table-cell">
                    KES {{ formatNumber(inventory.cost_price) }}
                  </td>
                  <td class="table-cell">
                    KES {{ formatNumber(inventory.selling_price) }}
                  </td>
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
                    <button
                      @click="deleteInventory(inventory.uuid)"
                      class="action-btn delete-btn"
                    >
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
              <div
                v-if="form.errors.warehouse_id || formErrors.warehouse_id"
                class="error-message"
              >
                {{ form.errors.warehouse_id || formErrors.warehouse_id }}
              </div>
              <form @submit.prevent="createInventory">
                <!-- Product Dropdown -->
                <div class="form-group">
                  <label>Product Name <span class="required">*</span></label>
                  <div class="custom-select-container">
                    <div 
                      class="custom-select-trigger" 
                      @click="toggleProductDropdown"
                      :class="{ 'active': isProductDropdownOpen }"
                    >
                      <span :data-has-value="!!form.product_id">{{ products.find(p => p.id === form.product_id)?.name || 'Select product' }}</span>
                      <svg 
                        class="dropdown-arrow" 
                        :class="{ 'open': isProductDropdownOpen }"
                        xmlns="http://www.w3.org/2000/svg" 
                        width="16" 
                        height="16" 
                        viewBox="0 0 24 24" 
                        fill="none" 
                        stroke="currentColor" 
                        stroke-width="2" 
                        stroke-linecap="round" 
                        stroke-linejoin="round"
                      >
                        <polyline points="6 9 12 15 18 9"></polyline>
                      </svg>
                    </div>
                    
                    <div class="custom-select-dropdown" v-show="isProductDropdownOpen">
                      <div class="search-box">
                        <input
                          type="text"
                          v-model="productSearch"
                          @input="filterProducts"
                          placeholder="Search product..."
                          class="dropdown-search"
                          @click.stop
                        >
                      </div>
                      
                      <div class="dropdown-options">
                        <div
                          v-for="product in filteredProducts"
                          :key="product.id"
                          class="dropdown-option"
                          @click="selectProduct(product)"
                        >
                          {{ product.name }}
                        </div>
                        <div v-if="filteredProducts.length === 0" class="no-results">
                          No products match your search
                        </div>
                      </div>
                    </div>
                  </div>
                  <InputError class="mt-2" :message="form.errors.product_id" />
                </div>

                <!-- Warehouse Dropdown -->
                <div class="form-group">
                  <label>Warehouse <span class="required">*</span></label>
                  <div class="custom-select-container">
                    <div 
                      class="custom-select-trigger" 
                      @click="toggleWarehouseDropdown"
                      :class="{ 'active': isWarehouseDropdownOpen }"
                    >
                      <span :data-has-value="!!form.warehouse_id">{{ warehouses.find(w => w.id === form.warehouse_id)?.name || 'Select warehouse' }}</span>
                      <svg 
                        class="dropdown-arrow" 
                        :class="{ 'open': isWarehouseDropdownOpen }"
                        xmlns="http://www.w3.org/2000/svg" 
                        width="16" 
                        height="16" 
                        viewBox="0 0 24 24" 
                        fill="none" 
                        stroke="currentColor" 
                        stroke-width="2" 
                        stroke-linecap="round" 
                        stroke-linejoin="round"
                      >
                        <polyline points="6 9 12 15 18 9"></polyline>
                      </svg>
                    </div>
                    
                    <div class="custom-select-dropdown" v-show="isWarehouseDropdownOpen">
                      <div class="search-box">
                        <input
                          type="text"
                          v-model="warehouseSearch"
                          @input="filterWarehouses"
                          placeholder="Search warehouse..."
                          class="dropdown-search"
                          @click.stop
                        >
                      </div>
                      
                      <div class="dropdown-options">
                        <div
                          v-for="warehouse in filteredWarehouses"
                          :key="warehouse.id"
                          class="dropdown-option"
                          @click="selectWarehouse(warehouse)"
                        >
                          {{ warehouse.name }}
                        </div>
                        <div v-if="filteredWarehouses.length === 0" class="no-results">
                          No warehouses match your search
                        </div>
                      </div>
                    </div>
                  </div>
                  <InputError class="mt-2" :message="form.errors.warehouse_id" />
                </div>

                <!-- Status Dropdown -->
                <div class="form-group">
                  <label>Status <span class="required">*</span></label>
                  <div class="custom-select-container">
                    <div 
                      class="custom-select-trigger" 
                      @click="toggleStatusDropdown"
                      :class="{ 'active': isStatusDropdownOpen }"
                    >
                      <span :data-has-value="!!form.status">{{ form.status || 'Select status' }}</span>
                      <svg 
                        class="dropdown-arrow" 
                        :class="{ 'open': isStatusDropdownOpen }"
                        xmlns="http://www.w3.org/2000/svg" 
                        width="16" 
                        height="16" 
                        viewBox="0 0 24 24" 
                        fill="none" 
                        stroke="currentColor" 
                        stroke-width="2" 
                        stroke-linecap="round" 
                        stroke-linejoin="round"
                      >
                        <polyline points="6 9 12 15 18 9"></polyline>
                      </svg>
                    </div>
                    
                    <div class="custom-select-dropdown" v-show="isStatusDropdownOpen">
                      <div class="search-box">
                        <input
                          type="text"
                          v-model="statusSearch"
                          @input="filterStatuses"
                          placeholder="Search status..."
                          class="dropdown-search"
                          @click.stop
                        >
                      </div>
                      
                      <div class="dropdown-options">
                        <div
                          v-for="status in filteredStatuses"
                          :key="status"
                          class="dropdown-option"
                          @click="selectStatus(status)"
                        >
                          {{ status }}
                        </div>
                        <div v-if="filteredStatuses.length === 0" class="no-results">
                          No statuses match your search
                        </div>
                      </div>
                    </div>
                  </div>
                  <InputError class="mt-2" :message="form.errors.status" />
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
  </div>
</template>

<style scoped>
@import "./SupplierComponents/SupplierSharedStyles.css";

.product-image-container {
  display: flex;
  align-items: center;
  gap: 12px;
}

.product-thumbnail {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 4px;
}

.product-name {
  font-weight: 500;
}

.table-header-sortable {
  cursor: pointer;
  user-select: none;
}

.sort-icon {
  display: inline-block;
  width: 16px;
  height: 16px;
  background-repeat: no-repeat;
  background-position: center;
  margin-left: 4px;
  vertical-align: text-bottom;
}

.sort-none::after {
  content: "⇵";
  opacity: 0.3;
  font-size: 12px;
}

.sort-asc::after {
  content: "↑";
  color: #2563eb;
  font-size: 12px;
}

.sort-desc::after {
  content: "↓";
  color: #2563eb;
  font-size: 12px;
}
</style>