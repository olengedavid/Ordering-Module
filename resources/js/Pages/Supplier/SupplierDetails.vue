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
                :bankAccounts="bankAccounts"
              />
            </div>
    
            <!-- Warehouses Tab Content -->
            <div v-if="activeTab === 'warehouses'" class="tab-content">
              <div class="table-controls">
                <div class="table-title">Warehouses </div>
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
  
      <!-- Add Bank Account Modal -->
      <div v-if="showBankAccountModal" class="modal-overlay" @click.self="closeBankAccountModal">
        <div class="modal-container">
          <div class="modal-header">
            <h2>{{ editingBankAccount ? 'Edit Bank Account' : 'Add New Bank Account' }}</h2>
            <button class="close-btn" @click="closeBankAccountModal">&times;</button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveBankAccount">
              <div class="form-group">
                <label for="bankName">Bank Name <span class="required">*</span></label>
                
                <!-- Custom searchable dropdown for banks -->
                <div class="custom-select-container">
                  <div 
                    class="custom-select-trigger" 
                    @click="toggleBankDropdown"
                    :class="{ 'active': isBankDropdownOpen }"
                  >
                    <span>{{ newBankAccount.bankName || 'Select a bank' }}</span>
                    <svg 
                      class="dropdown-arrow" 
                      :class="{ 'open': isBankDropdownOpen }"
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
                  
                  <div class="custom-select-dropdown" v-show="isBankDropdownOpen">
                    <div class="search-box">
                      <input
                        type="text"
                        v-model="bankSearch"
                        @input="filterBanks"
                        placeholder="Search bank..."
                        class="dropdown-search"
                        @click.stop
                      >
                    </div>
                    
                    <div class="dropdown-options">
                      <div
                        v-for="bank in filteredBanks"
                        :key="bank"
                        class="dropdown-option"
                        @click="selectBank(bank)"
                      >
                        {{ bank }}
                      </div>
                      <div v-if="filteredBanks.length === 0" class="no-results">
                        No banks match your search
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="branch">Branch <span class="required">*</span></label>
                <input type="text" id="branch" v-model="newBankAccount.branch" required placeholder="Enter branch name">
              </div>
              <div class="form-group">
                <label for="accountName">Account Name <span class="required">*</span></label>
                <input type="text" id="accountName" v-model="newBankAccount.accountName" required placeholder="Enter account name">
              </div>
              <div class="form-group">
                <label for="accountNumber">Account Number <span class="required">*</span></label>
                <input type="text" id="accountNumber" v-model="newBankAccount.accountNumber" required placeholder="Enter account number">
              </div>
              <div class="form-actions">
                <button type="button" class="cancel-btn" @click="closeBankAccountModal">Cancel</button>
                <button type="submit" class="submit-btn">Save Bank Account</button>
              </div>
            </form>
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
  import { Head } from "@inertiajs/vue3";
  import AdminNavbar from "@/Components/AdminNavbar.vue";
  import Modal from '@/Components/Modal.vue';
  import { router } from '@inertiajs/vue3';
  import BankAccounts from "./SupplierComponents/BankAccounts.vue";
  import DeliveryRegions from "./SupplierComponents/DeliveryRegions.vue";
  
  export default {
    name: 'SupplierDetails',
    components: {
      Head,
      AdminNavbar,
      Modal,
      BankAccounts,
      DeliveryRegions
    },
    props: {
      supplier: {
        type: Object,
        required: true
      },
      bankAccounts: {
        type: Array,
        default: () => []
      }
    },
    mounted() {
      // Initialize filtered industries list
      this.filteredIndustries = [...this.industries];
      
      // Initialize sortedKey for tables
      this.sortedKey = '';
      this.sortedDir = 'asc';
      
      // Add event listener for closing dropdowns when clicking outside
      document.addEventListener('click', this.closeDropdownsOnClickOutside);
      
      console.log('Supplier component mounted with data:', this.supplier);
    },
    data() {
      return {
        productImages: [],
        selectedImageIndex: -1,
        supplierData: null,
        activeTab: "bankAccounts",
        tabs: [
          { id: 'bankAccounts', name: 'Bank Accounts' },
          { id: 'warehouses', name: 'Warehouses' },
          { id: 'deliveryRegions', name: 'Delivery Regions' },
          { id: 'products', name: 'Products' }
        ],
        sortKey: 'id',
        sortDir: 'asc',
        products: [
          { 
            id: 1, 
            productName: 'NPK 17-17-17 Complete Fertilizer', 
            skuNumber: 'NPK-17171701',
            category: 'Compound Fertilizer',
            unitOfMeasure: '50 kg',
            description: 'Balanced NPK fertilizer suitable for a wide range of crops, promoting healthy growth and high yields.',
            manufacturer: 'Green Agriculture Ltd',
            status: 'Active',
            imageUrl: 'https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg'
          },
          { 
            id: 2, 
            productName: 'Urea 46-0-0 Nitrogen Fertilizer', 
            skuNumber: 'URE-46001',
            category: 'Nitrogenous Fertilizer',
            unitOfMeasure: '25 kg',
            description: 'High-nitrogen fertilizer ideal for leafy vegetables and promoting vegetative growth in crops.',
            manufacturer: 'Farm Equipment Ltd',
            status: 'Active',
            imageUrl: 'https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg'
          },
          { 
            id: 3, 
            productName: 'DAP 18-46-0 Phosphate Fertilizer', 
            skuNumber: 'DAP-18461',
            category: 'Phosphatic Fertilizer',
            unitOfMeasure: '50 kg',
            description: 'Diammonium phosphate fertilizer with high phosphorus content for root development and flowering.',
            manufacturer: 'Aden Agri Supplies',
            status: 'Active',
            imageUrl: 'https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg'
          },
          { 
            id: 4, 
            productName: 'CAN 27% Calcium Ammonium Nitrate', 
            skuNumber: 'CAN-27001',
            category: 'Nitrogenous Fertilizer',
            unitOfMeasure: '50 kg',
            description: 'Calcium-based nitrogen fertilizer that reduces soil acidity while providing essential nitrogen.',
            manufacturer: 'Green Agriculture Ltd',
            status: 'Active',
            imageUrl: 'https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg'
          },
          { 
            id: 5, 
            productName: 'MOP 0-0-60 Potassium Fertilizer', 
            skuNumber: 'MOP-00601',
            category: 'Potassic Fertilizer',
            unitOfMeasure: '25 kg',
            description: 'Muriate of Potash fertilizer for improved crop quality, disease resistance and drought tolerance.',
            manufacturer: 'Farm Equipment Ltd',
            status: 'Inactive',
            imageUrl: 'https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg'
          }
        ],
        showBankAccountModal: false,
        newBankAccount: {
          bankName: '',
          branch: '',
          accountName: '',
          accountNumber: '',
          isPrimary: false
        },
        editingBankAccount: false,
        editingBankAccountId: null,
        isBankDropdownOpen: false,
        bankSearch: '',
        filteredBanks: [],
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
        showProductModal: false,
        newProduct: {
          productName: '',
          skuNumber: '',
          category: '',
          unitOfMeasure: '',
          description: '',
          manufacturer: '',
          status: 'Active',
          imageUrl: ''
        },
        editingProduct: false,
        editingProductId: null,
        imagePreview: null,
        isDragging: false,
        isCountryDropdownOpen: false,
        countrySearch: '',
        filteredCountries: [],
        countries: [
          'Kenya', 'Uganda', 'Tanzania', 'Rwanda', 'Burundi', 'Ethiopia', 'South Sudan'
        ],
        isRegionDropdownOpen: false,
        regionSearch: '',
        filteredRegions: [],
        regions: [
          'Baringo', 'Bomet', 'Bungoma', 'Busia', 'Elgeyo Marakwet', 'Embu', 'Garissa', 
          'Homa Bay', 'Isiolo', 'Kajiado', 'Kakamega', 'Kericho', 'Kiambu', 'Kilifi', 
          'Kirinyaga', 'Kisii', 'Kisumu', 'Kitui', 'Kwale', 'Laikipia', 'Lamu', 'Machakos', 
          'Makueni', 'Mandera', 'Marsabit', 'Meru', 'Migori', 'Mombasa', 'Murang\'a', 
          'Nairobi', 'Nakuru', 'Nandi', 'Narok', 'Nyamira', 'Nyandarua', 'Nyeri', 'Samburu', 
          'Siaya', 'Taita Taveta', 'Tana River', 'Tharaka Nithi', 'Trans Nzoia', 'Turkana', 
          'Uasin Gishu', 'Vihiga', 'Wajir', 'West Pokot'
        ],
        banks: [
          'Absa Bank Kenya',
          'Access Bank Kenya',
          'Bank of Africa',
          'Bank of Baroda',
          'Bank of India',
          'Consolidated Bank of Kenya',
          'Co-operative Bank',
          'Credit Bank',
          'Development Bank of Kenya',
          'Diamond Trust Bank',
          'DIB Bank Kenya',
          'Ecobank Kenya',
          'Equity Bank',
          'Family Bank',
          'First Community Bank',
          'Guaranty Trust Bank',
          'Guardian Bank',
          'Gulf African Bank',
          'Housing Finance Company of Kenya',
          'I&M Bank',
          'Jamii Bora Bank',
          'KCB Bank Kenya',
          'Kenya Commercial Bank (KCB)',
          'Mayfair Bank',
          'Middle East Bank Kenya',
          'National Bank of Kenya',
          'NCBA Bank Kenya',
          'Paramount Bank',
          'Prime Bank (Kenya)',
          'SBM Bank Kenya',
          'Sidian Bank',
          'Spire Bank',
          'Stanbic Bank Kenya',
          'Standard Chartered Kenya',
          'Victoria Commercial Bank'
        ],
        // New properties for supplier edit functionality
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
        isIndustryDropdownOpen: false,
        industrySearch: '',
        filteredIndustries: [],
        industries: [
          'Agriculture', 
          'Food Processing', 
          'Manufacturing', 
          'Construction',
          'Energy',
          'Healthcare',
          'Information Technology',
          'Telecommunications',
          'Transportation',
          'Retail',
          'Wholesale',
          'Financial Services',
          'Real Estate',
          'Education',
          'Hospitality',
          'Mining',
          'Oil & Gas',
          'Pharmaceuticals',
          'Textiles',
          'Automotive'
        ]
      }
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
      sortedBankAccounts() {
        const accounts = [...this.bankAccounts];
        accounts.sort((a, b) => {
          let modifier = this.sortDir === 'asc' ? 1 : -1;
          // Handle different property name formats
          let aValue, bValue;
          
          // Convert sortKey to both formats
          const camelKey = this.sortKey; // e.g. 'bankName'
          const snakeKey = this.sortKey.replace(/[A-Z]/g, letter => `_${letter.toLowerCase()}`); // e.g. 'bank_name'
          
          // Try to get value in both formats
          aValue = a[camelKey] !== undefined ? a[camelKey] : a[snakeKey];
          bValue = b[camelKey] !== undefined ? b[camelKey] : b[snakeKey];
          
          // Handle undefined or null values
          if (aValue === undefined || aValue === null) aValue = '';
          if (bValue === undefined || bValue === null) bValue = '';
          
          // Sort based on value types
          if (typeof aValue === 'number' && typeof bValue === 'number') {
            return aValue < bValue ? -1 * modifier : 1 * modifier;
          } else {
            return aValue.toString().localeCompare(bValue.toString()) * modifier;
          }
        });
        
        return accounts;
      },
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
      // Supplier and bankAccounts are directly available as props
      // We only need the route query approach as a fallback if we're directly navigating to the page
      if (!this.supplier && this.$route && this.$route.query && this.$route.query.supplierData) {
        try {
          this.supplierData = JSON.parse(this.$route.query.supplierData);
        } catch (e) {
          console.error('Error parsing supplier data:', e);
        }
      }
      
      // Initialize the filtered banks with all banks
      this.filteredBanks = [...this.banks];
      
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
      document.addEventListener('click', this.closeBankDropdownOutside);
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
        status: 'Active',
        imageUrl: '',
        images: []
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
      }
    }
  }
  </script>
  
  <style scoped>
  .page-container {
    display: flex;
    justify-content: center;
    background-color: #f5f7fa;
    min-height: 100vh;
    padding: 40px 20px;
    font-family: 'Inter', 'Segoe UI', Roboto, sans-serif;
  }
  
  .content-container {
    width: 1200px;
    max-width: 100%;
    border-radius: 12px;
    padding: 24px;
  }
  
  .header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
  }
  
  .supplier-details-container {
    background-color: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    padding: 24px;
  }
  
  .page-title {
    font-size: 1.8rem;
    color: #1e293b;
    font-weight: 600;
    margin: 0;
  }
  
  .back-button {
    display: flex;
    align-items: center;
    padding: 8px 16px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 0.95rem;
    font-weight: 500;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s;
  }
  
  .back-button:hover {
    background-color: #f8fafc;
    border-color: #cbd5e1;
  }
  
  .back-button span {
    margin-right: 6px;
  }
  
  .order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e5e7eb;
  }
  
  .order-id {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1e293b;
  }
  
  .status-container {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  
  .status-badge {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.9rem;
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
  
  .activate-button {
    padding: 10px 14px;
    background-color: #10b981;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s;
  }
  
  .activate-button:hover {
    background-color: #047857;
  }
  
  .order-details-panel {
    background-color: #f8fafc;
    border-radius: 8px;
    padding: 24px 28px;
    margin-bottom: 32px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 16px 40px;
    text-align: left;
    box-shadow: none;
  }
  
  .detail-row {
    display: flex;
    flex-direction: column;
    padding: 8px 0;
  }
  
  .detail-label {
    font-size: 0.9rem;
    color: #64748b;
    margin-bottom: 4px;
  }
  
  .detail-value {
    font-size: 1.05rem;
    font-weight: 600;
    color: #1e293b;
  }
  
  /* Tabs styles */
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
  
  .tab-content {
    margin-top: 20px;
  }
  
  /* Table Controls */
  .table-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
  }
  
  .table-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
  }
  
  .add-btn {
    display: flex;
    align-items: center;
    padding: 10px 16px;
    background-color: #0E64A5;
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
    min-width: 750px; /* Minimum width to ensure horizontal scroll appears when needed */
    table-layout: auto; /* Allow table to adjust column width based on content */
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.95rem;
  }
  
  /* For the warehouse table, set a larger min-width due to more columns */
  [v-if="activeTab === 'warehouses'"] .data-table {
    min-width: 1200px;
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
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 20px;
      height: 100%;
      background: linear-gradient(to right, rgba(255, 255, 255, 0), rgba(230, 230, 230, 0.3));
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
    content: '⇵';
    opacity: 0.3;
    font-size: 12px;
  }
  
  .sort-asc::after {
    content: '↑';
    color: #2563eb;
    font-size: 12px;
  }
  
  .sort-desc::after {
    content: '↓';
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
  
  .primary-column {
    text-align: center;
  }
  
  .primary-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    background-color: rgba(16, 185, 129, 0.1);
    color: #10b981;
    border-radius: 50%;
  }
  
  .set-primary-btn {
    background: none;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 10px;
    font-size: 0.85rem;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s;
  }
  
  .set-primary-btn:hover {
    border-color: #2563eb;
    color: #2563eb;
    background-color: rgba(37, 99, 235, 0.05);
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
    color: #0E64A5;
    border-color: #0E64A5;
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
  
  /* Product Modal Styles */
  .image-gallery {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-bottom: 20px;
  }
  
  .gallery-preview {
    width: 100%;
    height: 200px;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #f8fafc;
    border-radius: 8px;
    overflow: hidden;
  }
  
  .primary-preview-img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
  }
  
  .gallery-thumbnails {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
  }
  
  .thumbnail-container {
    position: relative;
    width: 70px;
    height: 70px;
    border-radius: 6px;
    overflow: hidden;
    border: 2px solid #e5e7eb;
    transition: all 0.2s ease;
  }
  
  .primary-thumbnail {
    border-color: #0E64A5;
  }
  
  .thumbnail-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .thumbnail-actions {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    opacity: 0;
    transition: opacity 0.2s ease;
  }
  
  .thumbnail-container:hover .thumbnail-actions {
    opacity: 1;
  }
  
  .set-primary-btn {
    background-color: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    margin-right: 5px;
  }
  
  .set-primary-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
  
  .remove-thumbnail-btn {
    background-color: rgba(239, 68, 68, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 16px;
  }
  
  .add-more-container {
    width: 70px;
    height: 70px;
    border: 2px dashed #cbd5e1;
    border-radius: 6px;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  
  .add-more-container:hover {
    border-color: #0E64A5;
    background-color: rgba(14, 100, 165, 0.05);
  }
  
  .add-more-btn {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    height: 100%;
    color: #64748b;
    cursor: pointer;
  }
  
  .add-more-btn:hover {
    color: #0E64A5;
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
  
  .checkbox-group {
    margin-top: 12px;
  }
  
  .checkbox-label {
    display: flex;
    align-items: center;
    font-size: 0.95rem;
    color: #334155;
    cursor: pointer;
    text-align: left;
  }
  
  .checkbox-label input[type="checkbox"] {
    width: auto;
    margin-right: 8px;
    flex-shrink: 0;
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
    background-color: #0E64A5;
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
  
  .coming-soon {
    text-align: center;
    padding: 40px 20px;
    background-color: #f8fafc;
    border-radius: 8px;
    color: #64748b;
  }
  
  .coming-soon h3 {
    color: #1e293b;
    margin-bottom: 10px;
  }
  
  .status-pill {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
  }
  
  .data-table .product-thumbnail {
    width: 60px;
    height: 60px;
    object-fit: contain;
    border-radius: 4px;
    border: 1px solid #e0e0e0;
  }
  
  .data-table .description-cell {
    max-width: 250px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
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
  
  /* Custom Bank Dropdown Styles */
  .custom-select-container {
    position: relative;
    width: 100%;
    box-sizing: border-box;
    z-index: 5;
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
    
    .order-details-panel {
      grid-template-columns: 1fr;
      padding: 20px;
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
  }
  
  @media (max-width: 640px) {
    .page-container {
      padding: 20px 16px;
    }
    
    .content-container {
      padding: 16px;
    }
    
    .order-header {
      flex-direction: column;
      align-items: flex-start;
      gap: 12px;
    }
    
    .status-container {
      align-self: flex-start;
    }
    
    .tabs {
      overflow-x: auto;
      padding-bottom: 8px;
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
    
    .table-controls {
      padding: 0 12px;
    }
  }
  
  /* Styles for Edit Supplier Details button */
  .edit-details-container {
    grid-column: 1 / -1;
    display: flex;
    justify-content: flex-end;
    margin-top: 16px;
    margin-right: 8px;
  }
  
  .edit-details-btn {
    display: flex;
    align-items: center;
    padding: 8px 14px;
    background-color: white;
    color: #0E64A5;
    border: 1px solid #0E64A5;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  
  .edit-details-btn:hover {
    background-color: rgba(14, 100, 165, 0.05);
  }
  
  .edit-details-btn svg {
    margin-right: 8px;
  }
  
  /* Industry dropdown specific styles */
  .industry-select-container {
    position: relative;
    width: 100%;
  }
  
  .industry-select-dropdown {
    max-height: 250px;
    overflow-y: auto;
  }
  
  /* Product Modal Styles */
  .image-upload-container {
    border: 2px dashed #ccc;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    margin-bottom: 15px;
    transition: all 0.3s ease;
    position: relative;
  }
  
  .image-upload-container:hover {
    border-color: #4a90e2;
  }
  
  .drag-over {
    border-color: #4a90e2;
    background-color: rgba(74, 144, 226, 0.05);
  }
  
  .upload-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    color: #666;
  }
  
  .upload-placeholder svg {
    margin-bottom: 10px;
    color: #999;
  }
  
  .browse-text {
    color: #4a90e2;
    text-decoration: underline;
    cursor: pointer;
  }
  
  .file-input {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    cursor: pointer;
  }
  
  .image-preview {
    position: relative;
    width: 100%;
    max-height: 200px;
    overflow: hidden;
    display: flex;
    justify-content: center;
  }
  
  .preview-img {
    max-width: 100%;
    max-height: 180px;
    object-fit: contain;
  }
  
  .remove-image-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 16px;
    padding: 0;
  }
  
  /* Image Gallery Styles */
  .image-gallery {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-bottom: 20px;
  }
  
  .gallery-preview {
    width: 100%;
    height: 200px;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #f8fafc;
    border-radius: 8px;
    overflow: hidden;
  }
  
  .primary-preview-img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
  }
  
  .gallery-thumbnails {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
  }
  
  /* Image Gallery Outside Styles */
  .images-gallery-outside {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-top: 20px;
    margin-bottom: 20px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 16px;
    background-color: #f8fafc;
  }
  
  .gallery-preview-outside {
    width: 100%;
    height: 250px;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: white;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
  }
  
  .gallery-thumbnails-outside {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 16px;
  }
  
  .thumbnail-container {
    position: relative;
    width: 70px;
    height: 70px;
    border-radius: 6px;
    overflow: hidden;
    border: 2px solid #e5e7eb;
    transition: all 0.2s ease;
  }
  
  .primary-thumbnail {
    border-color: #0E64A5;
  }
  
  .thumbnail-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .thumbnail-actions {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    opacity: 0;
    transition: opacity 0.2s ease;
  }
  
  .thumbnail-container:hover .thumbnail-actions {
    opacity: 1;
  }
  
  .set-primary-btn {
    background-color: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    margin-right: 5px;
  }
  
  .set-primary-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
  
  .remove-thumbnail-btn {
    background-color: rgba(239, 68, 68, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 16px;
  }
  
  .add-more-container {
    width: 70px;
    height: 70px;
    border: 2px dashed #cbd5e1;
    border-radius: 6px;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  
  .add-more-container:hover {
    border-color: #0E64A5;
    background-color: rgba(14, 100, 165, 0.05);
  }
  
  .add-more-btn {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    height: 100%;
    color: #64748b;
    cursor: pointer;
  }
  
  .add-more-btn:hover {
    color: #0E64A5;
  }
  
  .product-thumbnail {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
  }
  
  .description-cell {
    max-width: 200px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  </style>