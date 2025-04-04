<script setup>
import { ref, computed, onMounted } from "vue";
import RetailerNavbar from "./Components/RetailerNavbar.vue";
import axios from "axios";

const deliveryAddress = ref("");
const paymentMethod = ref("");
const deliveryFee = ref(0);
const creditBalance = ref(55000);
const cartItems = ref([
  {
    name: "All Purpose Fertilizer 14 Kgs",
    weight: "14 Kilograms",
    type: "Fertilizer",
    price: 3450,
    pricePerItem: 3450,
    quantity: 1,
    minOrder: 1,
    maxOrder: 50,
    maxOrderUnit: "items",
    supplier: "Aden Agri Supplies",
    promotion: null,
    outOfStock: false,
    imageUrl:
      "https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg",
  },
  {
    name: "Starter Fertilizer 20 Kgs",
    weight: "20 Kilograms",
    type: "Fertilizer",
    price: 3450,
    pricePerItem: 3450,
    quantity: 1,
    minOrder: 1,
    maxOrder: 50,
    maxOrderUnit: "item",
    supplier: "Aden Agri Supplies",
    promotion: {
      discount: "50% OFF",
      endDate: "Mar 12, 2025",
    },
    outOfStock: false,
    imageUrl:
      "https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg",
  },
  {
    name: "DAP Fertilizer 46 Kgs",
    weight: "46 Kilograms",
    type: "Fertilizer",
    price: 6900,
    pricePerItem: 3450,
    quantity: 1,
    minOrder: 1,
    maxOrder: 50,
    maxOrderUnit: "items",
    supplier: "Green Agriculture Ltd",
    promotion: null,
    outOfStock: true,
    imageUrl:
      "https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg",
  },
]);
const loading = ref(false);
const error = ref(null);

const totalAmount = computed(() => {
  return (
    cartItems.value.reduce(
      (total, item) => total + item.price * item.quantity,
      0
    ) + deliveryFee.value
  );
});

const canPlaceOrder = computed(() => {
  return deliveryAddress.value.trim() !== "" && paymentMethod.value !== "";
});

const formatNumber = (number) => {
  return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};

const increaseQuantity = (index) => {
  if (
    cartItems.value[index].quantity < cartItems.value[index].maxOrder &&
    !cartItems.value[index].outOfStock
  ) {
    cartItems.value[index].quantity++;
  }
};

const decreaseQuantity = (index) => {
  if (cartItems.value[index].quantity > cartItems.value[index].minOrder) {
    cartItems.value[index].quantity--;
  }
};


const removeItem = async (index) => {
  try {
    const itemToRemove = cartItems.value[index];
    const response = await axios.delete(`/retailers/cart/remove/${itemToRemove.uuid}`);
    
    if (response.status === 200) {
      cartItems.value.splice(index, 1);
      
      // Show success notification
      toast.success('Item removed from cart successfully');
    }
  } catch (error) {
    // Show error notification
    toast.error(error.response?.data?.message || 'Failed to remove item from cart');
  }
};

// const placeOrder = () => {
//   if (canPlaceOrder.value) {
//     // Implement order placement logic
//     alert("Order placed successfully!");
//   }
// };

function getPrimaryImagePreviewPath(product) {
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
}

const fetchCartItems = async () => {
  loading.value = true;
  try {
    const response = await axios.get("/retailers/cart/items", {
      params: {
        retailer_id: 1,
      },
    });

    cartItems.value = response.data.map((item) => {
      return {
        ...item,
        name: item.inventory.product.name,
        category: item.inventory.product.category,
        supplier: item.supplier.company_name,
        outOfStock: Number(item.inventory.stock_quantity) === 0,
        imageUrl: getPrimaryImagePreviewPath(item.inventory.product),
      };
    });
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to fetch cart items";
  } finally {
    loading.value = false;
  }
};

