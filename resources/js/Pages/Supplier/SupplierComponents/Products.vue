<template>
  <div>
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

    <div class="table-controls">
      <div class="table-title">Products</div>
      <button @click="openAddProductModal" class="add-btn">
        <span class="plus-icon">+</span>
        Add Product
      </button>
    </div>

    <div class="table-container">
      <div class="table-wrapper">
        <table class="data-table">
          <thead>
            <tr>
              <th>Product</th>
              <th @click="sortBy('productName')" class="sortable">
                Name <i :class="getSortIcon('productName')"></i>
              </th>
              <th @click="sortBy('skuNumber')" class="sortable">
                SKU Number <i :class="getSortIcon('skuNumber')"></i>
              </th>
              <th @click="sortBy('category')" class="sortable">
                Category <i :class="getSortIcon('category')"></i>
              </th>
              <th @click="sortBy('unitOfMeasure')" class="sortable">
                Unit of Measure <i :class="getSortIcon('unitOfMeasure')"></i>
              </th>
              <th @click="sortBy('description')" class="sortable">
                Description <i :class="getSortIcon('description')"></i>
              </th>
              <th @click="sortBy('manufacturer')" class="sortable">
                Manufacturer <i :class="getSortIcon('manufacturer')"></i>
              </th>
              <th @click="sortBy('status')" class="sortable">
                Status <i :class="getSortIcon('status')"></i>
              </th>
              <th class="actions-column">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="product in sortedProducts"
              :key="product.id"
              class="data-row"
            >
              <!-- <td>
                <img
                  :src="getPrimaryImageUrl(product)"
                  alt="Product image"
                  class="product-thumbnail"
                />
              </td> -->
              <td>
                <div class="product-image-container">
                  <img
                    :src="getPrimaryImagePreviewPath(product)"
                    class="product-thumbnail"
                    @error="$event.target.src = 'https://via.placeholder.com/50'"
                  />
                </div>
              </td>
              <td>{{ product.name || product.productName }}</td>
              <td>{{ product.sku_number || product.skuNumber }}</td>
              <td>{{ product.category }}</td>
              <td>{{ product.unit_of_measure || product.unitOfMeasure }}</td>
              <td class="description-cell">{{ product.description }}</td>
              <td>{{ product.manufucturer || product.manufacturer }}</td>
              <td>
                <span
                  :class="[
                    'status-pill',
                    product.status === 'Active' || product.status === 'active'
                      ? 'status-active'
                      : 'status-inactive',
                  ]"
                >
                  {{ product.status }}
                </span>
              </td>
              <td class="actions-column">
                <button
                  @click="editProduct(product)"
                  class="action-btn edit-btn"
                >
                  Edit
                </button>
              </td>
            </tr>
            <tr v-if="products.length === 0">
              <td colspan="9" class="empty-state">No products added yet</td>
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
                v-model="newProduct.productName"
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
                v-model="newProduct.skuNumber"
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
                  <span :data-has-value="!!newProduct.category">{{ newProduct.category || 'Select category' }}</span>
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
                  <span :data-has-value="!!newProduct.unitOfMeasure">{{ newProduct.unitOfMeasure || 'Select unit' }}</span>
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
              <label for="manufacturer"
                >Manufacturer <span class="required">*</span></label
              >
              <input
                type="text"
                id="manufacturer"
                v-model="newProduct.manufacturer"
                required
                placeholder="Enter manufacturer name"
              />
            </div>

            <div class="form-group">
              <label for="description"
                >Description <span class="required">*</span></label
              >
              <textarea
                id="description"
                v-model="newProduct.description"
                required
                placeholder="Enter product description"
                rows="3"
              ></textarea>
            </div>

            <div class="form-group">
              <label for="status">Status</label>
              <div class="custom-select-container">
                <div 
                  class="custom-select-trigger" 
                  @click="toggleStatusDropdown"
                  :class="{ 'active': isStatusOpen }"
                >
                  <span :data-has-value="!!newProduct.status">{{ newProduct.status || 'Select status' }}</span>
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
              <button
                type="submit"
                class="submit-btn"
                :disabled="newProduct.processing"
              >
                Save Product
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { router, useForm, usePage } from "@inertiajs/vue3";
import { ref, computed, onMounted, onUnmounted } from "vue";
import CustomPagination from "@/Components/CustomPagination.vue";
import SuccessMessage from "@/Components/SuccessMessage.vue";
import ErrorMessage from "@/Components/ErrorMessage.vue";

