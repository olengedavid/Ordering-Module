<template>
  <div>
    <AdminNavbar />
    <div class="page-container">
      <div class="message-container">
        <SuccessMessage
          v-if="successMessage"
          @close="successMessage = ''"
          v-slot
          >{{ successMessage }}</SuccessMessage
        >
        <ErrorMessage v-if="errorMessage" @close="errorMessage = ''" v-slot>{{
          errorMessage
        }}</ErrorMessage>
      </div>
      <div class="content-container">
        <h1 class="page-title">Users</h1>

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
                  <th @click="sortBy('name')" class="sortable">
                    Name <i :class="getSortIcon('name')"></i>
                  </th>
                  <th @click="sortBy('email')" class="sortable">
                    Email Address <i :class="getSortIcon('email')"></i>
                  </th>
                  <th @click="sortBy('role')" class="sortable">
                    Role <i :class="getSortIcon('role')"></i>
                  </th>
                  <th @click="sortBy('created_at')" class="sortable">
                    Created At <i :class="getSortIcon('created_at')"></i>
                  </th>
                  <th @click="sortBy('status')" class="sortable">
                    Status <i :class="getSortIcon('status')"></i>
                  </th>
                  <th class="actions-column">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="user in filteredUsers"
                  :key="user.id"
                  class="data-row"
                >
                  <td>{{ user.name }}</td>
                  <td>{{ user.email }}</td>
                  <td>{{ user.role }}</td>
                  <td>{{ formatDate(user.created_at) }}</td>
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
                  <td colspan="6" class="empty-state">No users found</td>
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
              <!-- Name Field -->
              <div class="form-group">
                <label for="name">Name <span class="required">*</span></label>
                <input type="text" id="name" v-model="form.name" required />
              </div>

              <!-- Email Field -->
              <div class="form-group">
                <label for="email"
                  >Email Address <span class="required">*</span></label
                >
                <input type="email" id="email" v-model="form.email" required />
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
                    :required="!editingUser"
                    :placeholder="
                      editingUser ? 'Leave blank to keep current password' : ''
                    "
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

              <!-- Role Dropdown -->
              <div class="form-group">
                <label for="role">Role <span class="required">*</span></label>
                <select id="role" v-model="form.role" required>
                  <option value="">Select a Role</option>
                  <option value="Admin">Admin</option>
                  <option value="Manager">Manager</option>
                  <option value="User">User</option>
                </select>
              </div>

              <!-- Status Field -->
              <div class="form-group">
                <label for="status">Status</label>
                <div class="toggle-container">
                  <input
                    type="checkbox"
                    id="status"
                    v-model="form.active"
                    class="toggle-checkbox"
                  />
                  <label for="status" class="toggle-label"></label>
                  <span class="toggle-text">{{
                    form.active ? "Active" : "Inactive"
                  }}</span>
                </div>
              </div>

              <div class="form-actions">
                <button
                  type="button"
                  class="cancel-btn"
                  @click="closeUserModal"
                >
                  Cancel
                </button>
                <button type="submit" class="submit-btn">
                  {{ editingUser ? "Update" : "Save" }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import AdminNavbar from "@/Components/AdminNavbar.vue";
import CustomPagination from "@/Components/CustomPagination.vue";
import SuccessMessage from "@/Components/SuccessMessage.vue";
import ErrorMessage from "@/Components/ErrorMessage.vue";
import { ref, computed, onMounted } from "vue";
import axios from "axios";

export default {
  components: {
    Link,
    AdminNavbar,
    CustomPagination,
    SuccessMessage,
    ErrorMessage,
  },

  setup() {
    const users = ref([]);
    const searchQuery = ref("");
    const currentSort = ref({ field: "name", direction: "asc" });
    const currentPage = ref(1);
    const lastPage = ref(1);
    const perPage = ref(10);
    const showUserModal = ref(false);
    const editingUser = ref(null);
    const showPassword = ref(false);
    const successMessage = ref("");
    const errorMessage = ref("");

    const form = ref({
      name: "",
      email: "",
      password: "",
      role: "",
      active: true,
    });

    const filteredUsers = computed(() => {
      let filtered = [...users.value];

      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
          (user) =>
            user.name.toLowerCase().includes(query) ||
            user.email.toLowerCase().includes(query) ||
            user.role.toLowerCase().includes(query)
        );
      }

      // Sort users
      filtered.sort((a, b) => {
        const fieldA = String(a[currentSort.value.field] || "").toLowerCase();
        const fieldB = String(b[currentSort.value.field] || "").toLowerCase();

        if (currentSort.value.direction === "asc") {
          return fieldA.localeCompare(fieldB);
        } else {
          return fieldB.localeCompare(fieldA);
        }
      });

      return filtered;
    });

    const fetchUsers = async () => {
      try {
        const response = await axios.get(route("admin.users.list"), {
          params: {
            page: currentPage.value,
            perPage: perPage.value,
          },
        });
        users.value = response.data.data;
        currentPage.value = response.data.current_page;
        lastPage.value = response.data.last_page;
        perPage.value = response.data.per_page;
      } catch (error) {
        errorMessage.value =
          "Failed to load users. Please contact administrator.";
        console.error("Error fetching users:", error);
        users.value = [];
      }
    };

    const sortBy = (field) => {
      if (currentSort.value.field === field) {
        currentSort.value.direction =
          currentSort.value.direction === "asc" ? "desc" : "asc";
      } else {
        currentSort.value.field = field;
        currentSort.value.direction = "asc";
      }
    };

    const getSortIcon = (field) => {
      if (currentSort.value.field !== field) return "fa fa-sort";
      return currentSort.value.direction === "asc"
        ? "fa fa-sort-up"
        : "fa fa-sort-down";
    };

    const formatDate = (dateString) => {
      if (!dateString) return "";
      const date = new Date(dateString);
      return date.toLocaleDateString();
    };

    const openAddUserModal = () => {
      editingUser.value = null;
      form.value = {
        name: "",
        email: "",
        password: "",
        role: "",
        active: true,
      };
      showUserModal.value = true;
    };

    const editUser = (user) => {
      editingUser.value = user;
      form.value = {
        name: user.name,
        email: user.email,
        password: "",
        role: user.role,
        active: user.status === "Active",
      };
      showUserModal.value = true;
    };

    const closeUserModal = () => {
      showUserModal.value = false;
    };

    const togglePasswordVisibility = () => {
      showPassword.value = !showPassword.value;
    };

    const saveUser = async () => {
      try {
        successMessage.value =
          "This feature is not available yet. Backend implementation required.";

        /* API calls disabled until backend is implemented
        let response;
        
        if (editingUser.value) {
          response = await axios.put(route('admin.users.update', editingUser.value.id), form.value);
        } else {
          response = await axios.post(route('admin.users.store'), form.value);
        }
        
        if (response.data.success) {
          successMessage.value = editingUser.value 
            ? 'User updated successfully!'
            : 'User created successfully!';
          
          showUserModal.value = false;
          fetchUsers();
        }
        */

        showUserModal.value = false;
      } catch (error) {
        errorMessage.value =
          error.response?.data?.message ||
          "An error occurred. Please try again.";
        console.error("Error saving user:", error);
      }
    };

    const handlePageChange = (page) => {
      currentPage.value = page;
      fetchUsers();
    };

    const handlePerPageChange = (newPerPage) => {
      perPage.value = newPerPage;
      currentPage.value = 1;
      fetchUsers();
    };

    // Function to show success message with auto-clear
    const showSuccess = (message) => {
      successMessage.value = message;
      setTimeout(() => {
        successMessage.value = "";
      }, 3000);
    };

    // Function to show error message with auto-clear
    const showError = (message) => {
      errorMessage.value = message;
      setTimeout(() => {
        errorMessage.value = "";
      }, 3000);
    };

    onMounted(() => {
      fetchUsers();
    });

    return {
      users,
      filteredUsers,
      searchQuery,
      currentSort,
      currentPage,
      lastPage,
      perPage,
      showUserModal,
      editingUser,
      form,
      showPassword,
      successMessage,
      errorMessage,
      sortBy,
      getSortIcon,
      formatDate,
      openAddUserModal,
      editUser,
      closeUserModal,
      togglePasswordVisibility,
      saveUser,
      handlePageChange,
      handlePerPageChange,
      showSuccess,
      showError,
    };
  },
};
</script>

<style scoped>
@import "../Supplier/SupplierComponents/SupplierSharedStyles.css";

.password-input-container {
  position: relative;
}

.password-toggle-btn {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: #6b7280;
}

.toggle-container {
  display: flex;
  align-items: center;
}

.toggle-checkbox {
  display: none;
}

.toggle-label {
  display: inline-block;
  width: 3rem;
  height: 1.5rem;
  background-color: #e5e7eb;
  border-radius: 999px;
  position: relative;
  cursor: pointer;
  transition: background-color 0.2s;
}

.toggle-label::after {
  content: "";
  position: absolute;
  top: 0.125rem;
  left: 0.125rem;
  width: 1.25rem;
  height: 1.25rem;
  background-color: white;
  border-radius: 50%;
  transition: transform 0.2s;
}

.toggle-checkbox:checked + .toggle-label {
  background-color: #10b981;
}

.toggle-checkbox:checked + .toggle-label::after {
  transform: translateX(1.5rem);
}

.toggle-text {
  margin-left: 0.75rem;
  font-size: 0.875rem;
  color: #4b5563;
}
</style>