const placeOrder = async () => {
  // look into this later
  // if (!canPlaceOrder.value) return;

  try {
    // Group cart items by supplier
    const ordersBySupplier = cartItems.value.reduce((acc, item) => {
      if (!acc[item.supplier_id]) {
        acc[item.supplier_id] = {
          retailer_id: item.retailer_id,
          supplier_id: item.supplier_id,
          status: 'pending',
          payment_terms: paymentMethod.value,
          delivery_address: deliveryAddress.value,
          region: 'default', // You might want to get this from user selection
          expected_delivery_date: null, // You might want to add a date picker
          created_by: item.created_by,
          products: []
        };
      }
      
      acc[item.supplier_id].products.push({
        warehouse_inventory_id: item.warehouse_inventory_id,
        quantity: item.quantity,
        unit_price: item.unit_price,
        total_price: item.quantity * item.unit_price
      });
      
      return acc;
    }, {});

    // Create orders for each supplier
    for (const supplierOrder of Object.values(ordersBySupplier)) {
      console.log("Creating order for supplier:", supplierOrder);
      const response = await axios.post('/retailers/create-order', supplierOrder);
      
      if (response.status === 201) {
        // Clear cart items for this supplier
        // This should be done on the server side as wel
        await axios.delete('/retailers/cart/clear', {
          params: {
            retailer_id: supplierOrder.retailer_id,
            supplier_id: supplierOrder.supplier_id
          }
        });
      }
    }

    // Refresh cart items
    await fetchCartItems();

    toast.success('Orders placed successfully!');
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to place orders');
  }
};

onMounted(() => {
  fetchCartItems();
});
</script>

