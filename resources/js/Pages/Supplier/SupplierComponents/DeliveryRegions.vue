<template>
  <div>
    <div class="message-container">
      <SuccessMessage v-if="successMessage" @close="successMessage = ''" v-slot>{{ successMessage }}</SuccessMessage>
      <ErrorMessage v-if="errorMessage" @close="errorMessage = ''" v-slot>{{ errorMessage }}</ErrorMessage>
    </div>
    
    <div class="table-controls">
      <div class="table-title">Delivery Regions</div>
      <button @click="openAddDeliveryRegionModal" class="add-btn">
        <span class="plus-icon">+</span>
        Add Delivery Region
      </button>
    </div>
    
    <div class="table-container">
      <div class="table-wrapper">
        <table class="data-table">
          <thead>
            <tr>
              <th @click="sortBy('warehouseName')" class="sortable">
                Warehouse Name <i :class="getSortIcon('warehouseName')"></i>
              </th>
              <th @click="sortBy('deliverTo')" class="sortable">
                Deliver To <i :class="getSortIcon('deliverTo')"></i>
              </th>
              <th @click="sortBy('deliveryFee')" class="sortable">
                Delivery Fee <i :class="getSortIcon('deliveryFee')"></i>
              </th>
              <th @click="sortBy('status')" class="sortable">
                Status <i :class="getSortIcon('status')"></i>
              </th>
              <th class="actions-column">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="region in sortedDeliveryRegions" :key="region.id" class="data-row">
              <td>{{ getWarehouseName(region.warehouse_id) }}</td>
              <td>{{ region.region }}</td>
              <td>{{ formatCurrency(region.delivery_fee) }}</td>
              <td>
                <span :class="['status-pill', region.status === 'active' ? 'status-active' : 'status-inactive']">
                  {{ capitalizeFirstLetter(region.status) }}
                </span>
              </td>
              <td class="actions-column">
                <button @click="editDeliveryRegion(region)" class="action-btn edit-btn">
                  Edit
                </button>
                <button @click="deleteDeliveryRegion(region.uuid)" class="action-btn delete-btn">
                  Delete
                </button>
              </td>
            </tr>
            <tr v-if="deliveryRegions.length === 0 && !loading">
              <td colspan="5" class="empty-state">
                No delivery regions added yet
              </td>
            </tr>
            <tr v-if="loading">
              <td colspan="5" class="empty-state">
                Loading delivery regions...
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add Delivery Region Modal -->
    <div v-if="showDeliveryRegionModal" class="modal-overlay" @click.self="closeDeliveryRegionModal">
      <div class="modal-container">
        <div class="modal-header">
          <h2>{{ editingDeliveryRegion ? 'Edit Delivery Region' : 'Add New Delivery Region' }}</h2>
          <button class="close-btn" @click="closeDeliveryRegionModal">&times;</button>
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
                  <span :data-has-value="!!form.warehouse_id">{{ getWarehouseName(form.warehouse_id) || 'Select warehouse' }}</span>
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
              <input type="text" id="deliverTo" v-model="form.region" required placeholder="Enter region name">
            </div>
            
            <div class="form-group">
              <label for="deliveryFee">Delivery Fee <span class="required">*</span></label>
              <input type="text" id="deliveryFee" v-model="form.delivery_fee" @input="formatDeliveryFee" required placeholder="Enter delivery fee">
            </div>
            
            <div class="form-group">
              <label for="status">Status <span class="required">*</span></label>
              <div class="custom-select-container">
                <div 
                  class="custom-select-trigger" 
                  @click="toggleStatusDropdown"
                  :class="{ 'active': isStatusDropdownOpen }"
                >
                  <span :data-has-value="!!form.status">{{ capitalizeFirstLetter(form.status) || 'Select status' }}</span>
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
              <button type="button" class="cancel-btn" @click="closeDeliveryRegionModal">Cancel</button>
              <button type="submit" class="submit-btn" :disabled="form.processing">Save Delivery Region</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import SuccessMessage from '@/Components/SuccessMessage.vue';
