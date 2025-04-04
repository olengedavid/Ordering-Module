<template>
  <div class="product-card">
    <!-- Image container positioned above product details -->
    <div class="product-image-container">
      <img
        class="product-image"
        :src="getPrimaryImagePreviewPath(product)"
        :alt="product.name"
      />
      <div class="badge in-stock" v-if="product.inStock">In Stock</div>
      <div class="badge out-of-stock" v-else>Out of Stock</div>
      <div class="promotion-badge" v-if="product.promotion">
        <div class="discount">{{ product.promotion.discount }}</div>
        <div class="promo-details">
          Ends {{ formatDate(product.promotion.endDate) }}
        </div>
      </div>
    </div>

    <div class="product-details">
      <div class="product-header">
        <h2 class="product-title">{{ product.name }}</h2>
        <div class="product-tag">{{ product.category }}</div>
      </div>
      <div class="product-weight">
        {{ product.quantity_per_unit }} {{ product.unit_of_measure }}
      </div>
      <p class="product-description">
        {{ truncateDescription(product.description) }}
      </p>

      <div class="order-details">
        <div class="supplier-info">
          <div class="supplier-name">{{ product.supplier }}</div>
          <div class="inventory-count">{{ product.stock_quantity }} items</div>
        </div>
        <div class="quantity-controls">
          <div class="quantity-label">Quantity</div>
          <div class="quantity-selector">
            <button
              @click="decreaseQuantity"
              :disabled="quantity <= product.min_order || !product.inStock"
              class="quantity-btn"
            >
              -
            </button>
            <span class="quantity">{{ quantity }}</span>
            <button
              @click="increaseQuantity"
              :disabled="quantity >= product.max_order || !product.inStock"
              class="quantity-btn"
            >
              +
            </button>
          </div>
        </div>
      </div>

      <div class="order-limits">
        <div>Min: {{ product.min_order }} item</div>
        <div>Max: {{ product.max_order }} items</div>
      </div>

      <div class="pricing" v-if="product.promotion">
        <div class="original-price">
          {{ formatPrice(calculateOriginalTotal()) }}
        </div>
        <div class="discounted-price">
          {{ formatPrice(calculateDiscountedTotal()) }}
        </div>
      </div>
      <div class="pricing" v-else>
        <div class="current-price">
          {{ formatPrice(calculateOriginalTotal()) }}
        </div>
      </div>

      <button
        class="add-to-cart-btn"
        @click="addToCart"
        :disabled="!product.inStock"
        :class="{ disabled: !product.inStock }"
      >
        <!-- <span class="cart-icon">🛒</span> -->
        {{ product.inStock ? "Add to Cart" : "Out of Stock" }}
      </button>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "ProductCard",
  props: {
    product: {
      type: Object,
      required: true,
    },
    currentUser: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      quantity: 1,
    };
  },
  methods: {
    increaseQuantity() {
      if (this.quantity < this.product.max_order && this.product.inStock) {
        this.quantity++;
      }
    },
    decreaseQuantity() {
      if (this.quantity > this.product.min_order && this.product.inStock) {
        this.quantity--;
      }
    },
    async addToCart() {
      if (!this.product.inStock) return;
      console.log("product",this.product);
      try {
        const cartData = {
          retailer_id: this.product.company_id,
          supplier_id: this.product.company_id,
          warehouse_inventory_id: this.product.inventory_id,
          quantity: this.quantity,
          unit_price: Number(this.product.selling_price),
          min_order: Number(this.product.min_order),
          max_order: Number(this.product.max_order),
          created_by: this.currentUser.id,
        };

        console.log("cart",cartData);

        const response = await axios.post("/retailers/cart/add", cartData);

        if (response.status === 201) {
          this.$emit("cart-updated");
        }
      } catch (error) {
        console.error("Error adding to cart:", error);
      }
    },
    formatDate(date) {
      const options = { year: "numeric", month: "short", day: "numeric" };
      return date.toLocaleDateString("en-US", options);
    },
    calculateOriginalTotal() {
      return this.product.selling_price * this.quantity;
    },
    calculateDiscountedTotal() {
      if (!this.product.promotion) return this.calculateOriginalTotal();

      const discountMatch = this.product.promotion.discount.match(/\d+/);
      if (!discountMatch) return this.calculateOriginalTotal();

      const discountPercent = parseInt(discountMatch[0]);
      return this.calculateOriginalTotal() * (1 - discountPercent / 100);
    },
    formatPrice(price) {
      // Format price with comma separators and currency
      return `${this.product.currency} ${price
        .toFixed(0)
        .replace(/\B(?=(\d{3})+(?!\d))/g, ",")}`;
    },
    truncateDescription(description) {
      // Truncate description to keep card heights more consistent
      if (description.length > 120) {
        return description.substring(0, 120) + "...";
      }
      return description;
    },

    getPrimaryImagePreviewPath(product) {
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
    },
  },
};
</script>