<template>
  <RetailerNavbar />
  <div class="page-container">
    <div class="content-container">
      <h1 class="page-title">
        Cart: <span class="item-count">{{ cartItems.length }} items</span>
      </h1>

      <div class="cart-layout">
        <!-- Left Column - Cart Items -->
        <div class="cart-items-container">
          <div
            v-for="(item, index) in cartItems"
            :key="index"
            class="cart-item"
          >
            <!-- Moved promotion badge here, outside of cart-item-image -->
            <div v-if="item.promotion" class="promotion-badge">
              <div class="discount">{{ item.promotion.discount }}</div>
              <div class="promo-details">Ends {{ item.promotion.endDate }}</div>
            </div>

            <div class="cart-item-image">
              <img :src="item.imageUrl" :alt="item.name" />
            </div>

            <div class="cart-item-details">
              <h3 class="item-name">{{ item.name }}</h3>
              <p class="item-weight"> {{ item?.inventory?.quantity_per_unit }} {{ item?.inventory?.product?.unit_of_measure }}</p>

              <div class="item-tag-container">
                <span class="item-tag">{{ item.category }}</span>
                <span v-if="item.outOfStock" class="item-tag out-of-stock"
                  >Out of stock</span
                >
              </div>

              <!-- Moved item-controls here, above the price -->
              <div class="item-controls-inline">
                <div class="quantity-control">
                  <button
                    @click="decreaseQuantity(index)"
                    class="qty-btn decrease"
                  >
                    −
                  </button>
                  <span class="qty-value">{{ item.quantity }}</span>
                  <button
                    @click="increaseQuantity(index)"
                    class="qty-btn increase"
                  >
                    +
                  </button>
                </div>
              </div>

              <div class="item-price">
                <!-- <span class="price"
                  >Ksh {{ formatNumber(item.price * item.quantity) }}</span
                > -->
                <!-- <span class="price-per-item"
                  >Ksh {{ formatNumber(item.pricePerItem) }} per item</span
                > -->
              </div>

              <div class="product-meta-grid">
                <div class="meta-row">
                  <div class="product-meta-card">
                    <div class="meta-card-icon">
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
                          d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"
                        ></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                      </svg>
                    </div>
                    <div class="meta-card-content">
                      <span class="meta-card-label">Min. Order</span>
                      <span class="meta-card-value"
                        >{{ item.min_order }} items</span
                      >
                    </div>
                  </div>

                  <div class="product-meta-card">
                    <div class="meta-card-icon">
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
                        <rect
                          x="2"
                          y="7"
                          width="20"
                          height="14"
                          rx="2"
                          ry="2"
                        ></rect>
                        <path
                          d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"
                        ></path>
                      </svg>
                    </div>
                    <div class="meta-card-content">
                      <span class="meta-card-label">Max. Order</span>
                      <span class="meta-card-value"
                        >{{ item.max_order }} items</span
                      >
                    </div>
                  </div>
                </div>

                <div class="product-meta-card supplier-card">
                  <div class="meta-card-icon">
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
                        d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"
                      ></path>
                      <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                  </div>
                  <div class="meta-card-content">
                    <span class="meta-card-label">Supplier</span>
                    <span class="meta-card-value">{{ item.supplier }}</span>
                  </div>
                </div>

                <!-- Moved remove button after the supplier-card -->
                <button @click="removeItem(index)" class="remove-btn">
                  Remove item
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column - Order Summary -->
        <div class="order-summary-container">
          <div class="order-summary">
            <div class="summary-section">
              <h3 class="section-title">
                Delivery Address <span class="required">*</span>
              </h3>
              <textarea
                class="address-input"
                placeholder="Enter your delivery address."
                v-model="deliveryAddress"
              ></textarea>
            </div>

            <div class="summary-section">
              <h3 class="section-title">
                Choose how to pay <span class="required">*</span>
              </h3>
              <div class="payment-options">
                <label
                  class="payment-option"
                  :class="{ active: paymentMethod === 'cash' }"
                >
                  <input
                    type="radio"
                    v-model="paymentMethod"
                    value="cash"
                    name="paymentMethod"
                  />
                  <span class="radio-label">Cash on Delivery</span>
                </label>

                <label
                  class="payment-option"
                  :class="{ active: paymentMethod === 'mobile' }"
                >
                  <input
                    type="radio"
                    v-model="paymentMethod"
                    value="mobile"
                    name="paymentMethod"
                  />
                  <span class="radio-label">Mobile Money</span>
                </label>

                <label
                  class="payment-option"
                  :class="{ active: paymentMethod === 'credit' }"
                >
                  <input
                    type="radio"
                    v-model="paymentMethod"
                    value="credit"
                    name="paymentMethod"
                  />
                  <span class="radio-label">Pay with Credit Facility</span>
                </label>
                <div class="balance-info">
                  Available Balance:
                  <span class="balance"
                    >Ksh {{ formatNumber(creditBalance) }}</span
                  >
                </div>
              </div>
            </div>

            <div class="fee-section">
              <div class="fee-row">
                <span class="fee-label">Delivery Fee:</span>
                <span class="fee-value"
                  >Ksh {{ formatNumber(deliveryFee) }}</span
                >
              </div>
            </div>

            <div class="total-section">
              <div class="total-row">
                <span class="total-label">Total Amount</span>
                <span class="total-value"
                  >Ksh {{ formatNumber(totalAmount) }}</span
                >
              </div>
            </div>

            <!--  :disabled="!canPlaceOrder" -->
            <button
              @click="placeOrder"
              class="place-order-btn"
             
            >
              Place Order Request
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
  
<style scoped>
.page-container {
  display: flex;
  justify-content: center;
  background-color: #f5f7fa;
  min-height: 100vh;
  padding: 40px 20px;
}

.content-container {
  width: 1200px;
  max-width: 100%;
  text-align: left;
}

.page-title {
  font-size: 1.8rem;
  color: #37474f;
  margin-bottom: 32px;
  font-weight: 600;
  text-align: left;
}

.item-count {
  font-weight: 500;
  color: #546e7a;
}

.cart-layout {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
}

.cart-items-container {
  flex: 1;
  min-width: 520px;
}

.cart-item {
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
  padding: 24px 24px 3px 24px;
  margin-bottom: 16px;
  display: flex;
  gap: 24px;
  text-align: left;
  position: relative;
}

.cart-item-image {
  width: 120px;
  height: 120px;
  flex-shrink: 0;
  position: relative;
}

