<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import InputError from '@/Components/InputError.vue';

const props = defineProps({
  suppliers: {
    type: Array,
    required: true,
  },
});

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
  created_by: 1
});

const openModal = () => {
  showModal.value = true;
  form.reset();
};

const closeModal = () => {
  showModal.value = false;
  form.reset();
};

const submit = () => {
  form.post(route("suppliers.create"), {
    onSuccess: () => {
      closeModal();
    },
  });
};

const filteredSuppliers = computed(() => {
  if (searchQuery.value.trim() === '') {
    return props.suppliers;
  }
  
  const query = searchQuery.value.toLowerCase();
  return props.suppliers.filter(supplier => {
    return Object.values(supplier).some(value => {
      if (value === null || value === undefined) return false;
      return String(value).toLowerCase().includes(query);
    });
  });
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
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Suppliers
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white">
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
              <button @click="openModal" class="add-btn">
                <span class="plus-icon">+</span>
                Add Supplier
              </button>
            </div>
            
            <div class="table-container">
              <div class="table-wrapper">
                <table class="data-table">
                  <thead>
                    <tr>
                      <th @click="sortBy('company_name')" class="sortable">
                        Company Name <i :class="getSortIcon('company_name')"></i>
                      </th>
                      <th @click="sortBy('contact_person')" class="sortable">
                        Contact Person <i :class="getSortIcon('contact_person')"></i>
                      </th>
                      <th @click="sortBy('email')" class="sortable">
                        Email <i :class="getSortIcon('email')"></i>
                      </th>
                      <th @click="sortBy('phone_number')" class="sortable">
                        Phone <i :class="getSortIcon('phone_number')"></i>
                      </th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="supplier in paginatedSuppliers" :key="supplier.id" class="data-row">
                      <td>{{ supplier.company_name }}</td>
                      <td>{{ supplier.contact_person }}</td>
                      <td>{{ supplier.email }}</td>
                      <td>{{ supplier.phone_number }}</td>
                      <Link
                        :href="route('suppliers.show', { uuid: supplier.uuid })"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                    >
                        View Details
                    </Link>
                    </tr>
                    <tr v-if="paginatedSuppliers.length === 0">
                      <td colspan="4" class="empty-state">
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
                      >
                      <InputError :message="form.errors.company_name" />
                    </div>

                    <div class="form-group">
                      <label for="email">Business Email <span class="required">*</span></label>
                      <input 
                        id="email" 
                        type="email" 
                        v-model="form.email"
                        required
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
                      >
                      <InputError :message="form.errors.phone_number" />
                    </div>

                    <div class="form-group">
                      <label for="office_address">Business Address <span class="required">*</span></label>
                      <input 
                        id="office_address" 
                        type="text" 
                        v-model="form.office_address"
                        required
                      >
                      <InputError :message="form.errors.office_address" />
                    </div>

                    <div class="form-group">
                      <label for="krapin">Tax Number <span class="required">*</span></label>
                      <input 
                        id="krapin" 
                        type="text" 
                        v-model="form.krapin"
                        required
                      >
                      <InputError :message="form.errors.krapin" />
                    </div>

                    <div class="form-group">
                      <label for="contact_person">Contact Person Name <span class="required">*</span></label>
                      <input 
                        id="contact_person" 
                        type="text" 
                        v-model="form.contact_person"
                        required
                      >
                      <InputError :message="form.errors.contact_person" />
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
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
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
  border-color: #0E64A5;
  box-shadow: 0 0 0 2px rgba(14, 100, 165, 0.1);
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
  text-decoration: none;
}

.add-btn:hover {
  background-color: #0a4f83;
}

.plus-icon {
  font-size: 1.2rem;
  margin-right: 8px;
  font-weight: bold;
}

/* Table styles */
.table-container {
  position: relative;
  width: 100%;
  background-color: white;
  border-radius: 8px;
  overflow: hidden;
  margin-bottom: 24px;
}

.table-wrapper {
  width: 100%;
  overflow-x: auto;
  scroll-behavior: smooth;
  padding-bottom: 6px;
  scrollbar-width: thin;
}

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

.data-table {
  width: 100%;
  min-width: 750px;
  table-layout: auto;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 0.95rem;
}

.data-table th, 
.data-table td {
  white-space: nowrap;
  min-width: 120px;
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
}

.data-table tr:last-child td {
  border-bottom: none;
}

.data-row:hover td {
  background-color: #f8fafc;
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
  background-color: #0E64A5;
  color: white;
}

/* Responsive styles */
@media (max-width: 768px) {
  .table-controls {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }
  
  .search-container {
    width: 100%;
  }
}

@media (max-width: 640px) {
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

/* Add these new styles for the modal */
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
  z-index: 50;
}

.modal-container {
  background-color: white;
  border-radius: 8px;
  width: 500px;
  max-width: 90%;
  max-height: 90vh;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
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
  padding: 0;
  line-height: 1;
}

.modal-body {
  padding: 24px;
  overflow-y: auto;
  max-height: calc(90vh - 60px);
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-size: 0.95rem;
  font-weight: 500;
  color: #334155;
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
  transition: border-color 0.2s;
}

.form-group input:focus {
  outline: none;
  border-color: #0E64A5;
  box-shadow: 0 0 0 2px rgba(14, 100, 165, 0.1);
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
</style>