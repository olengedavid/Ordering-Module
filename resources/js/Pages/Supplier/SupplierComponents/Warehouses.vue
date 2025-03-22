<template>
  <div class="tab-content">
    <div class="table-controls">
      <div class="table-title">Warehouses</div>
      <button @click="openAddWarehouseModal" class="add-btn">
        <span class="plus-icon">+</span>
        Add Warehouse
      </button>
    </div>
    
    <div class="table-container">
      <div class="table-wrapper">
        <table class="data-table">
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
            <tr v-for="warehouse in sortedWarehouses" :key="warehouse.id" class="data-row">
              <td>{{ warehouse.name }}</td>
              <td>{{ warehouse.contactPerson }}</td>
              <td>{{ warehouse.email }}</td>
              <td>{{ warehouse.phone }}</td>
              <td>{{ warehouse.address }}</td>
              <td>{{ warehouse.kraPin }}</td>
              <td>{{ warehouse.country }}</td>
              <td>{{ warehouse.region }}</td>
              <td>{{ warehouse.gps }}</td>
              <td>
                <span :class="['status-pill', warehouse.status === 'Active' ? 'status-active' : 'status-inactive']">
                  {{ warehouse.status }}
                </span>
              </td>
              <td class="actions-column">
                <button @click="editWarehouse(warehouse)" class="action-btn edit-btn">
                  Edit
                </button>
                <button @click="deleteWarehouse(warehouse.id)" class="action-btn delete-btn">
                  Delete
                </button>
              </td>
            </tr>
            <tr v-if="warehouses.length === 0">
              <td colspan="11" class="empty-state">
                No warehouses added yet
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Add/Edit Warehouse Modal -->
  <div v-if="showWarehouseModal" class="modal-overlay" @click.self="closeWarehouseModal">
    <div class="modal-container">
      <div class="modal-header">
        <h2>{{ editingWarehouse ? 'Edit Warehouse' : 'Add New Warehouse' }}</h2>
        <button class="close-btn" @click="closeWarehouseModal">&times;</button>
      </div>
      <div class="modal-body">
        <form @submit.prevent="saveWarehouse">
          <div class="form-group">
            <label for="warehouseName">Warehouse Name <span class="required">*</span></label>
            <input type="text" id="warehouseName" v-model="newWarehouse.name" required placeholder="Enter warehouse name">
          </div>
          <div class="form-group">
            <label for="contactPerson">Contact Person <span class="required">*</span></label>
            <input type="text" id="contactPerson" v-model="newWarehouse.contactPerson" required placeholder="Enter contact person">
          </div>
          <div class="form-group">
            <label for="email">Email <span class="required">*</span></label>
            <input type="email" id="email" v-model="newWarehouse.email" required placeholder="Enter email address">
          </div>
          <div class="form-group">
            <label for="phone">Phone <span class="required">*</span></label>
            <input type="text" id="phone" v-model="newWarehouse.phone" required placeholder="Enter phone number">
          </div>
          <div class="form-group">
            <label for="address">Address <span class="required">*</span></label>
            <input type="text" id="address" v-model="newWarehouse.address" required placeholder="Enter physical address">
          </div>
          <div class="form-group">
            <label for="kraPin">KRA PIN <span class="required">*</span></label>
            <input type="text" id="kraPin" v-model="newWarehouse.kraPin" required placeholder="Enter KRA PIN">
          </div>
          
          <!-- Country Dropdown -->
          <div class="form-group">
            <label for="country">Country <span class="required">*</span></label>
            <div class="custom-select-container country-select-container">
              <div 
                class="custom-select-trigger country-select-trigger" 
                @click="toggleCountryDropdown"
                :class="{ 'active': isCountryDropdownOpen }"
              >
                <span>{{ newWarehouse.country || 'Select a country' }}</span>
                <svg 
                  class="dropdown-arrow" 
                  :class="{ 'open': isCountryDropdownOpen }"
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
                :class="{ 'active': isRegionDropdownOpen }"
              >
                <span>{{ newWarehouse.region || 'Select a region' }}</span>
                <svg 
                  class="dropdown-arrow" 
                  :class="{ 'open': isRegionDropdownOpen }"
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
                </div>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <label for="gps">GPS Coordinates</label>
            <input type="text" id="gps" v-model="newWarehouse.gps" placeholder="Enter GPS coordinates (latitude,longitude)">
          </div>
          
          <div class="form-group">
            <label for="status">Status <span class="required">*</span></label>
            <select id="status" v-model="newWarehouse.status" required>
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>
          </div>
          
          <div class="form-actions">
            <button type="button" class="cancel-btn" @click="closeWarehouseModal">Cancel</button>
            <button type="submit" class="submit-btn">Save Warehouse</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Warehouses',
  props: {
    // Only accept the supplier object from parent
    supplier: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      // Sorting
      sortKey: 'name',
      sortDir: 'asc',
      
      // Warehouse data - this will be populated based on the supplier
      warehouses: [
        { id: 1, name: 'Nairobi Central Warehouse', contactPerson: 'John Doe', email: 'john@example.com', phone: '+254700000000', address: '123 Moi Avenue', kraPin: 'A123456789B', country: 'Kenya', region: 'Nairobi', gps: '-1.286389,36.817223', status: 'Active' },
        { id: 2, name: 'Mombasa Distribution Center', contactPerson: 'Jane Smith', email: 'jane@example.com', phone: '+254711111111', address: '456 Nkrumah Road', kraPin: 'C987654321D', country: 'Kenya', region: 'Mombasa', gps: '-4.043477,39.668205', status: 'Active' },
        { id: 3, name: 'Kisumu Warehouse', contactPerson: 'Michael Brown', email: 'michael@example.com', phone: '+254722222222', address: '789 Oginga Odinga Street', kraPin: 'E567891234F', country: 'Kenya', region: 'Kisumu', gps: '-0.102671,34.761770', status: 'Inactive' }
      ],
      
      // Warehouse modal
      showWarehouseModal: false,
      editingWarehouse: false,
      editingWarehouseId: null,
      newWarehouse: {
        name: '',
        contactPerson: '',
        email: '',
        phone: '',
        address: '',
        kraPin: '',
        country: 'Kenya',
        region: '',
        gps: '',
        status: 'Active'
      },
      
      // Country dropdown
      isCountryDropdownOpen: false,
      countrySearch: '',
      filteredCountries: [],
      countries: [
        'Kenya', 'Uganda', 'Tanzania', 'Rwanda', 'Burundi', 'Ethiopia', 'South Sudan', 'Somalia'
      ],
      
      // Region dropdown
      isRegionDropdownOpen: false,
      regionSearch: '',
      filteredRegions: [],
      regions: {
        'Kenya': ['Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret', 'Nyeri', 'Kakamega', 'Malindi'],
        'Uganda': ['Kampala', 'Entebbe', 'Jinja', 'Gulu', 'Mbarara'],
        'Tanzania': ['Dar es Salaam', 'Arusha', 'Mwanza', 'Dodoma', 'Zanzibar'],
        'Rwanda': ['Kigali', 'Butare', 'Gisenyi', 'Cyangugu'],
        'Burundi': ['Bujumbura', 'Gitega', 'Ngozi'],
        'Ethiopia': ['Addis Ababa', 'Dire Dawa', 'Gondar', 'Bahir Dar'],
        'South Sudan': ['Juba', 'Wau', 'Malakal'],
        'Somalia': ['Mogadishu', 'Hargeisa', 'Kismayo']
      }
    };
  },
  mounted() {
    // Set up event listeners for dropdown closures
    document.addEventListener('click', this.closeCountryDropdownOutside);
    document.addEventListener('click', this.closeRegionDropdownOutside);
    
    // Initialize filtered countries and regions
    this.filteredCountries = [...this.countries];
    this.updateRegionOptions();
    
    // In a real scenario, this is where we would fetch the warehouses from the API
    // this.fetchWarehouses();
  },
  beforeUnmount() {
    // Clean up event listeners
    document.removeEventListener('click', this.closeCountryDropdownOutside);
    document.removeEventListener('click', this.closeRegionDropdownOutside);
  },
  computed: {
    sortedWarehouses() {
      const warehouses = [...this.warehouses];
      warehouses.sort((a, b) => {
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
          return aValue.toString().localeCompare(bValue.toString()) * modifier;
        }
      });
      
      return warehouses;
    }
  },
  methods: {
    // API Methods
    fetchWarehouses() {
      // In a real scenario, this would fetch warehouses from API
      // Example:
      // axios.get(`/api/suppliers/${this.supplier.id}/warehouses`)
      //   .then(response => {
      //     this.warehouses = response.data;
      //   })
      //   .catch(error => {
      //     console.error('Error fetching warehouses:', error);
      //   });
      
      // For now, we'll just use our default data in the data() section
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
      if (this.sortKey === key) {
        return this.sortDir === 'asc' ? 'sort-asc' : 'sort-desc';
      }
      return 'sort-none';
    },
    
    // Warehouse methods
    openAddWarehouseModal() {
      this.editingWarehouse = false;
      this.editingWarehouseId = null;
      this.newWarehouse = {
        name: '',
        contactPerson: '',
        email: '',
        phone: '',
        address: '',
        kraPin: '',
        country: 'Kenya',
        region: '',
        gps: '',
        status: 'Active'
      };
      this.showWarehouseModal = true;
      this.isCountryDropdownOpen = false;
      this.isRegionDropdownOpen = false;
      
      // Reset dropdown filters
      this.updateRegionOptions();
    },
    closeWarehouseModal() {
      this.showWarehouseModal = false;
    },
    editWarehouse(warehouse) {
      this.editingWarehouse = true;
      this.editingWarehouseId = warehouse.id;
      this.newWarehouse = { ...warehouse };
      this.showWarehouseModal = true;
      
      // Update region options based on selected country
      this.updateRegionOptions();
    },
    deleteWarehouse(warehouseId) {
      // In a real scenario, this would make an API call to delete the warehouse
      // Example:
      // axios.delete(`/api/suppliers/${this.supplier.id}/warehouses/${warehouseId}`)
      //   .then(() => {
      //     // Remove the warehouse from the local list on success
      //     this.warehouses = this.warehouses.filter(warehouse => warehouse.id !== warehouseId);
      //     // Notify the parent that warehouses have been updated
      //     this.$emit('warehouses-updated', this.warehouses);
      //   })
      //   .catch(error => {
      //     console.error('Error deleting warehouse:', error);
      //   });
      
      // For now, just remove from the list without API call
      this.warehouses = this.warehouses.filter(warehouse => warehouse.id !== warehouseId);
      
      // Emit event to notify parent component
      this.$emit('warehouses-updated', this.warehouses);
    },
    saveWarehouse() {
      if (this.editingWarehouse) {
        // In a real scenario, this would make an API call to update the warehouse
        // Example:
        // axios.put(`/api/suppliers/${this.supplier.id}/warehouses/${this.editingWarehouseId}`, this.newWarehouse)
        //   .then(response => {
        //     // Update the warehouse in the local list on success
        //     const index = this.warehouses.findIndex(warehouse => warehouse.id === this.editingWarehouseId);
        //     if (index !== -1) {
        //       this.warehouses.splice(index, 1, { ...response.data });
        //     }
        //     this.closeWarehouseModal();
        //     // Notify the parent that warehouses have been updated
        //     this.$emit('warehouses-updated', this.warehouses);
        //   })
        //   .catch(error => {
        //     console.error('Error updating warehouse:', error);
        //   });
        
        // For now, just update the local list without API call
        const index = this.warehouses.findIndex(warehouse => warehouse.id === this.editingWarehouseId);
        if (index !== -1) {
          this.warehouses.splice(index, 1, { ...this.newWarehouse, id: this.editingWarehouseId });
        }
      } else {
        // In a real scenario, this would make an API call to create a new warehouse
        // Example:
        // axios.post(`/api/suppliers/${this.supplier.id}/warehouses`, this.newWarehouse)
        //   .then(response => {
        //     // Add the new warehouse to the local list on success
        //     this.warehouses.push(response.data);
        //     this.closeWarehouseModal();
        //     // Notify the parent that warehouses have been updated
        //     this.$emit('warehouses-updated', this.warehouses);
        //   })
        //   .catch(error => {
        //     console.error('Error creating warehouse:', error);
        //   });
        
        // For now, just add to the local list without API call
        const maxId = this.warehouses.length > 0 ? Math.max(...this.warehouses.map(w => w.id)) : 0;
        
        const warehouse = {
          id: maxId + 1,
          ...this.newWarehouse
        };
        
        this.warehouses.push(warehouse);
      }
      
      // Emit event to notify parent component
      this.$emit('warehouses-updated', this.warehouses);
      
      this.closeWarehouseModal();
    },
    
    // Country dropdown methods
    toggleCountryDropdown() {
      this.isCountryDropdownOpen = !this.isCountryDropdownOpen;
      
      if (this.isCountryDropdownOpen) {
        // Reset search when opening
        this.countrySearch = '';
        this.filteredCountries = [...this.countries];
        
        this.$nextTick(() => {
          const trigger = document.querySelector('.country-select-trigger');
          const dropdown = document.querySelector('.country-select-dropdown');
          
          if (!trigger || !dropdown) return;
          
          const triggerRect = trigger.getBoundingClientRect();
          const viewportHeight = window.innerHeight;
          
          const spaceBelow = viewportHeight - triggerRect.bottom;
          const dropdownHeight = Math.min(250, this.filteredCountries.length * 36 + 70);
          
          if (spaceBelow < dropdownHeight) {
            dropdown.classList.add('dropdown-top');
          } else {
            dropdown.classList.remove('dropdown-top');
          }
        });
      }
    },
    closeCountryDropdownOutside(event) {
      const dropdown = document.querySelector('.country-select-container');
      if (dropdown && !dropdown.contains(event.target)) {
        this.isCountryDropdownOpen = false;
      }
    },
    filterCountries() {
      if (this.countrySearch.trim() === '') {
        this.filteredCountries = [...this.countries];
      } else {
        const query = this.countrySearch.toLowerCase();
        this.filteredCountries = this.countries.filter(
          country => country.toLowerCase().includes(query)
        );
      }
    },
    selectCountry(country) {
      this.newWarehouse.country = country;
      // Reset region when country changes
      this.newWarehouse.region = '';
      this.isCountryDropdownOpen = false;
      
      // Update regions based on selected country
      this.updateRegionOptions();
    },
    
    // Region dropdown methods
    updateRegionOptions() {
      if (this.newWarehouse.country && this.regions[this.newWarehouse.country]) {
        this.filteredRegions = [...this.regions[this.newWarehouse.country]];
      } else {
        this.filteredRegions = [];
      }
    },
    toggleRegionDropdown() {
      this.isRegionDropdownOpen = !this.isRegionDropdownOpen;
      
      if (this.isRegionDropdownOpen) {
        // Reset search when opening
        this.regionSearch = '';
        this.updateRegionOptions();
        
        this.$nextTick(() => {
          const trigger = document.querySelector('.region-select-trigger');
          const dropdown = document.querySelector('.region-select-dropdown');
          
          if (!trigger || !dropdown) return;
          
          const triggerRect = trigger.getBoundingClientRect();
          const viewportHeight = window.innerHeight;
          
          const spaceBelow = viewportHeight - triggerRect.bottom;
          const dropdownHeight = Math.min(250, this.filteredRegions.length * 36 + 70);
          
          if (spaceBelow < dropdownHeight) {
            dropdown.classList.add('dropdown-top');
          } else {
            dropdown.classList.remove('dropdown-top');
          }
        });
      }
    },
    closeRegionDropdownOutside(event) {
      const dropdown = document.querySelector('.region-select-container');
      if (dropdown && !dropdown.contains(event.target)) {
        this.isRegionDropdownOpen = false;
      }
    },
    filterRegions() {
      if (this.regionSearch.trim() === '') {
        this.updateRegionOptions();
      } else {
        const query = this.regionSearch.toLowerCase();
        this.filteredRegions = this.regions[this.newWarehouse.country].filter(
          region => region.toLowerCase().includes(query)
        );
      }
    },
    selectRegion(region) {
      this.newWarehouse.region = region;
      this.isRegionDropdownOpen = false;
    }
  }
};
</script>

<style scoped>
@import './SupplierSharedStyles.css';
</style> 
