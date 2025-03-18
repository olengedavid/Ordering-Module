<script setup>
import { ref, onMounted } from "vue";
import Modal from "@/Components/Modal.vue";
import { useForm, usePage } from "@inertiajs/vue3";

const props = defineProps({
  supplier: {
    type: Object,
    required: true,
  },
});

const page = usePage();
const user = page.props.auth.user;

const warehouses = ref([]);
const loading = ref(true);
const showingModal = ref(false);

const fetchWarehouses = async () => {
  loading.value = true;
  try {
    const response = await axios.get(
      route("supplier.warehouses.list", { uuid: props.supplier.uuid })
    );
    warehouses.value = response.data;
  } catch (error) {
    console.error("Error fetching warehouses:", error);
  } finally {
    loading.value = false;
  }
};

const form = useForm({
  name: "",
  contact_person: "",
  phone_number: "",
  address: "",
  email: "",
  krapin: "",
  company_id: props.supplier.id,
  created_by: user.id,
  status: "active"
});

const submit = () => {
  form.post(route("supplierwarehouse.create"), {
    preserveScroll: true,
    onSuccess: () => {
      closeModal();
      form.reset();
      fetchWarehouses();
    },
  });
};

const closeModal = () => {
  showingModal.value = false;
  form.reset();
};

onMounted(() => {
  fetchWarehouses();
});
</script>

<template>
  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-medium text-gray-900">Warehouses</h3>
        <button
          @click="showingModal = true"
          class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
        >
          Add Warehouse
        </button>
      </div>

      <div v-if="loading" class="text-center py-4">Loading warehouses...</div>

      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
            >
              Name
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
            >
              Address
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
            >
              Contact Person
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
            >
              Phone
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
            >
              Email
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
            >
              Status
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
            >
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="warehouse in warehouses" :key="warehouse.id">
            <td class="px-6 py-4 whitespace-nowrap">{{ warehouse.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ warehouse.address }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              {{ warehouse.contact_person }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              {{ warehouse.phone_number }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">{{ warehouse.email }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                :class="
                  warehouse.status === 'active'
                    ? 'text-green-600'
                    : 'text-red-600'
                "
              >
                {{ warehouse.status }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <button class="text-indigo-600 hover:text-indigo-900 mr-3">
                Edit
              </button>
              <button class="text-red-600 hover:text-red-900">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Add Warehouse Modal -->
  <Modal :show="showingModal" @close="closeModal">
    <div class="p-6">
      <h2 class="text-lg font-medium text-gray-900 mb-4">Add Warehouse</h2>

      <form @submit.prevent="submit">
        <div class="grid grid-cols-1 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input
              type="text"
              v-model="form.name"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700"
              >Contact Person</label
            >
            <input
              type="text"
              v-model="form.contact_person"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700"
              >Phone Number</label
            >
            <input
              type="text"
              v-model="form.phone_number"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700"
              >Address</label
            >
            <input
              type="text"
              v-model="form.address"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input
              type="email"
              v-model="form.email"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700"
              >KRA PIN</label
            >
            <input
              type="text"
              v-model="form.krapin"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
              required
            />
          </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
          <button
            type="button"
            class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900"
            @click="closeModal"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
            :disabled="form.processing"
          >
            Save Warehouse
          </button>
        </div>
      </form>
    </div>
  </Modal>
</template>