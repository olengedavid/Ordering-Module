<template>
  <SupplierNavbar />
  <Head title="Supplier Users" />

  <div class="page-container">
    <div class="content-container">
      <div class="message-container">
        <SuccessMessage v-if="successMessage" @close="successMessage = ''" v-slot="{}">{{ successMessage }}</SuccessMessage>
        <ErrorMessage v-if="errorMessage" @close="errorMessage = ''" v-slot="{}">{{ errorMessage }}</ErrorMessage>
      </div>
      <div class="header-container">
        <h1 class="page-title">Supplier Users</h1>
      </div>

      <div class="table-controls">
        <div class="search-container">
          <div class="search-icon">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              width="20"
              height="20"
              color="#000000"
              fill="none"
            >
              <path
                opacity="0.4"
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M16.7929 16.7929C17.1834 16.4024 17.8166 16.4024 18.2071 16.7929L22.7071 21.2929C23.0976 21.6834 23.0976 22.3166 22.7071 22.7071C22.3166 23.0976 21.6834 23.0976 21.2929 22.7071L16.7929 18.2071C16.4024 17.8166 16.4024 17.1834 16.7929 16.7929Z"
                fill="currentColor"
              />
              <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M1 11C1 5.47715 5.47715 1 11 1C16.5228 1 21 5.47715 21 11C21 16.5228 16.5228 21 11 21C5.47715 21 1 16.5228 1 11ZM11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3Z"
                fill="currentColor"
              />
            </svg>
          </div>
          <input
            type="text"
            class="search-input"
            placeholder="Search users..."
            v-model="searchQuery"
            @keypress.enter="fetchSupplierUsers"
          />
        </div>
        <button @click="openAddUserModal" class="add-btn">
          <span class="plus-icon">+</span>
          Add User
        </button>
      </div>

      <div class="table-container">
        <div class="table-wrapper">
          <table class="data-table">
            <thead>
              <tr>
                <th @click="sortBy('username')" class="sortable">
                  Username <i :class="getSortIcon('username')"></i>
                </th>
                <th @click="sortBy('email')" class="sortable">
                  Email Address <i :class="getSortIcon('email')"></i>
                </th>
                <th @click="sortBy('password')" class="sortable">
                  Password <i :class="getSortIcon('password')"></i>
                </th>
                <th @click="sortBy('warehouse')" class="sortable">
                  Warehouse <i :class="getSortIcon('warehouse')"></i>
                </th>
                <th @click="sortBy('permissions')" class="sortable">
                  Permissions <i :class="getSortIcon('permissions')"></i>
                </th>
                <th @click="sortBy('status')" class="sortable">
                  Status <i :class="getSortIcon('status')"></i>
                </th>
                <th class="actions-column">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users" :key="user.id" class="data-row">
                <td>{{ user.username }}</td>
                <td>{{ user.email }}</td>
                <td>{{ user.displayPassword }}</td>
                <td>{{ user.warehouse }}</td>
                <td>{{ getPermissionsDisplayText(user.permissions) }}</td>
                <td>
                  <span
                    :class="[
                      'status-pill capitalize',
                      user.status === 'active'
                        ? 'status-active'
                        : 'status-inactive',
                    ]"
                  >
                    {{ user.status }}
                  </span>
                </td>
                <td class="actions-column">
                  <button @click="editUser(user)" class="action-btn edit-btn">
                    Edit
                  </button>
                </td>
              </tr>
              <tr v-if="filteredUsers.length === 0">
                <td colspan="7" class="empty-state">No users found</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <CustomPagination
        :current-page="currentPage"
        :last-page="lastPage"
        :per-page="perPage"
        @page-changed="handlePageChange"
        @update:per-page="handlePerPageChange"
      />
    </div>

    <!-- Add/Edit User Modal -->
    <div
      v-if="showUserModal"
      class="modal-overlay"
      @click.self="closeUserModal"
    >
      <div class="modal-container">
        <div class="modal-header">
          <h2>{{ editingUser ? "Edit User" : "Add New User" }}</h2>
          <button class="close-btn" @click="closeUserModal">&times;</button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="saveUser">
            <!-- Username Field -->
            <div class="form-group">
              <label for="username"
                >Username <span class="required">*</span></label
              >
              <input
                type="text"
                id="username"
                v-model="form.username"
                required
              />
            </div>

            <!-- Email Field -->
            <div class="form-group">
              <label for="email"
                >Email Address <span class="required">*</span></label
              >
              <input
                type="email"
                id="email"
                v-model="form.email"
                required
              />
            </div>

            <!-- Password Field with show/hide toggle -->
            <div class="form-group">
              <label for="password"
                >Password <span class="required">*</span></label
              >
              <div class="password-input-container">
                <input
                  :type="showPassword ? 'text' : 'password'"
                  id="password"
                  v-model="form.password"
                  required
                />
                <button
                  type="button"
                  class="password-toggle-btn"
                  @click="togglePasswordVisibility"
                >
                  <!-- Eye Close Icon (shown when password is hidden) -->
                  <svg
                    v-if="!showPassword"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    width="24"
                    height="24"
                    color="#000000"
                    fill="none"
                  >
                    <path
                      d="M19.439 15.439C21 14 22 12 22 12C20.5 9 16.6892 5 12 5C11.0922 5 10.2294 5.15476 9.41827 5.41827M17 17.4186C15.5657 18.3368 13.8793 19 12 19C7.31078 19 3.5 15 2 12C2 12 3.5 9 6.5 6.91847"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <path
                      d="M9.85786 10C9.32783 10.53 9 11.2623 9 12.0711C9 13.6887 10.3113 15 11.9289 15C12.7377 15 13.47 14.6722 14 14.1421"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <path
                      d="M3 3L21 21"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                  <!-- Eye Open Icon (shown when password is visible) -->
                  <svg
                    v-else
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    width="24"
                    height="24"
                    color="#000000"
                    fill="none"
                  >
                    <path
                      d="M15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15C13.6569 15 15 13.6569 15 12Z"
                      stroke="currentColor"
                      stroke-width="1.5"
                    />
                    <path
                      d="M12 5C17.5228 5 22 12 22 12C22 12 17.5228 19 12 19C6.47715 19 2 12 2 12C2 12 6.47715 5 12 5Z"
                      stroke="currentColor"
                      stroke-width="1.5"
                    />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Warehouse Dropdown -->
            <div class="form-group">
              <label for="warehouse"
                >Warehouse <span class="required">*</span></label
              >
              <select id="warehouse" v-model="form.warehouse_id" required>
                <option value="">Select a Warehouse</option>
                <option
                  v-for="warehouse in warehouses"
                  :key="warehouse.id"
                  :value="warehouse.id"
                >
                  {{ warehouse.name }}
                </option>
              </select>
            </div>

            <!-- Permissions Checkboxes -->
            <div class="form-group">
              <label>Permissions <span class="required">*</span></label>
              <div class="permissions-container">
                <div
                  v-for="permission in availablePermissions"
                  :key="permission.id"
                  class="permission-checkbox"
                >
                  <input
                    type="checkbox"
                    :id="permission.id"
                    v-model="form.permissions"
                    :value="permission.id"
                  />
                  <label :for="permission.id">{{ permission.label }}</label>
                </div>
              </div>
            </div>

            <!-- Status Dropdown -->
            <div class="form-group">
              <label for="status"
                >Status <span class="required">*</span></label
              >
              <select id="status" v-model="form.status" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>

            <div class="form-actions">
              <button
                type="button"
                class="cancel-btn"
                @click="closeUserModal"
              >
                Cancel
              </button>
              <button type="submit" class="submit-btn">Save User</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
  
