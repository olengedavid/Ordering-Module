<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import { ref, onMounted } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import CustomPagination from "@/Components/CustomPagination.vue";
import SuccessMessage from '@/Components/SuccessMessage.vue';
import ErrorMessage from '@/Components/ErrorMessage.vue';

const showProductModal = ref(false);
const editingProduct = ref(false);
const isDragging = ref(false);
const productImages = ref([]);
const imagePreview = ref(null);

const page = usePage();
const user = page.props.auth.user;
const supplier_uuid = page.props.auth.user.company.uuid;
const products = ref([]);
const showingModal = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const searchQuery = ref('');

const currentPage = ref(1);
const perPage = ref(10);
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
      successMessage.value = 'Product added successfully';
      setTimeout(() => {
        successMessage.value = '';
      }, 3000);
    },
    onError: () => {
      errorMessage.value = 'Error adding product';
      setTimeout(() => {
        errorMessage.value = '';
      }, 3000);
    }
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

// Image handling methods
const onDragOver = (e) => {
  isDragging.value = true;
};

const onDragLeave = (e) => {
  isDragging.value = false;
};

const onDrop = (e) => {
  isDragging.value = false;
  const files = e.dataTransfer.files;
  handleFiles(files);
};

const onFileSelected = (e) => {
  const files = e.target.files;
  handleFiles(files);
};

const handleFiles = (files) => {
  Array.from(files).forEach((file) => {
    const reader = new FileReader();
    reader.onload = (e) => {
      productImages.value.push({
        id: Date.now(),
        url: e.target.result,
        file: file,
        isPrimary: productImages.value.length === 0,
      });
      if (productImages.value.length === 1) {
        imagePreview.value = e.target.result;
      }
    };
    reader.readAsDataURL(file);
  });
};

const setPrimaryImage = (imageId) => {
  productImages.value.forEach((image) => {
    image.isPrimary = image.id === imageId;
    if (image.isPrimary) {
      imagePreview.value = image.url;
    }
  });
};

const removeImage = (imageId) => {
  const index = productImages.value.findIndex((img) => img.id === imageId);
  if (index > -1) {
    const wasRemovingPrimary = productImages.value[index].isPrimary;
    productImages.value.splice(index, 1);
    if (wasRemovingPrimary && productImages.value.length > 0) {
      productImages.value[0].isPrimary = true;
      imagePreview.value = productImages.value[0].url;
    }
  }
};

const closeProductModal = () => {
  showProductModal.value = false;
  editingProduct.value = false;
  productImages.value = []; // Clear images array
  imagePreview.value = null;
  form.reset();
  form.clearErrors();
};

const editProduct = (product) => {
  editingProduct.value = product;
  form.name = product.name;
  form.sku_number = product.sku_number;
  form.category = product.category;
  form.unit_of_measure = product.unit_of_measure;
  form.description = product.description;
  form.manufucturer = product.manufucturer;
  form.status = product.status;
  
  // Handle images
  if (product.images) {
    const images = typeof product.images === 'string' ? JSON.parse(product.images) : product.images;
    images.forEach(async (image) => {
      const response = await fetch(`/storage/${image.path}`);
      const blob = await response.blob();
      const file = new File([blob], image.path.split('/').pop(), { type: blob.type });
      
      productImages.value.push({
        id: Date.now(),
        url: URL.createObjectURL(blob),
        file: file,
        isPrimary: image.type === 'primary'
      });
      
      if (image.type === 'primary') {
        imagePreview.value = URL.createObjectURL(blob);
      }
    });
  }
  
  showProductModal.value = true;
};

