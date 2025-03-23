<template>
  <div>
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
            <tr v-for="product in sortedProducts" :key="product.id" class="data-row">
              <td>
                <img :src="getPrimaryImageUrl(product)" alt="Product image" class="product-thumbnail">
              </td>
              <td>{{ product.name || product.productName }}</td>
              <td>{{ product.sku_number || product.skuNumber }}</td>
              <td>{{ product.category }}</td>
              <td>{{ product.unit_of_measure || product.unitOfMeasure }}</td>
              <td class="description-cell">{{ product.description }}</td>
              <td>{{ product.manufucturer || product.manufacturer }}</td>
              <td>
                <span :class="['status-pill', (product.status === 'Active' || product.status === 'active') ? 'status-active' : 'status-inactive']">
                  {{ product.status }}
                </span>
              </td>
              <td class="actions-column">
                <button @click="editProduct(product)" class="action-btn edit-btn">
                  Edit
                </button>
              </td>
            </tr>
            <tr v-if="products.length === 0">
              <td colspan="9" class="empty-state">
                No products added yet
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
    <div v-if="showProductModal" class="modal-overlay" @click.self="closeProductModal">
      <div class="modal-container">
        <div class="modal-header">
          <h2>{{ editingProduct ? 'Edit Product' : 'Add New Product' }}</h2>
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
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                  </svg>
                  <p>Drag & drop images here or <span class="browse-text">browse</span></p>
                  <input 
                    type="file" 
                    id="productImage" 
                    class="file-input" 
                    accept="image/*"
                    multiple
                    @change="onFileSelected"
                  >
                </div>
              </div>
              
              <!-- Images Gallery (Outside the upload container) -->
              <div v-if="productImages.length > 0" class="images-gallery-outside">
                <div class="gallery-preview-outside">
                  <img v-if="imagePreview" :src="imagePreview" alt="Primary product image" class="primary-preview-img">
                </div>
                <div class="gallery-thumbnails-outside">
                  <div 
                    v-for="image in productImages" 
                    :key="image.id"
                    class="thumbnail-container"
                    :class="{ 'primary-thumbnail': image.isPrimary }"
                  >
                    <img :src="image.url" alt="Product image" class="thumbnail-img">
                    <div class="thumbnail-actions">
                      <button 
                        type="button" 
                        class="set-primary-btn" 
                        @click="setPrimaryImage(image.id)"
                        :disabled="image.isPrimary"
                        :title="image.isPrimary ? 'Primary Image' : 'Set as Primary'"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                      </button>
                      <button type="button" class="remove-thumbnail-btn" @click="removeImage(image.id)">&times;</button>
                    </div>
                  </div>
                  <div class="add-more-container">
                    <label for="additionalImages" class="add-more-btn">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                    >
                  </div>
                </div>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group half-width">
                <label for="productName">Product Name <span class="required">*</span></label>
                <input type="text" id="productName" v-model="newProduct.productName" required placeholder="Enter product name">
              </div>
              <div class="form-group half-width">
                <label for="skuNumber">SKU Number <span class="required">*</span></label>
                <input type="text" id="skuNumber" v-model="newProduct.skuNumber" required placeholder="Enter SKU number">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group half-width">
                <label for="category">Category <span class="required">*</span></label>
                <select id="category" v-model="newProduct.category" required>
                  <option value="" disabled selected>Select category</option>
                  <option v-for="category in productCategories" :key="category" :value="category">
                    {{ category }}
                  </option>
                </select>
              </div>
              <div class="form-group half-width">
                <label for="unitOfMeasure">Unit of Measure <span class="required">*</span></label>
                <select id="unitOfMeasure" v-model="newProduct.unitOfMeasure" required>
                  <option value="" disabled selected>Select unit</option>
                  <option v-for="unit in unitsOfMeasure" :key="unit" :value="unit">
                    {{ unit }}
                  </option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label for="manufacturer">Manufacturer <span class="required">*</span></label>
              <input type="text" id="manufacturer" v-model="newProduct.manufacturer" required placeholder="Enter manufacturer name">
            </div>

            <div class="form-group">
              <label for="description">Description <span class="required">*</span></label>
              <textarea id="description" v-model="newProduct.description" required placeholder="Enter product description" rows="3"></textarea>
            </div>

            <div class="form-group">
              <label for="status">Status</label>
              <select id="status" v-model="newProduct.status">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>

            <div class="form-actions">
              <button type="button" class="cancel-btn" @click="closeProductModal">Cancel</button>
              <button type="submit" class="submit-btn" :disabled="newProduct.processing">Save Product</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { router, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import CustomPagination from "@/Components/CustomPagination.vue";

export default {
  name: 'Products',
  components: {
    CustomPagination
  },
  props: {
    supplier: {
      type: Object,
      required: true
    }
  },
  setup(props) {
    // Get user info
    const page = usePage();
    const user = page.props.auth.user;
    
    // Ensure we have default values in case user or user.company is null
    const userId = user?.id ? parseInt(user.id) : null;
    
    // For company ID, prioritize supplier's company_id if available
    const companyId = props.supplier.company_id ? parseInt(props.supplier.company_id) :
                      props.supplier.id ? parseInt(props.supplier.id) :
                      user?.company?.id ? parseInt(user.company.id) : 
                      user?.company_id ? parseInt(user.company_id) : null;
    
    console.log('Initial company ID:', companyId);
    console.log('Supplier object:', props.supplier);
    
    // Sorting
    const sortKey = ref('productName');
    const sortDir = ref('asc');
    
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
      productName: '',
      skuNumber: '',
      category: '',
      unitOfMeasure: '',
      description: '',
      manufacturer: '',
      status: 'Active',
      supplier_id: props.supplier.id,
      company_id: companyId,
      created_by: userId,
      primary_image: null,
      secondary_images: []
    });
    
    // Product categories and units of measure
    const productCategories = [
      'Beverages', 'Dairy', 'Meat & Poultry', 'Seafood', 'Fruits & Vegetables', 
      'Bakery', 'Grains & Pasta', 'Canned Goods', 'Snacks', 'Condiments & Sauces', 
      'Baking Supplies', 'Oils & Vinegars'
    ];
    const unitsOfMeasure = ['kg', 'g', 'liter', 'ml', 'unit', 'pack', 'box', 'carton', 'dozen', 'case'];
    
    // Computed properties
    const sortedProducts = computed(() => {
      const productsList = [...products.value];
      productsList.sort((a, b) => {
        let modifier = sortDir.value === 'asc' ? 1 : -1;
        let aValue = a[sortKey.value];
        let bValue = b[sortKey.value];
        
        // Handle undefined or null values
        if (aValue === undefined || aValue === null) {
          aValue = '';
        }
        if (bValue === undefined || bValue === null) {
          bValue = '';
        }
        
        if (typeof aValue === 'number' && typeof bValue === 'number') {
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
        console.error('Error fetching products:', error);
      }
    };
    
    const sortBy = (key) => {
      if (sortKey.value === key) {
        sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
      } else {
        sortKey.value = key;
        sortDir.value = 'asc';
      }
    };
    
    const getSortIcon = (key) => {
      if (sortKey.value !== key) return 'sort-icon sort-none';
      return sortDir.value === 'asc' ? 'sort-icon sort-asc' : 'sort-icon sort-desc';
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
      editingProductId.value = product.id;
      
      // Map API fields to form fields
      newProduct.productName = product.name || product.productName;
      newProduct.skuNumber = product.sku_number || product.skuNumber;
      newProduct.category = product.category;
      newProduct.unitOfMeasure = product.unit_of_measure || product.unitOfMeasure;
      newProduct.description = product.description;
      newProduct.manufacturer = product.manufucturer || product.manufacturer;
      newProduct.status = product.status;
      
      // Handle images
      if (product.imageUrl) {
        imagePreview.value = product.imageUrl;
      } else if (product.images) {
        const images = typeof product.images === 'string' 
          ? JSON.parse(product.images) 
          : product.images;
          
        if (images && images.length > 0) {
          productImages.value = images.map((img, index) => ({
            id: index + 1,
            url: img.path || img.url,
            isPrimary: img.type === 'primary' || img.isPrimary
          }));
          
          const primaryImage = productImages.value.find(img => img.isPrimary);
          if (primaryImage) {
            imagePreview.value = primaryImage.url;
          }
        }
      }
      
      showProductModal.value = true;
    };
    
    const saveProduct = () => {
      // Prepare form data for submission
      const formData = new FormData();
      
      // Add basic product information
      formData.append('name', newProduct.productName);
      formData.append('sku_number', newProduct.skuNumber);
      formData.append('category', newProduct.category);
      formData.append('unit_of_measure', newProduct.unitOfMeasure);
      formData.append('description', newProduct.description);
      formData.append('manufucturer', newProduct.manufacturer);
      formData.append('status', newProduct.status.toLowerCase());
      
      // Add supplier_id if available
      if (props.supplier && props.supplier.id) {
        formData.append('supplier_id', parseInt(props.supplier.id));
      }
      
      // IMPORTANT: Ensure company_id is included as an integer
      // Use the supplier's company_id first, as it should be the most reliable source
      const companyIdToUse = props.supplier.company_id ? parseInt(props.supplier.company_id) : 
                             props.supplier.id ? parseInt(props.supplier.id) : 
                             user?.company?.id ? parseInt(user.company.id) : 
                             user?.company_id ? parseInt(user.company_id) : null;
      
      // Company ID is required - ensure it's always included and is an integer
      if (companyIdToUse) {
        formData.append('company_id', companyIdToUse);
        console.log('Using company_id:', companyIdToUse);
      } else {
        console.error('No company_id available - this will cause an error');
        // Fallback to a default if absolutely necessary (modify based on your app structure)
        formData.append('company_id', parseInt(props.supplier.id));
      }
      
      // Add created_by user ID if available
      const userIdToUse = userId || (user?.id ? parseInt(user.id) : null);
      if (userIdToUse) {
        formData.append('created_by', userIdToUse);
      }
      
      // Add primary image
      const primaryImage = productImages.value.find(img => img.isPrimary);
      if (primaryImage && primaryImage.file) {
        formData.append('primary_image', primaryImage.file);
      }
      
      // Add secondary images
      const secondaryImages = productImages.value.filter(img => !img.isPrimary);
      secondaryImages.forEach(img => {
        if (img.file) {
          formData.append('secondary_images[]', img.file);
        }
      });
      
      // Determine endpoint
      const endpoint = editingProduct.value 
        ? route('products.update', editingProductId.value) 
        : route('products.store');
      
      // Submit form
      router.post(endpoint, formData, {
        onSuccess: () => {
          closeProductModal();
          fetchProducts();
        },
        onError: (errors) => {
          console.error('Error saving product:', errors);
        }
      });
    };
    
    const getPrimaryImageUrl = (product) => {
      // If the product has a direct imageUrl property, use that
      if (product.imageUrl) {
        return product.imageUrl;
      }
      
      // If the product has images in API format
      if (product.images) {
        const images = typeof product.images === 'string'
          ? JSON.parse(product.images)
          : product.images;
          
        const primaryImage = images?.find(img => img.type === 'primary' || img.isPrimary);
        if (primaryImage) {
          return primaryImage.path || primaryImage.url;
        }
      }
      
      // Default image if none found
      return 'https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg';
    };
    
    // Image Upload Methods
    const onFileSelected = (event) => {
      const files = event.target.files;
      if (files && files.length > 0) {
        Array.from(files).forEach(file => {
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
        Array.from(files).forEach(file => {
          if (file.type.match('image.*')) {
            addImageToGallery(file);
          }
        });
      }
    };
    
    const addImageToGallery = (file) => {
      const reader = new FileReader();
      reader.onload = (e) => {
        const newImageId = productImages.value.length > 0 ? 
          Math.max(...productImages.value.map(img => img.id)) + 1 : 1;
        
        const newImage = {
          id: newImageId,
          url: e.target.result,
          file: file,
          isPrimary: productImages.value.length === 0 // First image is primary by default
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
      const index = productImages.value.findIndex(img => img.id === imageId);
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
      // Reset all images to non-primary
      productImages.value.forEach(img => {
        img.isPrimary = (img.id === imageId);
      });
      
      // Update the preview with the new primary image
      const primaryImage = productImages.value.find(img => img.id === imageId);
      if (primaryImage) {
        imagePreview.value = primaryImage.url;
        selectedImageIndex.value = productImages.value.findIndex(img => img.id === imageId);
      }
    };
    
    // Initialize
    onMounted(() => {
      fetchProducts();
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
      unitsOfMeasure,
      sortedProducts,
      currentPage,
      perPage,
      lastPage,
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
      getPrimaryImageUrl
    };
  }
};
</script>

<style scoped>
@import './SupplierSharedStyles.css';
</style> 