<script setup >
import { ref, onMounted, computed } from "vue";
import { Head, usePage, useForm } from "@inertiajs/vue3";
import SupplierNavbar from "@/Components/SupplierNavbar.vue";
import CustomPagination from "@/Components/CustomPagination.vue";
import SuccessMessage from "@/Components/SuccessMessage.vue";
import ErrorMessage from "@/Components/ErrorMessage.vue";

const page = usePage();
const user = page.props.auth.user;
const users = ref([]);
const warehouses = ref([]);
const searchQuery = ref("");
const showUserModal = ref(false);
const showPassword = ref(false);
const editingUser = ref(false);
const editingUserId = ref(null);
const editingUserUuid = ref(null);
const sortKey = ref("username");
const sortDir = ref("asc");
const currentPage = ref(1);
const perPage = ref(10);
const lastPage = ref(1);
const successMessage = ref("");

// Data from API
const supplier_uuid = page.props.auth.user.company.uuid;


// Form for API submission
const form = useForm({
  username: "",
  email: "",
  password: "",
  warehouse_id: "",
  permissions: [], // Now stores an array for multiple selections
  status: "active",
  created_by: user.id,
  updated_by: user.id,
  company_id: user.company_id,
  entity_type: "supplier",
});

const selectedPermissions = ref([]);