import ErrorMessage from '@/Components/ErrorMessage.vue';

export default {
  name: 'DeliveryRegions',
  props: {
    supplier: {
      type: Object,
      required: true
    },
    warehouseId: {
      type: Number,
      default: null
    }
  },
  components: {
    SuccessMessage,
    ErrorMessage
  },
  data() {
    const page = usePage();
    const user = page.props.auth.user;

    return {
      // Sorting
      sortKey: 'warehouseName',
      sortDir: 'asc',
      
      // Delivery Regions
      deliveryRegions: [],
      loading: true,
      showDeliveryRegionModal: false,
      editingDeliveryRegion: false,
      editingDeliveryRegionId: null,
      
      // Form data
      form: useForm({
        warehouse_id: this.warehouseId || '',
        region: '',
        delivery_fee: '',
        status: 'active',
        company_id: this.supplier?.id || null,
        created_by: user?.id || null
      }),
      
      // Warehouse dropdown
      warehouses: [],
      isWarehouseDropdownOpen: false,
      warehouseSearch: '',
      filteredWarehouses: [],
      
      // Status dropdown
      isStatusDropdownOpen: false,
      statusSearch: '',
      filteredStatuses: ['active', 'inactive'],
      
      // Message state
      successMessage: '',
      errorMessage: '',
      regionUuid: '',
    };
  },
  computed: {
    sortedDeliveryRegions() {
      if (!this.deliveryRegions || !Array.isArray(this.deliveryRegions)) {
        return [];
      }
      
      const regions = [...this.deliveryRegions];
      regions.sort((a, b) => {
        let modifier = this.sortDir === 'asc' ? 1 : -1;
        
        // Map the sortKey to the actual property names in the data
        let aKey, bKey;
        
        if (this.sortKey === 'warehouseName') {
          aKey = this.getWarehouseName(a?.warehouse_id);
          bKey = this.getWarehouseName(b?.warehouse_id);
        } else if (this.sortKey === 'deliverTo') {
          aKey = a?.region || '';
          bKey = b?.region || '';
        } else if (this.sortKey === 'deliveryFee') {
          aKey = a?.delivery_fee || 0;
          bKey = b?.delivery_fee || 0;
        } else if (this.sortKey === 'status') {
          aKey = a?.status || '';
          bKey = b?.status || '';
        } else {
          aKey = a?.[this.sortKey];
          bKey = b?.[this.sortKey];
        }
        
        // Handle undefined or null values
        if (aKey === undefined || aKey === null) {
          aKey = '';
        }
        if (bKey === undefined || bKey === null) {
          bKey = '';
        }
        
        if (typeof aKey === 'number' && typeof bKey === 'number') {
          return aKey < bKey ? -1 * modifier : 1 * modifier;
        } else {
          // Convert to strings for comparison
          aKey = String(aKey).toLowerCase();
          bKey = String(bKey).toLowerCase();
          return aKey < bKey ? -1 * modifier : 1 * modifier;
        }
      });
      return regions;
    }
  },
  mounted() {
    // Add event listeners for closing dropdowns when clicking outside
    document.addEventListener('click', this.closeDropdownsOutside);
    
    // Fetch data
    this.$nextTick(() => {
      this.fetchDeliveryRegions();
      this.fetchWarehouses();
    });
  },
  beforeUnmount() {
    // Clean up event listeners when component is destroyed
    document.removeEventListener('click', this.closeDropdownsOutside);
  },
  methods: {
    // API methods
    async fetchDeliveryRegions() {
      if (!this.supplier || !this.supplier.id) {
        console.error("Supplier information is missing");
        this.loading = false;
        return;
      }
      
      this.loading = true;
      try {
        const response = await axios.get(
          route("supplier.delivery-regions.index", {
            company_id: this.supplier.id,
          })
        );
        this.deliveryRegions = response.data || [];
      } catch (error) {
        console.error("Error fetching delivery regions:", error);
        this.deliveryRegions = [];
      } finally {
        this.loading = false;
      }
    },
    
    async fetchWarehouses() {
      if (!this.supplier || !this.supplier.uuid) {
        console.error("Supplier UUID is missing");
        return;
      }
      
      try {
        const response = await axios.get(
          route("supplier.warehouses.list", { uuid: this.supplier.uuid })
        );
        this.warehouses = response.data || [];
        this.filteredWarehouses = [...this.warehouses];
      } catch (error) {
        console.error("Error fetching warehouses:", error);
        this.warehouses = [];
        this.filteredWarehouses = [];
      }
    },
    
    // Sorting methods
    sortBy(key) {
      if (this.sortKey === key) {
        this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
      } else {
        this.sortKey = key;
        this.sortDir = 'asc';
      }
    },
    
    getSortIcon(key) {
      if (this.sortKey !== key) return 'sort-icon';
      return this.sortDir === 'asc' ? 'sort-icon sort-asc' : 'sort-icon sort-desc';
    },
    
    // Delivery Region methods
    openAddDeliveryRegionModal() {
      this.editingDeliveryRegion = false;
      this.editingDeliveryRegionId = null;
      this.form.reset();
      this.form.warehouse_id = this.warehouseId || '';
      this.form.company_id = this.supplier?.id || null;
      this.form.created_by = usePage().props.auth.user?.id || null;
      this.showDeliveryRegionModal = true;
      this.isWarehouseDropdownOpen = false;
    },
    
    closeDeliveryRegionModal() {
      this.showDeliveryRegionModal = false;
      this.form.reset();
    },
    
    editDeliveryRegion(region) {
      this.editingDeliveryRegion = true;
      this.editingDeliveryRegionId = region.id;
      this.regionUuid = region.uuid;
      
      this.form.reset();
      this.form.warehouse_id = region.warehouse_id;
      this.form.region = region.region;
      this.form.delivery_fee = region.delivery_fee;
      this.form.status = region.status;
      this.form.company_id = this.supplier.id;
      
      this.showDeliveryRegionModal = true;
    },
    
    async deleteDeliveryRegion(regionUuid) {
      if (!confirm('Are you sure you want to delete this delivery region?')) {
        return;
      }
      
      try {
        await axios.delete(route('supplier.delivery-regions.destroy', regionUuid));
        this.successMessage = 'Delivery region deleted successfully';
        this.fetchDeliveryRegions();
        console.log('Setting success message in DeliveryRegions - delete');
        setTimeout(() => { 
          this.successMessage = '';
          console.log('Clearing success message in DeliveryRegions - delete');
        }, 3000);
      } catch (error) {
        this.errorMessage = 'Error deleting delivery region';
        console.error('Error deleting delivery region:', error);
        console.log('Setting error message in DeliveryRegions - delete');
        setTimeout(() => { 
          this.errorMessage = '';
          console.log('Clearing error message in DeliveryRegions - delete');
        }, 3000);
      }
    },
    
    saveDeliveryRegion() {
      if (this.editingDeliveryRegion) {
        this.form.put(route('supplier.delivery-regions.update', this.regionUuid), {
          preserveScroll: true,
          onSuccess: () => {
            this.successMessage = 'Delivery region updated successfully';
            this.closeDeliveryRegionModal();
            this.fetchDeliveryRegions();
            console.log('Setting success message in DeliveryRegions - update');
            setTimeout(() => { 
              this.successMessage = '';
              console.log('Clearing success message in DeliveryRegions - update');
            }, 3000);
          },
          onError: (errors) => {
            this.errorMessage = 'Error updating delivery region';
            console.error('Error:', errors);
            console.log('Setting error message in DeliveryRegions - update');
            setTimeout(() => { 
              this.errorMessage = '';
              console.log('Clearing error message in DeliveryRegions - update');
            }, 3000);
          }
        });
      } else {
        this.form.post(route('supplier.delivery-regions.store'), {
          preserveScroll: true,
          onSuccess: () => {
            this.successMessage = 'Delivery region added successfully';
            this.closeDeliveryRegionModal();
            this.fetchDeliveryRegions();
            console.log('Setting success message in DeliveryRegions - create');
            setTimeout(() => { 
              this.successMessage = '';
              console.log('Clearing success message in DeliveryRegions - create');
            }, 3000);
          },
          onError: (errors) => {
            this.errorMessage = 'Error creating delivery region';
            console.error('Error:', errors);
            console.log('Setting error message in DeliveryRegions - create');
            setTimeout(() => { 
              this.errorMessage = '';
              console.log('Clearing error message in DeliveryRegions - create');
            }, 3000);
          }
        });
      }
    },
    
    // Warehouse dropdown methods
    toggleWarehouseDropdown() {
      this.isWarehouseDropdownOpen = !this.isWarehouseDropdownOpen;
      if (this.isWarehouseDropdownOpen) {
        this.warehouseSearch = '';
        this.filteredWarehouses = [...this.warehouses];
      }
    },
    
    closeDropdownsOutside(event) {
      const dropdowns = document.querySelectorAll('.custom-select-container');
      let clickedInside = false;
      
      dropdowns.forEach(dropdown => {
        if (dropdown.contains(event.target)) {
          clickedInside = true;
        }
      });

      if (!clickedInside) {
        this.isWarehouseDropdownOpen = false;
        this.isStatusDropdownOpen = false;
      }
    },
    
    filterWarehouses() {
      if (!this.warehouseSearch.trim()) {
        this.filteredWarehouses = [...this.warehouses];
      } else {
        const query = this.warehouseSearch.toLowerCase();
        this.filteredWarehouses = this.warehouses.filter(warehouse => 
          warehouse.name.toLowerCase().includes(query)
        );
      }
    },
    
    selectWarehouse(warehouse) {
      if (warehouse && warehouse.id) {
        this.form.warehouse_id = warehouse.id;
        this.isWarehouseDropdownOpen = false;
        this.warehouseSearch = '';
      }
    },
    
    // Format delivery fee with comma separators for thousands
    formatDeliveryFee() {
      // If the input is empty, do nothing
      if (!this.form.delivery_fee) return;
      
      // Remove any non-numeric characters except for the decimal point
      let value = this.form.delivery_fee.toString().replace(/[^0-9.]/g, '');
      
      // Parse the value as a number
      const numValue = parseFloat(value);
      
      // If it's a valid number, format it
      if (!isNaN(numValue)) {
        this.form.delivery_fee = numValue;
      }
    },
    
    // Helper methods
    getWarehouseName(warehouseId) {
      if (!warehouseId) return '';
      if (!this.warehouses || !this.warehouses.length) return '';
      const warehouse = this.warehouses.find(w => w.id === warehouseId);
      return warehouse ? warehouse.name : '';
    },
    
    formatCurrency(amount) {
      if (amount === null || amount === undefined) return '';
      return 'KES ' + parseFloat(amount).toLocaleString('en-US');
    },
    
    capitalizeFirstLetter(string) {
      if (!string) return '';
      return string.charAt(0).toUpperCase() + string.slice(1);
    },
    
    // Status dropdown methods
    toggleStatusDropdown() {
      this.isStatusDropdownOpen = !this.isStatusDropdownOpen;
      if (this.isStatusDropdownOpen) {
        this.statusSearch = '';
        this.filteredStatuses = ['active', 'inactive'];
      }
    },
    
    filterStatuses() {
      if (!this.statusSearch.trim()) {
        this.filteredStatuses = ['active', 'inactive'];
      } else {
        const query = this.statusSearch.toLowerCase();
        this.filteredStatuses = ['active', 'inactive'].filter(status => 
          status.toLowerCase().includes(query)
        );
      }
    },
    
    selectStatus(status) {
      if (status) {
        this.form.status = status;
        this.isStatusDropdownOpen = false;
        this.statusSearch = '';
      }
    }
  }
};
</script>

<style scoped>
@import './SupplierSharedStyles.css';
</style> 