<style scoped>
.product-card {
  display: flex;
  flex-direction: column;
  background: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  width: 100%;
  height: 100%;
  font-family: "Inter", sans-serif;
  transition: transform 0.3s ease;
}

.product-card:hover {
  transform: translateY(-4px);
}

.product-image-container {
  position: relative;
  width: 100%;
  height: 200px;
  overflow: hidden;
  background-color: #f9f9f9;
}

.product-image {
  width: 100%;
  height: 100%;
  object-fit: contain;
  transition: transform 0.5s ease;
  opacity: 1;
  padding: 16px;
}

.product-card:hover .product-image {
  transform: scale(1.03);
}

/* Out of Stock styling */
.product-card:not(.in-stock) .product-image {
  opacity: 0.7;
  filter: grayscale(40%);
}

.badge {
  position: absolute;
  top: 12px;
  right: 12px;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.7rem;
  font-weight: 600;
  z-index: 2;
}

.in-stock {
  background: linear-gradient(135deg, #00c853, #009624);
  color: white;
}

.out-of-stock {
  background: linear-gradient(135deg, #f44336, #d32f2f);
  color: white;
}

/* Promotion badge styling */
.promotion-badge {
  position: absolute;
  top: 0;
  left: 0;
  background: linear-gradient(135deg, #ff6d00, #ff3d00);
  color: white;
  padding: 6px 10px;
  border-radius: 0 0 16px 0;
  z-index: 2;
  box-shadow: 0 2px 8px rgba(255, 109, 0, 0.3);
}

.discount {
  font-size: 1rem;
  font-weight: 800;
  letter-spacing: 0.5px;
}

.promo-details {
  font-size: 0.6rem;
  opacity: 0.9;
}

.product-details {
  padding: 16px;
  display: flex;
  flex-direction: column;
  flex: 1;
  text-align: left;
}

.product-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 8px;
}

.product-title {
  font-size: 1.1rem;
  font-weight: 700;
  color: #1c2023;
  margin: 0;
  text-align: left;
}

.product-tag {
  background-color: #e3f2fd;
  color: #1976d2;
  padding: 3px 8px;
  border-radius: 16px;
  font-size: 0.7rem;
  font-weight: 600;
  white-space: nowrap;
  margin-left: 8px;
}

.product-weight {
  font-size: 0.85rem;
  color: #4caf50;
  font-weight: 600;
  margin-bottom: 8px;
  text-align: left;
}

.product-description {
  color: #546e7a;
  font-size: 0.8rem;
  line-height: 1.4;
  margin-bottom: 12px;
  flex-grow: 1;
  text-align: left;
}

.order-details {
  display: flex;
  justify-content: space-between;
  margin-bottom: 12px;
}

.supplier-info {
  display: flex;
  flex-direction: column;
  text-align: left;
}

.supplier-name {
  font-weight: 600;
  color: #37474f;
  font-size: 0.8rem;
  text-align: left;
}

.inventory-count {
  color: #78909c;
  font-size: 0.7rem;
  text-align: left;
}

.quantity-controls {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.quantity-label {
  font-size: 0.7rem;
  color: #78909c;
  margin-bottom: 3px;
  text-align: left;
}

.quantity-selector {
  display: flex;
  align-items: center;
  gap: 8px;
}

.quantity-btn {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  border: none;
  background-color: #f1f3f5;
  color: #37474f;
  font-size: 0.9rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.2s;
}

.quantity-btn:hover:not(:disabled) {
  background-color: #e9ecef;
}

.quantity-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.quantity {
  font-weight: 600;
  color: #37474f;
  font-size: 0.9rem;
}

.order-limits {
  display: flex;
  justify-content: space-between;
  color: #78909c;
  font-size: 0.7rem;
  margin-bottom: 12px;
  text-align: left;
}

/* Pricing styles */
.pricing {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
  text-align: left;
}

.original-price {
  color: #78909c;
  text-decoration: line-through;
  font-size: 0.8rem;
}

.discounted-price {
  color: #e91e63;
  font-weight: 700;
  font-size: 1rem;
}

.current-price {
  color: #37474f;
  font-weight: 700;
  font-size: 1rem;
}

.add-to-cart-btn {
  width: 100%;
  padding: 10px;
  background: linear-gradient(135deg, #0EAD09, #0EAD09);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  transition: all 0.2s;
}

.add-to-cart-btn:hover:not(.disabled) {
  background: linear-gradient(135deg, #0EAD09, #0EAD09);
  transform: translateY(-2px);
}

.add-to-cart-btn.disabled {
  background: linear-gradient(135deg, #9e9e9e, #757575);
  cursor: not-allowed;
  transform: none;
}

.cart-icon {
  font-size: 1rem;
}
</style>