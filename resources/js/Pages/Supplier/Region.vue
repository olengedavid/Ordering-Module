<template>
<Head title="Regions" />
  
  <AdminNavbar />
    <div class="page-container">
      <div class="content-container">
        <div class="header-container">
          <h1 class="page-title">Regions</h1>
        </div>
        
        <div class="table-controls">
          <div class="search-container">
            <div class="search-icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" color="#000000" fill="none">
                <path opacity="0.4" fill-rule="evenodd" clip-rule="evenodd" d="M16.7929 16.7929C17.1834 16.4024 17.8166 16.4024 18.2071 16.7929L22.7071 21.2929C23.0976 21.6834 23.0976 22.3166 22.7071 22.7071C22.3166 23.0976 21.6834 23.0976 21.2929 22.7071L16.7929 18.2071C16.4024 17.8166 16.4024 17.1834 16.7929 16.7929Z" fill="currentColor" />
                <path fill-rule="evenodd" clip-rule="evenodd" d="M1 11C1 5.47715 5.47715 1 11 1C16.5228 1 21 5.47715 21 11C21 16.5228 16.5228 21 11 21C5.47715 21 1 16.5228 1 11ZM11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3Z" fill="currentColor" />
              </svg>
            </div>
            <input type="text" class="search-input" placeholder="Search regions..." v-model="searchQuery">
          </div>
          <button @click="openAddRegionModal" class="add-btn">
            <span class="plus-icon">+</span>
            Add Region
          </button>
        </div>
        
        <div class="table-container">
          <div class="table-wrapper">
            <table class="data-table">
              <thead>
                <tr>
                  <th @click="sortBy('country')" class="sortable">
                    Country <i :class="getSortIcon('country')"></i>
                  </th>
                  <th @click="sortBy('region')" class="sortable">
                    Region <i :class="getSortIcon('region')"></i>
                  </th>
                  <th @click="sortBy('status')" class="sortable">
                    Status <i :class="getSortIcon('status')"></i>
                  </th>
                  <th class="actions-column">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="region in paginatedRegions" :key="region.id" class="data-row">
                  <td>{{ region.country }}</td>
                  <td>{{ region.region }}</td>
                  <td>
                    <span :class="['status-pill capitalize', region.status.toLowerCase() === 'active' ? 'status-active' : 'status-inactive']">
                      {{ region.status }}
                    </span>
                  </td>
                  <td class="actions-column">
                    <button @click="editRegion(region)" class="action-btn edit-btn">
                      Edit
                    </button>
                    <button @click="deleteRegion(region.id)" class="action-btn delete-btn">
                      Delete
                    </button>
                  </td>
                </tr>
                <tr v-if="filteredRegions.length === 0">
                  <td colspan="4" class="empty-state">
                    No regions found
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
        <!-- Region Modal -->
        <div v-if="showRegionModal" class="modal-overlay" @click.self="closeRegionModal">
          <div class="modal-container">
            <div class="modal-header">
              <h2>{{ editingRegion ? 'Edit Region' : 'Add New Region' }}</h2>
              <button class="close-btn" @click="closeRegionModal">&times;</button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="saveRegion">
                <!-- Country Dropdown -->
                <div class="form-group">
                  <label for="country">Country <span class="required">*</span></label>
                  <div class="custom-select-container country-select-container">
                    <div 
                      class="custom-select-trigger country-select-trigger" 
                      @click="toggleCountryDropdown"
                      :class="{ 'active': isCountryDropdownOpen }"
                    >
                      <span>{{ form.country || 'Select a country' }}</span>
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

                <!-- Region Field -->
                <div class="form-group">
                  <label for="region">Region <span class="required">*</span></label>
                  <input type="text" id="region" v-model="form.region" required>
                </div>

                <!-- Status Dropdown -->
                <div class="form-group">
                  <label for="status">Status <span class="required">*</span></label>
                  <select id="status" v-model="form.status" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                  </select>
                </div>

                <div class="form-actions">
                  <button type="button" class="cancel-btn" @click="closeRegionModal">Cancel</button>
                  <button type="submit" class="submit-btn">{{ editingRegion ? 'Update Region' : 'Save Region' }}</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useForm, usePage } from "@inertiajs/vue3";
