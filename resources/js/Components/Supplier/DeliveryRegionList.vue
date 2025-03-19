<script setup>
import { ref, onMounted } from "vue";
import Modal from "@/Components/Modal.vue";
import { useForm, router, usePage} from "@inertiajs/vue3";
import axios from "axios";


const props = defineProps({
  warehouseId: {
    type: Number,
  },
  supplier: {
    type: Object,
    required: true,
  },
});
const page = usePage();
const user = page.props.auth.user;

const deliveryRegions = ref([]);
const warehouses = ref([]);
const loading = ref(true);
const showingModal = ref(false);

const fetchDeliveryRegions = async () => {
  loading.value = true;
  try {
    const response = await axios.get(
      route("supplier.delivery-regions.index", {
        company_id: props.supplier.id,
      })
    );
    deliveryRegions.value = response.data;
  } catch (error) {
    console.error("Error fetching delivery regions:", error);
  } finally {
    loading.value = false;
  }
};

const fetchWarehouses = async () => {
  try {
    const response = await axios.get(
    route("supplier.warehouses.list", { uuid: props.supplier.uuid })
    );
    warehouses.value = response.data;
  } catch (error) {
    console.error("Error fetching warehouses:", error);
  }
};

const form = useForm({
  warehouse_id: "",
  region: "",
  delivery_fee: "",
  status: "active",
  company_id: props.supplier.id,
  created_by: user.id
});

const submit = () => {
  form.post(route("supplier.delivery-regions.store"), {
    preserveScroll: true,
    onSuccess: () => {
      closeModal();
      form.reset();
      fetchDeliveryRegions();
    },
  });
};

const closeModal = () => {
  showingModal.value = false;
  form.reset();
};

onMounted(() => {
  fetchDeliveryRegions();
  fetchWarehouses();
});
</script>

<template>
  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-medium text-gray-900">Delivery Regions</h3>
        <button
          @click="showingModal = true"
          class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
        >
          Add Delivery Region
        </button>
      </div>

      <div v-if="loading" class="text-center py-4">
        Loading delivery regions...
      </div>

      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
            >
              Region
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
            >
              Delivery Fee
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
          <tr v-for="region in deliveryRegions" :key="region.id">
            <td class="px-6 py-4 whitespace-nowrap">{{ region.region }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              KES {{ region.delivery_fee }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                :class="
                  region.status === 'active' ? 'text-green-600' : 'text-red-600'
                "
              >
                {{ region.status }}
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

  <!-- Add Delivery Region Modal -->
  <Modal :show="showingModal" @close="closeModal">
    <div class="p-6">
      <h2 class="text-lg font-medium text-gray-900 mb-4">
        Add Delivery Region
      </h2>

      <form @submit.prevent="submit">
        <div class="grid grid-cols-1 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700"
              >Warehouse</label
            >
            <select
              v-model="form.warehouse_id"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
              required
            >
              <option value="">Select a warehouse</option>
              <option
                v-for="warehouse in warehouses"
                :key="warehouse.id"
                :value="warehouse.id"
              >
                {{ warehouse.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700"
              >Region</label
            >
            <input
              type="text"
              v-model="form.region"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700"
              >Delivery Fee (KES)</label
            >
            <input
              type="number"
              step="0.01"
              v-model="form.delivery_fee"
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
            Save Region
          </button>
        </div>
      </form>
    </div>
  </Modal>
</template>