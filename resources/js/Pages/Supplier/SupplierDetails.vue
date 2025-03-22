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
                :initialWarehouses="warehouses"
                @warehouses-updated="updateWarehouses"
              />
            </div>
    
            <!-- Delivery Regions Tab Content -->
            <div v-if="activeTab === 'deliveryRegions'" class="tab-content">
              <DeliveryRegions 
                :initialRegions="deliveryRegions" 
                @regions-updated="updateDeliveryRegions"
              />
            </div>
    
            <!-- Products Tab Content -->
            <div v-if="activeTab === 'products'" class="tab-content">
              <div class="table-controls">
                <div class="table-title">Products</div>
                <button @click="openAddProductModal" class="add-btn">
                  <span class="plus-icon">+</span>
                  Add Product
                </button>
              </div>
              
              <div class="table-container">
                <div class="table-wrapper">
                  <table class="data-table">
                    <thead>
                      <tr>
                        <th>Product</th>
                        <th @click="sortBy('productName')" class="sortable">
                          Name <i :class="getSortIcon('productName')"></i>
                        </th>
                        <th @click="sortBy('skuNumber')" class="sortable">
                          SKU Number <i :class="getSortIcon('skuNumber')"></i>
                        </th>
                        <th @click="sortBy('category')" class="sortable">
                          Category <i :class="getSortIcon('category')"></i>
                        </th>
                        <th @click="sortBy('unitOfMeasure')" class="sortable">
                          Unit of Measure <i :class="getSortIcon('unitOfMeasure')"></i>
                        </th>
                        <th @click="sortBy('description')" class="sortable">
                          Description <i :class="getSortIcon('description')"></i>
                        </th>
                        <th @click="sortBy('manufacturer')" class="sortable">
                          Manufacturer <i :class="getSortIcon('manufacturer')"></i>
                        </th>
                        <th @click="sortBy('status')" class="sortable">
                          Status <i :class="getSortIcon('status')"></i>
                        </th>
                        <th class="actions-column">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="product in sortedProducts" :key="product.id" class="data-row">
                        <td>
                          <img :src="product.imageUrl" alt="Product image" class="product-thumbnail">
                        </td>
                        <td>{{ product.productName }}</td>
                        <td>{{ product.skuNumber }}</td>
                        <td>{{ product.category }}</td>
                        <td>{{ product.unitOfMeasure }}</td>
                        <td class="description-cell">{{ product.description }}</td>
                        <td>{{ product.manufacturer }}</td>
                        <td>
                          <span :class="['status-pill', product.status === 'Active' ? 'status-active' : 'status-inactive']">
                            {{ product.status }}
                          </span>
                        </td>
                        <td class="actions-column">
                          <button @click="editProduct(product)" class="action-btn edit-btn">
                            Edit
                          </button>
                        </td>
                      </tr>
                      <tr v-if="products.length === 0">
                        <td colspan="9" class="empty-state">
                          No products added yet
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
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
          
          <!-- Delivery Regions Tab Content -->
          <div v-if="activeTab === 'deliveryRegions'" class="tab-content">
            <DeliveryRegions 
              :initialRegions="deliveryRegions" 
              @regions-updated="updateDeliveryRegions"
            />
          </div>
  
          <!-- Products Tab Content -->
          <div v-if="activeTab === 'products'" class="tab-content">
            <div class="table-controls">
              <div class="table-title">Products</div>
              <button @click="openAddProductModal" class="add-btn">
                <span class="plus-icon">+</span>
                Add Product
              </button>
            </div>
            
            <div class="table-container">
              <div class="table-wrapper">
                <table class="data-table">
                  <thead>
                    <tr>
                      <th>Product</th>
                      <th @click="sortBy('productName')" class="sortable">
                        Name <i :class="getSortIcon('productName')"></i>
                      </th>
                      <th @click="sortBy('skuNumber')" class="sortable">
                        SKU Number <i :class="getSortIcon('skuNumber')"></i>
                      </th>
                      <th @click="sortBy('category')" class="sortable">
                        Category <i :class="getSortIcon('category')"></i>
                      </th>
                      <th @click="sortBy('unitOfMeasure')" class="sortable">
                        Unit of Measure <i :class="getSortIcon('unitOfMeasure')"></i>
                      </th>
                      <th @click="sortBy('description')" class="sortable">
                        Description <i :class="getSortIcon('description')"></i>
                      </th>
                      <th @click="sortBy('manufacturer')" class="sortable">
                        Manufacturer <i :class="getSortIcon('manufacturer')"></i>
                      </th>
                      <th @click="sortBy('status')" class="sortable">
                        Status <i :class="getSortIcon('status')"></i>
                      </th>
                      <th class="actions-column">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="product in sortedProducts" :key="product.id" class="data-row">
                      <td>
                        <img :src="product.imageUrl" alt="Product image" class="product-thumbnail">
                      </td>
                      <td>{{ product.productName }}</td>
                      <td>{{ product.skuNumber }}</td>
                      <td>{{ product.category }}</td>
                      <td>{{ product.unitOfMeasure }}</td>
                      <td class="description-cell">{{ product.description }}</td>
                      <td>{{ product.manufacturer }}</td>
                      <td>
                        <span :class="['status-pill', product.status === 'Active' ? 'status-active' : 'status-inactive']">
                          {{ product.status }}
                        </span>
                      </td>
                      <td class="actions-column">
                        <button @click="editProduct(product)" class="action-btn edit-btn">
                          Edit
                        </button>
                      </td>
                    </tr>
                    <tr v-if="products.length === 0">
                      <td colspan="9" class="empty-state">
                        No products added yet
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
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
      
      <!-- Add Product Modal -->
      <div v-if="showProductModal" class="modal-overlay" @click.self="closeAddProductModal">
        <div class="modal-container">
          <!-- Product modal content -->
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { Head, Link, router } from '@inertiajs/vue3';
  import AdminNavbar from '@/Components/AdminNavbar.vue';
  import Modal from '@/Components/Modal.vue';
  import BankAccounts from './SupplierComponents/BankAccounts.vue';
  import DeliveryRegions from './SupplierComponents/DeliveryRegions.vue';
  import Warehouses from './SupplierComponents/Warehouses.vue';
  
  export default {
    name: 'SupplierDetails',
    components: {
      Head,
      AdminNavbar,
      Modal,
      BankAccounts,
      DeliveryRegions,
      Warehouses
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
        
        // Delivery Regions data
        deliveryRegions: [
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
        
        // Products data
        showProductModal: false,
        editingProduct: false,
        editingProductId: null,
        newProduct: {
          productName: '',
          skuNumber: '',
          category: '',
          unitOfMeasure: '',
          description: '',
          manufacturer: '',
          status: 'Active'
        },
        products: [
          { id: 1, imageUrl: 'https://via.placeholder.com/80', productName: 'Premium Coffee Beans', skuNumber: 'CB001', category: 'Beverages', unitOfMeasure: 'kg', description: 'High-quality arabica coffee beans from Ethiopia', manufacturer: 'Global Coffee Co.', status: 'Active' },
          { id: 2, imageUrl: 'https://via.placeholder.com/80', productName: 'Whole Wheat Flour', skuNumber: 'WF002', category: 'Baking Supplies', unitOfMeasure: 'kg', description: 'Organic stone-ground whole wheat flour', manufacturer: 'Healthy Mills Inc.', status: 'Active' },
          { id: 3, imageUrl: 'https://via.placeholder.com/80', productName: 'Extra Virgin Olive Oil', skuNumber: 'OL003', category: 'Oils & Condiments', unitOfMeasure: 'liter', description: 'Cold-pressed extra virgin olive oil from Spain', manufacturer: 'Mediterranean Delights', status: 'Inactive' }
        ],
        
        // Categories and units of measure for products
        productCategories: ['Beverages', 'Dairy', 'Meat & Poultry', 'Seafood', 'Fruits & Vegetables', 'Bakery', 'Grains & Pasta', 'Canned Goods', 'Snacks', 'Condiments & Sauces', 'Baking Supplies', 'Oils & Vinegars'],
        unitsOfMeasure: ['kg', 'g', 'liter', 'ml', 'unit', 'pack', 'box', 'carton', 'dozen', 'case'],
        
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
            return aValue.toString().localeCompare(bValue.toString()) * modifier;
          }
        });
        
        return regions;
      },
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
      
      // Initialize filtered warehouses for delivery regions
      this.filteredWarehouses = [...this.warehouses.map(w => w.name)];
      
      // Initialize filtered regions for Deliver To dropdown
      this.filteredDeliverToRegions = [...this.regions];
      
      // Add click outside listener for dropdowns
      document.addEventListener('click', this.closeCountryDropdownOutside);
      document.addEventListener('click', this.closeRegionDropdownOutside);
      document.addEventListener('click', this.closeIndustryDropdownOutside);
      document.addEventListener('click', this.closeWarehouseDropdownOutside);
      document.addEventListener('click', this.closeDeliverToDropdownOutside);
    },
    
    beforeDestroy() {
      // Remove event listener when component is destroyed
      document.removeEventListener('click', this.closeDropdownsOnClickOutside);
    },
    
    // Product Modal Methods
    openAddProductModal() {
      this.editingProduct = false;
      this.editingProductId = null;
      this.newProduct = {
        productName: '',
        skuNumber: '',
        category: '',
        unitOfMeasure: '',
        description: '',
        manufacturer: '',
        status: 'Active'
      };
      this.imagePreview = null;
      this.productImages = [];
      this.showProductModal = true;
    },
    methods: {
      closeProductModal() {
        this.showProductModal = false;
        this.imagePreview = null;
      },
      
      editProduct(product) {
        this.editingProduct = true;
        this.editingProductId = product.id;
        this.newProduct = { ...product };
        this.imagePreview = product.imageUrl;
        this.productImages = product.images ? [...product.images] : [];
        if (this.productImages.length === 0 && product.imageUrl) {
          // If there are no images but there is an imageUrl, create an image entry
          this.productImages = [{
            id: 1,
            url: product.imageUrl,
            isPrimary: true
          }];
        }
        this.selectedImageIndex = this.productImages.findIndex(img => img.isPrimary) || 0;
        this.showProductModal = true;
      },
      
      saveProduct() {
        // Get the primary image URL for the main imageUrl field
        const primaryImage = this.productImages.find(img => img.isPrimary);
        const primaryImageUrl = primaryImage ? primaryImage.url : 'https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg';
        
        if (this.editingProduct) {
          // Update existing product
          const index = this.products.findIndex(product => product.id === this.editingProductId);
          if (index !== -1) {
            this.products.splice(index, 1, { 
              ...this.newProduct, 
              id: this.editingProductId,
              imageUrl: primaryImageUrl,
              images: [...this.productImages]
            });
          }
        } else {
          // Add new product
          const maxId = this.products.length > 0 ? Math.max(...this.products.map(p => p.id)) : 0;
          this.products.push({
            ...this.newProduct,
            id: maxId + 1,
            imageUrl: primaryImageUrl,
            images: [...this.productImages]
          });
        }
        
        this.closeProductModal();
      },
      
      // Image Upload Methods
      onFileSelected(event) {
        const files = event.target.files;
        if (files && files.length > 0) {
          Array.from(files).forEach(file => {
            this.addImageToGallery(file);
          });
        }
      },
      
      onDragOver(event) {
        event.preventDefault();
        this.isDragging = true;
      },
      
      onDragLeave(event) {
        event.preventDefault();
        this.isDragging = false;
      },
      
      onDrop(event) {
        this.isDragging = false;
        const files = event.dataTransfer.files;
        if (files && files.length > 0) {
          Array.from(files).forEach(file => {
            if (file.type.match('image.*')) {
              this.addImageToGallery(file);
            }
          });
        }
      },
      
      addImageToGallery(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          const newImageId = this.productImages.length > 0 ? 
            Math.max(...this.productImages.map(img => img.id)) + 1 : 1;
          
          const newImage = {
            id: newImageId,
            url: e.target.result,
            isPrimary: this.productImages.length === 0 // First image is primary by default
          };
          
          this.productImages.push(newImage);
          
          // Update imagePreview if this is the primary image
          if (newImage.isPrimary) {
            this.imagePreview = newImage.url;
            this.selectedImageIndex = this.productImages.length - 1;
          }
        };
        reader.readAsDataURL(file);
      },
      
      removeImage(imageId) {
        const index = this.productImages.findIndex(img => img.id === imageId);
        if (index !== -1) {
          const wasRemovingPrimary = this.productImages[index].isPrimary;
          this.productImages.splice(index, 1);
          
          // If we removed the primary image and there are other images, set a new primary
          if (wasRemovingPrimary && this.productImages.length > 0) {
            this.productImages[0].isPrimary = true;
            this.imagePreview = this.productImages[0].url;
            this.selectedImageIndex = 0;
          } else if (this.productImages.length === 0) {
            // If no images left
            this.imagePreview = null;
            this.selectedImageIndex = -1;
          }
        }
      },
      
      setPrimaryImage(imageId) {
        // Reset all images to non-primary
        this.productImages.forEach(img => {
          img.isPrimary = (img.id === imageId);
        });
        
        // Update the preview with the new primary image
        const primaryImage = this.productImages.find(img => img.id === imageId);
        if (primaryImage) {
          this.imagePreview = primaryImage.url;
          this.selectedImageIndex = this.productImages.findIndex(img => img.id === imageId);
        }
      },
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
      
      // Product Methods
      openAddProductModal() {
        this.editingProduct = false;
        this.editingProductId = null;
        this.newProduct = {
          productName: '',
          category: '',
          price: '',
          stock: '',
          status: 'Active'
        };
        this.showProductModal = true;
      },
      
      deleteProduct(productId) {
        if (confirm('Are you sure you want to delete this product?')) {
          this.products = this.products.filter(product => product.id !== productId);
        }
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
      },
      closeDropdownsOnClickOutside(event) {
        // Close industry dropdown if clicking outside
        if (this.isIndustryDropdownOpen) {
          const container = document.querySelector('.industry-select-container');
          if (container && !container.contains(event.target)) {
            this.isIndustryDropdownOpen = false;
          }
        }
        
        // Close other dropdowns similarly if needed
      },
      updateDeliveryRegions(updatedRegions) {
        this.deliveryRegions = updatedRegions;
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