.cart-item-image img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  border-radius: 8px;
}

/* Updated promotion badge styling */
.promotion-badge {
  position: absolute;
  top: 0;
  right: 0;
  background: linear-gradient(135deg, #ff6d00, #ff3d00);
  color: white;
  padding: 6px 10px;
  border-radius: 0 0 0 8px;
  z-index: 2;
  box-shadow: 0 2px 8px rgba(255, 109, 0, 0.3);
}

.discount {
  font-size: 0.9rem;
  font-weight: 800;
  letter-spacing: 0.5px;
}

.promo-details {
  font-size: 0.6rem;
  opacity: 0.9;
}

.item-tag.out-of-stock {
  background: linear-gradient(135deg, #f44336, #d32f2f);
  color: white;
  font-size: 0.85rem;
  text-transform: capitalize;
  font-weight: 600;
  padding: 6px 12px;
}

.cart-item-details {
  flex: 1;
  text-align: left;
}

.item-name {
  font-size: 1.2rem;
  font-weight: 600;
  margin: 0 0 6px 0;
  color: #2c3e50;
  text-align: left;
}

.item-weight {
  font-size: 0.95rem;
  color: #4caf50;
  font-weight: 500;
  margin: 0 0 12px 0;
  text-align: left;
}

.item-tag-container {
  display: flex;
  gap: 8px;
  margin-bottom: 12px;
  text-align: left;
}

.item-tag {
  font-size: 0.85rem;
  background-color: #e3f2fd;
  color: #1976d2;
  padding: 6px 12px;
  border-radius: 16px;
  display: inline-block;
  text-align: left;
  font-weight: 600;
}

.item-price {
  margin-bottom: 20px;
  text-align: left;
  position: relative;
  padding-bottom: 12px;
}

.item-price:after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  width: 60px;
  height: 3px;
  background: linear-gradient(90deg, #5b9bd5, #8ab4f8);
  border-radius: 3px;
}

.price {
  font-size: 1.4rem;
  font-weight: 700;
  color: #2c3e50;
  display: block;
  text-align: left;
}

.price-per-item {
  font-size: 0.9rem;
  color: #78909c;
  display: block;
  margin-top: 4px;
  text-align: left;
}

.product-meta-grid {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 16px;
}

.meta-row {
  display: flex;
  gap: 12px;
}

.product-meta-card {
  display: flex;
  align-items: center;
  padding: 14px;
  background-color: #f8fafd;
  border-radius: 8px;
  border: 1px solid #edf2f7;
  transition: all 0.2s ease;
  flex: 1;
}

.supplier-card {
  margin-bottom: 8px;
}

.product-meta-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  border-color: #e1e7ef;
}

.meta-card-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  background-color: #e3f2fd;
  border-radius: 50%;
  margin-right: 10px;
  color: #1976d2;
  flex-shrink: 0;
}

.meta-card-content {
  display: flex;
  flex-direction: column;
}

