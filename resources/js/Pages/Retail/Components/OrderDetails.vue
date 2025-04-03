<template>
    <div class="page-container">
      <div class="content-container">
        <div class="header-container">
          <h1 class="page-title">Order Details</h1>
          <button class="back-button" @click="goBack">
            <span>←</span> Back to Orders
          </button>
        </div>
  
        <div v-if="order" class="order-details-container">
          <!-- Order Header with Status -->
          <div class="order-header">
            <div class="order-id">Order #{{ order.orderId }}</div>
            <div v-if="order.payStatus" :class="getPayStatusClass(order)">
              {{ order.payStatus }}
            </div>
            <div v-else-if="order.paymentMode" class="payment-mode">
              {{ order.paymentMode }}
            </div>
          </div>
  
          <!-- Order Details Panel - New single container design -->
          <div class="order-details-panel">
            <div class="detail-row">
              <span class="detail-label">Order Date:</span>
              <span class="detail-value">{{ order.date }}</span>
            </div>
  
            <div class="detail-row" v-if="order.expectedDeliveryDate">
              <span class="detail-label">Expected Delivery:</span>
              <span class="detail-value">{{ order.expectedDeliveryDate }}</span>
            </div>
  
            <div class="detail-row" v-if="order.deliveryDate">
              <span class="detail-label">Delivered On:</span>
              <span class="detail-value">{{ order.deliveryDate }}</span>
            </div>
  
            <div class="detail-row" v-if="hasDeliveryTimeframe">
              <span class="detail-label">Delivery Timeframe:</span>
              <span class="detail-value">
                <span :class="getDeliveryTimeframeClass(order)">
                  {{ getDeliveryTimeframe(order) }}
                </span>
              </span>
            </div>
  
            <div class="detail-row">
              <span class="detail-label">Supplier:</span>
              <span class="detail-value">{{ order.supplier }}</span>
            </div>
  
            <div class="detail-row">
              <span class="detail-label">Amount:</span>
              <span class="detail-value">Ksh {{ formatNumber(order.amount) }}</span>
            </div>
  
            <div class="detail-row">
              <span class="detail-label">Ordered By:</span>
              <span class="detail-value">{{ order.orderedBy }}</span>
            </div>
  
            <div class="detail-row" v-if="order.paymentMode">
              <span class="detail-label">Payment Mode:</span>
              <span class="detail-value">{{ order.paymentMode }}</span>
            </div>
            
            <div class="detail-row" v-if="order.payStatus">
              <span class="detail-label">Payment Status:</span>
              <span class="detail-value">
                <span :class="getPayStatusClass(order)">
                  {{ order.payStatus }}
                </span>
              </span>
            </div>
  
            <div class="detail-row" v-if="order.cancellationReason">
              <span class="detail-label">Cancellation Reason:</span>
              <span class="detail-value">{{ order.cancellationReason }}</span>
            </div>
            
            <!-- Dynamically display any other properties we might have missed -->
            <template v-for="(value, key) in getAdditionalProperties()" :key="key">
              <div class="detail-row">
                <span class="detail-label">{{ formatPropertyName(key) }}:</span>
                <span class="detail-value">{{ value }}</span>
              </div>
            </template>
          </div>
  
          <!-- Product Items Table Section -->
          <div class="product-items-section">
            <h2 class="section-title">Order Items</h2>
            
            <!-- Table Controls - Search only -->
            <div class="table-controls">
              <div class="search-container">
                <div class="search-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" color="#000000" fill="none">
                    <path opacity="0.4" fill-rule="evenodd" clip-rule="evenodd" d="M16.7929 16.7929C17.1834 16.4024 17.8166 16.4024 18.2071 16.7929L22.7071 21.2929C23.0976 21.6834 23.0976 22.3166 22.7071 22.7071C22.3166 23.0976 21.6834 23.0976 21.2929 22.7071L16.7929 18.2071C16.4024 17.8166 16.4024 17.1834 16.7929 16.7929Z" fill="currentColor" />
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1 11C1 5.47715 5.47715 1 11 1C16.5228 1 21 5.47715 21 11C21 16.5228 16.5228 21 11 21C5.47715 21 1 16.5228 1 11ZM11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3Z" fill="currentColor" />
                  </svg>
                </div>
                <input type="text" class="search-input" placeholder="Search items..." v-model="itemSearchQuery">
              </div>
            </div>
            
            <!-- Products Table -->
            <div class="table-wrapper">
              <table class="products-table">
                <thead>
                  <tr>
                    <th @click="sortItemsBy('name')" class="sortable">
                      Product <i :class="getItemSortIcon('name')"></i>
                    </th>
                    <th @click="sortItemsBy('availableQty')" class="sortable">
                      Available Qty <i :class="getItemSortIcon('availableQty')"></i>
                    </th>
                    <th @click="sortItemsBy('quantity')" class="sortable">
                      Ordered Qty <i :class="getItemSortIcon('quantity')"></i>
                    </th>
                    <th @click="sortItemsBy('pricePerItem')" class="sortable">
                      Unit Price <i :class="getItemSortIcon('pricePerItem')"></i>
                    </th>
                    <th @click="sortItemsBy('price')" class="sortable">
                      Total Price <i :class="getItemSortIcon('price')"></i>
                    </th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in paginatedItems" :key="index" class="item-row">
                    <td>
                      <div class="product-cell">
                        <div class="product-image">
                          <img :src="item.imageUrl" :alt="item.name">
                        </div>
                        <div class="product-info">
                          <span class="product-name">{{ item.name }}</span>
                          <div class="product-meta">{{ item.type }} · {{ item.weight }}</div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span :class="{'status-badge': true, 'out-of-stock': item.outOfStock, 'in-stock': !item.outOfStock}">
                        {{ item.outOfStock ? 'Out of Stock' : (item.availableQty || 'In Stock') }}
                      </span>
                    </td>
                    <td>
                      <div class="quantity-selector">
                        <button @click="decreaseItemQuantity(item)" 
                                :disabled="item.quantity <= (item.minOrder || 1) || item.outOfStock" 
                                class="quantity-btn">-</button>
                        <span class="quantity">{{ item.quantity }}</span>
                        <button @click="increaseItemQuantity(item)" 
                                :disabled="item.quantity >= (item.maxOrder || 50) || item.outOfStock"
                                class="quantity-btn">+</button>
                      </div>
                    </td>
                    <td>Ksh {{ formatNumber(item.pricePerItem) }}</td>
                    <td>Ksh {{ formatNumber(item.price) }}</td>
                    <td>
                      <button @click="showDeleteDialog(index)" class="delete-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M3 6h18"></path>
                          <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                          <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                        </svg>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            
            <!-- Pagination for Items Table -->
            <div class="pagination-controls">
              <div class="per-page">
                <span>Show</span>
                <select v-model="itemsPerPage" @change="resetItemsPagination" class="per-page-select">
                  <option v-for="option in perPageOptions" :key="option" :value="option">
                    {{ option }}
                  </option>
                </select>
                <span>per page</span>
              </div>
              <div class="pagination-buttons">
                <button class="pagination-btn" :disabled="currentItemsPage === 1" @click="prevItemsPage">
                  Previous
                </button>
                <div class="page-numbers">
                  <span v-for="page in totalItemsPages" :key="page"
                      :class="['page-number', { active: currentItemsPage === page }]"
                      @click="goToItemsPage(page)">
                    {{ page }}
                  </span>
                </div>
                <button class="pagination-btn" :disabled="currentItemsPage === totalItemsPages" @click="nextItemsPage">
                  Next
                </button>
              </div>
            </div>
          </div>
  
          <!-- Order Actions -->
          <div class="order-actions">
            <button class="action-button payment" v-if="showPayButton">Pay Now</button>
            <button class="action-button secondary" v-if="order.payStatus === 'Not Paid'">Send Reminder</button>
            <button class="action-button confirm" @click="showConfirmOrderDialog">Confirm Order</button>
            <button class="action-button delivered" @click="showMarkAsDeliveredDialog">Mark as Delivered</button>
            <button class="action-button danger" v-if="canCancelOrder" @click="showCancelOrderDialog">Cancel Order</button>
          </div>
        </div>
  
        <div v-else class="loading-state">Loading order details...</div>
      </div>
    </div>
  
    <!-- Custom Dialog Component -->
    <div v-if="dialog.show" class="dialog-overlay" @click="closeDialog">
      <div class="dialog-container" @click.stop>
        <div class="dialog-header">
          <h3 class="dialog-title">{{ dialog.title }}</h3>
          <button class="dialog-close-btn" @click="closeDialog">×</button>
        </div>
        <div class="dialog-content">
          <p>{{ dialog.message }}</p>
        </div>
        <div class="dialog-actions">
          <button class="dialog-btn secondary" @click="closeDialog">Cancel</button>
          <button :class="['dialog-btn', dialog.buttonType]" @click="confirmDialog">{{ dialog.confirmText }}</button>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'OrderView',
    props: {
      id: {
        type: String,
        required: true
      },
      orderData: {
        type: String,
        default: null
      }
    },
    data() {
      return {
        order: null,
        orderItems: [
          {
            name: 'All Purpose Fertilizer 14 Kgs',
            weight: '14 Kilograms',
            type: 'Fertilizer',
            price: 3450,
            pricePerItem: 3450,
            quantity: 1,
            availableQty: 25,
            minOrder: 1,
            maxOrder: 50,
            maxOrderUnit: 'items',
            supplier: 'Aden Agri Supplies',
            promotion: null,
            outOfStock: false,
            imageUrl: 'https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg'
          },
          {
            name: 'Starter Fertilizer 20 Kgs',
            weight: '20 Kilograms',
            type: 'Fertilizer',
            price: 3450,
            pricePerItem: 3450,
            quantity: 1,
            availableQty: 15,
            minOrder: 1,
            maxOrder: 50,
            maxOrderUnit: 'item',
            supplier: 'Aden Agri Supplies',
            promotion: {
              discount: '50% OFF',
              endDate: 'Mar 12, 2025'
            },
            outOfStock: false,
            imageUrl: 'https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg'
          },
          {
            name: 'DAP Fertilizer 46 Kgs',
            weight: '46 Kilograms',
            type: 'Fertilizer',
            price: 6900,
            pricePerItem: 3450,
            quantity: 2,
            availableQty: 0,
            minOrder: 1,
            maxOrder: 50,
            maxOrderUnit: 'items',
            supplier: 'Green Agriculture Ltd',
            promotion: null,
            outOfStock: true,
            imageUrl: 'https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg'
          },
          {
            name: 'Organic Compost 25 Kgs',
            weight: '25 Kilograms',
            type: 'Soil Amendment',
            price: 4800,
            pricePerItem: 2400,
            quantity: 2,
            availableQty: 32,
            minOrder: 1,
            maxOrder: 30,
            maxOrderUnit: 'items',
            supplier: 'Green Agriculture Ltd',
            promotion: null,
            outOfStock: false,
            imageUrl: 'https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg'
          },
          {
            name: 'Phosphate Fertilizer 15 Kgs',
            weight: '15 Kilograms',
            type: 'Fertilizer',
            price: 2950,
            pricePerItem: 2950,
            quantity: 1,
            availableQty: 8,
            minOrder: 1,
            maxOrder: 40,
            maxOrderUnit: 'items',
            supplier: 'Farm Equipment Ltd',
            promotion: {
              discount: '25% OFF',
              endDate: 'Mar 05, 2025'
            },
            outOfStock: false,
            imageUrl: 'https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg'
          }
        ],
        itemSearchQuery: '',
        itemSortKey: 'name',
        itemSortDir: 'asc',
        currentItemsPage: 1,
        itemsPerPage: 10,
        perPageOptions: [5, 10, 20, 50],
        // Dialog state
        dialog: {
          show: false,
          title: '',
          message: '',
          confirmText: 'Confirm',
          action: null,
          data: null
        }
      };
    },
    created() {
      console.log('Route params received:', this.$route.params);
      console.log('Route query received:', this.$route.query);
      
      // Parse the order data from the route query if available
      if (this.$route.query.orderData) {
        try {
          this.order = JSON.parse(this.$route.query.orderData);
          console.log('Order data loaded from query:', this.order);
        } catch (error) {
          console.error('Error parsing order data from query:', error);
        }
      } else if (this.orderData) {
        // Fallback to props if available
        try {
          this.order = JSON.parse(this.orderData);
          console.log('Order data loaded from props:', this.order);
        } catch (error) {
          console.error('Error parsing order data from props:', error);
        }
      } else {
        // Fetch order data using ID as a last resort
        console.log('No order data received, would fetch order data for ID:', this.id);
        
        // For demo purposes, just set a loading state
        setTimeout(() => {
          this.order = {
            orderId: this.id,
            date: 'Feb 25, 2025',
            supplier: 'Sample Supplier',
            amount: 12345,
            orderedBy: 'Demo User'
          };
          console.log('Fallback data loaded instead:', this.order);
        }, 1000);
      }
    },
    computed: {
      showPayButton() {
        return this.order && 
          (this.order.payStatus === 'Not Paid' || 
          (this.order.paymentMode && this.order.paymentMode !== 'Paid'));
      },
      hasDeliveryTimeframe() {
        return this.order && this.order.date && this.order.expectedDeliveryDate;
      },
      canCancelOrder() {
        // Only allow cancellation for requests or confirmed orders that are not yet delivered
        return this.order && 
          (this.order.orderId.startsWith('RQ-') || this.order.orderId.startsWith('CN-')) && 
          !this.order.deliveryDate;
      },
      filteredItems() {
        let filtered = [...this.orderItems];
        
        // Filter by search query
        if (this.itemSearchQuery.trim() !== '') {
          const query = this.itemSearchQuery.toLowerCase();
          filtered = filtered.filter(item => {
            // Search through relevant properties of the item - still including type and weight in search
            return ['name', 'type', 'weight', 'quantity'].some(prop => {
              // Convert value to string and check if it includes the query
              if (item[prop] === null || item[prop] === undefined) return false;
              return item[prop].toString().toLowerCase().includes(query);
            });
          });
        }
        
        // Sort items
        filtered.sort((a, b) => {
          let modifier = this.itemSortDir === 'asc' ? 1 : -1;
          let aValue = a[this.itemSortKey];
          let bValue = b[this.itemSortKey];
          
          if (typeof aValue === 'number' && typeof bValue === 'number') {
            return aValue < bValue ? -1 * modifier : 1 * modifier;
          } else {
            return aValue.toString().localeCompare(bValue.toString()) * modifier;
          }
        });
        
        return filtered;
      },
      paginatedItems() {
        const start = (this.currentItemsPage - 1) * this.itemsPerPage;
        const end = start + this.itemsPerPage;
        return this.filteredItems.slice(start, end);
      },
      totalItemsPages() {
        return Math.ceil(this.filteredItems.length / this.itemsPerPage) || 1;
      }
    },
    methods: {
      formatNumber(value) {
        return value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      },
      getPayStatusClass(order) {
        return {
          'pay-status-badge': true,
          'pay-status-paid': order.payStatus === 'Paid',
          'pay-status-not-paid': order.payStatus === 'Not Paid'
        };
      },
      getDeliveryTimeframe(order) {
        if (!order.date || !order.expectedDeliveryDate) return '';
        
        const days = this.calculateDaysBetween(order.date, order.expectedDeliveryDate);
        return `${days} ${days === 1 ? 'Day' : 'Days'}`;
      },
      getDeliveryTimeframeClass(order) {
        if (!order.date || !order.expectedDeliveryDate) return '';
        
        const days = this.calculateDaysBetween(order.date, order.expectedDeliveryDate);
        return {
          'delivery-timeframe-badge': true,
          'delivery-timeframe-danger': days <= 0,
          'delivery-timeframe-warning': days > 0 && days <= 2,
          'delivery-timeframe-success': days > 2 && days <= 5,
          'delivery-timeframe-info': days > 5
        };
      },
      calculateDaysBetween(startDateStr, endDateStr) {
        // Parse date strings (assuming format like "Feb 26, 2025")
        const parseDate = (dateStr) => {
          const [month, day, year] = dateStr.split(' ');
          const monthMap = {
            'Jan': 0, 'Feb': 1, 'Mar': 2, 'Apr': 3, 'May': 4, 'Jun': 5,
            'Jul': 6, 'Aug': 7, 'Sep': 8, 'Oct': 9, 'Nov': 10, 'Dec': 11
          };
          
          // Remove comma from day
          const cleanDay = day.replace(',', '');
          
          return new Date(year, monthMap[month], parseInt(cleanDay));
        };
        
        const startDate = parseDate(startDateStr);
        const endDate = parseDate(endDateStr);
        
        // Calculate difference in days
        const diffTime = endDate - startDate;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        return diffDays;
      },
      goBack() {
        this.$router.go(-1);
      },
      
      // Dialog Methods
      showDialog(title, message, confirmText, action, buttonType = 'primary', data = null) {
        this.dialog = {
          show: true,
          title,
          message,
          confirmText,
          action,
          buttonType,
          data
        };
      },
      closeDialog() {
        this.dialog.show = false;
      },
      confirmDialog() {
        if (this.dialog.action && typeof this[this.dialog.action] === 'function') {
          this[this.dialog.action](this.dialog.data);
        }
        this.closeDialog();
      },
  
      // Item delete dialog
      showDeleteDialog(index) {
        const item = this.paginatedItems[index];
        this.showDialog(
          'Delete Item', 
          `Are you sure you want to remove ${item.name} from your order?`,
          'Delete',
          'deleteItem',
          'danger',
          index
        );
      },
      
      // Action button dialogs
      showConfirmOrderDialog() {
        this.showDialog(
          'Confirm Order',
          'Are you sure you want to confirm this order? This action cannot be undone.',
          'Confirm',
          'confirmOrder',
          'confirm'
        );
      },
      showMarkAsDeliveredDialog() {
        this.showDialog(
          'Mark as Delivered',
          'Are you sure you want to mark this order as delivered? This action cannot be undone.',
          'Mark as Delivered',
          'markAsDelivered',
          'delivered'
        );
      },
      showCancelOrderDialog() {
        this.showDialog(
          'Cancel Order',
          'Are you sure you want to cancel this order? This action cannot be undone.',
          'Cancel Order',
          'cancelOrder',
          'danger'
        );
      },
      
      // Action handlers
      confirmOrder() {
        console.log('Order confirmed');
        // Implement confirmation logic here
      },
      markAsDelivered() {
        console.log('Order marked as delivered');
        // Implement delivery marking logic here
      },
      cancelOrder() {
        console.log('Order cancelled');
        // Implement cancellation logic here
      },
      
      // This method returns any properties we haven't explicitly handled
      getAdditionalProperties() {
        if (!this.order) return {};
        
        // List of properties we already display explicitly
        const handledProps = [
          'orderId', 'date', 'expectedDeliveryDate', 'deliveryDate', 
          'supplier', 'amount', 'orderedBy', 'paymentMode', 
          'payStatus', 'cancellationReason'
        ];
        
        // Create an object with only the properties we haven't explicitly handled
        const additionalProps = {};
        Object.keys(this.order).forEach(key => {
          if (!handledProps.includes(key) && this.order[key] !== null && this.order[key] !== undefined) {
            additionalProps[key] = this.order[key];
          }
        });
        
        return additionalProps;
      },
      
      // Format property names for display (convert camelCase to Title Case)
      formatPropertyName(key) {
        // Convert camelCase to separate words
        const words = key.replace(/([A-Z])/g, ' $1').toLowerCase();
        
        // Capitalize first letter of each word
        return words.split(' ')
          .map(word => word.charAt(0).toUpperCase() + word.slice(1))
          .join(' ');
      },
      
      // Product items table methods
      sortItemsBy(key) {
        if (this.itemSortKey === key) {
          this.itemSortDir = this.itemSortDir === 'asc' ? 'desc' : 'asc';
        } else {
          this.itemSortKey = key;
          this.itemSortDir = 'asc';
        }
      },
      getItemSortIcon(key) {
        if (this.itemSortKey !== key) return 'sort-icon sort-none';
        return this.itemSortDir === 'asc' ? 'sort-icon sort-asc' : 'sort-icon sort-desc';
      },
      goToItemsPage(page) {
        this.currentItemsPage = page;
      },
      nextItemsPage() {
        if (this.currentItemsPage < this.totalItemsPages) {
          this.currentItemsPage++;
        }
      },
      prevItemsPage() {
        if (this.currentItemsPage > 1) {
          this.currentItemsPage--;
        }
      },
      resetItemsPagination() {
        this.currentItemsPage = 1;
      },
      viewItemDetails(item) {
        console.log('View item details:', item);
        // Implement item details view if needed
        alert(`Viewing details for ${item.name}`);
      },
      
      // Item quantity control methods
      increaseItemQuantity(item) {
        if (item.quantity < (item.maxOrder || 50) && !item.outOfStock) {
          item.quantity++;
          this.updateItemTotal(item);
          console.log(`Increased quantity for ${item.name} to ${item.quantity}`);
        }
      },
      
      decreaseItemQuantity(item) {
        if (item.quantity > (item.minOrder || 1) && !item.outOfStock) {
          item.quantity--;
          this.updateItemTotal(item);
          console.log(`Decreased quantity for ${item.name} to ${item.quantity}`);
        }
      },
      
      deleteItem(index) {
        // Get the actual item from the paginated array
        const item = this.paginatedItems[index];
        
        // Find the actual index in the original array
        const originalIndex = this.orderItems.findIndex(i => 
          i.name === item.name && i.weight === item.weight && i.type === item.type
        );
        
        if (originalIndex !== -1) {
          // Remove the item
          this.orderItems.splice(originalIndex, 1);
          
          // Update order total
          if (this.order) {
            this.order.amount = this.orderItems.reduce((total, item) => total + item.price, 0);
          }
          
          console.log(`Deleted item: ${item.name}`);
          
          // If we deleted the last item on the page, go to previous page
          if (this.paginatedItems.length === 1 && this.currentItemsPage > 1) {
            this.currentItemsPage--;
          }
        }
      },
      
      updateItemTotal(item) {
        // Update item total price based on quantity
        item.price = item.pricePerItem * item.quantity;
        
        // If we need to update the order total
        if (this.order) {
          this.order.amount = this.orderItems.reduce((total, item) => total + item.price, 0);
        }
      }
    }
  };
  </script>
  
  <style scoped>
  .page-container {
    display: flex;
    justify-content: center;
    background-color: #f5f7fa;
    min-height: 100vh;
    padding: 40px 20px;
    font-family: 'Inter', 'Segoe UI', Roboto, sans-serif;
  }
  
  .content-container {
    width: 1200px;
    max-width: 100%;
  }
  
  .header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
  }
  
  .page-title {
    font-size: 1.8rem;
    color: #1e293b;
    font-weight: 600;
    margin: 0;
  }
  
  .back-button {
    display: flex;
    align-items: center;
    padding: 8px 16px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 0.95rem;
    font-weight: 500;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s;
  }
  
  .back-button:hover {
    background-color: #f8fafc;
    border-color: #cbd5e1;
  }
  
  .back-button span {
    margin-right: 6px;
  }
  
  .order-details-container {
    background-color: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    padding: 24px;
  }
  
  .order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e5e7eb;
  }
  
  .order-id {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1e293b;
  }
  
  .pay-status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 0.9rem;
    font-weight: 500;
    text-align: center;
  }
  
  .pay-status-paid {
    background-color: #D1FAE5;
    color: #047857;
  }
  
  .pay-status-not-paid {
    background-color: #FECACA;
    color: #B91C1C;
  }
  
  .payment-mode {
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 0.9rem;
    font-weight: 500;
    background-color: #f1f5f9;
    color: #64748b;
  }
  
  /* Updated Order Details Panel */
  .order-details-panel {
    background-color: #f8fafc;
    border-radius: 8px;
    padding: 24px 28px;
    margin-bottom: 32px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 16px 40px;
    text-align: left;
    box-shadow: none;
  }
  
  .detail-row {
    display: flex;
    flex-direction: column;
    padding: 8px 0;
  }
  
  .detail-label {
    font-size: 0.9rem;
    color: #64748b;
    margin-bottom: 4px;
  }
  
  .detail-value {
    font-size: 1.05rem;
    font-weight: 600;
    color: #1e293b;
  }
  
  /* Product Items Section */
  .product-items-section {
    margin-top: 32px;
    margin-bottom: 32px;
    border-top: 1px solid #e5e7eb;
    padding-top: 24px;
  }
  
  .section-title {
    font-size: 1.3rem;
    color: #1e293b;
    font-weight: 600;
    margin-bottom: 20px;
    text-align: left;
  }
  
  /* Table Controls */
  .table-controls {
    display: flex;
    margin-bottom: 16px;
  }
  
  .search-container {
    position: relative;
    width: 300px;
    max-width: 100%;
  }
  
  .search-input {
    width: 100%;
    box-sizing: border-box;
    padding: 10px 16px 10px 40px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 0.95rem;
    color: #1e293b;
    background-color: white;
    transition: border-color 0.2s;
  }
  
  .search-input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
  }
  
  .search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
  }
  
  /* Products Table */
  .table-wrapper {
    background-color: white;
    border-radius: 8px;
    overflow-x: auto;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    margin-bottom: 16px;
  }
  
  .products-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.95rem;
  }
  
  .products-table th {
    background-color: #f8fafc;
    color: #64748b;
    font-weight: 600;
    text-align: left;
    padding: 14px 15px;
    border-bottom: 1px solid #e5e7eb;
    white-space: nowrap;
  }
  
  .products-table th.sortable {
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
    content: '⇵';
    opacity: 0.3;
    font-size: 12px;
  }
  
  .sort-asc::after {
    content: '↑';
    color: #2563eb;
    font-size: 12px;
  }
  
  .sort-desc::after {
    content: '↓';
    color: #2563eb;
    font-size: 12px;
  }
  
  .products-table td {
    padding: 14px 15px;
    border-bottom: 1px solid #e5e7eb;
    color: #334155;
    text-align: left;
  }
  
  .products-table tr:last-child td {
    border-bottom: none;
  }
  
  .products-table tr:hover td {
    background-color: #f8fafc;
  }
  
  .item-row {
    transition: background-color 0.2s ease;
  }
  
  .item-row:hover {
    background-color: #f0f5ff;
  }
  
  /* Delete button styling */
  .delete-btn {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: none;
    background-color: rgba(239, 68, 68, 0.05);
    color: #ef4444;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
  }
  
  .delete-btn:hover {
    background-color: rgba(239, 68, 68, 0.1);
    transform: scale(1.05);
  }
  
  .delete-btn:active {
    transform: scale(0.95);
  }
  
  /* Quantity controls styling borrowed from ProductCard.vue */
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
    min-width: 24px;
    text-align: center;
  }
  
  .product-cell {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  
  .product-image {
    width: 48px;
    height: 48px;
    border-radius: 4px;
    overflow: hidden;
    flex-shrink: 0;
  }
  
  .product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .product-info {
    display: flex;
    flex-direction: column;
  }
  
  .product-name {
    font-weight: 500;
    color: #334155;
  }
  
  .product-meta {
    font-size: 0.85rem;
    color: #64748b;
    margin-top: 2px;
  }
  
  /* Status badges */
  .status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 2px;
    font-size: 0.85rem;
    font-weight: 500;
    text-align: center;
  }
  
  .status-badge.out-of-stock {
    background-color: #FECACA;
    color: #B91C1C;
  }
  
  .status-badge.promotion {
    background-color: #FEF3C7;
    color: #D97706;
  }
  
  .status-badge.in-stock {
    background-color: #D1FAE5;
    color: #047857;
  }
  
  /* Delivery Timeframe Badges */
  .delivery-timeframe-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 2px;
    font-size: 0.85rem;
    font-weight: 500;
    text-align: center;
  }
  
  .delivery-timeframe-danger {
    background-color: #FECACA;
    color: #B91C1C;
  }
  
  .delivery-timeframe-warning {
    background-color: #FEF3C7;
    color: #D97706;
  }
  
  .delivery-timeframe-success {
    background-color: #D1FAE5;
    color: #047857;
  }
  
  .delivery-timeframe-info {
    background-color: #DBEAFE;
    color: #1D4ED8;
  }
  
  /* Pagination */
  .pagination-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 16px;
  }
  
  .per-page {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #64748b;
    font-size: 0.95rem;
  }
  
  .per-page-select {
    padding: 8px 16px;
    padding-right: 30px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 0.9rem;
    background-color: white;
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
  }
  
  .pagination-buttons {
    display: flex;
    gap: 8px;
    align-items: center;
  }
  
  .pagination-btn {
    padding: 8px 16px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    background-color: white;
    color: #334155;
    font-size: 0.95rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
  }
  
  .pagination-btn:hover:not(:disabled) {
    background-color: #f8fafc;
    border-color: #cbd5e1;
  }
  
  .pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
  
  .page-numbers {
    display: flex;
    gap: 4px;
    margin: 0 8px;
  }
  
  .page-number {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    font-size: 0.9rem;
    cursor: pointer;
    color: #64748b;
  }
  
  .page-number:hover {
    background-color: #f1f5f9;
  }
  
  .page-number.active {
    background-color: #2563eb;
    color: white;
    font-weight: 600;
  }
  
  /* Order Actions */
  .order-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 32px;
  }
  
  .action-button {
    padding: 10px 16px;
    border-radius: 6px;
    font-size: 0.95rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
  }
  
  .action-button.payment {
    background-color: #2563eb;
    color: white;
  }
  
  .action-button.payment:hover {
    background-color: #1d4ed8;
  }
  
  .action-button.confirm {
    background-color: #0891b2;
    color: white;
  }
  
  .action-button.confirm:hover {
    background-color: #0e7490;
  }
  
  .action-button.delivered {
    background-color: #059669;
    color: white;
  }
  
  .action-button.delivered:hover {
    background-color: #047857;
  }
  
  .action-button.secondary {
    background-color: white;
    color: #1e293b;
    border: 1px solid #e5e7eb;
  }
  
  .action-button.secondary:hover {
    background-color: #f8fafc;
    border-color: #cbd5e1;
  }
  
  .action-button.danger {
    background-color: white;
    color: #ef4444;
    border: 1px solid #fecaca;
  }
  
  .action-button.danger:hover {
    background-color: #fef2f2;
    border-color: #f87171;
  }
  
  .loading-state {
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    padding: 48px;
    font-size: 1.1rem;
    color: #64748b;
  }
  
  /* Custom Dialog Styles */
  .dialog-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    animation: fadeIn 0.2s ease;
  }
  
  .dialog-container {
    width: 100%;
    max-width: 450px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    animation: slideIn 0.2s ease;
  }
  
  .dialog-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 24px;
    border-bottom: 1px solid #e5e7eb;
  }
  
  .dialog-title {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
    color: #1e293b;
  }
  
  .dialog-close-btn {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: none;
    background-color: #f1f5f9;
    color: #64748b;
    font-size: 1.2rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
  }
  
  .dialog-close-btn:hover {
    background-color: #e2e8f0;
    color: #334155;
  }
  
  .dialog-content {
    padding: 24px;
    color: #475569;
    font-size: 1rem;
    line-height: 1.5;
  }
  
  .dialog-actions {
    display: flex;
    justify-content: flex-end;
    padding: 16px 24px;
    border-top: 1px solid #e5e7eb;
    gap: 12px;
  }
  
  .dialog-btn {
    padding: 10px 16px;
    border-radius: 6px;
    font-size: 0.95rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
  }
  
  .dialog-btn.primary {
    background-color: #2563eb;
    color: white;
  }
  
  .dialog-btn.primary:hover {
    background-color: #1d4ed8;
  }
  
  .dialog-btn.confirm {
    background-color: #0891b2;
    color: white;
  }
  
  .dialog-btn.confirm:hover {
    background-color: #0e7490;
  }
  
  .dialog-btn.delivered {
    background-color: #059669;
    color: white;
  }
  
  .dialog-btn.delivered:hover {
    background-color: #047857;
  }
  
  .dialog-btn.payment {
    background-color: #2563eb;
    color: white;
  }
  
  .dialog-btn.payment:hover {
    background-color: #1d4ed8;
  }
  
  .dialog-btn.secondary {
    background-color: white;
    color: #1e293b;
    border: 1px solid #e5e7eb;
  }
  
  .dialog-btn.secondary:hover {
    background-color: #f8fafc;
    border-color: #cbd5e1;
  }
  
  .dialog-btn.danger {
    background-color: #ef4444;
    color: white;
  }
  
  .dialog-btn.danger:hover {
    background-color: #dc2626;
  }
  
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  
  @keyframes slideIn {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }
  
  @media (max-width: 768px) {
    .header-container {
      flex-direction: column;
      align-items: flex-start;
      gap: 16px;
    }
    
    .order-details-panel {
      grid-template-columns: 1fr;
      padding: 20px;
    }
    
    .order-actions {
      flex-direction: column;
    }
    
    .pagination-controls {
      flex-direction: column;
      gap: 16px;
    }
    
    .products-table {
      font-size: 0.9rem;
    }
  }
  
  @media (max-width: 640px) {
    .page-numbers {
      display: none;
    }
    
    .product-cell {
      flex-direction: row;
      align-items: center;
      gap: 10px;
    }
    
    .product-image {
      width: 36px;
      height: 36px;
    }
  }
  </style>