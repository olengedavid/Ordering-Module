<script setup>
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import InputError from '@/Components/InputError.vue';
import AdminNavbar from '@/Components/AdminNavbar.vue';
import SuccessMessage from '@/Components/SuccessMessage.vue';
import ErrorMessage from '@/Components/ErrorMessage.vue';

const props = defineProps({
  suppliers: {
    type: Array,
    required: true,
  },
});

// Tab system
const activeTab = ref('active');
const tabs = ref([
  { id: 'inactive', name: 'Inactive', count: 3 },
  { id: 'active', name: 'Active', count: 20 }
]);

// Update tab counts based on suppliers
const updateTabCounts = () => {
  const activeSuppliers = props.suppliers.filter(s => s.status === 'active');
  const inactiveSuppliers = props.suppliers.filter(s => s.status === 'inactive');
  
  tabs.value.find(t => t.id === 'active').count = activeSuppliers.length;
  tabs.value.find(t => t.id === 'inactive').count = inactiveSuppliers.length;
};

// Industry dropdown
const isIndustryDropdownOpen = ref(false);
const industrySearch = ref('');
const industries = [
  'Agriculture', 'Automotive', 'Chemicals', 'Coffee', 'Construction', 
  'Dairy', 'Education', 'Electronics', 'Energy', 'Entertainment',
  'Fisheries', 'Food & Beverage', 'Forestry', 'Grains', 'Healthcare',
  'Hospitality', 'Information Technology', 'Livestock', 'Machinery', 'Manufacturing',
  'Media', 'Milling', 'Mining', 'Oil & Gas', 'Organic Farming',
  'Pharmaceuticals', 'Ranching', 'Real Estate', 'Retail', 'Seafood',
  'Seeds', 'Shipping', 'Sisal', 'Tea', 'Telecommunications',
  'Textiles', 'Tourism', 'Transportation', 'Utilities'
];
const filteredIndustries = ref([...industries]);

// Basic state
const searchQuery = ref('');
const currentPage = ref(1);
const perPage = ref(10);
const perPageOptions = [5, 10, 20, 50];
const sortKey = ref('company_name');
const sortDir = ref('asc');

// Add modal and form handling
const showModal = ref(false);
const form = useForm({
  company_name: '',
  email: '',
  phone_number: '',
  office_address: '',
  krapin: '',
  contact_person: '',
  industry: '',
  status: 'active',
  created_by: 1
});

// Add success and error message handling
const showSuccessMessage = ref(false);
const showErrorMessage = ref(false);
const successMessageText = ref('');
const errorMessageText = ref('');

// Set active tab
const setActiveTab = (tabId) => {
  activeTab.value = tabId;
  resetPagination();
};

// Industry dropdown functions
const toggleIndustryDropdown = () => {
  isIndustryDropdownOpen.value = !isIndustryDropdownOpen.value;
  
  if (isIndustryDropdownOpen.value) {
    // Reset search when opening
    industrySearch.value = '';
    filteredIndustries.value = [...industries];
  }
};

const closeIndustryDropdownOutside = (event) => {
  const dropdown = document.querySelector('.custom-select-container');
  if (dropdown && !dropdown.contains(event.target)) {
    isIndustryDropdownOpen.value = false;
  }
};

const filterIndustries = () => {
  if (industrySearch.value.trim() === '') {
    filteredIndustries.value = [...industries];
  } else {
    const query = industrySearch.value.toLowerCase();
    filteredIndustries.value = industries.filter(
      industry => industry.toLowerCase().includes(query)
    );
  }
};

const selectIndustry = (industry) => {
  form.industry = industry;
  isIndustryDropdownOpen.value = false;
};

// Modal functions
const openModal = () => {
  showModal.value = true;
  form.reset();
  form.status = 'active';
};

const closeModal = () => {
  showModal.value = false;
  form.reset();
  isIndustryDropdownOpen.value = false;
};

const submit = () => {
  form.post(route("suppliers.create"), {
    onSuccess: () => {
      closeModal();
      successMessageText.value = 'Supplier has been successfully added';
      showSuccessMessage.value = true;
      setTimeout(() => {
        showSuccessMessage.value = false;
      }, 3000);
    },
    onError: (errors) => {
      errorMessageText.value = 'Failed to add supplier. Please check the form and try again.';
      showErrorMessage.value = true;
      setTimeout(() => {
        showErrorMessage.value = false;
      }, 3000);
    }
  });
};

