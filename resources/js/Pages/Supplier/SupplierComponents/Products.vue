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
                <img :src="product.imageUrl" alt="Product image" class="product-thumbnail">
              </td>
              <td>{{ product.productName }}</td>
              <td>{{ product.skuNumber }}</td>
              <td>{{ product.category }}</td>
              <td>{{ product.unitOfMeasure }}</td>
              <td class="description-cell">{{ product.description }}</td>
              <td>{{ product.manufacturer }}</td>
              <td>
                <span :class="['status-pill', product.status === 'Active' ? 'status-active' : 'status-inactive']">
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
            <!-- Product Image -->
            <div class="form-group">
              <label>Product Image</label>
              <div 
                class="image-upload-area" 
                :class="{ 'is-dragging': isDragging }"
                @dragover="onDragOver"
                @dragleave="onDragLeave"
                @drop="onDrop"
              >
                <div v-if="productImages.length > 0" class="image-preview-area">
                  <img :src="imagePreview" alt="Product preview" class="main-image-preview">
                  <div class="image-gallery">
                    <div 
                      v-for="(image, index) in productImages" 
                      :key="image.id"
                      class="gallery-image-wrapper"
                      :class="{ 'selected': index === selectedImageIndex }"
                      @click="setPrimaryImage(image.id)"
                    >
                      <img :src="image.url" alt="Product image" class="gallery-image">
                      <button 
                        type="button" 
                        class="remove-image-btn"
                        @click.stop="removeImage(image.id)"
                      >✕</button>
                      <div v-if="image.isPrimary" class="primary-badge">Primary</div>
                    </div>
                  </div>
                </div>
                <div v-else class="upload-placeholder">
                  <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                    <polyline points="21 15 16 10 5 21"></polyline>
                  </svg>
                  <p>Drag & drop product images here or click to browse</p>
                </div>
                <input 
                  type="file" 
                  accept="image/*" 
                  @change="onFileSelected" 
                  class="file-input"
                  multiple
                >
              </div>
              <p class="upload-help-text">Upload at least one product image. First image will be set as the primary image.</p>
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
              <button type="submit" class="submit-btn">Save Product</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { router } from '@inertiajs/vue3';

