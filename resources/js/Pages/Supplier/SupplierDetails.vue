<template>
    <div>
      <AdminNavbar />
      <div class="page-container">
        <Head :title="`Supplier #${supplier.id}`" />
        <div class="content-container">
          <div class="header-container">
            <h1 class="page-title">Supplier Details</h1>
            <button class="back-button" @click="goBack">
              <span>←</span> Back to Suppliers
            </button>
          </div>
          <div class="supplier-details-container" v-if="supplier">
            <!-- Supplier Header with Status -->
            <div class="order-header">
              <div class="order-id">Supplier #{{ supplier.id }}</div>
              <div class="status-container">
                <div :class="['status-badge', supplier.status === 'active' ? 'status-active' : 'status-inactive']">
                  {{ supplier.status }}
                </div>
                <button 
                  v-if="supplier.status !== 'active'" 
                  class="activate-button" 
                  @click="activateSupplier">
                  Activate
                </button>
              </div>
            </div>
    
            <!-- Supplier Details Panel -->
            <div class="order-details-panel">
              <div class="detail-row">
                <span class="detail-label">Company Name:</span>
                <span class="detail-value">{{ supplier.company_name }}</span>
              </div>
    
              <div class="detail-row">
                <span class="detail-label">Office Address:</span>
                <span class="detail-value">{{ supplier.office_address }}</span>
              </div>
    
              <div class="detail-row">
                <span class="detail-label">Contact Person:</span>
                <span class="detail-value">{{ supplier.contact_person }}</span>
              </div>
    
              <div class="detail-row">
                <span class="detail-label">Email Address:</span>
                <span class="detail-value">{{ supplier.email }}</span>
              </div>
    
              <div class="detail-row">
                <span class="detail-label">Phone Number:</span>
                <span class="detail-value">{{ supplier.phone_number }}</span>
              </div>
    
              <div class="detail-row">
                <span class="detail-label">KRA PIN:</span>
                <span class="detail-value">{{ supplier.krapin || supplier.kra_pin }}</span>
              </div>
    
              <div class="detail-row">
                <span class="detail-label">Industry:</span>
                <span class="detail-value">{{ supplier.industry }}</span>
              </div>
              
              <!-- Edit Supplier Details Button -->
              <div class="edit-details-container">
                <button @click="openEditSupplierModal" class="edit-details-btn">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                  </svg>
                  Edit Supplier Details
                </button>
              </div>
            </div>
    
            <!-- Tabs Section -->
            <div class="tabs-container">
              <ul class="tabs">
                <li 
                  v-for="tab in tabs" 
                  :key="tab.id" 
                  :class="['tab', { active: activeTab === tab.id }]"
                  @click="setActiveTab(tab.id)">
                  {{ tab.name }}
                </li>
              </ul>
            </div>
    
            <!-- Bank Accounts Tab Content -->
            <div v-if="activeTab === 'bankAccounts'" class="tab-content">
              <BankAccounts 
                :supplier="supplier"
              />
            </div>
    
            <!-- Warehouses Tab Content -->
            <div v-if="activeTab === 'warehouses'" class="tab-content">
              <Warehouses 
                :supplier="supplier"
                @warehouses-updated="updateWarehouses"
              />
            </div>
    
            <!-- Delivery Regions Tab Content -->
            <div v-if="activeTab === 'deliveryRegions'" class="tab-content">
              <DeliveryRegions 
                :supplier="supplier"
              />
            </div>
    
            <!-- Products Tab Content -->
            <div v-if="activeTab === 'products'" class="tab-content">
              <Products 
                :supplier="supplier"
              />
            </div>
          </div>
        </div>
      </div>
  
      <!-- Edit Supplier Details Modal -->
      <div v-if="showSupplierModal" class="modal-overlay" @click.self="closeSupplierModal">
        <div class="modal-container">
          <div class="modal-header">
            <h2>Edit Supplier Details</h2>
            <button class="close-btn" @click="closeSupplierModal">&times;</button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveSupplierDetails">
              <div class="form-group">
                <label for="companyName">Company Name <span class="required">*</span></label>
                <input type="text" id="companyName" v-model="editedSupplier.companyName" required placeholder="Enter company name">
              </div>
              <div class="form-group">
                <label for="officeAddress">Office Address <span class="required">*</span></label>
                <input type="text" id="officeAddress" v-model="editedSupplier.officeAddress" required placeholder="Enter office address">
              </div>
              <div class="form-group">
                <label for="contactPerson">Contact Person <span class="required">*</span></label>
                <input type="text" id="contactPerson" v-model="editedSupplier.contactPerson" required placeholder="Enter contact person">
              </div>
              <div class="form-group">
                <label for="email">Email Address <span class="required">*</span></label>
                <input type="email" id="email" v-model="editedSupplier.email" required placeholder="Enter email address">
              </div>
              <div class="form-group">
                <label for="phoneNumber">Phone Number <span class="required">*</span></label>
                <input type="text" id="phoneNumber" v-model="editedSupplier.phoneNumber" required placeholder="Enter phone number">
              </div>
              <div class="form-group">
                <label for="kraPin">KRA PIN <span class="required">*</span></label>
                <input type="text" id="kraPin" v-model="editedSupplier.kraPin" required placeholder="Enter KRA PIN">
              </div>
              
              <!-- Industry Dropdown -->
              <div class="form-group">
                <label for="industry">Industry <span class="required">*</span></label>
                <div class="custom-select-container industry-select-container">
                  <div 
                    class="custom-select-trigger industry-select-trigger" 
                    @click="toggleIndustryDropdown"
                    :class="{ 'active': isIndustryDropdownOpen }"
                  >
                    <span>{{ editedSupplier.industry || 'Select an industry' }}</span>
                    <svg 
                      class="dropdown-arrow" 
                      :class="{ 'open': isIndustryDropdownOpen }"
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
                  
                  <div class="custom-select-dropdown industry-select-dropdown" v-show="isIndustryDropdownOpen">
                    <div class="search-box">
                      <input
                        type="text"
                        v-model="industrySearch"
                        @input="filterIndustries"
                        placeholder="Search industry..."
                        class="dropdown-search"
                        @click.stop
                      >
                    </div>
                    
                    <div class="dropdown-options">
                      <div
                        v-for="industry in filteredIndustries"
                        :key="industry"
                        class="dropdown-option"
                        @click="selectIndustry(industry)"
                      >
                        {{ industry }}
                      </div>
                      <div v-if="filteredIndustries.length === 0" class="no-results">
                        No industries match your search
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="form-actions">
                <button type="button" class="cancel-btn" @click="closeSupplierModal">Cancel</button>
                <button type="submit" class="submit-btn">Save Changes</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { Head, router } from '@inertiajs/vue3';
  import AdminNavbar from '@/Components/AdminNavbar.vue';
  import Modal from '@/Components/Modal.vue';
  import BankAccounts from './SupplierComponents/BankAccounts.vue';
  import DeliveryRegions from './SupplierComponents/DeliveryRegions.vue';
  import Warehouses from './SupplierComponents/Warehouses.vue';
  import Products from './SupplierComponents/Products.vue';
  
  export default {
    name: 'SupplierDetails',
    components: {
      Head,
      AdminNavbar,
      Modal,
      BankAccounts,
      DeliveryRegions,
      Warehouses,
      Products
    },
    props: {
      supplier: {
        type: Object,
        required: true
      }
    },
    mounted() {
      console.log('Supplier component mounted with data:', this.supplier);
      
      // Add industry dropdown event listener
      document.addEventListener('click', this.closeIndustryDropdownOutside);
    },
    beforeUnmount() {
      // Remove industry dropdown event listener
      document.removeEventListener('click', this.closeIndustryDropdownOutside);
    },
    data() {
      return {
        // Basic data
        supplier: this.supplier,
        activeTab: 'bankAccounts',
        sortKey: 'name',
        sortDir: 'asc',
        
        // Country and region data
        isCountryDropdownOpen: false,
        countrySearch: '',
        filteredCountries: [],
        countries: ['Kenya', 'Uganda', 'Tanzania', 'Rwanda', 'Ethiopia', 'South Africa', 'Nigeria'],
        
        isRegionDropdownOpen: false,
        regionSearch: '',
        filteredRegions: [],
        regions: [
          'Nairobi CBD',
          'Westlands',
          'Kiambu',
          'Karen',
          'Parklands',
          'Mombasa Island',
          'Kilimani',
          'Lavington',
          'South B',
          'South C',
          'Eastleigh',
          'Gigiri',
          'Hurlingham',
          'Nyari'
        ],
        
        // Warehouses data - keep this for passing to the Warehouses component
        warehouses: [
          { id: 1, name: 'Nairobi Central Warehouse', contactPerson: 'John Doe', email: 'john@example.com', phone: '+254700000000', address: '123 Moi Avenue', kraPin: 'A123456789B', country: 'Kenya', region: 'Nairobi', gps: '-1.286389,36.817223', status: 'Active' },
          { id: 2, name: 'Mombasa Distribution Center', contactPerson: 'Jane Smith', email: 'jane@example.com', phone: '+254711111111', address: '456 Nkrumah Road', kraPin: 'C987654321D', country: 'Kenya', region: 'Mombasa', gps: '-4.043477,39.668205', status: 'Active' },
          { id: 3, name: 'Kisumu Warehouse', contactPerson: 'Michael Brown', email: 'michael@example.com', phone: '+254722222222', address: '789 Oginga Odinga Street', kraPin: 'E567891234F', country: 'Kenya', region: 'Kisumu', gps: '-0.102671,34.761770', status: 'Inactive' }
        ],
        
        // Edit Supplier Modal
        showSupplierModal: false,
        editedSupplier: {
          companyName: '',
          officeAddress: '',
          contactPerson: '',
          email: '',
          phoneNumber: '',
          kraPin: '',
          industry: ''
        },
        
        // Industry dropdown
        isIndustryDropdownOpen: false,
        industrySearch: '',
        filteredIndustries: [],
        industries: [
          'Food & Beverage', 'Retail', 'Manufacturing', 'Agriculture', 'Healthcare', 
          'Technology', 'Construction', 'Hospitality', 'Education', 'Transportation',
          'Energy', 'Financial Services', 'Mining', 'Pharmaceuticals', 'Telecommunications'
        ],
        
        // Tabs
        tabs: [
          { id: 'bankAccounts', name: 'Bank Accounts' },
          { id: 'warehouses', name: 'Warehouses' },
          { id: 'deliveryRegions', name: 'Delivery Regions' },
          { id: 'products', name: 'Products' }
        ]
      };
    },
    computed: {
      sortedProducts() {
        const products = [...this.products];
        products.sort((a, b) => {
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
        
        return products;
      }
    },
    created() {
      // Supplier is directly available as props
      // We only need the route query approach as a fallback if we're directly navigating to the page
      if (!this.supplier && this.$route && this.$route.query && this.$route.query.supplierData) {
        try {
          this.supplierData = JSON.parse(this.$route.query.supplierData);
        } catch (e) {
          console.error('Error parsing supplier data:', e);
        }
      }
      
      // Initialize filtered countries and regions
      this.filteredCountries = [...this.countries];
      this.filteredRegions = [...this.regions];
      
      // Initialize filtered industries
      this.filteredIndustries = [...this.industries];
      
      // Add click outside listener for dropdowns
      document.addEventListener('click', this.closeCountryDropdownOutside);
      document.addEventListener('click', this.closeRegionDropdownOutside);
      document.addEventListener('click', this.closeIndustryDropdownOutside);
    },
    
    beforeDestroy() {
      // Remove event listener when component is destroyed
      document.removeEventListener('click', this.closeDropdownsOnClickOutside);
    },
    
    methods: {
      goBack() {
        window.history.back();
      },
      activateSupplier() {
        try {
          // Prepare data for API call
          const supplierData = {
            uuid: this.supplier.uuid,
            status: 'active'
          };
          
          console.log('Activating supplier:', supplierData);
          
          // Use Inertia router to activate the supplier
          router.post('/admin/suppliers/activate', supplierData, {
            preserveScroll: true,
            onError: (errors) => {
              alert('Error activating supplier');
              console.error(errors);
            }
          });
        } catch (error) {
          alert('Error activating supplier: ' + error.message);
          console.error('Error in activateSupplier:', error);
        }
      },
      setActiveTab(tabId) {
        this.activeTab = tabId;
      },
      sortBy(key) {
        if (this.sortKey === key) {
          this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
        } else {
          this.sortKey = key;
          this.sortDir = 'asc';
        }
      },
      getSortIcon(key) {
        if (this.sortKey !== key) return 'sort-icon sort-none';
        return this.sortDir === 'asc' ? 'sort-icon sort-asc' : 'sort-icon sort-desc';
      },
      
      // Region dropdown methods
      toggleRegionDropdown() {
        this.isRegionDropdownOpen = !this.isRegionDropdownOpen;
        
        if (this.isRegionDropdownOpen) {
          // Reset search when opening
          this.regionSearch = '';
          this.filteredRegions = [...this.regions];
          
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
          this.filteredRegions = [...this.regions];
        } else {
          const query = this.regionSearch.toLowerCase();
          this.filteredRegions = this.regions.filter(
            region => region.toLowerCase().includes(query)
          );
        }
      },
      selectRegion(region) {
        this.newWarehouse.region = region;
        this.isRegionDropdownOpen = false;
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
        this.isCountryDropdownOpen = false;
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
      },
      closeWarehouseModal() {
        this.showWarehouseModal = false;
      },
      editWarehouse(warehouse) {
        this.editingWarehouse = true;
        this.editingWarehouseId = warehouse.id;
        this.newWarehouse = { ...warehouse };
        this.showWarehouseModal = true;
      },
      deleteWarehouse(warehouseId) {
        // Remove the warehouse
        this.warehouses = this.warehouses.filter(warehouse => warehouse.id !== warehouseId);
      },
      saveWarehouse() {
        if (this.editingWarehouse) {
          // Update existing warehouse
          const index = this.warehouses.findIndex(warehouse => warehouse.id === this.editingWarehouseId);
          if (index !== -1) {
            this.warehouses.splice(index, 1, { ...this.newWarehouse, id: this.editingWarehouseId });
          }
        } else {
          // Create a new warehouse with unique ID
          const maxId = this.warehouses.length > 0 ? Math.max(...this.warehouses.map(w => w.id)) : 0;
          
          const warehouse = {
            id: maxId + 1,
            ...this.newWarehouse
          };
          
          this.warehouses.push(warehouse);
        }
        this.closeWarehouseModal();
      },
      
      // Supplier edit methods
      openEditSupplierModal() {
        try {
          console.log('Opening edit supplier modal for supplier:', this.supplier);
          
          // Clone supplier data to avoid direct modification
          // Map database field names to form field names
          this.editedSupplier = {
            companyName: this.supplier.company_name,
            officeAddress: this.supplier.office_address,
            contactPerson: this.supplier.contact_person,
            email: this.supplier.email,
            phoneNumber: this.supplier.phone_number,
            kraPin: this.supplier.krapin || this.supplier.kra_pin,
            industry: this.supplier.industry
          };
          
          this.showSupplierModal = true;
          this.isIndustryDropdownOpen = false;
          
          // Initialize filtered industries if not already done
          if (this.filteredIndustries.length === 0) {
            this.filteredIndustries = [...this.industries];
          }
        } catch (error) {
          alert('Error opening edit supplier modal');
        }
      },
      
      closeSupplierModal() {
        this.showSupplierModal = false;
      },
      
      saveSupplierDetails() {
        // Validate required fields
        if (!this.editedSupplier.companyName || 
            !this.editedSupplier.officeAddress || 
            !this.editedSupplier.contactPerson ||
            !this.editedSupplier.email ||
            !this.editedSupplier.phoneNumber ||
            !this.editedSupplier.kraPin ||
            !this.editedSupplier.industry) {
          alert('Please fill in all required fields');
          return;
        }
        
        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(this.editedSupplier.email)) {
          alert('Please enter a valid email address');
          return;
        }
        
        try {
          // Prepare data for API call, mapping form fields to database fields
          const supplierData = {
            uuid: this.supplier.uuid,
            company_name: this.editedSupplier.companyName,
            office_address: this.editedSupplier.officeAddress,
            contact_person: this.editedSupplier.contactPerson,
            email: this.editedSupplier.email,
            phone_number: this.editedSupplier.phoneNumber,
            krapin: this.editedSupplier.kraPin,
            industry: this.editedSupplier.industry
          };
          
          console.log('Updating supplier with data:', supplierData);
          
          // Use Inertia router to update the supplier
          router.post('/admin/suppliers/update', supplierData, {
            preserveScroll: true,
            onSuccess: () => {
              this.closeSupplierModal();
            },
            onError: (errors) => {
              alert('Error updating supplier details');
              console.error(errors);
            }
          });
        } catch (error) {
          alert('Error updating supplier: ' + error.message);
          console.error('Error in saveSupplierDetails:', error);
        }
      },
      
      // Industry dropdown methods
      toggleIndustryDropdown() {
        this.isIndustryDropdownOpen = !this.isIndustryDropdownOpen;
        
        if (this.isIndustryDropdownOpen) {
          // Reset search when opening
          this.industrySearch = '';
          this.filteredIndustries = [...this.industries];
          
          this.$nextTick(() => {
            const trigger = document.querySelector('.industry-select-trigger');
            const dropdown = document.querySelector('.industry-select-dropdown');
            
            if (!trigger || !dropdown) return;
            
            const triggerRect = trigger.getBoundingClientRect();
            const viewportHeight = window.innerHeight;
            
            const spaceBelow = viewportHeight - triggerRect.bottom;
            const dropdownHeight = Math.min(250, this.filteredIndustries.length * 36 + 70);
            
            if (spaceBelow < dropdownHeight) {
              dropdown.classList.add('dropdown-top');
            } else {
              dropdown.classList.remove('dropdown-top');
            }
          });
        }
      },
      
      closeIndustryDropdownOutside(event) {
        const dropdown = document.querySelector('.industry-select-container');
        if (dropdown && !dropdown.contains(event.target)) {
          this.isIndustryDropdownOpen = false;
        }
      },
      
      filterIndustries() {
        const search = this.industrySearch.toLowerCase();
        this.filteredIndustries = this.industries.filter(industry => 
          industry.toLowerCase().includes(search)
        );
      },
      
      selectIndustry(industry) {
        this.editedSupplier.industry = industry;
        this.isIndustryDropdownOpen = false;
      },
      
      updateWarehouses(updatedWarehouses) {
        this.warehouses = updatedWarehouses;
      },
    }
  }
  </script>
  
  <style scoped>
  @import './SupplierComponents/SupplierSharedStyles.css';
  </style>