// Computed properties
const filteredSuppliers = computed(() => {
  let result = props.suppliers;
  
  // Filter by tab
  if (activeTab.value === 'active') {
    result = result.filter(supplier => supplier.status === 'active');
  } else if (activeTab.value === 'inactive') {
    result = result.filter(supplier => supplier.status === 'inactive');
  }
  
  // Filter by search query
  if (searchQuery.value.trim() !== '') {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(supplier => {
      return Object.values(supplier).some(value => {
        if (value === null || value === undefined) return false;
        return String(value).toLowerCase().includes(query);
      });
    });
  }
  
  return result;
});

const totalPages = computed(() => {
  return Math.ceil(filteredSuppliers.value.length / perPage.value);
});

const paginatedSuppliers = computed(() => {
  const startIndex = (currentPage.value - 1) * perPage.value;
  const endIndex = startIndex + perPage.value;
  
  const sortedSuppliers = [...filteredSuppliers.value];
  sortedSuppliers.sort((a, b) => {
    let modifier = sortDir.value === 'asc' ? 1 : -1;
    let aValue = a[sortKey.value];
    let bValue = b[sortKey.value];
    
    if (aValue === undefined || aValue === null) aValue = '';
    if (bValue === undefined || bValue === null) bValue = '';
    
    return aValue.toString().localeCompare(bValue.toString()) * modifier;
  });
  
  return sortedSuppliers.slice(startIndex, endIndex);
});

// Table sorting
const sortBy = (key) => {
  if (sortKey.value === key) {
    sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortKey.value = key;
    sortDir.value = 'asc';
  }
};

const getSortIcon = (key) => {
  if (sortKey.value !== key) return 'sort-icon sort-none';
  return sortDir.value === 'asc' ? 'sort-icon sort-asc' : 'sort-icon sort-desc';
};

// Pagination
const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
};

const goToPage = (page) => {
  currentPage.value = page;
};

const resetPagination = () => {
  currentPage.value = 1;
};

// Navigation
const viewSupplierDetails = (supplier) => {
  router.visit(route('suppliers.show', { uuid: supplier.uuid }));
};