const saveProduct = () => {
  const formData = new FormData();
  formData.append("name", form.name);
  formData.append("sku_number", form.sku_number);
  formData.append("category", form.category);
  formData.append("unit_of_measure", form.unit_of_measure);
  formData.append("description", form.description);
  formData.append("manufucturer", form.manufucturer);
  formData.append("status", form.status);
  formData.append("company_id", form.company_id);
  formData.append("created_by", form.created_by);

  // Add images
  const primaryImage = productImages.value.find((img) => img.isPrimary);
  if (primaryImage) {
    formData.append("primary_image", primaryImage.file);
  }

  // Add secondary images
  productImages.value.forEach((image) => {
    if (!image.isPrimary) {
      formData.append("secondary_images[]", image.file);
    }
  });
  // Log the FormData contents
  for (let pair of formData.entries()) {
    console.log(pair[0], pair[1]);
  }

  if (editingProduct.value) {
    axios
      .post(route("products.update", editingProduct.value.uuid), formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      })
      .then(() => {
        closeProductModal();
        fetchSupplierProducts();
      });
  }else {
    axios
      .post(route("products.store"), formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      })
      .then(() => {
        closeProductModal();
        fetchSupplierProducts();
      });
  }
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
        <div class="message-container">
          <SuccessMessage v-if="successMessage" @close="successMessage = ''" v-slot>{{ successMessage }}</SuccessMessage>
          <ErrorMessage v-if="errorMessage" @close="errorMessage = ''" v-slot>{{ errorMessage }}</ErrorMessage>
        </div>
        
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
          <button @click="showProductModal = true" class="add-btn">
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
                    <button @click="editProduct(product)" class="action-btn edit-btn">
                      Edit
                    </button>
                    <button class="action-btn delete-btn">
                      Delete
                    </button>
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

        <!-- Add Product Modal -->
        <div
          v-if="showProductModal"
          class="modal-overlay"
          @click.self="closeProductModal"
        >
          <div class="modal-container">
            <div class="modal-header">
              <h2>{{ editingProduct ? "Edit Product" : "Add New Product" }}</h2>
              <button class="close-btn" @click="closeProductModal">
                &times;
              </button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="saveProduct">
                <!-- Product Images Upload -->
                <div class="form-group">
                  <label for="productImage">Product Images</label>
                  <div
                    class="image-upload-container"
                    @dragover.prevent="onDragOver"
                    @dragleave.prevent="onDragLeave"
                    @drop.prevent="onDrop"
                    :class="{ 'drag-over': isDragging }"
                  >
                    <div class="upload-placeholder">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      >
                        <rect
                          x="3"
                          y="3"
                          width="18"
                          height="18"
                          rx="2"
                          ry="2"
                        />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <polyline points="21 15 16 10 5 21" />
                      </svg>
                      <p>
                        Drag & drop images here or
                        <span class="browse-text">browse</span>
                      </p>
                      <input
                        type="file"
                        id="productImage"
                        class="file-input"
                        accept="image/*"
                        multiple
                        @change="onFileSelected"
                      />
                    </div>
                  </div>

                  <!-- Images Gallery (Outside the upload container) -->
                  <div
                    v-if="productImages.length > 0"
                    class="images-gallery-outside"
                  >
                    <div class="gallery-preview-outside">
                      <img
                        v-if="imagePreview"
                        :src="imagePreview"
                        alt="Primary product image"
                        class="primary-preview-img"
                      />
                    </div>
                    <div class="gallery-thumbnails-outside">
                      <div
                        v-for="image in productImages"
                        :key="image.id"
                        class="thumbnail-container"
                        :class="{ 'primary-thumbnail': image.isPrimary }"
                      >
                        <img
                          :src="image.url"
                          alt="Product image"
                          class="thumbnail-img"
                        />
                        <div class="thumbnail-actions">
                          <button
                            type="button"
                            class="set-primary-btn"
                            @click="setPrimaryImage(image.id)"
                            :disabled="image.isPrimary"
                            :title="
                              image.isPrimary
                                ? 'Primary Image'
                                : 'Set as Primary'
                            "
                          >
                            <svg
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
                              <path
                                d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
                              />
                            </svg>
                          </button>
                          <button
                            type="button"
                            class="remove-thumbnail-btn"
                            @click="removeImage(image.id)"
                          >
                            &times;
                          </button>
                        </div>
                      </div>
                      <div class="">
                        <label for="additionalImages" class="add-more-btn">
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                          >
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                          </svg>
                        </label>
                        <input
                          type="file"
                          id="additionalImages"
                          class="file-input"
                          accept="image/*"
                          multiple
                          @change="onFileSelected"
                        />
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="productName"
                    >Product Name <span class="required">*</span></label
                  >
                  <input
                    type="text"
                    id="productName"
                    v-model="form.name"
                    required
                    placeholder="Enter product name"
                  />
                </div>

                <div class="form-group">
                  <label for="skuNumber"
                    >SKU Number <span class="required">*</span></label
                  >
                  <input
                    type="text"
                    id="skuNumber"
                    v-model="form.sku_number"
                    required
                    placeholder="Enter SKU number"
                  />
                </div>

                <div class="form-group">
                  <label for="category"
                    >Category <span class="required">*</span></label
                  >
                  <input
                    type="text"
                    id="category"
                    v-model="form.category"
                    required
                    placeholder="Enter product category"
                  />
                </div>

                <div class="form-group">
                  <label for="unitOfMeasure"
                    >Unit of Measure <span class="required">*</span></label
                  >
                  <input
                    type="text"
                    id="unitOfMeasure"
                    v-model="form.unit_of_measure"
                    required
                    placeholder="Enter unit of measure (e.g. kg, liter)"
                  />
                </div>

                <div class="form-group">
                  <label for="description"
                    >Description <span class="required">*</span></label
                  >
                  <textarea
                    id="description"
                    v-model="form.description"
                    required
                    placeholder="Enter product description"
                    rows="3"
                  ></textarea>
                </div>

                <div class="form-group">
                  <label for="manufacturer"
                    >Manufacturer <span class="required">*</span></label
                  >
                  <input
                    type="text"
                    id="manufacturer"
                    v-model="form.manufucturer"
                    required
                    placeholder="Enter manufacturer name"
                  />
                </div>

                <div class="form-group">
                  <label for="productStatus"
                    >Status <span class="required">*</span></label
                  >
                  <select id="productStatus" v-model="form.status" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                  </select>
                </div>

                <div class="form-actions">
                  <button
                    type="button"
                    class="cancel-btn"
                    @click="closeProductModal"
                  >
                    Cancel
                  </button>
                  <button type="submit" class="submit-btn">Save Product</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div v-if="products.length === 0" class="text-center py-12">
          <p class="text-gray-500">No products found</p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
@import '../Supplier/SupplierComponents/SupplierSharedStyles.css';
</style>