import AdminNavbar from '@/Components/AdminNavbar.vue';
import CustomPagination from "@/Components/CustomPagination.vue";

const page = usePage();
const user = page.props.auth.user;

const currentPage = ref(1);
const perPage = ref(10);
const lastPage = ref(1);

// State
const regions = ref([]);
const searchQuery = ref('');
const showRegionModal = ref(false);
const editingRegion = ref(false);
const editingRegionId = ref(null);
const editingRegionUuid = ref(null);
const sortKey = ref('region');
const sortDir = ref('asc');

// Dropdown state
const isCountryDropdownOpen = ref(false);
const countrySearch = ref('');
const countries = ref(['Kenya', 'Uganda', 'Tanzania', 'Rwanda', 'Burundi', 'Ethiopia', 'South Sudan']);
const filteredCountries = ref([...countries.value]);

// Form
const form = useForm({
  country: '',
  region: '',
  status: 'active',
  created_by: user.id,
  updated_by: user.id,
});

// Methods
const fetchRegions = async () => {
  try {
    const response = await axios.get(route("admin.regions.list"));
    regions.value = response.data;
  } catch (error) {
    console.error("Error fetching regions:", error);
  }
};

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

const toggleCountryDropdown = () => {
  isCountryDropdownOpen.value = !isCountryDropdownOpen.value;
};

const filterCountries = () => {
  const search = countrySearch.value.toLowerCase();
  filteredCountries.value = countries.value.filter(country => 
    country.toLowerCase().includes(search)
  );
};

const selectCountry = (country) => {
  form.country = country;
  isCountryDropdownOpen.value = false;
};

const openAddRegionModal = () => {
  editingRegion.value = false;
  editingRegionId.value = null;
  editingRegionUuid.value = null;
  form.reset();
  showRegionModal.value = true;
};

const closeRegionModal = () => {
  showRegionModal.value = false;
  form.reset();
};

const editRegion = (region) => {
  editingRegion.value = true;
  editingRegionId.value = region.id;
  editingRegionUuid.value = region.uuid;
  form.country = region.country;
  form.region = region.region;
  form.status = region.status;
  showRegionModal.value = true;
};

const saveRegion = () => {
  if (editingRegion.value) {
    form.put(route('admin.regions.update', editingRegionUuid.value), {
      onSuccess: () => {
        closeRegionModal();
        fetchRegions();
      },
    });
  } else {
    form.post(route('admin.regions.store'), {
      onSuccess: () => {
        closeRegionModal();
        fetchRegions();
      },
    });
  }
};

const deleteRegion = (regionId) => {
  if (confirm('Are you sure you want to delete this region?')) {
    useForm().delete(route('admin.regions.delete', regionId), {
      onSuccess: () => {
        fetchRegions();
      },
    });
  }
};

// Computed
const filteredRegions = computed(() => {
  return regions.value.filter(region => {
    const searchLower = searchQuery.value.toLowerCase();
    return region.country.toLowerCase().includes(searchLower) ||
           region.region.toLowerCase().includes(searchLower);
  });
});

const paginatedRegions = computed(() => {
  const sorted = [...filteredRegions.value].sort((a, b) => {
    const modifier = sortDir.value === 'asc' ? 1 : -1;
    return a[sortKey.value].localeCompare(b[sortKey.value]) * modifier;
  });
  return sorted;
});

// Lifecycle
onMounted(() => {
  fetchRegions();
});
</script>

<style scoped>
@import '../Supplier/SupplierComponents/SupplierSharedStyles.css';

/* Additional custom styles specific to regions */
.country-select-container,
.region-select-container {
  position: relative;
  width: 100%;
}

.dropdown-search {
  width: 100%;
  padding: 8px;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
  margin-bottom: 8px;
}

.dropdown-options {
  max-height: 200px;
  overflow-y: auto;
}

.dropdown-option {
  padding: 8px;
  cursor: pointer;
}

.dropdown-option:hover {
  background-color: #f7fafc;
}

.no-results {
  padding: 8px;
  color: #718096;
  text-align: center;
}

.status-pill {
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 500;
}

.status-active {
  background-color: #dcfce7;
  color: #166534;
}

.status-inactive {
  background-color: #fee2e2;
  color: #991b1b;
}
</style>