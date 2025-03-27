<template>
  <AuthenticatedLayout>
    <Head title="Supplier Warehouses" />

    <div class="page-container">
      <div class="content-container">
        <div class="message-container">
          <SuccessMessage
            v-if="successMessage"
            @close="successMessage = ''"
            v-slot="{}"
            >{{ successMessage }}</SuccessMessage
          >
          <ErrorMessage v-if="errorMessage" @close="errorMessage = ''" v-slot>{{
            errorMessage
          }}</ErrorMessage>
        </div>

        <!-- Add tabs here -->
        <div class="tabs-container">
          <ul class="tabs">
            <li v-for="tab in tabs" 
                :key="tab.id" 
                :class="['tab', { active: activeTab === tab.id }]"
                @click="setActiveTab(tab.id)">
              {{ tab.name }}
              <span class="tab-count" v-if="tab.count">{{ tab.count }}</span>
            </li>
          </ul>
        </div>

        <!-- Conditional rendering based on active tab -->
        <div v-if="activeTab === 'warehouses'">
          <div class="header-container">
            <h1 class="page-title">Warehouses</h1>
          </div>
          <!-- Table Controls with Search Bar -->
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
                placeholder="Search by name..."
                v-model="searchQuery"
                @keypress.enter="fetchSupplierWarehouses"
              />
            </div>
            <button @click="openAddWarehouseModal" class="add-btn">
              <span class="plus-icon">+</span>
              Add Warehouse
            </button>
          </div>

          <!-- Warehouses Table -->
          <div class="table-container">
            <div class="table-wrapper">
              <table v-if="filteredWarehouses.length" class="data-table">
                <thead>
                  <tr>
                    <th @click="sortBy('name')" class="sortable">
                      Warehouse Name <i :class="getSortIcon('name')"></i>
                    </th>
                    <th @click="sortBy('contactPerson')" class="sortable">
                      Contact Person <i :class="getSortIcon('contactPerson')"></i>
                    </th>
                    <th @click="sortBy('email')" class="sortable">
                      Email <i :class="getSortIcon('email')"></i>
                    </th>
                    <th @click="sortBy('phone')" class="sortable">
                      Phone <i :class="getSortIcon('phone')"></i>
                    </th>
                    <th @click="sortBy('address')" class="sortable">
                      Address <i :class="getSortIcon('address')"></i>
                    </th>
                    <th @click="sortBy('kraPin')" class="sortable">
                      KRA PIN <i :class="getSortIcon('kraPin')"></i>
                    </th>
                    <th @click="sortBy('country')" class="sortable">
                      Country <i :class="getSortIcon('country')"></i>
                    </th>
                    <th @click="sortBy('region')" class="sortable">
                      Region <i :class="getSortIcon('region')"></i>
                    </th>
                    <th @click="sortBy('gps')" class="sortable">
                      GPS <i :class="getSortIcon('gps')"></i>
                    </th>
                    <th @click="sortBy('status')" class="sortable">
                      Status <i :class="getSortIcon('status')"></i>
                    </th>
                    <th class="actions-column">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="warehouse in warehouses"
                    :key="warehouse.id"
                    class="data-row"
                  >
                    <td>{{ warehouse.name }}</td>
                    <td>{{ warehouse.contact_person }}</td>
                    <td>{{ warehouse.email }}</td>
                    <td>{{ warehouse.phone_number }}</td>
                    <td>{{ warehouse.address }}</td>
                    <td>{{ warehouse.krapin }}</td>
                    <td>{{ warehouse.country || "Kenya" }}</td>
                    <td>{{ warehouse.region || warehouse.location }}</td>
                    <td>{{ warehouse.gps || "--" }}</td>
                    <td>
                      <span
                        :class="[
                          'status-pill',
                          warehouse.status === 'active'
                            ? 'status-active'
                            : 'status-inactive',
                        ]"
                      >
                        {{
                          warehouse.status.charAt(0).toUpperCase() +
                          warehouse.status.slice(1)
                        }}
                      </span>
                    </td>
                    <td class="actions-column">
                      <button
                        @click="editWarehouse(warehouse)"
                        class="action-btn edit-btn"
                      >
                        Edit
                      </button>
                      <button
                        @click="deleteWarehouse(warehouse.uuid)"
                        class="action-btn delete-btn"
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
              <tr v-if="filteredWarehouses.length === 0">
                <td colspan="11" class="empty-state">No warehouses found</td>
              </tr>
            </div>
          </div>

          <!-- Pagination Controls -->
          <CustomPagination
            :current-page="currentPage"
            :last-page="lastPage"
            :per-page="perPage"
            @page-changed="handlePageChange"
            @update:per-page="handlePerPageChange"
          />
        </div>

        <div v-else-if="activeTab === 'regions'">
          <!-- Delivery regions content -->
          <div class="header-container">
            <h1 class="page-title">Delivery Regions</h1>
          </div>
          
          <!-- Table Controls with Search Bar -->
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
                placeholder="Search by region..."
                v-model="deliveryRegionSearch"
                @keypress.enter="fetchDeliveryRegions"
              />
            </div>
            <button @click="openAddDeliveryRegionModal" class="add-btn">
              <span class="plus-icon">+</span>
              Add Delivery Region
            </button>
          </div>

          <!-- Delivery Regions Table -->
          <div class="table-container">
            <div class="table-wrapper">
              <table v-if="paginatedDeliveryRegions.length" class="data-table">
                <thead>
                  <tr>
                    <th @click="sortDeliveryRegionsBy('warehouseName')" class="sortable">
                      Warehouse Name <i :class="getDeliveryRegionSortIcon('warehouseName')"></i>
                    </th>
                    <th @click="sortDeliveryRegionsBy('deliverTo')" class="sortable">
                      Deliver To <i :class="getDeliveryRegionSortIcon('deliverTo')"></i>
                    </th>
                    <th @click="sortDeliveryRegionsBy('deliveryFee')" class="sortable">
                      Delivery Fee <i :class="getDeliveryRegionSortIcon('deliveryFee')"></i>
                    </th>
                    <th @click="sortDeliveryRegionsBy('status')" class="sortable">
                      Status <i :class="getDeliveryRegionSortIcon('status')"></i>
                    </th>
                    <th class="actions-column">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="region in paginatedDeliveryRegions"
                    :key="region.id"
                    class="data-row"
                  >
                    <td>{{ getWarehouseName(region.warehouse_id) }}</td>
                    <td>{{ region.region }}</td>
                    <td>{{ formatCurrency(region.delivery_fee) }}</td>
                    <td>
                      <span
                        :class="[
                          'status-pill',
                          region.status === 'active'
                            ? 'status-active'
                            : 'status-inactive',
                        ]"
                      >
                        {{ capitalizeFirstLetter(region.status) }}
                      </span>
                    </td>
                    <td class="actions-column">
                      <button
                        @click="editDeliveryRegion(region)"
                        class="action-btn edit-btn"
                      >
                        Edit
                      </button>
                      <button
                        @click="deleteDeliveryRegion(region.uuid)"
                        class="action-btn delete-btn"
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
              <tr v-if="paginatedDeliveryRegions.length === 0">
                <td colspan="5" class="empty-state">No delivery regions found</td>
              </tr>
            </div>
          </div>

          <!-- Update Pagination Controls for Delivery Regions -->
          <CustomPagination
            :current-page="deliveryRegionCurrentPage"
            :last-page="deliveryRegionTotalPages"
            :per-page="deliveryRegionPerPage"
            @page-changed="handleDeliveryRegionPageChange"
            @update:per-page="handleDeliveryRegionPerPageChange"
          />

          <!-- Delivery Region Modal -->
          <div
            v-if="showDeliveryRegionModal"
            class="modal-overlay"
            @click.self="closeDeliveryRegionModal"
          >
            <div class="modal-container">
              <div class="modal-header">
                <h2>
                  {{ editingDeliveryRegion ? "Edit Delivery Region" : "Add New Delivery Region" }}
                </h2>
                <button class="close-btn" @click="closeDeliveryRegionModal">
                  &times;
                </button>
              </div>
              <div class="modal-body">
                <form @submit.prevent="saveDeliveryRegion">
                  <!-- Warehouse Name Dropdown -->
                  <div class="form-group">
                    <label for="warehouseName">Warehouse Name <span class="required">*</span></label>
                    <div class="custom-select-container">
                      <div 
                        class="custom-select-trigger" 
                        @click="toggleWarehouseDropdown"
                        :class="{ 'active': isWarehouseDropdownOpen }"
                      >
                        <span :data-has-value="!!deliveryRegionForm.warehouse_id">{{ getWarehouseName(deliveryRegionForm.warehouse_id) || 'Select warehouse' }}</span>
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
                  </div>
                  
                  <!-- Region Input for Deliver To -->
                  <div class="form-group">
                    <label for="deliverTo">Deliver To <span class="required">*</span></label>
                    <input type="text" id="deliverTo" v-model="deliveryRegionForm.region" required placeholder="Enter region name">
                  </div>
                  
                  <div class="form-group">
                    <label for="deliveryFee">Delivery Fee <span class="required">*</span></label>
                    <input type="text" id="deliveryFee" v-model="deliveryRegionForm.delivery_fee" @input="formatDeliveryFee" required placeholder="Enter delivery fee">
                  </div>
                  
                  <div class="form-group">
                    <label for="status">Status <span class="required">*</span></label>
                    <div class="custom-select-container">
                      <div 
                        class="custom-select-trigger" 
                        @click="toggleStatusDropdown"
                        :class="{ 'active': isStatusDropdownOpen }"
                      >
                        <span :data-has-value="!!deliveryRegionForm.status">{{ capitalizeFirstLetter(deliveryRegionForm.status) || 'Select status' }}</span>
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
                            {{ capitalizeFirstLetter(status) }}
                          </div>
                          <div v-if="filteredStatuses.length === 0" class="no-results">
                            No statuses match your search
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-actions">
                    <button
                      type="button"
                      class="cancel-btn"
                      @click="closeDeliveryRegionModal"
                    >
                      Cancel
                    </button>
                    <button
                      type="submit"
                      class="submit-btn"
                      :disabled="deliveryRegionForm.processing"
                    >
                      {{ editingDeliveryRegion ? "Update Delivery Region" : "Save Delivery Region" }}
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Add/Edit Warehouse Modal -->
      <div
        v-if="showWarehouseModal"
        class="modal-overlay"
        @click.self="closeWarehouseModal"
      >
        <div class="modal-container">
          <div class="modal-header">
            <h2>
              {{ editingWarehouse ? "Edit Warehouse" : "Add New Warehouse" }}
            </h2>
            <button class="close-btn" @click="closeWarehouseModal">
              &times;
            </button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveWarehouse">
              <div class="form-group">
                <label for="warehouseName"
                  >Warehouse Name <span class="required">*</span></label
                >
                <input
                  type="text"
                  id="warehouseName"
                  v-model="form.name"
                  required
                  placeholder="Enter warehouse name"
                />
                <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                  {{ form.errors.name }}
                </div>
              </div>
              <div class="form-group">
                <label for="contactPerson"
                  >Contact Person <span class="required">*</span></label
                >
                <input
                  type="text"
                  id="contactPerson"
                  v-model="form.contact_person"
                  required
                  placeholder="Enter contact person"
                />
                <div
                  v-if="form.errors.contact_person"
                  class="text-red-500 text-sm mt-1"
                >
                  {{ form.errors.contact_person }}
                </div>
              </div>
              <div class="form-group">
                <label for="email">Email <span class="required">*</span></label>
                <input
                  type="email"
                  id="email"
                  v-model="form.email"
                  required
                  placeholder="Enter email address"
                />
                <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">
                  {{ form.errors.email }}
                </div>
              </div>
              <div class="form-group">
                <label for="phone">Phone <span class="required">*</span></label>
                <input
                  type="text"
                  id="phone"
                  v-model="form.phone_number"
                  required
                  placeholder="Enter phone number"
                />
                <div
                  v-if="form.errors.phone_number"
                  class="text-red-500 text-sm mt-1"
                >
                  {{ form.errors.phone_number }}
                </div>
              </div>
              <div class="form-group">
                <label for="address"
                  >Address <span class="required">*</span></label
                >
                <input
                  type="text"
                  id="address"
                  v-model="form.address"
                  required
                  placeholder="Enter physical address"
                />
                <div
                  v-if="form.errors.address"
                  class="text-red-500 text-sm mt-1"
                >
                  {{ form.errors.address }}
                </div>
              </div>
              <div class="form-group">
                <label for="kraPin"
                  >KRA PIN <span class="required">*</span></label
                >
                <input
                  type="text"
                  id="kraPin"
                  v-model="form.krapin"
                  required
                  placeholder="Enter KRA PIN"
                />
                <div
                  v-if="form.errors.krapin"
                  class="text-red-500 text-sm mt-1"
                >
                  {{ form.errors.krapin }}
                </div>
              </div>

              <!-- Country Dropdown -->
              <div class="form-group">
                <label for="country">Country <span class="required">*</span></label>
                <div class="custom-select-container country-select-container">
                  <div 
                    class="custom-select-trigger country-select-trigger" 
                    @click="toggleCountryDropdown"
                    :class="{ active: isCountryDropdownOpen }"
                  >
                    <span>{{ form.country || "Select country" }}</span>
                    <svg 
                      class="dropdown-arrow" 
                      :class="{ open: isCountryDropdownOpen }"
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
                  
                  <div class="custom-select-dropdown country-select-dropdown" v-show="isCountryDropdownOpen">
                    <div class="search-box">
                      <input
                        type="text"
                        v-model="countrySearch"
                        @input="filterCountries"
                        placeholder="Search country..."
                        class="dropdown-search"
                        @click.stop
                      >
                    </div>
                    
                    <div class="dropdown-options">
                      <div
                        v-for="country in filteredCountries"
                        :key="country"
                        class="dropdown-option"
                        @click="selectCountry(country)"
                      >
                        {{ country }}
                      </div>
                      <div v-if="filteredCountries.length === 0" class="no-results">
                        No countries match your search
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Region Dropdown -->
              <div class="form-group">
                <label for="region">Region <span class="required">*</span></label>
                <div class="custom-select-container region-select-container">
                  <div 
                    class="custom-select-trigger region-select-trigger" 
                    @click="toggleRegionDropdown"
                    :class="{ active: isRegionDropdownOpen }"
                  >
                    <span>{{ form.region || "Select region" }}</span>
                    <svg 
                      class="dropdown-arrow" 
                      :class="{ open: isRegionDropdownOpen }"
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
                  
                  <div class="custom-select-dropdown region-select-dropdown" v-show="isRegionDropdownOpen">
                    <div class="search-box">
                      <input
                        type="text"
                        v-model="regionSearch"
                        @input="filterRegions"
                        placeholder="Search region..."
                        class="dropdown-search"
                        @click.stop
                      >
                    </div>
                    
                    <div class="dropdown-options">
                      <div v-if="loadingRegions" class="loading-state">
                        Loading regions...
                      </div>
                      <template v-else>
                        <div
                          v-for="region in filteredRegions"
                          :key="region"
                          class="dropdown-option"
                          @click="selectRegion(region)"
                        >
                          {{ region }}
                        </div>
                        <div v-if="filteredRegions.length === 0" class="no-results">
                          No regions match your search
                        </div>
                      </template>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="gps">GPS Coordinates</label>
                <input
                  type="text"
                  id="gps"
                  v-model="form.gps"
                  placeholder="Enter GPS coordinates (latitude,longitude)"
                />
              </div>

              <div class="form-group">
                <label for="status"
                  >Status <span class="required">*</span></label
                >
                <select id="status" v-model="form.status" required>
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>
              </div>

              <div class="form-actions">
                <button
                  type="button"
                  class="cancel-btn"
                  @click="closeWarehouseModal"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  class="submit-btn"
                  :disabled="form.processing"
                >
                  {{ editingWarehouse ? "Update Warehouse" : "Save Warehouse" }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from "vue";
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import SuccessMessage from "@/Components/SuccessMessage.vue";
import ErrorMessage from "@/Components/ErrorMessage.vue";
import CustomPagination from "@/Components/CustomPagination.vue";

// const props = defineProps({
//   warehouses: {
//     type: Array,
//     default: () => [],
//   },
// });
const warehouses = ref([]);
const successMessage = ref("");
const errorMessage = ref("");

const page = usePage();
const user = page.props.auth.user;

// Table state
const searchQuery = ref("");
const sortKey = ref("name");
const sortDir = ref("asc");

// Pagination
const currentPage = ref(1);
const perPage = ref(10);
const lastPage = ref(1);

// Modal and form
const showWarehouseModal = ref(false);
const editingWarehouse = ref(false);
const editingWarehouseId = ref(null);

// Dropdown state
const isCountryDropdownOpen = ref(false);
const countrySearch = ref("");
const countries = ref([
  "Kenya",
  "Uganda",
  "Tanzania",
  "Rwanda",
  "Burundi",
  "Ethiopia",
  "South Sudan",
]);
const filteredCountries = ref([...countries.value]);

const isRegionDropdownOpen = ref(false);
const regionSearch = ref("");
const regions = ref([]);
const loadingRegions = ref(false);
const filteredRegions = ref([...regions.value]);
const supplier_uuid = page.props.auth.user.company.uuid;
// Form for warehouse
const form = useForm({
  name: "",
  address: "",
  email: "",
  krapin: "",
  contact_person: "",
  phone_number: "",
  status: "active",
  country: "Kenya",
  region: "",
  gps: "",
  company_id: user.company_id,
  created_by: user.id,
  uuid: "",
});

// Helper function to get warehouse name by id
const getWarehouseName = (warehouseId) => {
  const warehouse = warehouses.value.find(w => w.id === warehouseId);
  return warehouse ? warehouse.name : '';
};

// Computed properties for filtering and sorting
const filteredWarehouses = computed(() => {
  if (searchQuery.value.trim() === "") {
    return warehouses.value;
  }

  const query = searchQuery.value.toLowerCase();
  return warehouses.value.filter((warehouse) => {
    return Object.values(warehouse).some((value) => {
      if (value === null || value === undefined) return false;
      return String(value).toLowerCase().includes(query);
    });
  });
});

const sortedWarehouses = computed(() => {
  const warehouses = [...filteredWarehouses.value];
  warehouses.sort((a, b) => {
    let modifier = sortDir.value === "asc" ? 1 : -1;

    // Map frontend keys to backend keys
    const keyMap = {
      contactPerson: "contact_person",
      phone: "phone_number",
      kraPin: "krapin",
    };

    let aKey = keyMap[sortKey.value] || sortKey.value;
    let bKey = keyMap[sortKey.value] || sortKey.value;

    let aValue = a[aKey];
    let bValue = b[bKey];

    // Handle undefined or null values
    if (aValue === undefined || aValue === null) {
      aValue = "";
    }
    if (bValue === undefined || bValue === null) {
      bValue = "";
    }

    if (typeof aValue === "number" && typeof bValue === "number") {
      return aValue < bValue ? -1 * modifier : 1 * modifier;
    } else {
      return aValue.toString().localeCompare(bValue.toString()) * modifier;
    }
  });

  return warehouses;
});

const totalPages = computed(() => {
  return Math.ceil(filteredWarehouses.value.length / perPage.value);
});

// const paginatedWarehouses = computed(() => {
//   const startIndex = (currentPage.value - 1) * perPage.value;
//   const endIndex = startIndex + perPage.value;
//   return sortedWarehouses.value.slice(startIndex, endIndex);
// });

// Event handlers
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

const openAddWarehouseModal = () => {
  editingWarehouse.value = false;
  editingWarehouseId.value = null;
  form.reset();
  form.clearErrors();
  showWarehouseModal.value = true;
  isCountryDropdownOpen.value = false;
  isRegionDropdownOpen.value = false;
};

const closeWarehouseModal = () => {
  showWarehouseModal.value = false;
  form.reset();
  form.clearErrors();
};

const editWarehouse = (warehouse) => {
  editingWarehouse.value = true;
  editingWarehouseId.value = warehouse.id;

  // Map warehouse fields to form
  form.name = warehouse.name;
  form.contact_person = warehouse.contact_person;
  form.email = warehouse.email;
  form.phone_number = warehouse.phone_number;
  form.address = warehouse.address;
  form.krapin = warehouse.krapin;
  form.country = warehouse.country || "Kenya";
  form.region = warehouse.region || warehouse.location;
  form.gps = warehouse.gps || "";
  form.status = warehouse.status;
  form.uuid = warehouse.uuid;

  showWarehouseModal.value = true;
  isCountryDropdownOpen.value = false;
  isRegionDropdownOpen.value = false;
};

const deleteWarehouse = (warehouseUuid) => {
  if (confirm("Are you sure you want to delete this warehouse?")) {
    useForm().delete(route("supplierwarehouse.delete", warehouseUuid), {
      preserveScroll: true,
      onSuccess: () => {
        // Message will be handled by the inertia success flash message
        fetchSupplierWarehouses();
        successMessage.value = "Warehouse deleted successfully";
        setTimeout(() => {
          successMessage.value = "";
        }, 3000);
      },
      onError: () => {
        errorMessage.value = "Error deleting warehouse";
        setTimeout(() => {
          errorMessage.value = "";
        }, 3000);
      },
    });
  }
};

const saveWarehouse = () => {
  if (editingWarehouse.value) {
    form.put(route("supplierwarehouse.update", editingWarehouseId.value), {
      preserveScroll: true,
      onSuccess: () => {
        closeWarehouseModal();
        fetchSupplierWarehouses();
        successMessage.value = "Warehouse updated successfully";
        setTimeout(() => {
          successMessage.value = "";
        }, 3000);
      },
      onError: () => {
        errorMessage.value = "Error updating warehouse";
        setTimeout(() => {
          errorMessage.value = "";
        }, 3000);
      },
    });
  } else {
    form.post(route("supplierwarehouse.create"), {
      preserveScroll: true,
      onSuccess: () => {
        closeWarehouseModal();
        fetchSupplierWarehouses();
        successMessage.value = "Warehouse added successfully";
        setTimeout(() => {
          successMessage.value = "";
        }, 3000);
      },
      onError: () => {
        errorMessage.value = "Error adding warehouse";
        setTimeout(() => {
          errorMessage.value = "";
        }, 3000);
      },
    });
  }
};

const fetchSupplierWarehouses = async () => {
  try {
    const response = await axios.get(
      route("supplier.warehouses.paginate", {
        uuid: supplier_uuid,
        page: currentPage.value,
        pageSize: perPage.value,
        search: searchQuery.value.trim(),
      })
    );
    warehouses.value = response.data.data;
    currentPage.value = response.data.current_page;
    lastPage.value = response.data.last_page;
    perPage.value = response.data.per_page;
  } catch (error) {
    console.error("Error fetching warehouses:", error);
  }
};

// Dropdown methods
const toggleCountryDropdown = () => {
  isCountryDropdownOpen.value = !isCountryDropdownOpen.value;

  if (isCountryDropdownOpen.value) {
    // Reset search when opening
    countrySearch.value = "";
    filteredCountries.value = [...countries.value];

    // Close the other dropdown if open
    isRegionDropdownOpen.value = false;

    // Calculate dropdown position
    nextTick(() => {
      const trigger = document.querySelector(".country-select-trigger");
      const dropdown = document.querySelector(".country-select-dropdown");

      if (!trigger || !dropdown) return;

      const triggerRect = trigger.getBoundingClientRect();
      const viewportHeight = window.innerHeight;

      const spaceBelow = viewportHeight - triggerRect.bottom;
      const dropdownHeight = Math.min(
        250,
        filteredCountries.value.length * 36 + 70
      );

      if (spaceBelow < dropdownHeight) {
        dropdown.classList.add("dropdown-top");
      } else {
        dropdown.classList.remove("dropdown-top");
      }
    });
  }
};

const closeCountryDropdownOutside = (event) => {
  const dropdown = document.querySelector(".country-select-container");
  if (dropdown && !dropdown.contains(event.target)) {
    isCountryDropdownOpen.value = false;
  }
};

const filterCountries = () => {
  if (countrySearch.value.trim() === "") {
    filteredCountries.value = [...countries.value];
  } else {
    const query = countrySearch.value.toLowerCase();
    filteredCountries.value = countries.value.filter((country) =>
      country.toLowerCase().includes(query)
    );
  }
};

const selectCountry = (country) => {
  form.country = country;
  isCountryDropdownOpen.value = false;
  // Reset and fetch new regions when country changes
  form.region = "";
  searchRegions(country);
};

const toggleRegionDropdown = () => {
  isRegionDropdownOpen.value = !isRegionDropdownOpen.value;

  if (isRegionDropdownOpen.value) {
    // Reset search when opening
    regionSearch.value = "";
    filteredRegions.value = [...regions.value];

    // Close the other dropdown if open
    isCountryDropdownOpen.value = false;

    // Calculate dropdown position
    nextTick(() => {
      const trigger = document.querySelector(".region-select-trigger");
      const dropdown = document.querySelector(".region-select-dropdown");

      if (!trigger || !dropdown) return;

      const triggerRect = trigger.getBoundingClientRect();
      const viewportHeight = window.innerHeight;

      const spaceBelow = viewportHeight - triggerRect.bottom;
      const dropdownHeight = Math.min(
        250,
        filteredRegions.value.length * 36 + 70
      );

      if (spaceBelow < dropdownHeight) {
        dropdown.classList.add("dropdown-top");
      } else {
        dropdown.classList.remove("dropdown-top");
      }
    });
  }
};

const closeRegionDropdownOutside = (event) => {
  const dropdown = document.querySelector(".region-select-container");
  if (dropdown && !dropdown.contains(event.target)) {
    isRegionDropdownOpen.value = false;
  }
};

const filterRegions = () => {
  if (regionSearch.value.trim() === "") {
    filteredRegions.value = [...regions.value];
  } else {
    const query = regionSearch.value.toLowerCase();
    filteredRegions.value = regions.value.filter((region) =>
      region.toLowerCase().includes(query)
    );
  }
};

const selectRegion = (region) => {
  form.region = region;
  isRegionDropdownOpen.value = false;
};

// Pagination methods
const handlePageChange = (page) => {
  currentPage.value = page;
  fetchSupplierWarehouses();
};

const handlePerPageChange = (newPerPage) => {
  perPage.value = newPerPage;
  fetchSupplierWarehouses();
};

const searchRegions = async (country) => {
  if (!country) return;

  loadingRegions.value = true;
  try {
    const response = await axios.get(route("admin.regions.search"), {
      params: {
        country: country,
        per_page: 10,
      },
    });
    regions.value = response.data.data.map((region) => region.region);
    filteredRegions.value = [...regions.value];
  } catch (error) {
    console.error("Error fetching regions:", error);
    regions.value = [];
    filteredRegions.value = [];
  } finally {
    loadingRegions.value = false;
  }
};

// Lifecycle hooks
onMounted(() => {
  // Initialize filtered countries and regions
  searchRegions("Kenya");
  fetchSupplierWarehouses();
  filteredCountries.value = [...countries.value];
  filteredRegions.value = [...regions.value];

  // Add click outside listener for dropdowns
  document.addEventListener("click", closeCountryDropdownOutside);
  document.addEventListener("click", closeRegionDropdownOutside);
});

onBeforeUnmount(() => {
  // Clean up the event listeners
  document.removeEventListener("click", closeCountryDropdownOutside);
  document.removeEventListener("click", closeRegionDropdownOutside);
});

// Add this with your other refs
const activeTab = ref('warehouses');
const tabs = ref([
  { id: 'warehouses', name: 'Warehouses', count: 0 },
  { id: 'regions', name: 'Delivery Regions', count: 0 }
]);

// Add this with your other methods
const setActiveTab = (tabId) => {
  activeTab.value = tabId;
};

// Update your onMounted to include the tab count update
onMounted(() => {
  updateTabCounts();
  // ... your existing onMounted code ...
});

// Delivery Regions state
const deliveryRegions = ref([]);
const deliveryRegionSearch = ref("");
const deliveryRegionSortKey = ref("warehouseName");
const deliveryRegionSortDir = ref("asc");
const showDeliveryRegionModal = ref(false);
const editingDeliveryRegion = ref(false);
const editingDeliveryRegionId = ref(null);
const regionUuid = ref("");
const isWarehouseDropdownOpen = ref(false);
const isStatusDropdownOpen = ref(false);
const warehouseSearch = ref("");
const statusSearch = ref("");
const filteredStatuses = ref(["active", "inactive"]);

// Helper function to capitalize first letter
const capitalizeFirstLetter = (string) => {
  if (!string) return '';
  return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
};

// Form for delivery region
const deliveryRegionForm = useForm({
  warehouse_id: "",
  region: "",
  delivery_fee: "",
  status: "active",
  company_id: user.company_id,
  created_by: user.id,
  uuid: "",
});

// Computed properties for delivery regions
const filteredDeliveryRegions = computed(() => {
  if (!deliveryRegionSearch.value.trim()) {
    return deliveryRegions.value;
  }

  const query = deliveryRegionSearch.value.toLowerCase();
  return deliveryRegions.value.filter((region) => {
    const warehouseName = getWarehouseName(region.warehouse_id).toLowerCase();
    const regionName = region.region.toLowerCase();
    const deliveryFee = String(region.delivery_fee).toLowerCase();
    const status = region.status.toLowerCase();

    return (
      warehouseName.includes(query) ||
      regionName.includes(query) ||
      deliveryFee.includes(query) ||
      status.includes(query)
    );
  });
});

const sortedDeliveryRegions = computed(() => {
  if (!filteredDeliveryRegions.value || !Array.isArray(filteredDeliveryRegions.value)) {
    return [];
  }
  
  const regions = [...filteredDeliveryRegions.value];
  regions.sort((a, b) => {
    let modifier = deliveryRegionSortDir.value === "asc" ? 1 : -1;
    
    // Map the sortKey to the actual property names in the data
    let aKey, bKey;
    
    if (deliveryRegionSortKey.value === "warehouseName") {
      aKey = getWarehouseName(a?.warehouse_id);
      bKey = getWarehouseName(b?.warehouse_id);
    } else if (deliveryRegionSortKey.value === "deliverTo") {
      aKey = a?.region || "";
      bKey = b?.region || "";
    } else if (deliveryRegionSortKey.value === "deliveryFee") {
      aKey = a?.delivery_fee || 0;
      bKey = b?.delivery_fee || 0;
    } else if (deliveryRegionSortKey.value === "status") {
      aKey = a?.status || "";
      bKey = b?.status || "";
    } else {
      aKey = a?.[deliveryRegionSortKey.value];
      bKey = b?.[deliveryRegionSortKey.value];
    }
    
    // Handle undefined or null values
    if (aKey === undefined || aKey === null) {
      aKey = "";
    }
    if (bKey === undefined || bKey === null) {
      bKey = "";
    }
    
    if (typeof aKey === "number" && typeof bKey === "number") {
      return aKey < bKey ? -1 * modifier : 1 * modifier;
    } else {
      // Convert to strings for comparison
      aKey = String(aKey).toLowerCase();
      bKey = String(bKey).toLowerCase();
      return aKey < bKey ? -1 * modifier : 1 * modifier;
    }
  });
  return regions;
});

// Add computed property for paginated delivery regions
const paginatedDeliveryRegions = computed(() => {
  const startIndex = (deliveryRegionCurrentPage.value - 1) * deliveryRegionPerPage.value;
  const endIndex = startIndex + deliveryRegionPerPage.value;
  return sortedDeliveryRegions.value.slice(startIndex, endIndex);
});

// Update the total pages computed property for delivery regions
const deliveryRegionTotalPages = computed(() => {
  return Math.ceil(sortedDeliveryRegions.value.length / deliveryRegionPerPage.value);
});

// Delivery Region methods
const fetchDeliveryRegions = async () => {
  try {
    const response = await axios.get(
      route("supplier.delivery-regions.index", {
        company_id: user.company_id,
      })
    );
    deliveryRegions.value = response.data || [];
  } catch (error) {
    console.error("Error fetching delivery regions:", error);
    deliveryRegions.value = [];
  }
};

const sortDeliveryRegionsBy = (key) => {
  if (deliveryRegionSortKey.value === key) {
    deliveryRegionSortDir.value = deliveryRegionSortDir.value === "asc" ? "desc" : "asc";
  } else {
    deliveryRegionSortKey.value = key;
    deliveryRegionSortDir.value = "asc";
  }
};

const getDeliveryRegionSortIcon = (key) => {
  if (deliveryRegionSortKey.value !== key) return "sort-icon sort-none";
  return deliveryRegionSortDir.value === "asc" ? "sort-icon sort-asc" : "sort-icon sort-desc";
};

const openAddDeliveryRegionModal = () => {
  editingDeliveryRegion.value = false;
  editingDeliveryRegionId.value = null;
  deliveryRegionForm.reset();
  deliveryRegionForm.warehouse_id = "";
  deliveryRegionForm.company_id = user.company_id;
  deliveryRegionForm.created_by = user.id;
  showDeliveryRegionModal.value = true;
  isWarehouseDropdownOpen.value = false;
};

const closeDeliveryRegionModal = () => {
  showDeliveryRegionModal.value = false;
  deliveryRegionForm.reset();
};

const editDeliveryRegion = (region) => {
  editingDeliveryRegion.value = true;
  editingDeliveryRegionId.value = region.id;
  regionUuid.value = region.uuid;
  
  deliveryRegionForm.reset();
  deliveryRegionForm.warehouse_id = region.warehouse_id;
  deliveryRegionForm.region = region.region;
  deliveryRegionForm.delivery_fee = region.delivery_fee;
  deliveryRegionForm.status = region.status;
  deliveryRegionForm.company_id = user.company_id;
  
  showDeliveryRegionModal.value = true;
};

const deleteDeliveryRegion = async (regionUuid) => {
  if (!confirm("Are you sure you want to delete this delivery region?")) {
    return;
  }
  
  try {
    await axios.delete(route("supplier.delivery-regions.destroy", regionUuid));
    successMessage.value = "Delivery region deleted successfully";
    fetchDeliveryRegions();
    setTimeout(() => {
      successMessage.value = "";
    }, 3000);
  } catch (error) {
    errorMessage.value = "Error deleting delivery region";
    console.error("Error deleting delivery region:", error);
    setTimeout(() => {
      errorMessage.value = "";
    }, 3000);
  }
};

const saveDeliveryRegion = () => {
  if (editingDeliveryRegion.value) {
    deliveryRegionForm.put(route("supplier.delivery-regions.update", regionUuid.value), {
      preserveScroll: true,
      onSuccess: () => {
        successMessage.value = "Delivery region updated successfully";
        closeDeliveryRegionModal();
        fetchDeliveryRegions();
        setTimeout(() => {
          successMessage.value = "";
        }, 3000);
      },
      onError: () => {
        errorMessage.value = "Error updating delivery region";
        setTimeout(() => {
          errorMessage.value = "";
        }, 3000);
      },
    });
  } else {
    deliveryRegionForm.post(route("supplier.delivery-regions.store"), {
      preserveScroll: true,
      onSuccess: () => {
        successMessage.value = "Delivery region added successfully";
        closeDeliveryRegionModal();
        fetchDeliveryRegions();
        setTimeout(() => {
          successMessage.value = "";
        }, 3000);
      },
      onError: () => {
        errorMessage.value = "Error creating delivery region";
        setTimeout(() => {
          errorMessage.value = "";
        }, 3000);
      },
    });
  }
};

// Warehouse dropdown methods for delivery regions
const toggleWarehouseDropdown = () => {
  isWarehouseDropdownOpen.value = !isWarehouseDropdownOpen.value;
  if (isWarehouseDropdownOpen.value) {
    warehouseSearch.value = "";
    filteredWarehouses.value = [...warehouses.value];
  }
};

const filterWarehouses = () => {
  if (!warehouseSearch.value.trim()) {
    filteredWarehouses.value = [...warehouses.value];
  } else {
    const query = warehouseSearch.value.toLowerCase();
    filteredWarehouses.value = warehouses.value.filter((warehouse) =>
      warehouse.name.toLowerCase().includes(query)
    );
  }
};

const selectWarehouse = (warehouse) => {
  if (warehouse && warehouse.id) {
    deliveryRegionForm.warehouse_id = warehouse.id;
    isWarehouseDropdownOpen.value = false;
    warehouseSearch.value = "";
  }
};

// Status dropdown methods for delivery regions
const toggleStatusDropdown = () => {
  isStatusDropdownOpen.value = !isStatusDropdownOpen.value;
  if (isStatusDropdownOpen.value) {
    statusSearch.value = "";
    filteredStatuses.value = ["active", "inactive"];
  }
};

const filterStatuses = () => {
  if (!statusSearch.value.trim()) {
    filteredStatuses.value = ["active", "inactive"];
  } else {
    const query = statusSearch.value.toLowerCase();
    filteredStatuses.value = ["active", "inactive"].filter((status) =>
      status.toLowerCase().includes(query)
    );
  }
};

const selectStatus = (status) => {
  if (status) {
    deliveryRegionForm.status = status;
    isStatusDropdownOpen.value = false;
    statusSearch.value = "";
  }
};

// Format delivery fee with comma separators for thousands
const formatDeliveryFee = () => {
  // If the input is empty, do nothing
  if (!deliveryRegionForm.delivery_fee) return;
  
  // Remove any non-numeric characters except for the decimal point
  let value = deliveryRegionForm.delivery_fee.toString().replace(/[^0-9.]/g, "");
  
  // Parse the value as a number
  const numValue = parseFloat(value);
  
  // If it's a valid number, format it
  if (!isNaN(numValue)) {
    deliveryRegionForm.delivery_fee = numValue;
  }
};

// Update onMounted to include delivery regions
onMounted(() => {
  // Initialize filtered countries and regions
  searchRegions("Kenya");
  fetchSupplierWarehouses();
  fetchDeliveryRegions();
  filteredCountries.value = [...countries.value];
  filteredRegions.value = [...regions.value];

  // Add click outside listener for dropdowns
  document.addEventListener("click", closeCountryDropdownOutside);
  document.addEventListener("click", closeRegionDropdownOutside);
  document.addEventListener("click", closeDropdownsOutside);
});

// Update onBeforeUnmount to include new event listener
onBeforeUnmount(() => {
  // Clean up the event listeners
  document.removeEventListener("click", closeCountryDropdownOutside);
  document.removeEventListener("click", closeRegionDropdownOutside);
  document.removeEventListener("click", closeDropdownsOutside);
});

// Add closeDropdownsOutside method
const closeDropdownsOutside = (event) => {
  const dropdowns = document.querySelectorAll(".custom-select-container");
  let clickedInside = false;
  
  dropdowns.forEach((dropdown) => {
    if (dropdown.contains(event.target)) {
      clickedInside = true;
    }
  });

  if (!clickedInside) {
    isWarehouseDropdownOpen.value = false;
    isStatusDropdownOpen.value = false;
  }
};

// Update updateTabCounts to include delivery regions count
const updateTabCounts = () => {
  const warehouseCount = warehouses.value.length;
  const deliveryRegionCount = deliveryRegions.value.length;
  tabs.value.find((t) => t.id === "warehouses").count = warehouseCount;
  tabs.value.find((t) => t.id === "regions").count = deliveryRegionCount;
};

// ... rest of your existing script ...

// Helper function to format currency
const formatCurrency = (amount) => {
  if (!amount) return 'KES 0.00';
  return `KES ${Number(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
};

// Pagination state for delivery regions
const deliveryRegionCurrentPage = ref(1);
const deliveryRegionPerPage = ref(10);

// Event handlers for delivery regions pagination
const handleDeliveryRegionPageChange = (page) => {
  deliveryRegionCurrentPage.value = page;
  fetchDeliveryRegions();
};

const handleDeliveryRegionPerPageChange = (newPerPage) => {
  deliveryRegionPerPage.value = newPerPage;
  fetchDeliveryRegions();
};
</script>

<style scoped>
/* Empty state */
.empty-state-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 48px 20px;
  text-align: center;
  background-color: #fff;
  border-radius: 8px;
  min-height: 300px;
}

.empty-state-icon {
  color: #94a3b8;
  margin-bottom: 16px;
}

.empty-state-title {
  color: #334155;
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0 0 8px;
}

.empty-state-description {
  color: #64748b;
  font-size: 0.95rem;
  margin: 0 0 24px;
  max-width: 300px;
}

.page-container {
  display: flex;
  justify-content: center;
  background-color: #f5f7fa;
  min-height: 100vh;
  padding: 40px 20px;
  font-family: "Inter", "Segoe UI", Roboto, sans-serif;
}

.content-container {
  width: 1200px;
  max-width: 100%;
  border-radius: 12px;
  padding: 24px;
}

.message-container {
  position: fixed;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 1000;
  width: auto;
  max-width: 500px;
}

.header-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.page-title {
  font-size: 1.8rem;
  color: #1e293b;
  font-weight: 600;
  margin: 0;
}

/* Table Controls */
.table-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

/* Search Container */
.search-container {
  position: relative;
  width: 300px;
}

.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #64748b;
}

.search-input {
  width: 100%;
  padding: 10px 10px 10px 40px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 0.9rem;
}

.search-input:focus {
  outline: none;
  border-color: #0e64a5;
  box-shadow: 0 0 0 2px rgba(14, 100, 165, 0.1);
}

.add-btn {
  display: flex;
  align-items: center;
  padding: 10px 16px;
  background-color: #0e64a5;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 0.95rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.add-btn:hover {
  background-color: #0a4f83;
}

.plus-icon {
  font-size: 1.2rem;
  margin-right: 8px;
  font-weight: bold;
}

.loading-state {
  padding: 12px 16px;
  color: #64748b;
  font-size: 0.9rem;
  text-align: center;
}
/* Table styles with horizontal scroll */
.table-container {
  position: relative;
  width: 100%;
  background-color: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  margin-bottom: 24px;
}

.table-wrapper {
  width: 100%;
  overflow-x: auto;
  /* Enable smooth scrolling */
  scroll-behavior: smooth;
  /* Add some padding for better appearance on mobile */
  padding-bottom: 6px;
  /* Hide scrollbar for WebKit browsers */
  scrollbar-width: thin;
}

/* Customize scrollbar for Webkit browsers */
.table-wrapper::-webkit-scrollbar {
  height: 8px;
}

.table-wrapper::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}

.table-wrapper::-webkit-scrollbar-thumb {
  background-color: #cbd5e1;
  border-radius: 4px;
}

.table-wrapper::-webkit-scrollbar-thumb:hover {
  background-color: #94a3b8;
}

/* Make sure the table takes full width of its container */
.data-table {
  width: 100%;
  min-width: 1200px; /* Minimum width to ensure horizontal scroll appears when needed */
  table-layout: auto; /* Allow table to adjust column width based on content */
  border-collapse: separate;
  border-spacing: 0;
  font-size: 0.95rem;
}

/* Ensure the table headers and data cells have proper min-width */
.data-table th,
.data-table td {
  white-space: nowrap; /* Prevent text wrapping */
  min-width: 120px; /* Minimum width for each column */
}

/* Actions column should be narrower and fixed */
.actions-column {
  min-width: 110px;
  width: 110px;
}

/* Add a visible indicator for horizontal scroll on mobile */
@media (max-width: 768px) {
  .table-container::after {
    content: "";
    position: absolute;
    top: 0;
    right: 0;
    width: 20px;
    height: 100%;
    background: linear-gradient(
      to right,
      rgba(255, 255, 255, 0),
      rgba(230, 230, 230, 0.3)
    );
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.3s;
  }

  .table-container:hover::after {
    opacity: 1;
  }
}

.data-table th {
  background-color: #f8fafc;
  color: #64748b;
  font-weight: 600;
  text-align: left;
  padding: 14px 15px;
  border-bottom: 1px solid #e5e7eb;
}

.data-table th.sortable {
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

.data-table td {
  padding: 14px 15px;
  border-bottom: 1px solid #e5e7eb;
  color: #334155;
  text-align: left;
}

.data-table tr:last-child td {
  border-bottom: none;
}

.data-row:hover td {
  background-color: #f8fafc;
}

.status-pill {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 500;
}

.status-active {
  background-color: rgba(16, 185, 129, 0.1);
  color: #047857;
}

.status-inactive {
  background-color: rgba(239, 68, 68, 0.1);
  color: #dc2626;
}

.action-btn {
  background: none;
  border: 1px solid #e5e7eb;
  border-radius: 4px;
  padding: 6px 10px;
  font-size: 0.85rem;
  margin-right: 6px;
  cursor: pointer;
  transition: all 0.2s;
}

.edit-btn {
  color: #0e64a5;
  border-color: #0e64a5;
}

.edit-btn:hover {
  background-color: rgba(14, 100, 165, 0.05);
}

.delete-btn {
  color: #ef4444;
  border-color: #ef4444;
}

.delete-btn:hover {
  background-color: rgba(239, 68, 68, 0.05);
}

.empty-state {
  text-align: center;
  color: #64748b;
  padding: 30px;
  font-style: italic;
}

/* Pagination Controls */
.pagination-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 24px;
}

.per-page {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #64748b;
  font-size: 0.9rem;
}

.per-page-select {
  padding: 6px 8px;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
  background-color: white;
  color: #1e293b;
  font-size: 0.9rem;
}

.pagination-buttons {
  display: flex;
  align-items: center;
  gap: 8px;
}

.pagination-btn {
  padding: 6px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
  background-color: white;
  color: #1e293b;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s;
}

.pagination-btn:hover:not(:disabled) {
  background-color: #f1f5f9;
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-numbers {
  display: flex;
  gap: 4px;
}

.page-number {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.2s;
}

.page-number:hover {
  background-color: #f1f5f9;
}

.page-number.active {
  background-color: #0e64a5;
  color: white;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-container {
  background-color: white;
  border-radius: 8px;
  width: 500px;
  max-width: 90%;
  max-height: 90vh;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
  display: flex;
  flex-direction: column;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  margin: 0;
  font-size: 1.25rem;
  color: #1e293b;
  font-weight: 600;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #64748b;
  cursor: pointer;
}

.modal-body {
  padding: 24px;
  text-align: left;
  overflow-y: auto;
  max-height: 70vh;
}

.form-group {
  margin-bottom: 16px;
  text-align: left;
  width: 100%;
  box-sizing: border-box;
  position: relative;
  overflow: visible;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-size: 0.95rem;
  font-weight: 500;
  color: #334155;
  text-align: left;
}

.required {
  color: #ef4444;
  margin-left: 2px;
}

.form-group input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  font-size: 0.95rem;
  color: #1e293b;
  background-color: white;
  transition: border-color 0.2s;
  box-sizing: border-box;
}

.form-group input:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-group textarea {
  width: 100%;
  height: 120px;
  padding: 10px 12px;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  font-size: 0.95rem;
  color: #1e293b;
  background-color: white;
  transition: border-color 0.2s;
  box-sizing: border-box;
  resize: vertical;
  font-family: inherit;
}

.form-group textarea:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-group select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  font-size: 0.95rem;
  color: #1e293b;
  background-color: white;
  transition: border-color 0.2s;
  box-sizing: border-box;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 10px center;
}

.form-group select:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 24px;
}

.cancel-btn {
  padding: 10px 16px;
  background-color: white;
  color: #64748b;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  font-size: 0.95rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.cancel-btn:hover {
  background-color: #f1f5f9;
  border-color: #cbd5e1;
}

.submit-btn {
  padding: 10px 16px;
  background-color: #0e64a5;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 0.95rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.submit-btn:hover {
  background-color: #0a4f83;
}

/* Updated dropdown styles */
.custom-select-container {
  position: relative;
  width: 100%;
  box-sizing: border-box;
  /* z-index: 5; */
}

.custom-select-trigger {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  padding: 10px 12px;
  background-color: white;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  font-size: 0.95rem;
  color: #1e293b;
  cursor: pointer;
  transition: all 0.2s ease;
  box-sizing: border-box;
  text-overflow: ellipsis;
  text-align: left;
}

.custom-select-trigger span {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-right: 8px;
  flex: 1;
}

.custom-select-trigger.active {
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.dropdown-arrow {
  transition: transform 0.2s ease;
}

.dropdown-arrow.open {
  transform: rotate(180deg);
}

.custom-select-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  width: 100%;
  max-height: 250px;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  z-index: 10;
  overflow: hidden;
  animation: dropdownFadeIn 0.2s ease;
  margin-top: 5px;
  text-align: left;
}

.custom-select-dropdown.dropdown-top {
  bottom: calc(100% + 5px);
  top: auto;
  animation: dropdownFadeInTop 0.2s ease;
  margin-top: 0;
}

@keyframes dropdownFadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes dropdownFadeInTop {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.search-box {
  position: relative;
  padding: 10px 12px;
  border-bottom: 1px solid #f1f5f9;
}

.dropdown-search {
  width: 100%;
  padding: 8px 8px 8px 12px;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  font-size: 0.9rem;
  color: #1e293b;
  background-color: #f8fafc;
}

.dropdown-search:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
}

.dropdown-options {
  max-height: 195px;
  overflow-y: auto;
  padding: 8px 0;
}

.dropdown-option {
  padding: 8px 16px;
  font-size: 0.95rem;
  color: #334155;
  cursor: pointer;
  transition: background-color 0.2s ease;
  text-align: left;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.dropdown-option:hover {
  background-color: #f1f5f9;
  color: #2563eb;
}

.no-results {
  padding: 12px 16px;
  color: #94a3b8;
  font-size: 0.9rem;
  text-align: center;
  font-style: italic;
}

/* Scrollbar styles for dropdown */
.dropdown-options::-webkit-scrollbar {
  width: 6px;
}

.dropdown-options::-webkit-scrollbar-track {
  background: #f8fafc;
}

.dropdown-options::-webkit-scrollbar-thumb {
  background-color: #cbd5e1;
  border-radius: 3px;
}

.dropdown-options::-webkit-scrollbar-thumb:hover {
  background-color: #94a3b8;
}

/* Responsive styles */
@media (max-width: 768px) {
  .header-container {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }

  .table-controls {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }

  .add-btn {
    width: 100%;
    justify-content: center;
  }

  .search-container {
    width: 100%;
  }

  .pagination-controls {
    flex-direction: column;
    gap: 16px;
  }

  .per-page {
    width: 100%;
    justify-content: center;
  }

  .pagination-buttons {
    width: 100%;
    justify-content: center;
  }
}

@media (max-width: 640px) {
  .page-container {
    padding: 20px 16px;
  }

  .content-container {
    padding: 16px;
  }

  .actions-column {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .action-btn {
    margin-right: 0;
  }

  .table-container {
    border-radius: 6px;
  }

  .table-wrapper {
    padding-bottom: 4px;
  }

  .page-numbers {
    display: none;
  }
}

/* Add these new styles */
.tabs-container {
  margin-bottom: 24px;
  border-bottom: 1px solid #e5e7eb;
}

.tabs {
  display: flex;
  list-style: none;
  padding: 0;
  margin: 0;
  gap: 8px;
}

.tab {
  padding: 12px 16px;
  font-size: 0.95rem;
  font-weight: 500;
  color: #64748b;
  cursor: pointer;
  position: relative;
  display: flex;
  align-items: center;
  transition: all 0.2s ease;
}

.tab.active {
  color: #2563eb;
  font-weight: 600;
}

.tab.active::after {
  content: '';
  position: absolute;
  bottom: -1px;
  left: 0;
  width: 100%;
  height: 2px;
  background-color: #2563eb;
}

.tab-count {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 24px;
  height: 24px;
  padding: 0 6px;
  margin-left: 8px;
  border-radius: 10px;
  font-size: 0.75rem;
  font-weight: 600;
}

/* Update tab count colors */
.tabs li:nth-child(1) .tab-count {
  background-color: #d1f0e0;
  color: #2a9d74;
}

.tabs li:nth-child(2) .tab-count {
  background-color: #fadadd;
  color: #e55c5c;
}

/* Add responsive styles for tabs */
@media (max-width: 640px) {
  .tabs {
    overflow-x: auto;
    padding-bottom: 8px;
  }
}
</style>