.meta-card-label {
  font-size: 0.8rem;
  color: #78909c;
  margin-bottom: 4px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.meta-card-value {
  font-size: 1rem;
  color: #37474f;
  font-weight: 600;
}

@media (max-width: 768px) {
  .product-meta-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 480px) {
  .product-meta-grid {
    grid-template-columns: 1fr;
  }
}

.item-controls-inline {
  display: flex;
  align-items: center;
  margin: 16px 0;
}

.quantity-control {
  display: flex;
  align-items: center;
  gap: 8px;
}

.qty-btn {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: none;
  background-color: #f1f3f5;
  color: #37474f;
  font-size: 1.2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background-color 0.2s;
}

.qty-btn:hover {
  background-color: #e9ecef;
}

.qty-value {
  min-width: 28px;
  text-align: center;
  font-weight: 500;
  color: #37474f;
  font-size: 1rem;
}

.remove-btn {
  color: #f44336;
  background: none;
  border: none;
  padding: 6px 12px;
  font-size: 0.9rem;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.2s;
  border-radius: 4px;
  text-align: center;
  white-space: nowrap;
  margin-top: 4px;
  margin-bottom: 4px;
  align-self: flex-start;
}

.remove-btn:hover {
  background-color: #ffebee;
}

.order-summary-container {
  width: 380px;
  flex-shrink: 0;
}

.order-summary {
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
  overflow: hidden;
  text-align: left;
}

.summary-section {
  padding: 24px;
  border-bottom: 1px solid #f1f3f5;
  text-align: left;
}

.section-title {
  font-size: 1.1rem;
  font-weight: 600;
  margin: 0 0 16px 0;
  color: #2c3e50;
  text-align: left;
}

.required {
  color: #f44336;
}

.address-input {
  width: 100%;
  box-sizing: border-box;
  padding: 12px 16px;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  font-size: 1rem;
  color: #37474f;
  transition: border-color 0.2s;
  text-align: left;
  height: 80px;
  resize: vertical;
  font-family: inherit;
}

.address-input::placeholder {
  font-family: inherit;
  color: #9e9e9e;
}

.address-input:focus {
  outline: none;
  border-color: #2196f3;
  box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
}

.payment-options {
  display: flex;
  flex-direction: column;
  gap: 16px;
  text-align: left;
}

.payment-option {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 12px 16px;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
  text-align: left;
}

.payment-option:hover,
.payment-option input[type="radio"]:checked + .radio-label {
  border-color: #bbdefb;
  background-color: #f8fafd;
}

.payment-option.active {
  border-color: #2196f3;
  background-color: #f8fafd;
  box-shadow: 0 0 0 1px #2196f3;
}

.payment-option input[type="radio"] {
  margin-top: 2px;
}

.radio-label {
  font-size: 1rem;
  color: #37474f;
  font-weight: 500;
  text-align: left;
}

.balance-info {
  margin-top: 4px;
  margin-bottom: 12px;
  font-size: 1rem;
  color: #37474f;
  text-align: left;
  font-weight: 600;
}

.balance {
  color: #4caf50;
  font-weight: 700;
  font-size: 1.1rem;
}

.fee-section {
  padding: 16px 24px;
  border-bottom: 1px solid #f1f3f5;
  text-align: left;
}

.fee-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  text-align: left;
}

.fee-label {
  font-size: 1rem;
  color: #37474f;
  text-align: left;
}

.fee-value {
  font-size: 1rem;
  color: #37474f;
  font-weight: 500;
  text-align: right;
}

.total-section {
  padding: 24px;
  background-color: #f8fafd;
  text-align: left;
}

.total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  text-align: left;
}

.total-label {
  font-size: 1.1rem;
  color: #2c3e50;
  font-weight: 500;
  text-align: left;
}

.total-value {
  font-size: 1.3rem;
  color: #2c3e50;
  font-weight: 700;
  text-align: right;
}

.place-order-btn {
  width: 100%;
  padding: 16px;
  background-color: #5b9bd5;
  color: white;
  border: none;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s;
  text-align: center;
}

.place-order-btn:hover:not(:disabled) {
  background-color: #4a89c7;
}

.place-order-btn:disabled {
  background-color: #b0bec5;
  cursor: not-allowed;
}

@media (max-width: 1024px) {
  .cart-layout {
    flex-direction: column;
  }

  .cart-items-container {
    min-width: auto;
  }

  .order-summary-container {
    width: 100%;
  }
}

@media (max-width: 768px) {
  .cart-item {
    flex-direction: column;
    align-items: flex-start;
  }

  .cart-item-details {
    padding-right: 0;
    width: 100%;
  }

  .cart-item-image {
    width: 120px;
    height: 120px;
  }

  .item-controls {
    position: static;
    transform: none;
    flex-direction: row;
    justify-content: space-between;
    width: 100%;
    margin-top: 16px;
  }

  .item-info-row {
    justify-content: flex-start;
  }

  .page-container {
    padding: 20px 12px;
  }
}
</style>