<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import { ref, onMounted } from "vue";
import { useForm, router, usePage } from "@inertiajs/vue3";
import CustomPagination from "@/Components/CustomPagination.vue";


const page = usePage();
const user = page.props.auth.user;
const supplier_uuid = page.props.auth.user.company.uuid;
const products = ref([]);
const showingModal = ref(false);

const currentPage = ref(1);
const perPage = ref(3);
const lastPage = ref(1);

const form = useForm({
  name: "",
  description: "",
  sku_number: "",
  category: "",
  unit_of_measure: "",
  manufucturer: "",
  company_id: user.company_id,
  primary_image: null,
  secondary_images: [],
  status: "active",
  created_by: user.id,
});

const fetchSupplierProducts = async () => {
  // loading.value = true;
  try {
    const response = await axios.get(
      route("supplier.products.paginate", {
        uuid: supplier_uuid,
        page: currentPage.value,
        pageSize: perPage.value,
      })
    );
    products.value = response.data.data;
    currentPage.value = response.data.current_page;
    lastPage.value = response.data.last_page;
    perPage.value = response.data.per_page;

  } catch (error) {
    console.error("Error fetching products:", error);
  }
};

const getPrimaryImagePath = (product) => {
  const images =
    typeof product.images === "string"
      ? JSON.parse(product.images)
      : product.images;

  return images?.find((img) => img.type === "primary")?.path || "";
};

const submit = () => {
  form.post(route("products.store"), {
    preserveScroll: true,
    onSuccess: () => {
      closeModal();
      form.reset();
      fetchSupplierProducts();
    },
  });
};

const closeModal = () => {
  showingModal.value = false;
  form.reset();
};

const handlePageChange = (page) => {
  currentPage.value = page;
  fetchSupplierProducts(page);
};

const handlePerPageChange = (newPerPage) => {
  perPage.value = newPerPage;
  fetchSupplierProducts();
};

onMounted(() => {
  fetchSupplierProducts();
});
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Products" />

    <div class="page-container">
      <div class="content-container">
        <div class="header-container">
          <h1 class="page-title">Products</h1>
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
              placeholder="Search inventory..."
              v-model="searchQuery"
            />
          </div>
          <button @click="showingModal = true" class="add-btn">
            <span class="plus-icon">+</span>
            Add Product
          </button>
        </div>

        <!-- Table -->
        <div class="table-container">
          <div class="table-wrapper">
            <table class="data-table">
              <thead class="bg-gray-50">
                <tr>
                  <th class="table-header table-header-sortable">Name</th>
                  <th class="table-header table-header-sortable">SKU</th>

                  <th class="table-header table-header-sortable">Category</th>
                  <th class="table-header table-header-sortable">
                    Unit of Measure
                  </th>
                  <th class="table-header table-header-sortable">
                    Description
                  </th>
                  <th class="table-header table-header-sortable">
                    Manufucturer
                  </th>
                  <th class="table-header table-header-sortable">Status</th>
                  <th class="table-header actions-column">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="product in products" :key="product.id">
                  <td class="table-cell">
                    {{ product.name }}
                  </td>
                  <td class="table-cell">
                    {{ product.sku_number }}
                  </td>
                  <td class="table-cell">
                    {{ product.category }}
                  </td>
                  <td class="table-cell">
                    {{ product.unit_of_measure }}
                  </td>
                  <td class="table-cell">
                    {{ product.description }}
                  </td>
                  <td class="table-cell">
                    {{ product.manufucturer }}
                  </td>
                  <td class="table-cell whitespace-nowrap">
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
                  <td class="table-cell">
                    <button class="text-indigo-600 hover:text-indigo-900 mr-3">
                      Edit
                    </button>
                    <button class="text-red-600 hover:text-red-900">
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <CustomPagination
            :current-page="currentPage"
            :last-page="lastPage"
            :per-page="perPage"
            @page-changed="handlePageChange"
            @update:per-page="handlePerPageChange"
          />
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
                  <label class="block text-sm font-medium text-gray-700"
                    >SKU</label
                  >
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
                    <label class="block text-sm font-medium text-gray-700"
                      >Primary Image</label
                    >
                    <input
                      type="file"
                      @input="form.primary_image = $event.target.files[0]"
                      class="mt-1 block w-full"
                      accept="image/png,image/jpeg,image/jpg"
                      required
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700"
                      >Secondary Images</label
                    >
                    <input
                      type="file"
                      @input="form.secondary_images = [...$event.target.files]"
                      class="mt-1 block w-full"
                      accept="image/png,image/jpeg,image/jpg"
                      multiple
                    />
                    <p class="mt-1 text-sm text-gray-500">
                      You can select multiple images
                    </p>
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

        <div v-if="products.length === 0" class="text-center py-12">
          <p class="text-gray-500">No products found</p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>