const availablePermissions = [
  { id: "manage_users", label: "Can Manage Users" },
  { id: "manage_orders", label: "Can Manage Orders" },
  { id: "manage_warehouses", label: "Can Manage Warehouses" },
  { id: "manage_products", label: "Can Manage Products" },
  { id: "manage_inventory", label: "Can Manage Inventory" },
  { id: "cancel_orders", label: "Can Cancel Order Requests" },
  { id: "confirm_deliveries", label: "Can Confirm Deliveries" },
];

// Load initial warehouse and user data
onMounted(() => {
  fetchSupplierUsers();
  fetchWarehouses();
});

const fetchWarehouses = async () => {
  try {
    const response = await axios.get(
      route("supplier.warehouses.list", {
        uuid: supplier_uuid,
      })
    );
    // Store complete warehouse data
    warehouses.value = response.data;
  } catch (error) {
    console.error("Error fetching warehouses:", error);
  }
};

const fetchSupplierUsers = async () => {
  try {
    const response = await axios.get(
      route("supplier.users.list", {
        uuid: supplier_uuid,
        search: searchQuery.value,
        page: currentPage.value,
        perPage: perPage.value,
      })
    );

    // Transform API response to match UI format

    users.value = response.data.data.map((user) => ({
      ...user,
      username: user.name,
      email: user.email,
      password: "securepass", // Placeholder for display
      displayPassword: "••••••••",
      warehouse: user.warehouse?.name || "",
      permissions: parsePermissions(user.user_permissions?.[0]?.permissions),
      status:  user.status,
    }));

    currentPage.value = response.data.current_page;
    lastPage.value = response.data.last_page;
    perPage.value = response.data.per_page;
  } catch (error) {
    console.error("Error fetching supplier users:", error);
  }
};

// Helper to parse permissions from API format to UI format
const parsePermissions = (permissionsJson) => {
  try {
    return JSON.parse(permissionsJson || "[]");
  } catch (error) {
    console.error("Error parsing permissions:", error);
    return [];
  }
};

// Map UI permissions back to API format for submission
const mapPermissionsToApi = (permissions) => {
  return Object.keys(permissions)
    .filter((key) => permissions[key])
    .map((key) => permissionsMapping[key])
    .filter(Boolean);
};

// Modal and form handling
const openAddUserModal = () => {
  editingUser.value = false;
  editingUserId.value = null;
  editingUserUuid.value = null;
  selectedPermissions.value = [];
  form.reset();
  showUserModal.value = true;
  showPassword.value = false;
};

const closeUserModal = () => {
  showUserModal.value = false;
  showPassword.value = false;
  form.reset();
};

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value;
};

const editUser = (user) => {
  console.log("Editing user:", user);
  editingUser.value = true;
  editingUserId.value = user.id;
  editingUserUuid.value = user.uuid;
  // Set the form data
  form.username = user.username;
  form.email = user.email;
  form.password = user.password;
  form.warehouse_id = user.warehouse_id;
  form.status = user.status;
  form.permissions = parsePermissions(user.user_permissions?.[0]?.permissions);
  showUserModal.value = true;
  showPassword.value = false;
};

