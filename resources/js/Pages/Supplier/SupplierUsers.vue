<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, onMounted } from "vue";
import Modal from "@/Components/Modal.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";

const showingModal = ref(false);
const page = usePage();
const user = page.props.auth.user;
const users = ref([]);
const searchQuery = ref("");
const perPage = ref(10);
const warehouses = ref([]);

const supplier_uuid = "a0405292-dc2c-44ad-88c4-302caad40239";
const form = useForm({
  username: "",
  email: "",
  password: "",
  warehouse_id: "",
  permissions: [],
  status: "active",
  created_by: user.id,
  updated_by: user.id,
  company_id: 1,
  entity_type: "supplier",
  status: "active",
});

const availablePermissions = [
  { id: "manage_users", label: "Can Manage Users" },
  { id: "manage_orders", label: "Can Manage Orders" },
  { id: "manage_warehouses", label: "Can Manage Warehouses" },
  { id: "manage_products", label: "Can Manage Products" },
  { id: "manage_inventory", label: "Can Manage Inventory" },
  { id: "cancel_orders", label: "Can Cancel Order Requests" },
  { id: "confirm_deliveries", label: "Can Confirm Deliveries" },
];

const createUser = () => {
  form.post(route("supplier.users.store"), {
    preserveScroll: true,
    onSuccess: () => {
      closeModal();
      form.reset();
      fetchSupplierUsers();
    },
  });
};

const fetchWarehouses = async () => {
  try {
    const response = await axios.get(
      route("supplier.warehouses.index", {
        uuid: supplier_uuid,
      })
    );
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
        perPage: perPage.value,
      })
    );
    users.value = response.data.data;
  } catch (error) {
    console.error("Error fetching supplier users:", error);
  }
};

onMounted(() => {
  fetchSupplierUsers();
  fetchWarehouses();
});

const closeModal = () => {
  showingModal.value = false;
  form.reset();
};
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Supplier Users" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold text-gray-800">
                Supplier Users
              </h2>
              <div class="flex items-center space-x-4">
                <div class="relative">
                  <input
                    type="text"
                    placeholder="Search users..."
                    v-model="searchQuery"
                    class="px-4 py-2 border rounded-md w-64"
                  />
                </div>
                <button
                  @click="showingModal = true"
                  class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                >
                  + Add User
                </button>
              </div>
            </div>

            <!-- Users Table -->
            <div class="overflow-x-auto">
              <table v-if="users.length" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                    >
                      Username
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                    >
                      Email Address
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                    >
                      Password
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                    >
                      Warehouse
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                    >
                      Permissions
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                    >
                      Status
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="user in users" :key="user.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ user.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ user.email }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">•••••••</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ user.warehouse?.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ JSON.parse(user.user_permissions?.[0]?.permissions || '[]').length }} permissions granted
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        :class="{
                          'px-2 py-1 text-xs rounded-full': true,
                          'bg-green-100 text-green-800':
                            user.status === 'active',
                          'bg-red-100 text-red-800': user.status === 'inactive',
                        }"
                      >
                        {{ user.status }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 flex items-center justify-between">
              <div class="flex items-center">
                <span class="mr-2">Show</span>
                <select v-model="perPage" class="border rounded px-2 py-1">
                  <option value="10">10</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                </select>
                <span class="ml-2">per page</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add User Modal -->
    <Modal :show="showingModal" @close="closeModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Add New User</h2>

        <form @submit.prevent="createUser">
          <div class="space-y-4">
            <div>
              <InputLabel for="username" value="Username" required />
              <TextInput
                id="username"
                type="text"
                class="mt-1 block w-full"
                v-model="form.username"
                required
              />
              <InputError class="mt-2" :message="form.errors.username" />
            </div>

            <div>
              <InputLabel for="email" value="Email Address" required />
              <TextInput
                id="email"
                type="email"
                class="mt-1 block w-full"
                v-model="form.email"
                required
              />
              <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
              <InputLabel for="password" value="Password" required />
              <TextInput
                id="password"
                type="password"
                class="mt-1 block w-full"
                v-model="form.password"
                required
              />
              <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
              <InputLabel for="warehouse" value="Warehouse" required />
              <select
                id="warehouse"
                v-model="form.warehouse_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                required
              >
                <option value="">Select a Warehouse</option>
                <option
                  v-for="warehouse in warehouses"
                  :key="warehouse.id"
                  :value="warehouse.id"
                >
                  {{ warehouse.name }}
                </option>
              </select>
              <InputError class="mt-2" :message="form.errors.warehouse_id" />
            </div>

            <div>
              <InputLabel value="Permissions" required />
              <div class="mt-2 space-y-2">
                <label
                  v-for="permission in availablePermissions"
                  :key="permission.id"
                  class="flex items-center"
                >
                  <input
                    type="checkbox"
                    :value="permission.id"
                    v-model="form.permissions"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                  />
                  <span class="ml-2 text-sm text-gray-600">{{
                    permission.label
                  }}</span>
                </label>
              </div>
              <InputError class="mt-2" :message="form.errors.permissions" />
            </div>

            <div>
              <InputLabel for="status" value="Status" required />
              <select
                id="status"
                v-model="form.status"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                required
              >
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
              <InputError class="mt-2" :message="form.errors.status" />
            </div>
          </div>

          <div class="mt-6 flex justify-end">
            <button
              type="button"
              class="mr-3 px-4 py-2 text-sm text-gray-700 hover:text-gray-900"
              @click="closeModal"
            >
              Cancel
            </button>
            <PrimaryButton :disabled="form.processing">
              Create User
            </PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>