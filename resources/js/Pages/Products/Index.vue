<script setup>
import SupplierNavbar from "@/Components/SupplierNavbar.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted, onUnmounted, computed } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import CustomPagination from "@/Components/CustomPagination.vue";
import SuccessMessage from "@/Components/SuccessMessage.vue";
import ErrorMessage from "@/Components/ErrorMessage.vue";

const showProductModal = ref(false);
const editingProduct = ref(false);
const isDragging = ref(false);
const productImages = ref([]);
const imagePreview = ref(null);

const page = usePage();
const user = page.props.auth.user;
const supplier_uuid = page.props.auth.user.company.uuid;
const products = ref([]);
const successMessage = ref("");
const errorMessage = ref("");
const searchQuery = ref("");
const sortKey = ref("name");
const sortDir = ref("asc");

const currentPage = ref(1);
const perPage = ref(10);
const lastPage = ref(1);

const units = ref([]);

// Add default units array
const defaultUnits = [
  "kg",
  "g",
  "liter",
  "ml",
  "unit",
  "pack",
  "box",
  "carton",
  "dozen",
  "case"
];

// Add product categories array
const productCategories = [
  "Beverages",
  "Dairy",
  "Meat & Poultry",
  "Seafood",
  "Fruits & Vegetables",
  "Bakery",
  "Grains & Pasta",
  "Canned Goods",
  "Snacks",
  "Condiments & Sauces",
  "Baking Supplies",
  "Oils & Vinegars",
];

const form = useForm({
  name: "",
  description: "",
  sku_number: "",
  category: "",
  unit_of_measure: "NO",
  manufucturer: "",
  company_id: user.company_id,
  primary_image: null,
  secondary_images: [],
  status: "active",
  created_by: user.id,
});

// Add dropdown states
const isCategoryOpen = ref(false);
const isUnitOpen = ref(false);
const isStatusOpen = ref(false);
const categorySearchQuery = ref('');
const unitSearchQuery = ref('');
const statusSearchQuery = ref('');
const filteredCategories = ref([...productCategories]);
const filteredUnits = ref([...defaultUnits]); // Initialize with default units
const filteredStatuses = ref(['Active', 'Inactive']);

// Add dropdown methods
const toggleCategoryDropdown = () => {
  isCategoryOpen.value = !isCategoryOpen.value;
  if (isCategoryOpen.value) {
    categorySearchQuery.value = '';
    filteredCategories.value = [...productCategories];
  }
  // Close other dropdowns
  isUnitOpen.value = false;
  isStatusOpen.value = false;
};

const toggleUnitDropdown = () => {
  isUnitOpen.value = !isUnitOpen.value;
  if (isUnitOpen.value) {
    unitSearchQuery.value = '';
    filteredUnits.value = [...units.value];
  }
  // Close other dropdowns
  isCategoryOpen.value = false;
  isStatusOpen.value = false;
};

const toggleStatusDropdown = () => {
  isStatusOpen.value = !isStatusOpen.value;
  if (isStatusOpen.value) {
    statusSearchQuery.value = '';
    filteredStatuses.value = ['Active', 'Inactive'];
  }
  // Close other dropdowns
  isCategoryOpen.value = false;
  isUnitOpen.value = false;
};

const filterCategories = () => {
  if (!categorySearchQuery.value.trim()) {
    filteredCategories.value = [...productCategories];
  } else {
    const query = categorySearchQuery.value.toLowerCase();
    filteredCategories.value = productCategories.filter(
      category => category.toLowerCase().includes(query)
    );
  }
};

const filterUnits = () => {
  if (!unitSearchQuery.value.trim()) {
    filteredUnits.value = [...units.value];
  } else {
    const query = unitSearchQuery.value.toLowerCase();
    filteredUnits.value = units.value.filter(
      unit => unit.label.toLowerCase().includes(query)
    );
  }
};

const filterStatuses = () => {
  if (!statusSearchQuery.value.trim()) {
    filteredStatuses.value = ['Active', 'Inactive'];
  } else {
    const query = statusSearchQuery.value.toLowerCase();
    filteredStatuses.value = ['Active', 'Inactive'].filter(
      status => status.toLowerCase().includes(query)
    );
  }
};

const selectCategory = (category) => {
  form.category = category;
  isCategoryOpen.value = false;
  categorySearchQuery.value = '';
};

const selectUnit = (unit) => {
  form.unit_of_measure = unit.value;
  isUnitOpen.value = false;
  unitSearchQuery.value = '';
};

const selectStatus = (status) => {
  form.status = status.toLowerCase();
  isStatusOpen.value = false;
  statusSearchQuery.value = '';
};