// Save user (create or update)
const saveUser = () => {
  if (editingUser.value) {
    // Log the UUID to verify it exists
    form.put(route("supplier.users.update", editingUserUuid.value), {
      preserveScroll: true,
      onSuccess: () => {
        closeUserModal();
        form.reset();
        fetchSupplierUsers();

        // Flash
        successMessage.value = "User updated successfully";
          setTimeout(() => {
            successMessage.value = "";
          }, 3000);
      },
    });
  } else {
    form.post(route("supplier.users.store"), {
      preserveScroll: true,
      onSuccess: () => {
        closeUserModal();
        form.reset();
        fetchSupplierUsers();

        // Flash
        successMessage.value = "User created successfully";
          setTimeout(() => {
            successMessage.value = "";
          }, 3000);
      },
    });
  }
};


// Sorting functions
const sortBy = (key) => {
  if (sortKey.value === key) {
    sortDir.value = sortDir.value === "asc" ? "desc" : "asc";
  } else {
    sortKey.value = key;
    sortDir.value = "asc";
  }
};

const getSortIcon = (key) => {
  if (sortKey.value !== key) return "sort-icon sort-none";
  return sortDir.value === "asc" ? "sort-icon sort-asc" : "sort-icon sort-desc";
};


const handlePageChange = (page) => {
  currentPage.value = page;
  fetchSupplierUsers();
};

const handlePerPageChange = (newPerPage) => {
  perPage.value = newPerPage;
  fetchSupplierUsers();
};

// Helper function for permissions display
const getPermissionsDisplayText = (permissions) => {
  if (!permissions || !Array.isArray(permissions)) {
    return "No permissions";
  }

  const enabledPermissions = permissions.map(permId => {
    const permission = availablePermissions.find(p => p.id === permId);
    return permission ? permission.label : null;
  }).filter(Boolean);

  if (enabledPermissions.length === 0) {
    return "No permissions";
  } else if (enabledPermissions.length <= 2) {
    return enabledPermissions.join(", ");
  } else {
    return `${enabledPermissions.length} permissions granted`;
  }
};

// Computed properties
const filteredUsers = computed(() => {
  if (searchQuery.value.trim() === "") {
    return users.value;
  }

  const query = searchQuery.value.toLowerCase();
  return users.value.filter((user) => {
    return Object.values(user).some((value) => {
      if (value === null || value === undefined) return false;
      if (typeof value === "object") {
        // For permissions object, check if any permission name matches
        return Object.entries(value).some(([key]) => {
          return (
            availablePermissions[key]?.toLowerCase().includes(query) || false
          );
        });
      }
      return String(value).toLowerCase().includes(query);
    });
  });
});

const totalPages = computed(() => {
  return Math.ceil(filteredUsers.value.length / perPage.value);
});

const paginatedUsers = computed(() => {
  const startIndex = (currentPage.value - 1) * perPage.value;
  const endIndex = startIndex + perPage.value;

  // Sort the users first
  const sortedUsers = [...filteredUsers.value];
  sortedUsers.sort((a, b) => {
    let modifier = sortDir.value === "asc" ? 1 : -1;

    // Special handling for permissions
    if (sortKey.value === "permissions") {
      // Count enabled permissions as a simple sorting metric
      const aPermCount = Object.values(a.permissions).filter((p) => p).length;
      const bPermCount = Object.values(b.permissions).filter((p) => p).length;
      return (aPermCount < bPermCount ? -1 : 1) * modifier;
    }

    let aValue = a[sortKey.value];
    let bValue = b[sortKey.value];

    // Handle undefined or null values
    if (aValue === undefined || aValue === null) {
      aValue = "";
    }
    if (bValue === undefined || bValue === null) {
      bValue = "";
    }

    if (typeof aValue === "number" && typeof bValue === "number") {
      return aValue < bValue ? -1 * modifier : 1 * modifier;
    } else {
      return aValue.toString().localeCompare(bValue.toString()) * modifier;
    }
  });

  return sortedUsers.slice(startIndex, endIndex);
});
</script>
  
