<template>
  <div>
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
              <td>{{ region.warehouseName }}</td>
              <td>{{ region.deliverTo }}</td>
              <td>{{ region.deliveryFee }}</td>
              <td>
                <span :class="['status-pill', region.status === 'Active' ? 'status-active' : 'status-inactive']">
                  {{ region.status }}
                </span>
              </td>
              <td class="actions-column">
                <button @click="editDeliveryRegion(region)" class="action-btn edit-btn">
                  Edit
                </button>
                <button @click="deleteDeliveryRegion(region.id)" class="action-btn delete-btn">
                  Delete
                </button>
              </td>
            </tr>
            <tr v-if="deliveryRegions.length === 0">
              <td colspan="5" class="empty-state">
                No delivery regions added yet
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
              <div class="custom-select-container warehouse-select-container">
                <div 
                  class="custom-select-trigger warehouse-select-trigger" 
                  @click="toggleWarehouseDropdown"
                  :class="{ 'active': isWarehouseDropdownOpen }"
                >
                  <span>{{ newDeliveryRegion.warehouseName || 'Select a warehouse' }}</span>
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
                
                <div class="custom-select-dropdown warehouse-select-dropdown" v-show="isWarehouseDropdownOpen">
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
                      :key="warehouse"
                      class="dropdown-option"
                      @click="selectWarehouse(warehouse)"
                    >
                      {{ warehouse }}
                    </div>
                    <div v-if="filteredWarehouses.length === 0" class="no-results">
                      No warehouses match your search
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Region Dropdown for Deliver To -->
            <div class="form-group">
              <label for="deliverTo">Deliver To <span class="required">*</span></label>
              <div class="custom-select-container deliverTo-select-container">
                <div 
                  class="custom-select-trigger deliverTo-select-trigger" 
                  @click="toggleDeliverToDropdown"
                  :class="{ 'active': isDeliverToDropdownOpen }"
                >
                  <span>{{ newDeliveryRegion.deliverTo || 'Select a region' }}</span>
                  <svg 
                    class="dropdown-arrow" 
                    :class="{ 'open': isDeliverToDropdownOpen }"
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
                
                <div class="custom-select-dropdown deliverTo-select-dropdown" v-show="isDeliverToDropdownOpen">
                  <div class="search-box">
                    <input
                      type="text"
                      v-model="deliverToSearch"
                      @input="filterDeliverToRegions"
                      placeholder="Search region..."
                      class="dropdown-search"
                      @click.stop
                    >
                  </div>
                  
                  <div class="dropdown-options">
                    <div
                      v-for="region in filteredDeliverToRegions"
                      :key="region"
                      class="dropdown-option"
                      @click="selectDeliverToRegion(region)"
                    >
                      {{ region }}
                    </div>
                    <div v-if="filteredDeliverToRegions.length === 0" class="no-results">
                      No regions match your search
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="form-group">
              <label for="deliveryFee">Delivery Fee <span class="required">*</span></label>
              <input type="text" id="deliveryFee" v-model="newDeliveryRegion.deliveryFee" @input="formatDeliveryFee" required placeholder="Enter delivery fee">
            </div>
            
            <div class="form-group">
              <label for="status">Status <span class="required">*</span></label>
              <select id="status" v-model="newDeliveryRegion.status" required>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
            
            <div class="form-actions">
              <button type="button" class="cancel-btn" @click="closeDeliveryRegionModal">Cancel</button>
              <button type="submit" class="submit-btn">Save Delivery Region</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DeliveryRegions',
  props: {
    // Allow passing delivery regions from parent if needed
    initialRegions: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      // Sorting
      sortKey: 'warehouseName',
      sortDir: 'asc',
      
      // Delivery Regions
      deliveryRegions: this.initialRegions.length > 0 ? this.initialRegions : [
        {
          id: 1,
          warehouseName: 'Nairobi Central Warehouse',
          deliverTo: 'Nairobi CBD',
          deliveryFee: 500,
          status: 'Active'
        },
        {
          id: 2,
          warehouseName: 'Nairobi Central Warehouse',
          deliverTo: 'Westlands',
          deliveryFee: 700,
          status: 'Active'
        },
        {
          id: 3,
          warehouseName: 'Mombasa Distribution Center',
          deliverTo: 'Mombasa Island',
          deliveryFee: 450,
          status: 'Active'
        }
      ],
      showDeliveryRegionModal: false,
      newDeliveryRegion: {
        warehouseName: '',
        deliverTo: '',
        deliveryFee: '',
        status: 'Active'
      },
      editingDeliveryRegion: false,
      editingDeliveryRegionId: null,
      
      // Warehouse dropdown
      isWarehouseDropdownOpen: false,
      warehouseSearch: '',
      filteredWarehouses: [],
      warehouses: [
        { id: 1, name: 'Nairobi Central Warehouse' },
        { id: 2, name: 'Mombasa Distribution Center' },
        { id: 3, name: 'Kisumu Warehouse' },
        { id: 4, name: 'Nakuru Fulfillment Center' },
        { id: 5, name: 'Eldoret Storage Facility' }
      ],
      
      // Region dropdown
      isDeliverToDropdownOpen: false,
      deliverToSearch: '',
      filteredDeliverToRegions: [],
      regions: [
        'Nairobi CBD',
        'Westlands',
        'Mombasa Island',
        'Kisumu Central',
        'Eldoret Town',
        'Nakuru Town',
        'Karen',
        'Kiambu',
        'Thika',
        'Machakos'
      ]
    };
  },
  computed: {
    sortedDeliveryRegions() {
      const regions = [...this.deliveryRegions];
      regions.sort((a, b) => {
        let modifier = this.sortDir === 'asc' ? 1 : -1;
        let aValue = a[this.sortKey];
        let bValue = b[this.sortKey];
        
        // Handle undefined or null values
        if (aValue === undefined || aValue === null) {
          aValue = '';
        }
        if (bValue === undefined || bValue === null) {
          bValue = '';
        }
        
        if (typeof aValue === 'number' && typeof bValue === 'number') {
          return aValue < bValue ? -1 * modifier : 1 * modifier;
        } else {
          // Convert to strings for comparison
          aValue = String(aValue).toLowerCase();
          bValue = String(bValue).toLowerCase();
          return aValue < bValue ? -1 * modifier : 1 * modifier;
        }
      });
      return regions;
    }
  },
  mounted() {
    // Add event listeners for closing dropdowns when clicking outside
    document.addEventListener('click', this.closeWarehouseDropdownOutside);
    document.addEventListener('click', this.closeDeliverToDropdownOutside);
  },
  beforeDestroy() {
    // Clean up event listeners when component is destroyed
    document.removeEventListener('click', this.closeWarehouseDropdownOutside);
    document.removeEventListener('click', this.closeDeliverToDropdownOutside);
  },
  methods: {
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
      this.newDeliveryRegion = {
        warehouseName: '',
        deliverTo: '',
        deliveryFee: '',
        status: 'Active'
      };
      this.showDeliveryRegionModal = true;
      this.isWarehouseDropdownOpen = false;
    },
    
    closeDeliveryRegionModal() {
      this.showDeliveryRegionModal = false;
    },
    
    editDeliveryRegion(region) {
      this.editingDeliveryRegion = true;
      this.editingDeliveryRegionId = region.id;
      
      // Create a copy of the region with formatted delivery fee
      const regionCopy = { ...region };
      
      // Format the delivery fee with comma separators
      if (regionCopy.deliveryFee) {
        regionCopy.deliveryFee = regionCopy.deliveryFee.toLocaleString('en-US');
      }
      
      this.newDeliveryRegion = regionCopy;
      this.showDeliveryRegionModal = true;
    },
    
    deleteDeliveryRegion(regionId) {
      // Remove the delivery region
      this.deliveryRegions = this.deliveryRegions.filter(region => region.id !== regionId);
      
      // Emit event to notify parent
      this.$emit('regions-updated', this.deliveryRegions);
    },
    
    saveDeliveryRegion() {
      // Convert the formatted delivery fee back to a number for storage
      const deliveryFeeValue = this.newDeliveryRegion.deliveryFee ? 
        parseFloat(this.newDeliveryRegion.deliveryFee.toString().replace(/,/g, '')) : 
        0;
      
      // Create a copy of the delivery region with the numeric delivery fee
      const deliveryRegionData = {
        ...this.newDeliveryRegion,
        deliveryFee: deliveryFeeValue
      };
      
      if (this.editingDeliveryRegion) {
        // Update existing delivery region
        const index = this.deliveryRegions.findIndex(region => region.id === this.editingDeliveryRegionId);
        if (index !== -1) {
          this.deliveryRegions.splice(index, 1, { ...deliveryRegionData, id: this.editingDeliveryRegionId });
        }
      } else {
        // Create a new delivery region with unique ID
        const maxId = this.deliveryRegions.length > 0 ? Math.max(...this.deliveryRegions.map(r => r.id)) : 0;
        
        const region = {
          id: maxId + 1,
          ...deliveryRegionData
        };
        
        this.deliveryRegions.push(region);
      }
      
      // Emit event to notify parent
      this.$emit('regions-updated', this.deliveryRegions);
      
      this.closeDeliveryRegionModal();
    },
    
    // Warehouse dropdown methods for delivery regions
    toggleWarehouseDropdown() {
      this.isWarehouseDropdownOpen = !this.isWarehouseDropdownOpen;
      
      if (this.isWarehouseDropdownOpen) {
        // Reset search when opening
        this.warehouseSearch = '';
        this.filteredWarehouses = [...this.warehouses.map(w => w.name)];
        
        this.$nextTick(() => {
          const trigger = document.querySelector('.warehouse-select-trigger');
          const dropdown = document.querySelector('.warehouse-select-dropdown');
          
          if (!trigger || !dropdown) return;
          
          const triggerRect = trigger.getBoundingClientRect();
          const viewportHeight = window.innerHeight;
          
          const spaceBelow = viewportHeight - triggerRect.bottom;
          const dropdownHeight = Math.min(250, this.filteredWarehouses.length * 36 + 70);
          
          if (spaceBelow < dropdownHeight) {
            dropdown.classList.add('dropdown-top');
          } else {
            dropdown.classList.remove('dropdown-top');
          }
        });
      }
    },
    
    closeWarehouseDropdownOutside(event) {
      const dropdown = document.querySelector('.warehouse-select-container');
      if (dropdown && !dropdown.contains(event.target)) {
        this.isWarehouseDropdownOpen = false;
      }
    },
    
    filterWarehouses() {
      if (this.warehouseSearch.trim() === '') {
        this.filteredWarehouses = [...this.warehouses.map(w => w.name)];
      } else {
        const query = this.warehouseSearch.toLowerCase();
        this.filteredWarehouses = this.warehouses
          .map(w => w.name)
          .filter(name => name.toLowerCase().includes(query));
      }
    },
    
    selectWarehouse(warehouse) {
      this.newDeliveryRegion.warehouseName = warehouse;
      this.isWarehouseDropdownOpen = false;
    },
    
    // Deliver To dropdown methods
    toggleDeliverToDropdown() {
      this.isDeliverToDropdownOpen = !this.isDeliverToDropdownOpen;
      
      if (this.isDeliverToDropdownOpen) {
        // Reset search when opening
        this.deliverToSearch = '';
        this.filteredDeliverToRegions = [...this.regions];
        
        this.$nextTick(() => {
          const trigger = document.querySelector('.deliverTo-select-trigger');
          const dropdown = document.querySelector('.deliverTo-select-dropdown');
          
          if (!trigger || !dropdown) return;
          
          const triggerRect = trigger.getBoundingClientRect();
          const viewportHeight = window.innerHeight;
          
          const spaceBelow = viewportHeight - triggerRect.bottom;
          const dropdownHeight = Math.min(250, this.filteredDeliverToRegions.length * 36 + 70);
          
          if (spaceBelow < dropdownHeight) {
            dropdown.classList.add('dropdown-top');
          } else {
            dropdown.classList.remove('dropdown-top');
          }
        });
      }
    },
    
    closeDeliverToDropdownOutside(event) {
      const dropdown = document.querySelector('.deliverTo-select-container');
      if (dropdown && !dropdown.contains(event.target)) {
        this.isDeliverToDropdownOpen = false;
      }
    },
    
    filterDeliverToRegions() {
      if (this.deliverToSearch.trim() === '') {
        this.filteredDeliverToRegions = [...this.regions];
      } else {
        const query = this.deliverToSearch.toLowerCase();
        this.filteredDeliverToRegions = this.regions.filter(
          region => region.toLowerCase().includes(query)
        );
      }
    },
    
    selectDeliverToRegion(region) {
      this.newDeliveryRegion.deliverTo = region;
      this.isDeliverToDropdownOpen = false;
    },
    
    // Format delivery fee with comma separators for thousands
    formatDeliveryFee() {
      // If the input is empty, do nothing
      if (!this.newDeliveryRegion.deliveryFee) return;
      
      // Remove any non-numeric characters except for the decimal point
      let value = this.newDeliveryRegion.deliveryFee.toString().replace(/[^0-9.]/g, '');
      
      // Parse the value as a number
      const numValue = parseFloat(value);
      
      // If it's a valid number, format it with commas
      if (!isNaN(numValue)) {
        // Format the number with commas for thousands
        this.newDeliveryRegion.deliveryFee = numValue.toLocaleString('en-US');
      }
    }
  }
};
</script>

<style scoped>
@import './SupplierSharedStyles.css';
</style> 