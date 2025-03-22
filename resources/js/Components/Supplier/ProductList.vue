<script setup>
import { ref, onMounted } from "vue";
import Modal from "@/Components/Modal.vue";
import { useForm, router, usePage } from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
  supplier: {
    type: Object,
    required: true,
  },
});

const page = usePage();
const user = page.props.auth.user;

const currentPage = ref(1);
const perPage = ref(10);
const lastPage = ref(1);

const products = ref([]);
const loading = ref(true);
const showingModal = ref(false);

const fetchProducts = async () => {
  loading.value = true;
  try {
    const response = await axios.get(
      route("supplier.products.list", {
        uuid: props.supplier.uuid,
        page: currentPage.value,
        perPage: perPage.value,
      })
    );
    products.value = response.data;
  } catch (error) {
    console.error("Error fetching products:", error);
  } finally {
    loading.value = false;
  }
};

// Update the form data
const form = useForm({
  name: "",
  description: "",
  sku_number: "",
  category: "",
  unit_of_measure: "",
  manufucturer: "",
  company_id: props.supplier.id,
  primary_image: null,
  secondary_images: [],
  status: "active",
  created_by: user.id,
});

const submit = () => {
  form.post(route("products.store"), {
    preserveScroll: true,
    onSuccess: () => {
      closeModal();
      form.reset();
      fetchProducts();
    },
  });
};

const closeModal = () => {
  showingModal.value = false;
  form.reset();
};

onMounted(() => {
  fetchProducts();
});
</script>

<template>
  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-medium text-gray-900">Products</h3>
        <button
          @click="showingModal = true"
          class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
        >
          Add Product
        </button>
      </div>

      <div v-if="loading" class="text-center py-4">Loading products...</div>

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
              SKU
            </th>

            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
            >
              Category
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
            >
              Unit of Measure
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
            >
              Description
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
            >
              Manufucturer
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
          <tr v-for="product in products" :key="product.id">
            <td class="px-6 py-4 whitespace-nowrap">{{ product.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              {{ product.sku_number }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">{{ product.category }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              {{ product.unit_of_measure }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              {{ product.description }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              {{ product.manufucturer }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                :class="
                  product.status === 'active'
                    ? 'text-green-600'
                    : 'text-red-600'
                "
              >
                {{ product.status }}
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

  <!-- Add Product Modal -->
  <Modal :show="showingModal" @close="closeModal">
    <div class="p-6">
      <h2 class="text-lg font-medium text-gray-900 mb-4">Add Product</h2>

      <form @submit.prevent="submit">
        <div class="grid grid-cols-1 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700"
              >Product Name</label
            >
            <input
              type="text"
              v-model="form.name"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700"
              >Description</label
            >
            <textarea
              v-model="form.description"
              rows="3"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
              required
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">SKU</label>
            <input
              type="text"
              v-model="form.sku_number"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700"
              >Category</label
            >
            <select
              v-model="form.category"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
              required
            >
              <option value="">Select category</option>
              <option value="Electronics">Electronics</option>
              <option value="Clothing">Clothing</option>
              <option value="Food">Food</option>
              <option value="Furniture">Furniture</option>
              <option value="Books">Books</option>
              <option value="Other">Other</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700"
              >Unit of Measure</label
            >
            <select
              v-model="form.unit_of_measure"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
              required
            >
              <option value="">Select unit</option>
              <option value="KG">Kilogram (KG)</option>
              <option value="G">Gram (G)</option>
              <option value="L">Liter (L)</option>
              <option value="ML">Milliliter (ML)</option>
              <option value="PCS">Pieces (PCS)</option>
              <option value="BOX">Box</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700"
              >Manufacturer</label
            >
            <input
              type="text"
              v-model="form.manufucturer"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
              required
            />
          </div>

          <div class="grid grid-cols-1 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Primary Image</label>
              <input
                type="file"
                @input="form.primary_image = $event.target.files[0]"
                class="mt-1 block w-full"
                accept="image/png,image/jpeg,image/jpg"
                required
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Secondary Images</label>
              <input
                type="file"
                @input="form.secondary_images = [...$event.target.files]"
                class="mt-1 block w-full"
                accept="image/png,image/jpeg,image/jpg"
                multiple
              />
              <p class="mt-1 text-sm text-gray-500">You can select multiple images</p>
            </div>
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
            Save Product
          </button>
        </div>
      </form>
    </div>
  </Modal>
</template>