<style scoped>
.page-container {
  display: flex;
  justify-content: center;
  background-color: #f5f7fa;
  min-height: 100vh;
  padding: 40px 20px;
  font-family: "Inter", "Segoe UI", Roboto, sans-serif;
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

.page-title {
  font-size: 1.8rem;
  color: #1e293b;
  font-weight: 600;
  margin: 0;
}

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
  border-color: #0e64a5;
  box-shadow: 0 0 0 2px rgba(14, 100, 165, 0.1);
}

.add-btn {
  display: flex;
  align-items: center;
  padding: 10px 16px;
  background-color: #0e64a5;
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

/* Table styles */
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

/* Ensure the table headers and data cells have proper min-width */
.data-table th,
.data-table td {
  white-space: nowrap; /* Prevent text wrapping */
  min-width: 120px; /* Minimum width for each column */
}

/* Actions column should be narrower and fixed */
.actions-column {
  min-width: 90px;
  width: 90px;
}

/* Add a visible indicator for horizontal scroll on mobile */
@media (max-width: 768px) {
  .table-container::after {
    content: "";
    position: absolute;
    top: 0;
    right: 0;
    width: 20px;
    height: 100%;
    background: linear-gradient(
      to right,
      rgba(255, 255, 255, 0),
      rgba(230, 230, 230, 0.3)
    );
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
  content: "⇵";
  opacity: 0.3;
  font-size: 12px;
}

.sort-asc::after {
  content: "↑";
  color: #2563eb;
  font-size: 12px;
}

.sort-desc::after {
  content: "↓";
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

/* Status Pills */
.status-pill {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.8rem;
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

/* Action Buttons */
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
  color: #0e64a5;
  border-color: #0e64a5;
}

.edit-btn:hover {
  background-color: rgba(14, 100, 165, 0.05);
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
  background-color: #0e64a5;
  color: white;
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

/* Permissions container styles */
.permissions-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  /* background-color: #f8fafc; */
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  padding: 16px;
}

.permission-checkbox {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px;
  border-radius: 4px;
}

.permission-checkbox input[type="checkbox"] {
  appearance: none;
  -webkit-appearance: none;
  width: 20px;
  height: 20px;
  border: 2px solid #e2e8f0;
  border-radius: 4px;
  margin-right: 8px;
  cursor: pointer;
  position: relative;
  background: white;
}

.permission-checkbox input[type="checkbox"]:checked {
  background-color: #1e293b;
  border-color: #1e293b;
}

.permission-checkbox input[type="checkbox"]:checked::after {
  content: '✓';
  position: absolute;
  color: white;
  font-size: 14px;
  left: 3px;
  top: -1px;
}

.permission-checkbox label {
  margin-bottom: 0;
  font-size: 0.9rem;
  cursor: pointer;
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
  background-color: #0e64a5;
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

/* Password Input Container Styles */
.password-input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.password-input-container input {
  width: 100%;
  padding-right: 40px; /* Make room for the eye icon */
}

.password-toggle-btn {
  position: absolute;
  right: 8px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  padding: 0;
  color: #64748b;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.2s;
}

.password-toggle-btn:hover {
  color: #0e64a5;
}

.password-toggle-btn svg {
  display: block;
}

/* Responsive styles */
@media (max-width: 768px) {
  .header-container {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }

  .table-controls {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }

  .add-btn {
    display: flex;
    align-items: center;
    padding: 10px 16px;
    background-color: #0e64a5;
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

  .search-container {
    width: 100%;
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
    background-color: #0e64a5;
    color: white;
  }

  .permissions-container {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 640px) {
  .page-container {
    padding: 20px 16px;
  }

  .content-container {
    padding: 16px;
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

  .page-numbers {
    display: none;
  }
}

.close-icon {
  line-height: 1;
  position: relative;
  top: -2px;
}

/* Message container */
.message-container {
  position: fixed;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 1000;
  min-width: 300px;
  max-width: 400px;
}

@media (max-width: 640px) {
  .page-container {
    padding: 20px 16px;
  }
  
  .content-container {
    padding: 16px;
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
  
  .page-numbers {
    display: none;
  }
}
</style>