// Lifecycle hooks
onMounted(() => {
  updateTabCounts();
  filteredIndustries.value = [...industries];
  document.addEventListener('click', closeIndustryDropdownOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', closeIndustryDropdownOutside);
});
</script>

<template>
  <Head title="Suppliers" />
  
  <AdminNavbar />
  
  <!-- Floating Success and Error Messages -->
  <div class="floating-messages">
    <SuccessMessage v-if="showSuccessMessage" @close="showSuccessMessage = false" class="floating-message">
      {{ successMessageText }}
    </SuccessMessage>
    <ErrorMessage v-if="showErrorMessage" @close="showErrorMessage = false" class="floating-message">
      {{ errorMessageText }}
    </ErrorMessage>
  </div>
  
  <div class="page-container">
    <div class="content-container">
      <h1 class="page-title">Suppliers</h1>
      
      <!-- Tabs -->
      <div class="tabs-container">
        <ul class="tabs">
          <li v-for="tab in tabs" :key="tab.id" 
              :class="['tab', { active: activeTab === tab.id }]"
              @click="setActiveTab(tab.id)">
            {{ tab.name }}
            <span class="tab-count" v-if="tab.count">{{ tab.count }}</span>
          </li>
        </ul>
      </div>
      
      <!-- Table Controls - Search and Add Button -->
      <div class="table-controls">
        <div class="search-container">
          <div class="search-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" color="#000000" fill="none">
              <path opacity="0.4" fill-rule="evenodd" clip-rule="evenodd" d="M16.7929 16.7929C17.1834 16.4024 17.8166 16.4024 18.2071 16.7929L22.7071 21.2929C23.0976 21.6834 23.0976 22.3166 22.7071 22.7071C22.3166 23.0976 21.6834 23.0976 21.2929 22.7071L16.7929 18.2071C16.4024 17.8166 16.4024 17.1834 16.7929 16.7929Z" fill="currentColor" />
              <path fill-rule="evenodd" clip-rule="evenodd" d="M1 11C1 5.47715 5.47715 1 11 1C16.5228 1 21 5.47715 21 11C21 16.5228 16.5228 21 11 21C5.47715 21 1 16.5228 1 11ZM11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3Z" fill="currentColor" />
            </svg>
          </div>
          <input type="text" class="search-input" placeholder="Search suppliers..." v-model="searchQuery">
        </div>
        <button @click="openModal" class="add-supplier-btn">
          <span class="plus-icon">+</span>
          Add New Supplier
        </button>
      </div>
      
      <!-- Suppliers Table with Horizontal Scroll -->
      <div class="table-wrapper">
        <div class="table-responsive">
          <table class="suppliers-table">
            <thead>
              <tr>
                <th @click="sortBy('company_name')" class="sortable">
                  Company Name <i :class="getSortIcon('company_name')"></i>
                </th>
                <th @click="sortBy('office_address')" class="sortable">
                  Office Address <i :class="getSortIcon('office_address')"></i>
                </th>
                <th @click="sortBy('contact_person')" class="sortable">
                  Contact Person <i :class="getSortIcon('contact_person')"></i>
                </th>
                <th @click="sortBy('email')" class="sortable">
                  Email Address <i :class="getSortIcon('email')"></i>
                </th>
                <th @click="sortBy('phone_number')" class="sortable">
                  Phone Number <i :class="getSortIcon('phone_number')"></i>
                </th>
                <th @click="sortBy('krapin')" class="sortable">
                  KRA PIN <i :class="getSortIcon('krapin')"></i>
                </th>
                <th @click="sortBy('industry')" class="sortable">
                  Industry <i :class="getSortIcon('industry')"></i>
                </th>
                <th @click="sortBy('status')" class="sortable">
                  Status <i :class="getSortIcon('status')"></i>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="supplier in paginatedSuppliers" 
                  :key="supplier.id" 
                  @click="viewSupplierDetails(supplier)" 
                  class="supplier-row">
                <td>{{ supplier.company_name }}</td>
                <td>{{ supplier.office_address || '' }}</td>
                <td>{{ supplier.contact_person }}</td>
                <td>{{ supplier.email }}</td>
                <td>{{ supplier.phone_number }}</td>
                <td>{{ supplier.krapin || '' }}</td>
                <td>{{ supplier.industry || '' }}</td>
                <td>{{ supplier.status || '' }}</td>
              </tr>
              <tr v-if="paginatedSuppliers.length === 0">
                <td colspan="8" class="empty-state">
                  No suppliers found
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Pagination -->
      <div class="pagination-controls">
        <div class="per-page">
          <span>Show</span>
          <select v-model="perPage" @change="resetPagination" class="per-page-select">
            <option v-for="option in perPageOptions" :key="option" :value="option">
              {{ option }}
            </option>
          </select>
          <span>per page</span>
        </div>
        <div class="pagination-buttons">
          <button class="pagination-btn" :disabled="currentPage === 1" @click="prevPage">
            Previous
          </button>
          <div class="page-numbers">
            <span v-for="page in totalPages" :key="page"
                :class="['page-number', { active: currentPage === page }]"
                @click="goToPage(page)">
              {{ page }}
            </span>
          </div>
          <button class="pagination-btn" :disabled="currentPage === totalPages" @click="nextPage">
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Add Supplier Modal -->
  <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
    <div class="modal-container">
      <div class="modal-header">
        <h2>Add New Supplier</h2>
        <button class="close-btn" @click="closeModal">&times;</button>
      </div>
      <div class="modal-body">
        <form @submit.prevent="submit">
          <div class="form-group">
            <label for="company_name">Company Name <span class="required">*</span></label>
            <input 
              id="company_name" 
              type="text" 
              v-model="form.company_name"
              required
              placeholder="Enter company name"
            >
            <InputError :message="form.errors.company_name" />
          </div>

          <div class="form-group">
            <label for="office_address">Office Address <span class="required">*</span></label>
            <input 
              id="office_address" 
              type="text" 
              v-model="form.office_address"
              required
              placeholder="Enter office address"
            >
            <InputError :message="form.errors.office_address" />
          </div>

          <div class="form-group">
            <label for="contact_person">Contact Person Name <span class="required">*</span></label>
            <input 
              id="contact_person" 
              type="text" 
              v-model="form.contact_person"
              required
              placeholder="Enter contact person name"
            >
            <InputError :message="form.errors.contact_person" />
          </div>

          <div class="form-group">
            <label for="email">Business Email <span class="required">*</span></label>
            <input 
              id="email" 
              type="email" 
              v-model="form.email"
              required
              placeholder="Enter email address"
            >
            <InputError :message="form.errors.email" />
          </div>

          <div class="form-group">
            <label for="phone_number">Phone Number <span class="required">*</span></label>
            <input 
              id="phone_number" 
              type="tel" 
              v-model="form.phone_number"
              required
              placeholder="Enter phone number"
            >
            <InputError :message="form.errors.phone_number" />
          </div>

          <div class="form-group">
            <label for="krapin">KRA PIN <span class="required">*</span></label>
            <input 
              id="krapin" 
              type="text" 
              v-model="form.krapin"
              required
              placeholder="Enter KRA PIN"
            >
            <InputError :message="form.errors.krapin" />
          </div>

          <div class="form-group">
            <label for="industry">Industry <span class="required">*</span></label>
            <div class="custom-select-container">
              <div 
                class="custom-select-trigger" 
                @click="toggleIndustryDropdown"
                :class="{ 'active': isIndustryDropdownOpen }"
              >
                <span>{{ form.industry || 'Select an industry' }}</span>
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
              
              <div class="custom-select-dropdown" v-show="isIndustryDropdownOpen">
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
            <InputError :message="form.errors.industry" />
          </div>

          <div class="form-actions">
            <button type="button" class="cancel-btn" @click="closeModal">Cancel</button>
            <button 
              type="submit" 
              class="submit-btn"
              :disabled="form.processing"
            >
              {{ form.processing ? 'Creating...' : 'Create Supplier' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Apply background to both the authenticated layout and our container */
:deep(body),
:deep(html),
:deep(.bg-gray-100),
:deep(main) {
  background-color: #f5f7fa !important;
}

:deep(#app) {
  background-color: #f5f7fa !important;
  min-height: 100vh;
}

.page-container {
  display: flex;
  justify-content: center;
  width: 100%;
  font-family: 'Inter', 'Segoe UI', Roboto, sans-serif;
  padding: 40px 20px;
}

.content-container {
  width: 1200px;
  max-width: 100%;
}

.page-title {
  font-size: 1.8rem;
  color: #1e293b;
  margin-bottom: 24px;
  font-weight: 600;
  text-align: left;
}

/* Tabs */
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
  background-color: #fadadd;
  color: #e55c5c;
}

.tabs li:nth-child(2) .tab-count {
  background-color: #d1f0e0;
  color: #2a9d74;
}

/* Table Controls */
.table-controls {
  display: flex;
  justify-content: space-between;
  margin-bottom: 16px;
}

.search-container {
  position: relative;
  width: 300px;
  max-width: 100%;
}

.add-supplier-btn {
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

.add-supplier-btn:hover {
  background-color: #0a4f83;
}

.plus-icon {
  font-size: 1.2rem;
  margin-right: 8px;
  font-weight: bold;
}

.search-input {
  width: 100%;
  box-sizing: border-box;
  padding: 10px 16px 10px 45px;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  font-size: 0.95rem;
  color: #1e293b;
  background-color: white;
  transition: border-color 0.2s;
}

.search-input:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
}

/* Table */
.table-wrapper {
  background-color: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  margin-bottom: 24px;
}

.table-responsive {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch; /* For better mobile experience */
  scrollbar-width: thin; /* For Firefox */
  position: relative;
}

/* Custom scrollbar for Webkit browsers */
.table-responsive::-webkit-scrollbar {
  height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb {
  background-color: #cbd5e1;
  border-radius: 4px;
  border: 1px solid #f1f5f9;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
  background-color: #94a3b8;
}

.suppliers-table {
  width: 100%;
  min-width: 900px; /* Ensures table doesn't collapse too much */
  border-collapse: separate;
  border-spacing: 0;
  font-size: 0.95rem;
}

.suppliers-table th {
  background-color: #f8fafc;
  color: #64748b;
  font-weight: 600;
  text-align: left;
  padding: 14px 15px;
  border-bottom: 1px solid #e5e7eb;
  white-space: nowrap;
}

.suppliers-table th.sortable {
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

.suppliers-table td {
  padding: 14px 15px;
  border-bottom: 1px solid #e5e7eb;
  color: #334155;
  text-align: left;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 200px;  
}

.suppliers-table tr:last-child td {
  border-bottom: none;
}

/* Supplier row styling */
.supplier-row {
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.supplier-row:hover {
  background-color: #f0f5ff !important;
}

.empty-state {
  text-align: center;
  color: #64748b;
  padding: 30px;
  font-style: italic;
}

/* Pagination */
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
  font-size: 0.95rem;
}

.per-page-select {
  padding: 8px 16px;
  padding-right: 30px;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  font-size: 0.9rem;
  background-color: white;
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 10px center;
}

.pagination-buttons {
  display: flex;
  gap: 8px;
  align-items: center;
}

.pagination-btn {
  padding: 8px 16px;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  background-color: white;
  color: #334155;
  font-size: 0.95rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.pagination-btn:hover:not(:disabled) {
  background-color: #f8fafc;
  border-color: #cbd5e1;
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-numbers {
  display: flex;
  gap: 4px;
  margin: 0 8px;
}

.page-number {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 6px;
  font-size: 0.9rem;
  cursor: pointer;
  color: #64748b;
}

.page-number:hover {
  background-color: #f1f5f9;
}

.page-number.active {
  background-color: #2563eb;
  color: white;
  font-weight: 600;
}

/* Custom Industry Dropdown Styles */
.custom-select-container {
  position: relative;
  width: 100%;
  box-sizing: border-box;
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
  overflow: hidden;
  text-overflow: ellipsis;
}

.custom-select-trigger span {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-right: 8px;
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
  top: calc(100% + 5px);
  left: 0;
  width: 100%;
  max-height: 250px;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  z-index: 10;
  overflow: hidden;
  animation: dropdownFadeIn 0.2s ease;
}

.custom-select-dropdown.dropdown-top {
  bottom: calc(100% + 5px);
  top: auto;
  animation: dropdownFadeInTop 0.2s ease;
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
  overflow-x: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
  position: relative;
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
  box-sizing: border-box;
  position: relative;
  overflow: visible;
}

.modal-body form {
  width: 100%;
  box-sizing: border-box;
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

.form-group input,
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
}

.form-group input:focus,
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
  background-color: #0E64A5;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 0.95rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.submit-btn:hover:not(:disabled) {
  background-color: #0a4f83;
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .pagination-controls {
    flex-direction: column;
    gap: 16px;
  }
  
  .search-container {
    max-width: 100%;
  }
}

@media (max-width: 640px) {
  .table-responsive {
    margin-left: -20px;
    margin-right: -20px;
    padding-left: 20px;
    padding-right: 20px;
    width: calc(100% + 40px);
  }
  
  .suppliers-table {
    min-width: 800px;
  }

  .tabs {
    overflow-x: auto;
    padding-bottom: 8px;
  }
  
  .page-numbers {
    display: none;
  }
  
  .table-controls {
    flex-direction: column;
    gap: 16px;
  }
  
  .search-container {
    width: 100%;
  }
  
  .add-supplier-btn {
    width: 100%;
    justify-content: center;
  }
}

/* Add shadow indicator when table can be scrolled */
@media (max-width: 1000px) {
  .table-responsive::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    height: 100%;
    width: 15px;
    background: linear-gradient(to right, rgba(255,255,255,0), rgba(0,0,0,0.05));
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.3s;
  }
  
  .table-responsive:not(:hover)::after {
    opacity: 1;
  }
}

/* Floating Messages */
.floating-messages {
  position: fixed;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 1000;
  width: 90%;
  max-width: 600px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.floating-message {
  animation: slideDown 0.3s ease-out;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

@keyframes slideDown {
  from {
    transform: translateY(-20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}
</style>