export default {
  name: "Products",
  components: {
    CustomPagination,
    SuccessMessage,
    ErrorMessage,
  },
  props: {
    supplier: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    // Get user info
    const page = usePage();
    const user = page.props.auth.user;

    // Ensure we have default values in case user or user.company is null
    const userId = user?.id ? parseInt(user.id) : null;

    // For company ID, prioritize supplier's company_id if available
    const companyId = props.supplier.company_id
      ? parseInt(props.supplier.company_id)
      : props.supplier.id
      ? parseInt(props.supplier.id)
      : user?.company?.id
      ? parseInt(user.company.id)
      : user?.company_id
      ? parseInt(user.company_id)
      : null;

    console.log("Initial company ID:", companyId);
    console.log("Supplier object:", props.supplier);

    // Sorting
    const sortKey = ref("productName");
    const sortDir = ref("asc");

    // Product modal state
    const showProductModal = ref(false);
    const editingProduct = ref(false);
    const editingProductId = ref(null);

    // Image upload state
    const imagePreview = ref(null);
    const productImages = ref([]);
    const selectedImageIndex = ref(-1);
    const isDragging = ref(false);

    // Pagination
    const currentPage = ref(1);
    const perPage = ref(10);
    const lastPage = ref(1);

    // Products data
    const products = ref([]);

    // Product form
    const newProduct = useForm({
      productName: "",
      skuNumber: "",
      category: "",
      unitOfMeasure: "",
      description: "",
      manufacturer: "",
      status: "Active",
      supplier_id: props.supplier.id,
      company_id: companyId,
      created_by: userId,
      primary_image: null,
      secondary_images: [],
    });

    // Product categories and units of measure
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

    // Replace static units with dynamic ref
    const units = ref([]);

    // Add default units for fallback
    const defaultUnits = [
      { label: "Kilogram", value: "kg" },
      { label: "Gram", value: "g" },
      { label: "Liter", value: "liter" },
      { label: "Milliliter", value: "ml" },
      { label: "Unit", value: "unit" },
      { label: "Pack", value: "pack" },
      { label: "Box", value: "box" },
      { label: "Carton", value: "carton" },
      { label: "Dozen", value: "dozen" },
      { label: "Case", value: "case" }
    ];

    // Message state
    const successMessage = ref("");
    const errorMessage = ref("");

    // Dropdown states
    const isCategoryOpen = ref(false);
    const isUnitOpen = ref(false);
    const isStatusOpen = ref(false);
    const categorySearchQuery = ref('');
    const unitSearchQuery = ref('');
    const statusSearchQuery = ref('');
    const filteredCategories = ref([...productCategories]);
    const filteredUnits = ref([...units.value]);
    const filteredStatuses = ref(['Active', 'Inactive']);

    // Computed properties
    const sortedProducts = computed(() => {
      const productsList = [...products.value];
      productsList.sort((a, b) => {
        let modifier = sortDir.value === "asc" ? 1 : -1;
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

      return productsList;
    });

    // Pagination handlers
    const handlePageChange = (page) => {
      currentPage.value = page;
      fetchProducts();
    };

    const handlePerPageChange = (newPerPage) => {
      perPage.value = newPerPage;
      currentPage.value = 1; // Reset to first page when changing items per page
      fetchProducts();
    };

    // Methods
    const fetchProducts = async () => {
      try {
        const response = await axios.get(
          route("supplier.products.paginate", {
            uuid: props.supplier.uuid || props.supplier.id,
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
      return sortDir.value === "asc"
        ? "sort-icon sort-asc"
        : "sort-icon sort-desc";
    };

    // Product Modal Methods
    const openAddProductModal = () => {
      editingProduct.value = false;
      editingProductId.value = null;
      newProduct.reset();
      imagePreview.value = null;
      productImages.value = [];
      selectedImageIndex.value = -1;
      isDragging.value = false;
      showProductModal.value = true;
    };

    const closeProductModal = () => {
      showProductModal.value = false;
      newProduct.reset();
      imagePreview.value = null;
      productImages.value = [];
      selectedImageIndex.value = -1;
      isDragging.value = false;
    };

    const editProduct = (product) => {
      editingProduct.value = true;
      editingProductId.value = product.uuid;

      // Map API fields to form fields
      newProduct.productName = product.name || product.productName;
      newProduct.skuNumber = product.sku_number || product.skuNumber;
      newProduct.category = product.category;
      newProduct.unitOfMeasure = product.unit_of_measure || product.unitOfMeasure;
      newProduct.description = product.description;
      newProduct.manufacturer = product.manufucturer || product.manufacturer;
      newProduct.status = product.status.charAt(0).toUpperCase() + product.status.slice(1);

      // Handle images
      if (product.images) {
        try {
          let images;
          const parsedImages = typeof product.images === "string" ? JSON.parse(product.images) : product.images;

          // Handle both array and object formats
          images = Array.isArray(parsedImages) ? parsedImages : Object.values(parsedImages);

          productImages.value = images.map((img, index) => ({
            id: index + 1,
            url: `/storage/${img.path}`,
            isPrimary: img.type === "primary",
            path: img.path
          }));

          const primaryImage = productImages.value.find((img) => img.isPrimary);
          if (primaryImage) {
            imagePreview.value = primaryImage.url;
          }
        } catch (error) {
          console.error("Error processing product images:", error);
        }
      }

      showProductModal.value = true;
    };

    const saveProduct = () => {
      const formData = new FormData();
      formData.append("name", newProduct.productName);
      formData.append("sku_number", newProduct.skuNumber);
      formData.append("category", newProduct.category);
      formData.append("unit_of_measure", newProduct.unitOfMeasure);
      formData.append("description", newProduct.description);
      formData.append("manufucturer", newProduct.manufacturer);
      formData.append("status", newProduct.status.toLowerCase());
      formData.append("company_id", companyId);
      formData.append("created_by", userId);

      // Add images
      const primaryImage = productImages.value.find((img) => img.isPrimary);
      if (primaryImage) {
        if (primaryImage.file) {
          formData.append("primary_image", primaryImage.file);
        } else if (primaryImage.path) {
          formData.append("primary_image_path", primaryImage.path);
        }
      }

      // Add secondary images
      productImages.value.forEach((image) => {
        if (!image.isPrimary) {
          if (image.file) {
            formData.append("secondary_images[]", image.file);
          } else if (image.path) {
            formData.append("secondary_image_paths[]", image.path);
          }
        }
      });

      if (editingProduct.value) {
        axios
          .post(route("products.update", editingProductId.value), formData, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          })
          .then(() => {
            closeProductModal();
            fetchProducts();
            successMessage.value = "Product updated successfully";
            setTimeout(() => {
              successMessage.value = "";
            }, 3000);
          })
          .catch((error) => {
            errorMessage.value = "Error updating product";
            console.error("Error updating product:", error);
            setTimeout(() => {
              errorMessage.value = "";
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
            fetchProducts();
            successMessage.value = "Product added successfully";
            setTimeout(() => {
              successMessage.value = "";
            }, 3000);
          })
          .catch((error) => {
            errorMessage.value = "Error adding product";
            console.error("Error adding product:", error);
            setTimeout(() => {
              errorMessage.value = "";
            }, 3000);
          });
      }
    };

    // Auto-dismiss messages after 3 seconds
    const autoDismissMessage = (type) => {
      console.log("Products - Auto dismissing message of type:", type);
      setTimeout(() => {
        if (type === "success") {
          successMessage.value = "";
        } else if (type === "error") {
          errorMessage.value = "";
        }
      }, 3000);
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

    const handleImageError = (event) => {
      event.target.src = "/path/to/default/image.jpg"; // Your default image path
    };

    // Image Upload Methods
    const onFileSelected = (event) => {
      const files = event.target.files;
      if (files && files.length > 0) {
        Array.from(files).forEach((file) => {
          addImageToGallery(file);
        });
      }
    };

    const onDragOver = (event) => {
      event.preventDefault();
      isDragging.value = true;
    };

    const onDragLeave = (event) => {
      event.preventDefault();
      isDragging.value = false;
    };

    const onDrop = (event) => {
      event.preventDefault();
      isDragging.value = false;
      const files = event.dataTransfer.files;
      if (files && files.length > 0) {
        Array.from(files).forEach((file) => {
          if (file.type.match("image.*")) {
            addImageToGallery(file);
          }
        });
      }
    };

    const addImageToGallery = (file) => {
      const reader = new FileReader();
      reader.onload = (e) => {
        const newImageId =
          productImages.value.length > 0
            ? Math.max(...productImages.value.map((img) => img.id)) + 1
            : 1;

        const newImage = {
          id: newImageId,
          url: e.target.result,
          file: file,
          isPrimary: productImages.value.length === 0, // First image is primary by default
        };

        productImages.value.push(newImage);

        // Update imagePreview if this is the primary image
        if (newImage.isPrimary) {
          imagePreview.value = newImage.url;
          selectedImageIndex.value = productImages.value.length - 1;
        }
      };
      reader.readAsDataURL(file);
    };

    const removeImage = (imageId) => {
      const index = productImages.value.findIndex((img) => img.id === imageId);
      if (index !== -1) {
        const wasRemovingPrimary = productImages.value[index].isPrimary;
        productImages.value.splice(index, 1);

        // If we removed the primary image and there are other images, set a new primary
        if (wasRemovingPrimary && productImages.value.length > 0) {
          productImages.value[0].isPrimary = true;
          imagePreview.value = productImages.value[0].url;
          selectedImageIndex.value = 0;
        } else if (productImages.value.length === 0) {
          // If no images left
          imagePreview.value = null;
          selectedImageIndex.value = -1;
        }
      }
    };

    const setPrimaryImage = (imageId) => {
      const selectedImageIndex = productImages.value.findIndex(
        (img) => img.id === imageId
      );
      
      if (selectedImageIndex !== -1) {
        // Get the selected image
        const selectedImage = productImages.value[selectedImageIndex];
        
        // Remove it from its current position
        productImages.value.splice(selectedImageIndex, 1);
        
        // Add it to the front of the array
        productImages.value.unshift(selectedImage);
        
        // Update primary status for all images
        productImages.value.forEach((img, index) => {
          img.isPrimary = index === 0;
        });

        // Update the preview with the new primary image
        imagePreview.value = selectedImage.url;
      }
    };

    // Dropdown methods
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
      newProduct.category = category;
      isCategoryOpen.value = false;
      categorySearchQuery.value = '';
    };

    const selectUnit = (unit) => {
      newProduct.unitOfMeasure = unit.value;
      isUnitOpen.value = false;
      unitSearchQuery.value = '';
    };

    const selectStatus = (status) => {
      newProduct.status = status;
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

    // Add fetchUnits method
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

    // Initialize dropdowns
    onMounted(() => {
      fetchProducts();
      fetchUnits();
      document.addEventListener('click', closeDropdownsOutside);
      // Initialize filtered arrays
      filteredCategories.value = [...productCategories];
      filteredStatuses.value = ['Active', 'Inactive'];
    });

    // Remove event listener when component is unmounted
    onUnmounted(() => {
      document.removeEventListener('click', closeDropdownsOutside);
    });

    return {
      sortKey,
      sortDir,
      showProductModal,
      editingProduct,
      editingProductId,
      imagePreview,
      productImages,
      selectedImageIndex,
      isDragging,
      newProduct,
      products,
      productCategories,
      units,
      sortedProducts,
      currentPage,
      perPage,
      lastPage,
      successMessage,
      errorMessage,
      handlePageChange,
      handlePerPageChange,
      sortBy,
      getSortIcon,
      openAddProductModal,
      closeProductModal,
      editProduct,
      saveProduct,
      onFileSelected,
      onDragOver,
      onDragLeave,
      onDrop,
      addImageToGallery,
      removeImage,
      setPrimaryImage,
      getPrimaryImagePreviewPath,
      autoDismissMessage,
      isCategoryOpen,
      isUnitOpen,
      isStatusOpen,
      categorySearchQuery,
      unitSearchQuery,
      statusSearchQuery,
      filteredCategories,
      filteredUnits,
      filteredStatuses,
      toggleCategoryDropdown,
      toggleUnitDropdown,
      toggleStatusDropdown,
      filterCategories,
      filterUnits,
      filterStatuses,
      selectCategory,
      selectUnit,
      selectStatus,
    };
  },
};
</script>

<style scoped>
@import "./SupplierSharedStyles.css";

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
</style> 