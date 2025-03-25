<template>
  <div class="tab-content">
    <div class="message-container">
      <SuccessMessage v-if="successMessage" @close="successMessage = ''" v-slot="{}">{{ successMessage }}</SuccessMessage>
      <ErrorMessage v-if="errorMessage" @close="errorMessage = ''" v-slot="{}">{{ errorMessage }}</ErrorMessage>
    </div>
    
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
              <td>{{ warehouse.contact_person }}</td>
              <td>{{ warehouse.email }}</td>
              <td>{{ warehouse.phone_number }}</td>
              <td>{{ warehouse.address }}</td>
              <td>{{ warehouse.krapin }}</td>
              <td>{{ warehouse.country || 'Kenya' }}</td>
              <td>{{ warehouse.region || warehouse.location }}</td>
              <td>{{ warehouse.gps || '--' }}</td>
              <td>
                <span :class="['status-pill', warehouse.status === 'active' ? 'status-active' : 'status-inactive']">
                  {{ warehouse.status.charAt(0).toUpperCase() + warehouse.status.slice(1) }}
                </span>
              </td>
              <td class="actions-column">
                <button @click="editWarehouse(warehouse)" class="action-btn edit-btn">
                  Edit
                </button>
                <button @click="deleteWarehouse(warehouse.uuid)" class="action-btn delete-btn">
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
            <input type="text" id="warehouseName" v-model="form.name" required placeholder="Enter warehouse name">
            <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
          </div>
          <div class="form-group">
            <label for="contactPerson">Contact Person <span class="required">*</span></label>
            <input type="text" id="contactPerson" v-model="form.contact_person" required placeholder="Enter contact person">
            <div v-if="form.errors.contact_person" class="text-red-500 text-sm mt-1">{{ form.errors.contact_person }}</div>
          </div>
          <div class="form-group">
            <label for="email">Email <span class="required">*</span></label>
            <input type="email" id="email" v-model="form.email" required placeholder="Enter email address">
            <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
          </div>
          <div class="form-group">
            <label for="phone">Phone <span class="required">*</span></label>
            <input type="text" id="phone" v-model="form.phone_number" required placeholder="Enter phone number">
            <div v-if="form.errors.phone_number" class="text-red-500 text-sm mt-1">{{ form.errors.phone_number }}</div>
          </div>
          <div class="form-group">
            <label for="address">Address <span class="required">*</span></label>
            <input type="text" id="address" v-model="form.address" required placeholder="Enter physical address">
            <div v-if="form.errors.address" class="text-red-500 text-sm mt-1">{{ form.errors.address }}</div>
          </div>
          <div class="form-group">
            <label for="kraPin">KRA PIN <span class="required">*</span></label>
            <input type="text" id="kraPin" v-model="form.krapin" required placeholder="Enter KRA PIN">
            <div v-if="form.errors.krapin" class="text-red-500 text-sm mt-1">{{ form.errors.krapin }}</div>
          </div>
          
          <!-- Country Dropdown -->
          <div class="form-group">
            <label for="country">Country <span class="required">*</span></label>
            <div class="custom-select-container">
              <div 
                class="custom-select-trigger" 
                @click="toggleCountryDropdown"
                :class="{ 'active': isCountryDropdownOpen }"
              >
                <span :data-has-value="!!form.country">{{ form.country || 'Select a country' }}</span>
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
              
              <div class="custom-select-dropdown" v-show="isCountryDropdownOpen">
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
            <div class="custom-select-container">
              <div 
                class="custom-select-trigger" 
                @click="toggleRegionDropdown"
                :class="{ 'active': isRegionDropdownOpen }"
              >
                <span :data-has-value="!!form.region">{{ form.region || 'Select a region' }}</span>
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
              
              <div class="custom-select-dropdown" v-show="isRegionDropdownOpen">
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
            <input type="text" id="gps" v-model="form.gps" placeholder="Enter GPS coordinates (latitude,longitude)">
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
            <button type="button" class="cancel-btn" @click="closeWarehouseModal">Cancel</button>
            <button type="submit" class="submit-btn" :disabled="form.processing">
              {{ editingWarehouse ? 'Update Warehouse' : 'Save Warehouse' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue';
import { useForm, usePage } from "@inertiajs/vue3";
import SuccessMessage from '@/Components/SuccessMessage.vue';
import ErrorMessage from '@/Components/ErrorMessage.vue';

const props = defineProps({
  supplier: {
    type: Object,
    required: true
  }
});

defineEmits(['warehouses-updated']);

// Get user from page props
const page = usePage();
const user = page.props.auth.user;
const searchQuery = ref("");

// Data and state
const warehouses = ref([]);
const sortKey = ref('name');
const sortDir = ref('asc');

// Modal and form
const showWarehouseModal = ref(false);
const editingWarehouse = ref(false);
const editingWarehouseId = ref(null);

// Dropdown state
const isCountryDropdownOpen = ref(false);
const countrySearch = ref('');
const countries = ref([
  'Kenya', 'Uganda', 'Tanzania', 'Rwanda', 'Burundi', 'Ethiopia', 'South Sudan'
]);
const filteredCountries = ref([...countries.value]);

const isRegionDropdownOpen = ref(false);
const regionSearch = ref('');
const regions = ref([
  'Baringo', 'Bomet', 'Bungoma', 'Busia', 'Elgeyo Marakwet', 'Embu', 'Garissa', 
  'Homa Bay', 'Isiolo', 'Kajiado', 'Kakamega', 'Kericho', 'Kiambu', 'Kilifi', 
  'Kirinyaga', 'Kisii', 'Kisumu', 'Kitui', 'Kwale', 'Laikipia', 'Lamu', 'Machakos', 
  'Makueni', 'Mandera', 'Marsabit', 'Meru', 'Migori', 'Mombasa', 'Murang\'a', 
  'Nairobi', 'Nakuru', 'Nandi', 'Narok', 'Nyamira', 'Nyandarua', 'Nyeri', 'Samburu', 
  'Siaya', 'Taita Taveta', 'Tana River', 'Tharaka Nithi', 'Trans Nzoia', 'Turkana', 
  'Uasin Gishu', 'Vihiga', 'Wajir', 'West Pokot'
]);
const filteredRegions = ref([...regions.value]);

const isStatusDropdownOpen = ref(false);
const statusSearch = ref('');
const filteredStatuses = ref(['active', 'inactive']);

// Form for warehouse
const form = useForm({
  name: '',
  address: '',
  email: '',
  krapin: '',
  contact_person: '',
  phone_number: '',
  status: 'active',
  country: 'Kenya',
  region: '',
  gps: '',
  uuid: '',
  company_id: props.supplier && props.supplier.company_id ? parseInt(props.supplier.company_id) : 
             (props.supplier && props.supplier.id ? parseInt(props.supplier.id) : ''),
  created_by: user && user.id ? parseInt(user.id) : '',
  supplier_id: props.supplier && props.supplier.id ? parseInt(props.supplier.id) : ''
});

// Message state
const successMessage = ref('');
const errorMessage = ref('');

// Computed properties for sorting
const sortedWarehouses = computed(() => {
  const warehousesList = [...warehouses.value];
  warehousesList.sort((a, b) => {
    let modifier = sortDir.value === 'asc' ? 1 : -1;
    
    // Map frontend keys to backend keys
    const keyMap = {
      'contactPerson': 'contact_person',
      'phone': 'phone_number',
      'kraPin': 'krapin'
    };
    
    let aKey = keyMap[sortKey.value] || sortKey.value;
    let bKey = keyMap[sortKey.value] || sortKey.value;
    
    let aValue = a[aKey];
    let bValue = b[bKey];
    
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
  
  return warehousesList;
});

// Methods
const fetchWarehouses = async () => {  
  try {
    const response = await axios.get(
      route("supplier.warehouses.list", {
        uuid: props.supplier.uuid || props.supplier.id,
      })
    );
    warehouses.value = response.data;
  } catch (error) {
    console.error("Error fetching warehouses:", error);
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

const openAddWarehouseModal = () => {
  editingWarehouse.value = false;
  editingWarehouseId.value = null;
  form.reset();
  form.clearErrors();
  
  // Set default values - ensure company_id is set as an integer
  if (props.supplier && props.supplier.company_id) {
    form.company_id = parseInt(props.supplier.company_id);
  } else if (props.supplier && props.supplier.id) {
    // If company_id is missing but supplier id exists, try using supplier id
    form.company_id = parseInt(props.supplier.id);
  }
  
  form.created_by = parseInt(user.id) || '';
  form.supplier_id = parseInt(props.supplier.id) || '';
  form.country = 'Kenya';
  form.status = 'active';
  
  showWarehouseModal.value = true;
  isCountryDropdownOpen.value = false;
  isRegionDropdownOpen.value = false;
  isStatusDropdownOpen.value = false;
};

const closeWarehouseModal = () => {
  showWarehouseModal.value = false;
  form.reset();
  form.clearErrors();
};

const editWarehouse = (warehouse) => {
  editingWarehouse.value = true;
  editingWarehouseId.value = warehouse.id;
  form.uuid = warehouse.uuid;
  // Map warehouse fields to form
  form.name = warehouse.name;
  form.contact_person = warehouse.contact_person;
  form.email = warehouse.email;
  form.phone_number = warehouse.phone_number;
  form.address = warehouse.address;
  form.krapin = warehouse.krapin;
  form.country = warehouse.country || 'Kenya';
  form.region = warehouse.region || warehouse.location;
  form.gps = warehouse.gps || '';
  form.status = warehouse.status;
  
  showWarehouseModal.value = true;
  isCountryDropdownOpen.value = false;
  isRegionDropdownOpen.value = false;
  isStatusDropdownOpen.value = false;
};

const deleteWarehouse = (warehouseUuid) => {
  if (confirm('Are you sure you want to delete this warehouse?')) {
    useForm().delete(route('supplierwarehouse.delete', warehouseUuid), {
      preserveScroll: true,
      onSuccess: () => {
        successMessage.value = 'Warehouse deleted successfully';
        fetchWarehouses();
        console.log('Setting success message in Warehouses - delete');
        setTimeout(() => { 
          successMessage.value = '';
          console.log('Clearing success message in Warehouses - delete');
        }, 3000);
      },
      onError: () => {
        errorMessage.value = 'Error deleting warehouse';
        console.log('Setting error message in Warehouses - delete');
        setTimeout(() => { 
          errorMessage.value = '';
          console.log('Clearing error message in Warehouses - delete');
        }, 3000);
      }
    });
  }
};

const saveWarehouse = () => {
  // Fix the company_id format issue - backend expects integer
  if (props.supplier && props.supplier.company_id) {
    form.company_id = parseInt(props.supplier.company_id);
  } else if (props.supplier && props.supplier.id) {
    // If company_id is missing but supplier id exists, try using supplier id
    form.company_id = parseInt(props.supplier.id);
  } 
  
  
  if (editingWarehouse.value) {
    form.put(route('supplierwarehouse.update'), {
      preserveScroll: true,
      onSuccess: (response) => {
        successMessage.value = 'Warehouse updated successfully';
        closeWarehouseModal();
        fetchWarehouses();
        console.log('Setting success message in Warehouses - update');
        setTimeout(() => { 
          successMessage.value = '';
          console.log('Clearing success message in Warehouses - update');
        }, 3000);
      },
      onError: (errors) => {
        errorMessage.value = 'Error updating warehouse';
        console.error('Update failed with errors:', errors);
        console.log('Setting error message in Warehouses - update');
        setTimeout(() => { 
          errorMessage.value = '';
          console.log('Clearing error message in Warehouses - update');
        }, 3000);
      }
    });
  } else {
    form.post(route('supplierwarehouse.create'), {
      preserveScroll: true,
      onSuccess: (response) => {
        successMessage.value = 'Warehouse added successfully';
        closeWarehouseModal();
        fetchWarehouses();
        console.log('Setting success message in Warehouses - create');
        setTimeout(() => { 
          successMessage.value = '';
          console.log('Clearing success message in Warehouses - create');
        }, 3000);
      },
      onError: (errors) => {
        errorMessage.value = 'Error creating warehouse';
        console.error('Creation failed with errors:', errors);
        console.log('Setting error message in Warehouses - create');
        setTimeout(() => { 
          errorMessage.value = '';
          console.log('Clearing error message in Warehouses - create');
        }, 3000);
      }
    });
  }
};

// Auto-dismiss messages after 3 seconds
const autoDismissMessage = (type) => {
  console.log('Warehouses - Auto dismissing message of type:', type);
  setTimeout(() => {
    if (type === 'success') {
      successMessage.value = '';
    } else if (type === 'error') {
      errorMessage.value = '';
    }
  }, 3000);
};

// Dropdown methods
const toggleCountryDropdown = () => {
  isCountryDropdownOpen.value = !isCountryDropdownOpen.value;
  if (isCountryDropdownOpen.value) {
    countrySearch.value = '';
    filteredCountries.value = [...countries.value];
  }
};

const closeDropdownsOutside = (event) => {
  const dropdowns = document.querySelectorAll('.custom-select-container');
  let clickedInside = false;
  
  dropdowns.forEach(dropdown => {
    if (dropdown.contains(event.target)) {
      clickedInside = true;
    }
  });

  if (!clickedInside) {
    isCountryDropdownOpen.value = false;
    isRegionDropdownOpen.value = false;
    isStatusDropdownOpen.value = false;
  }
};

const filterCountries = () => {
  if (!countrySearch.value.trim()) {
    filteredCountries.value = [...countries.value];
  } else {
    const query = countrySearch.value.toLowerCase();
    filteredCountries.value = countries.value.filter(country => 
      country.toLowerCase().includes(query)
    );
  }
};

const selectCountry = (country) => {
  if (country) {
    form.country = country;
    isCountryDropdownOpen.value = false;
    countrySearch.value = '';
  }
};

const toggleRegionDropdown = () => {
  isRegionDropdownOpen.value = !isRegionDropdownOpen.value;
  if (isRegionDropdownOpen.value) {
    regionSearch.value = '';
    filteredRegions.value = [...regions.value];
  }
};

const filterRegions = () => {
  if (!regionSearch.value.trim()) {
    filteredRegions.value = [...regions.value];
  } else {
    const query = regionSearch.value.toLowerCase();
    filteredRegions.value = regions.value.filter(region => 
      region.toLowerCase().includes(query)
    );
  }
};

const selectRegion = (region) => {
  if (region) {
    form.region = region;
    isRegionDropdownOpen.value = false;
    regionSearch.value = '';
  }
};

const toggleStatusDropdown = () => {
  isStatusDropdownOpen.value = !isStatusDropdownOpen.value;
  if (isStatusDropdownOpen.value) {
    statusSearch.value = '';
    filteredStatuses.value = ['active', 'inactive'];
  }
};

const filterStatuses = () => {
  if (!statusSearch.value.trim()) {
    filteredStatuses.value = ['active', 'inactive'];
  } else {
    const query = statusSearch.value.toLowerCase();
    filteredStatuses.value = ['active', 'inactive'].filter(status => 
      status.toLowerCase().includes(query)
    );
  }
};

const selectStatus = (status) => {
  if (status) {
    form.status = status;
    isStatusDropdownOpen.value = false;
    statusSearch.value = '';
  }
};

// Lifecycle hooks
onMounted(() => {  
  // Fetch warehouses data
  fetchWarehouses();
  
  // Initialize filtered countries and regions
  filteredCountries.value = [...countries.value];
  filteredRegions.value = [...regions.value];
  
  // Add click outside listener for dropdowns
  document.addEventListener('click', closeDropdownsOutside);
});

onBeforeUnmount(() => {
  // Clean up the event listeners
  document.removeEventListener('click', closeDropdownsOutside);
});

// Add capitalizeFirstLetter helper function if not already present
const capitalizeFirstLetter = (string) => {
  if (!string) return '';
  return string.charAt(0).toUpperCase() + string.slice(1);
};
</script>

<style scoped>
@import './SupplierSharedStyles.css';
</style> 