export default {
  name: 'Products',
  props: {
    supplier: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      // Sorting
      sortKey: 'productName',
      sortDir: 'asc',
      
      // Product modal state
      showProductModal: false,
      editingProduct: false,
      editingProductId: null,
      
      // Image upload state
      imagePreview: null,
      productImages: [],
      selectedImageIndex: -1,
      isDragging: false,
      
      // Product data
      newProduct: {
        productName: '',
        skuNumber: '',
        category: '',
        unitOfMeasure: '',
        description: '',
        manufacturer: '',
        status: 'Active'
      },
      products: [
        { id: 1, imageUrl: 'https://via.placeholder.com/80', productName: 'Premium Coffee Beans', skuNumber: 'CB001', category: 'Beverages', unitOfMeasure: 'kg', description: 'High-quality arabica coffee beans from Ethiopia', manufacturer: 'Global Coffee Co.', status: 'Active' },
        { id: 2, imageUrl: 'https://via.placeholder.com/80', productName: 'Whole Wheat Flour', skuNumber: 'WF002', category: 'Baking Supplies', unitOfMeasure: 'kg', description: 'Organic stone-ground whole wheat flour', manufacturer: 'Healthy Mills Inc.', status: 'Active' },
        { id: 3, imageUrl: 'https://via.placeholder.com/80', productName: 'Extra Virgin Olive Oil', skuNumber: 'OL003', category: 'Oils & Condiments', unitOfMeasure: 'liter', description: 'Cold-pressed extra virgin olive oil from Spain', manufacturer: 'Mediterranean Delights', status: 'Inactive' }
      ],
      
      // Product categories and units of measure
      productCategories: ['Beverages', 'Dairy', 'Meat & Poultry', 'Seafood', 'Fruits & Vegetables', 'Bakery', 'Grains & Pasta', 'Canned Goods', 'Snacks', 'Condiments & Sauces', 'Baking Supplies', 'Oils & Vinegars'],
      unitsOfMeasure: ['kg', 'g', 'liter', 'ml', 'unit', 'pack', 'box', 'carton', 'dozen', 'case']
    };
  },
  computed: {
    sortedProducts() {
      const products = [...this.products];
      products.sort((a, b) => {
        let modifier = this.sortDir === 'asc' ? 1 : -1;
        let aValue = a[this.sortKey];
        let bValue = b[this.sortKey];
        
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
      
      return products;
    }
  },
  mounted() {
    // Initialize with real data from API if available
    this.fetchProducts();
  },
  methods: {
    fetchProducts() {
      // In a real implementation, you would fetch products from the API
      // For now, we'll use the demo data
      console.log('Supplier ID to fetch products for:', this.supplier.id);
      
      // If real API integration is implemented, uncomment and modify this code:
      /*
      router.get(`/admin/suppliers/${this.supplier.id}/products`, {}, {
        preserveState: true,
        onSuccess: (response) => {
          this.products = response.data.products;
        },
        onError: (errors) => {
          console.error('Error fetching products:', errors);
        }
      });
      */
    },
    
    sortBy(key) {
      if (this.sortKey === key) {
        this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
      } else {
        this.sortKey = key;
        this.sortDir = 'asc';
      }
    },
    
    getSortIcon(key) {
      if (this.sortKey !== key) return 'sort-icon sort-none';
      return this.sortDir === 'asc' ? 'sort-icon sort-asc' : 'sort-icon sort-desc';
    },
    
    // Product Modal Methods
    openAddProductModal() {
      this.editingProduct = false;
      this.editingProductId = null;
      this.newProduct = {
        productName: '',
        skuNumber: '',
        category: '',
        unitOfMeasure: '',
        description: '',
        manufacturer: '',
        status: 'Active'
      };
      this.imagePreview = null;
      this.productImages = [];
      this.selectedImageIndex = -1;
      this.showProductModal = true;
    },
    
    closeProductModal() {
      this.showProductModal = false;
      this.imagePreview = null;
    },
    
    editProduct(product) {
      this.editingProduct = true;
      this.editingProductId = product.id;
      this.newProduct = { ...product };
      this.imagePreview = product.imageUrl;
      this.productImages = product.images ? [...product.images] : [];
      if (this.productImages.length === 0 && product.imageUrl) {
        // If there are no images but there is an imageUrl, create an image entry
        this.productImages = [{
          id: 1,
          url: product.imageUrl,
          isPrimary: true
        }];
      }
      this.selectedImageIndex = this.productImages.findIndex(img => img.isPrimary) || 0;
      this.showProductModal = true;
    },
    
    saveProduct() {
      // Get the primary image URL for the main imageUrl field
      const primaryImage = this.productImages.find(img => img.isPrimary);
      const primaryImageUrl = primaryImage ? primaryImage.url : 'https://via.placeholder.com/80';
      
      if (this.editingProduct) {
        // Update existing product
        const index = this.products.findIndex(product => product.id === this.editingProductId);
        if (index !== -1) {
          this.products.splice(index, 1, { 
            ...this.newProduct, 
            id: this.editingProductId,
            imageUrl: primaryImageUrl,
            images: [...this.productImages]
          });
        }
      } else {
        // Add new product
        const maxId = this.products.length > 0 ? Math.max(...this.products.map(p => p.id)) : 0;
        this.products.push({
          ...this.newProduct,
          id: maxId + 1,
          imageUrl: primaryImageUrl,
          images: [...this.productImages]
        });
      }
      
      // In a real implementation, you would save the product to the API
      /*
      const productData = {
        supplier_id: this.supplier.id,
        ...this.newProduct,
        primary_image_url: primaryImageUrl,
        images: this.productImages
      };
      
      const endpoint = this.editingProduct 
        ? `/admin/products/${this.editingProductId}` 
        : '/admin/products';
      
      router.post(endpoint, productData, {
        onSuccess: () => {
          this.closeProductModal();
          this.fetchProducts(); // Refresh the products list
        },
        onError: (errors) => {
          console.error('Error saving product:', errors);
        }
      });
      */
      
      this.closeProductModal();
    },
    
    // Image Upload Methods
    onFileSelected(event) {
      const files = event.target.files;
      if (files && files.length > 0) {
        Array.from(files).forEach(file => {
          this.addImageToGallery(file);
        });
      }
    },
    
    onDragOver(event) {
      event.preventDefault();
      this.isDragging = true;
    },
    
    onDragLeave(event) {
      event.preventDefault();
      this.isDragging = false;
    },
    
    onDrop(event) {
      event.preventDefault();
      this.isDragging = false;
      const files = event.dataTransfer.files;
      if (files && files.length > 0) {
        Array.from(files).forEach(file => {
          if (file.type.match('image.*')) {
            this.addImageToGallery(file);
          }
        });
      }
    },
    
    addImageToGallery(file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        const newImageId = this.productImages.length > 0 ? 
          Math.max(...this.productImages.map(img => img.id)) + 1 : 1;
        
        const newImage = {
          id: newImageId,
          url: e.target.result,
          isPrimary: this.productImages.length === 0 // First image is primary by default
        };
        
        this.productImages.push(newImage);
        
        // Update imagePreview if this is the primary image
        if (newImage.isPrimary) {
          this.imagePreview = newImage.url;
          this.selectedImageIndex = this.productImages.length - 1;
        }
      };
      reader.readAsDataURL(file);
    },
    
    removeImage(imageId) {
      const index = this.productImages.findIndex(img => img.id === imageId);
      if (index !== -1) {
        const wasRemovingPrimary = this.productImages[index].isPrimary;
        this.productImages.splice(index, 1);
        
        // If we removed the primary image and there are other images, set a new primary
        if (wasRemovingPrimary && this.productImages.length > 0) {
          this.productImages[0].isPrimary = true;
          this.imagePreview = this.productImages[0].url;
          this.selectedImageIndex = 0;
        } else if (this.productImages.length === 0) {
          // If no images left
          this.imagePreview = null;
          this.selectedImageIndex = -1;
        }
      }
    },
    
    setPrimaryImage(imageId) {
      // Reset all images to non-primary
      this.productImages.forEach(img => {
        img.isPrimary = (img.id === imageId);
      });
      
      // Update the preview with the new primary image
      const primaryImage = this.productImages.find(img => img.id === imageId);
      if (primaryImage) {
        this.imagePreview = primaryImage.url;
        this.selectedImageIndex = this.productImages.findIndex(img => img.id === imageId);
      }
    }
  }
};
</script>

<style scoped>
@import './SupplierSharedStyles.css';
</style> 