// Close dropdowns when clicking outside
const closeDropdownsOutside = (event) => {
  const dropdowns = document.querySelectorAll('.custom-select-container');
  let clickedInside = false;
  
  dropdowns.forEach(dropdown => {
    if (dropdown.contains(event.target)) {
      clickedInside = true;
    }
  });

  if (!clickedInside) {
    isCategoryOpen.value = false;
    isUnitOpen.value = false;
    isStatusOpen.value = false;
  }
};

const fetchSupplierProducts = async () => {
  // loading.value = true;
  try {
    const response = await axios.get(
      route("supplier.products.paginate", {
        uuid: supplier_uuid,
        page: currentPage.value,
        pageSize: perPage.value,
        search: searchQuery.value,
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
    const images =
      typeof product.images === "string"
        ? JSON.parse(product.images)
        : product.images;
    images.forEach(async (image) => {
      const response = await fetch(`/storage/${image.path}`);
      const blob = await response.blob();
      const file = new File([blob], image.path.split("/").pop(), {
        type: blob.type,
      });

      productImages.value.push({
        id: Date.now(),
        url: URL.createObjectURL(blob),
        file: file,
        isPrimary: image.type === "primary",
      });

      if (image.type === "primary") {
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

        // Flash
        successMessage.value = "Product updated successfully";
        setTimeout(() => {
          successMessage.value = "";
        }, 3000);
      });
  } else {
    axios
      .post(route("products.store"), formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      })
      .then(() => {
        closeProductModal();
        fetchSupplierProducts();

        // Flash
        successMessage.value = "Product created successfully";
        setTimeout(() => {
          successMessage.value = "";
        }, 3000);
      });
  }
};

const getPrimaryImagePreviewPath = (product) => {
  if (!product.images) return "https://via.placeholder.com/50";

  try {
    let images;
    const parsedImages =
      typeof product.images === "string"
        ? JSON.parse(product.images)
        : product.images;

    // Handle both array and object formats
    images = Array.isArray(parsedImages)
      ? parsedImages
      : Object.values(parsedImages);

    const primaryImage = images.find((img) => img.type === "primary");

    if (primaryImage?.path) {
      return `/storage/${primaryImage.path}`;
    }
  } catch (error) {
    console.error("Error processing product images:", error);
  }

  return "https://via.placeholder.com/50";
};

const deleteProduct = async (productUuid) => {
  if (!confirm("Are you sure you want to delete this product?")) return;

  try {
    await axios.delete(route("products.destroy", productUuid));

    successMessage.value = "Product deleted successfully";
    setTimeout(() => {
      successMessage.value = "";
    }, 3000);

    fetchSupplierProducts();
  } catch (error) {
    errorMessage.value =
      error.response?.data?.message || "Error deleting product";
    setTimeout(() => {
      errorMessage.value = "";
    }, 3000);
  }
};

const fetchUnits = async () => {
  try {
    const response = await axios.get(route("static.units"));
    units.value = response.data;
    // Update filtered units after fetching
    filteredUnits.value = response.data;
  } catch (error) {
    console.error("Error fetching units:", error);
    // Fallback to default units if fetch fails
    units.value = defaultUnits;
    filteredUnits.value = defaultUnits;
  }
};

// Computed properties for filtering and sorting
const filteredProducts = computed(() => {
  if (searchQuery.value.trim() === "") {
    return products.value;
  }

  const query = searchQuery.value.toLowerCase();
  return products.value.filter((product) => {
    return Object.values(product).some((value) => {
      if (value === null || value === undefined) return false;
      return String(value).toLowerCase().includes(query);
    });
  });
});

const sortedProducts = computed(() => {
  const products = [...filteredProducts.value];
  products.sort((a, b) => {
    let modifier = sortDir.value === "asc" ? 1 : -1;

    // Map frontend keys to backend keys
    const keyMap = {
      sku: "sku_number",
      unitOfMeasure: "unit_of_measure",
      manufacturer: "manufucturer"
    };

    let aKey = keyMap[sortKey.value] || sortKey.value;
    let bKey = keyMap[sortKey.value] || sortKey.value;

    let aValue = a[aKey];
    let bValue = b[bKey];

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

  return products;
});

// Event handlers
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

onMounted(() => {
  fetchSupplierProducts();
  fetchUnits();
  document.addEventListener('click', closeDropdownsOutside);
  // Initialize filtered arrays
  filteredCategories.value = [...productCategories];
  filteredStatuses.value = ['Active', 'Inactive'];
});

onUnmounted(() => {
  document.removeEventListener('click', closeDropdownsOutside);
});
</script>

<template>
  <div>
    <SupplierNavbar />
    <Head title="Products" />

    <div class="page-container">
      <div class="content-container">
        <div class="message-container">
          <SuccessMessage
            v-if="successMessage"
            @close="successMessage = ''"
            v-slot="{}"
            >{{ successMessage }}</SuccessMessage
          >
          <ErrorMessage
            v-if="errorMessage"
            @close="errorMessage = ''"
            v-slot="{}"
            >{{ errorMessage }}</ErrorMessage
          >
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
              placeholder="Search by name or sku..."
              v-model="searchQuery"
              @keypress.enter="fetchSupplierProducts"
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
                  <th class="table-header">Product</th>
                  <th @click="sortBy('name')" class="table-header table-header-sortable">
                    Name <i :class="getSortIcon('name')"></i>
                  </th>
                  <th @click="sortBy('sku')" class="table-header table-header-sortable">
                    SKU <i :class="getSortIcon('sku')"></i>
                  </th>
                  <th @click="sortBy('category')" class="table-header table-header-sortable">
                    Category <i :class="getSortIcon('category')"></i>
                  </th>
                  <th @click="sortBy('unitOfMeasure')" class="table-header table-header-sortable">
                    Unit of Measure <i :class="getSortIcon('unitOfMeasure')"></i>
                  </th>
                  <th @click="sortBy('description')" class="table-header table-header-sortable">
                    Description <i :class="getSortIcon('description')"></i>
                  </th>
                  <th @click="sortBy('manufacturer')" class="table-header table-header-sortable">
                    Manufacturer <i :class="getSortIcon('manufacturer')"></i>
                  </th>
                  <th @click="sortBy('status')" class="table-header table-header-sortable">
                    Status <i :class="getSortIcon('status')"></i>
                  </th>
                  <th class="table-header actions-column">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="product in sortedProducts" :key="product.id">
                  <td class="table-cell">
                    <div class="product-image-container">
                      <img
                        :src="getPrimaryImagePreviewPath(product)"
                        class="product-thumbnail"
                        @error="
                          $event.target.src = 'https://via.placeholder.com/50'
                        "
                      />
                    </div>
                  </td>

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
                    <button
                      @click="editProduct(product)"
                      class="action-btn edit-btn"
                    >
                      Edit
                    </button>
                    <button
                      @click="deleteProduct(product.uuid)"
                      class="action-btn delete-btn"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
                <tr v-if="products.length === 0">
                  <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                    No products found
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
              <button class="close-btn" @click="closeProductModal">&times;</button>
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
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
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
                        @click="setPrimaryImage(image.id)"
                      >
                        <img
                          :src="image.url"
                          alt="Product image"
                          class="thumbnail-img"
                        />
                      </div>
                      <div class="add-more-container">
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
                  <label for="category">Category <span class="required">*</span></label>
                  <div class="custom-select-container">
                    <div 
                      class="custom-select-trigger" 
                      @click="toggleCategoryDropdown"
                      :class="{ 'active': isCategoryOpen }"
                    >
                      <span :data-has-value="!!form.category">{{ form.category || 'Select category' }}</span>
                      <svg 
                        class="dropdown-arrow" 
                        :class="{ 'open': isCategoryOpen }"
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
                    
                    <div class="custom-select-dropdown" v-show="isCategoryOpen">
                      <div class="search-box">
                        <input
                          type="text"
                          v-model="categorySearchQuery"
                          @input="filterCategories"
                          placeholder="Search categories..."
                          class="dropdown-search"
                          @click.stop
                        >
                      </div>
                      
                      <div class="dropdown-options">
                        <div
                          v-for="category in filteredCategories"
                          :key="category"
                          class="dropdown-option"
                          @click="selectCategory(category)"
                        >
                          {{ category }}
                        </div>
                        <div v-if="filteredCategories.length === 0" class="no-results">
                          No categories match your search
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="unitOfMeasure">Unit of Measure <span class="required">*</span></label>
                  <div class="custom-select-container">
                    <div 
                      class="custom-select-trigger" 
                      @click="toggleUnitDropdown"
                      :class="{ 'active': isUnitOpen }"
                    >
                      <span :data-has-value="!!form.unit_of_measure">{{ form.unit_of_measure || 'Select unit' }}</span>
                      <svg 
                        class="dropdown-arrow" 
                        :class="{ 'open': isUnitOpen }"
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
                    
                    <div class="custom-select-dropdown" v-show="isUnitOpen">
                      <div class="search-box">
                        <input
                          type="text"
                          v-model="unitSearchQuery"
                          @input="filterUnits"
                          placeholder="Search units..."
                          class="dropdown-search"
                          @click.stop
                        >
                      </div>
                      
                      <div class="dropdown-options">
                        <div
                          v-for="unit in filteredUnits"
                          :key="unit.value"
                          class="dropdown-option"
                          @click="selectUnit(unit)"
                        >
                          {{ unit.label }}
                        </div>
                        <div v-if="filteredUnits.length === 0" class="no-results">
                          No units match your search
                        </div>
                      </div>
                    </div>
                  </div>
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
                  <label for="status">Status</label>
                  <div class="custom-select-container">
                    <div 
                      class="custom-select-trigger" 
                      @click="toggleStatusDropdown"
                      :class="{ 'active': isStatusOpen }"
                    >
                      <span :data-has-value="!!form.status">{{ form.status || 'Select status' }}</span>
                      <svg 
                        class="dropdown-arrow" 
                        :class="{ 'open': isStatusOpen }"
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
                    
                    <div class="custom-select-dropdown" v-show="isStatusOpen">
                      <div class="search-box">
                        <input
                          type="text"
                          v-model="statusSearchQuery"
                          @input="filterStatuses"
                          placeholder="Search statuses..."
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
                          {{ status }}
                        </div>
                        <div v-if="filteredStatuses.length === 0" class="no-results">
                          No statuses match your search
                        </div>
                      </div>
                    </div>
                  </div>
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
      </div>
    </div>
  </div>
</template>

<style scoped>
@import "../Supplier/SupplierComponents/SupplierSharedStyles.css";

.product-image-container {
  display: flex;
  align-items: center;
  gap: 12px;
}

.product-thumbnail {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 4px;
}

.product-name {
  font-weight: 500;
}

.image-upload-container {
  border: 2px dashed #ccc;
  border-radius: 8px;
  padding: 20px;
  text-align: center;
  cursor: pointer;
  margin-bottom: 15px;
  transition: all 0.3s ease;
  position: relative;
  min-height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.upload-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  width: 100%;
}

.upload-placeholder svg {
  margin-bottom: 10px;
}

.upload-placeholder p {
  margin: 0;
}

.add-more-container {
  width: 70px;
  height: 70px;
  border: 2px dashed #cbd5e1;
  border-radius: 6px;
  position: relative;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.add-more-btn {
  width: 24px;
  height: 24px;
  color: #64748b;
  cursor: pointer;
}

.add-more-btn svg {
  width: 100%;
  height: 100%;
}

.file-input {
  opacity: 0;
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0;
  left: 0;
  cursor: pointer;
}

.thumbnail-container {
  position: relative;
  width: 70px;
  height: 70px;
  border-radius: 6px;
  overflow: hidden;
  border: 2px solid #e5e7eb;
  transition: all 0.2s ease;
  cursor: pointer;
}

.thumbnail-container:hover {
  border-color: #0E64A5;
  transform: scale(1.05);
}

.primary-thumbnail {
  border-color: #0E64A5;
  transform: scale(1.05);
}

.thumbnail-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.custom-select-container {
  position: relative;
  width: 100%;
}

.custom-select-trigger {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 12px;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  cursor: pointer;
  background-color: white;
  transition: all 0.2s ease;
}

.custom-select-trigger:hover {
  border-color: #0E64A5;
}

.custom-select-trigger.active {
  border-color: #0E64A5;
  box-shadow: 0 0 0 2px rgba(14, 100, 165, 0.1);
}

.custom-select-trigger span {
  color: #374151;
}

.custom-select-trigger span[data-has-value="false"] {
  color: #9ca3af;
}

.dropdown-arrow {
  transition: transform 0.2s ease;
}

.dropdown-arrow.open {
  transform: rotate(180deg);
}

.custom-select-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background-color: white;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  margin-top: 4px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  z-index: 50;
  max-height: 300px;
  overflow-y: auto;
}

.search-box {
  padding: 8px;
  border-bottom: 1px solid #e5e7eb;
}

.dropdown-search {
  width: 100%;
  padding: 8px;
  border: 1px solid #e5e7eb;
  border-radius: 4px;
  outline: none;
}

.dropdown-search:focus {
  border-color: #0E64A5;
  box-shadow: 0 0 0 2px rgba(14, 100, 165, 0.1);
}

.dropdown-options {
  padding: 4px 0;
}

.dropdown-option {
  padding: 8px 12px;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.dropdown-option:hover {
  background-color: #f3f4f6;
}

.no-results {
  padding: 8px 12px;
  color: #6b7280;
  text-align: center;
}

.table-header-sortable {
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
</style>
