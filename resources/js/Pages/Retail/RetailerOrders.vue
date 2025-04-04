<template>
  <RetailerNavbar />
  <div class="page-container">
    <div class="content-container">
      <h1 class="page-title">Orders</h1>
      
      <!-- Tabs -->
      <div class="tabs-container">
        <ul class="tabs">
          <li v-for="tab in tabs" :key="tab.id" 
              :class="['tab', { active: activeTab === tab.id }]"
              @click="setActiveTab(tab.id)">
            {{ tab.name }}
            <span class="tab-count" v-if="tab.count">{{ tab.count }}</span>
          </li>
        </ul>
      </div>
      
      <!-- Table Controls - Search only -->
      <div class="table-controls">
        <div class="search-container">
          <div class="search-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" color="#000000" fill="none">
              <path opacity="0.4" fill-rule="evenodd" clip-rule="evenodd" d="M16.7929 16.7929C17.1834 16.4024 17.8166 16.4024 18.2071 16.7929L22.7071 21.2929C23.0976 21.6834 23.0976 22.3166 22.7071 22.7071C22.3166 23.0976 21.6834 23.0976 21.2929 22.7071L16.7929 18.2071C16.4024 17.8166 16.4024 17.1834 16.7929 16.7929Z" fill="currentColor" />
              <path fill-rule="evenodd" clip-rule="evenodd" d="M1 11C1 5.47715 5.47715 1 11 1C16.5228 1 21 5.47715 21 11C21 16.5228 16.5228 21 11 21C5.47715 21 1 16.5228 1 11ZM11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3Z" fill="currentColor" />
            </svg>
          </div>
          <input type="text" class="search-input" placeholder="Search..." v-model="searchQuery">
        </div>
      </div>
      
      <!-- Orders Table -->
      <div class="table-wrapper">
        <table class="orders-table">
          <thead>
            <tr>
              <th @click="sortBy('orderId')" class="sortable">
                Order# <i :class="getSortIcon('orderId')"></i>
              </th>
              <th @click="sortBy('date')" class="sortable">
                Order Date <i :class="getSortIcon('date')"></i>
              </th>
              <th v-if="activeTab === 'confirmed'" @click="sortBy('expectedDeliveryDate')" class="sortable">
                Expected Delivery Date <i :class="getSortIcon('expectedDeliveryDate')"></i>
              </th>
              <th v-if="activeTab === 'delivered'" @click="sortBy('expectedDeliveryDate')" class="sortable">
                Expected Delivery Date <i :class="getSortIcon('expectedDeliveryDate')"></i>
              </th>
              <th v-if="activeTab === 'confirmed'" @click="sortBy('expectedDeliveryDate')" class="sortable">
                Delivery Timeframe <i :class="getSortIcon('expectedDeliveryDate')"></i>
              </th>
              <th v-if="activeTab === 'delivered'" @click="sortBy('deliveryDate')" class="sortable">
                Delivered Date <i :class="getSortIcon('deliveryDate')"></i>
              </th>
              <th @click="sortBy('supplier')" class="sortable">
                Supplier <i :class="getSortIcon('supplier')"></i>
              </th>
              <th v-if="activeTab === 'requests' || activeTab === 'cancelled'" @click="sortBy('paymentMode')" class="sortable">
                Payment Mode <i :class="getSortIcon('paymentMode')"></i>
              </th>
              <th v-if="activeTab === 'confirmed' || activeTab === 'delivered'" @click="sortBy('payStatus')" class="sortable">
                Pay Status <i :class="getSortIcon('payStatus')"></i>
              </th>
              <th @click="sortBy('amount')" class="sortable">
                Amount <i :class="getSortIcon('amount')"></i>
              </th>
              <th v-if="activeTab === 'requests'" @click="sortBy('orderedBy')" class="sortable">
                Requested By <i :class="getSortIcon('orderedBy')"></i>
              </th>
              <th v-if="activeTab !== 'requests'" @click="sortBy('orderedBy')" class="sortable">
                Ordered By <i :class="getSortIcon('orderedBy')"></i>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="orders.length === 0">
              <td colspan="11" class="empty-state">
                No orders available
              </td>
            </tr>
            <tr v-for="order in orders" :key="order.id" @click="viewOrderDetails(order)" class="order-row">
              <td>{{order?.order_ref}}</td>
              <td>{{ formatDateOnly(order?.created_at) }}</td>
              <!-- <td v-if="activeTab === 'confirmed'">{{ order.expectedDeliveryDate }}</td>
              <td v-if="activeTab === 'delivered'">{{ order.expectedDeliveryDate }}</td>
              <td v-if="activeTab === 'confirmed'">
                <span :class="getDeliveryTimeframeClass(order)">
                  {{ getDeliveryTimeframe(order) }}
                </span>
              </td> -->
              <!-- <td v-if="activeTab === 'delivered'">{{ order.deliveryDate }}</td> -->
              <td>{{ order?.supplier?.company_name }}</td>
              <td v-if="activeTab === 'all' || activeTab === 'requests' || activeTab === 'cancelled'">{{ order?.payment_terms }}</td>
              <!-- <td v-if="activeTab === 'confirmed' || activeTab === 'delivered'">
                <span :class="getPayStatusClass(order)">
                  {{ order.payStatus }}
                </span>
              </td> -->
              <td>Ksh {{ formatNumber(order?.total_price) }}</td>
              <td>{{ order?.creator?.name }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div class="pagination-controls">
        <div class="per-page">
          <span>Show</span>
          <select v-model="perPage" @change="resetPagination" class="per-page-select">
            <option v-for="option in perPageOptions" :key="option" :value="option">
              {{ option }}
            </option>
          </select>
          <span>per page</span>
        </div>
        <div class="pagination-buttons">
          <button class="pagination-btn" :disabled="currentPage === 1" @click="prevPage">
            Previous
          </button>
          <div class="page-numbers">
            <span v-for="page in totalPages" :key="page"
                 :class="['page-number', { active: currentPage === page }]"
                 @click="goToPage(page)">
              {{ page }}
            </span>
          </div>
          <button class="pagination-btn" :disabled="currentPage === totalPages" @click="nextPage">
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import RetailerNavbar from "./Components/RetailerNavbar.vue";
import { formatNumber, formatDateOnly } from "@/utils/formatters";

export default {
  name: 'RetailerOrdersPage',
  components: {
    RetailerNavbar
  },
  data() {
    return {
      activeTab: 'REQUESTED',
      orders: [],
      tabs: [
        { id: 'REQUESTED', name: 'Requests', count: 2 },
        { id: 'confirmed', name: 'Confirmed', count: 10 },
        { id: 'delivered', name: 'Delivered', count: 32 },
        { id: 'cancelled', name: 'Cancelled', count: 4 },
      ],
      searchQuery: '',
      sortKey: 'orderId',
      sortDir: 'asc',
      currentPage: 1,
      totalPages: 0,
      perPage: 10,
      perPageOptions: [10, 20, 50, 100],
      formatNumber,
      formatDateOnly
    };
  },
  computed: {
    filteredOrders() {
      let filtered = [];
      
      // Select the appropriate orders array based on the active tab
      switch(this.activeTab) {
        case 'requests':
          filtered = [...this.requestsOrders];
          break;
        case 'confirmed':
          filtered = [...this.confirmedOrders];
          break;
        case 'delivered':
          filtered = [...this.deliveredOrders];
          break;
        case 'cancelled':
          filtered = [...this.cancelledOrders];
          break;
      }
      
      // Filter by search query
      if (this.searchQuery.trim() !== '') {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(order => {
          // Search through all properties of the order
          return Object.values(order).some(value => {
            // Convert value to string and check if it includes the query
            if (value === null || value === undefined) return false;
            return value.toString().toLowerCase().includes(query);
          });
        });
      }
      
      // Sort "Not Paid" orders to the top for confirmed and delivered tabs
      if (this.activeTab === 'confirmed' || this.activeTab === 'delivered') {
        filtered.sort((a, b) => {
          // First sort by payment status (Not Paid comes first)
          if (a.payStatus === 'Not Paid' && b.payStatus !== 'Not Paid') {
            return -1;
          } else if (a.payStatus !== 'Not Paid' && b.payStatus === 'Not Paid') {
            return 1;
          }
          
          // If payment status is the same, then sort by the current sort key
          let modifier = this.sortDir === 'asc' ? 1 : -1;
          let aValue = a[this.sortKey];
          let bValue = b[this.sortKey];
          
          if (typeof aValue === 'number' && typeof bValue === 'number') {
            return aValue < bValue ? -1 * modifier : 1 * modifier;
          } else {
            return aValue.toString().localeCompare(bValue.toString()) * modifier;
          }
        });
      } else {
        // Default sorting for other tabs
        filtered.sort((a, b) => {
          let modifier = this.sortDir === 'asc' ? 1 : -1;
          let aValue = a[this.sortKey];
          let bValue = b[this.sortKey];
          
          if (typeof aValue === 'number' && typeof bValue === 'number') {
            return aValue < bValue ? -1 * modifier : 1 * modifier;
          } else {
            return aValue.toString().localeCompare(bValue.toString()) * modifier;
          }
        });
      }
      
      return filtered;
    },
    paginatedOrders() {
      const start = (this.currentPage - 1) * this.perPage;
      const end = start + this.perPage;
      return this.filteredOrders.slice(start, end);
    },
    // totalPages() {
    //   return Math.ceil(this.filteredOrders.length / this.perPage) || 1;
    // }
  },
  methods: {
    formatNumber(value) {
      return value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    },
    setActiveTab(tabId) {
      this.activeTab = tabId;
      this.resetPagination();
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
    goToPage(page) {
      this.currentPage = page;
    },
    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
      }
    },
    prevPage() {
      if (this.currentPage > 1) {
        this.currentPage--;
      }
    },
    resetPagination() {
      this.currentPage = 1;
    },
    viewOrderDetails(order) {
      // Navigate to order details page with order ID and data
      this.$router.push({ 
        name: 'order-view',
        params: { 
          id: order.orderId
        },
        // Use query parameter to pass the complete order object
        query: {
          orderData: JSON.stringify(order)
        }
      });
    },
    getDeliveryTimeframe(order) {
      const days = this.calculateDaysBetween(order.date, order.expectedDeliveryDate);
      return `${days} ${days === 1 ? 'Day' : 'Days'}`;
    },
    getDeliveryTimeframeClass(order) {
      const days = this.calculateDaysBetween(order.date, order.expectedDeliveryDate);
      return {
        'delivery-timeframe-badge': true,
        'delivery-timeframe-danger': days <= 0,
        'delivery-timeframe-warning': days > 0 && days <= 2,
        'delivery-timeframe-success': days > 2 && days <= 5,
        'delivery-timeframe-info': days > 5
      };
    },
    getPayStatusClass(order) {
      return {
        'pay-status-badge': true,
        'pay-status-paid': order.payStatus === 'Paid',
        'pay-status-not-paid': order.payStatus === 'Not Paid'
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
    async fetchOrders() {
      this.loading = true;
      try {
        const response = await axios.get(route('retailer.orders.search'), {
          params: {
            status: this.activeTab.toUpperCase(),
            search: this.searchQuery,
            page: this.currentPage,
            per_page: this.perPage,
            sort_key: this.sortKey,
            sort_dir: this.sortDir
          }
        });
       const {data} = response;
        this.orders = data.data;
        this.totalPages = data.last_page;
        this.currentPage = data.current_page;
        this.perPage = data.per_page;
        this.totalOrders = data.total;

      } catch (error) {
        console.error('Error fetching orders:', error);
      } finally {
        this.loading = false;
      }
    },
  },
  mounted() {
    this.fetchOrders();
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

.page-title {
  font-size: 1.8rem;
  color: #1e293b;
  margin-bottom: 24px;
  font-weight: 600;
  text-align: left;
}

/* Tabs */
.tabs-container {
  margin-bottom: 24px;
  border-bottom: 1px solid #e5e7eb;
}

.tabs {
  display: flex;
  list-style: none;
  padding: 0;
  margin: 0;
  gap: 8px;
}

.tab {
  padding: 12px 16px;
  font-size: 0.95rem;
  font-weight: 500;
  color: #64748b;
  cursor: pointer;
  position: relative;
  display: flex;
  align-items: center;
  transition: all 0.2s ease;
}

.tab.active {
  color: #2563eb;
  font-weight: 600;
}

.tab.active::after {
  content: '';
  position: absolute;
  bottom: -1px;
  left: 0;
  width: 100%;
  height: 2px;
  background-color: #2563eb;
}

.tab-count {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 20px;
  height: 20px;
  padding: 0 6px;
  margin-left: 8px;
  border-radius: 10px;
  font-size: 0.75rem;
  font-weight: 600;
}

.tabs li:nth-child(1) .tab-count {
  background-color: rgba(251, 191, 36, 0.1);
  color: #d97706;
}

.tabs li:nth-child(2) .tab-count {
  background-color: rgba(37, 99, 235, 0.1);
  color: #2563eb;
}

.tabs li:nth-child(3) .tab-count {
  background-color: rgba(16, 185, 129, 0.1);
  color: #047857;
}

.tabs li:nth-child(4) .tab-count {
  background-color: rgba(239, 68, 68, 0.1);
  color: #ef4444;
}

/* Table Controls - Modified search container */
.table-controls {
  display: flex;
  margin-bottom: 16px;
}

.search-container {
  position: relative;
  width: 300px; /* Reduced width */
  max-width: 100%;
}

.search-input {
  width: 100%;
  box-sizing: border-box; /* Ensures padding is included in width */
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

/* Table */
.table-wrapper {
  background-color: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  margin-bottom: 24px;
}

.orders-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 0.95rem;
}

.orders-table th {
  background-color: #f8fafc;
  color: #64748b;
  font-weight: 600;
  text-align: left;
  padding: 14px 15px;
  border-bottom: 1px solid #e5e7eb;
  white-space: nowrap;
}

.orders-table th.sortable {
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

.orders-table td {
  padding: 14px 15px;
  border-bottom: 1px solid #e5e7eb;
  color: #334155;
  text-align: left;
}

.orders-table tr:last-child td {
  border-bottom: none;
}

.orders-table tr:hover td {
  background-color: #f8fafc;
}

/* Pagination */
.pagination-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 24px;
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
  padding-right: 30px; /* Added extra padding to the right */
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  font-size: 0.9rem;
  background-color: white;
  cursor: pointer;
  appearance: none; /* Removes default appearance */
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

/* Empty state styling */
.empty-state {
  text-align: center;
  padding: 40px;
  color: #64748b;
  font-size: 0.95rem;
  background-color: #f8fafc;
}

/* Order row styling */
.order-row {
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.order-row:hover {
  background-color: #f0f5ff !important;
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

/* Pay Status Badges */
.pay-status-badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 2px;
  font-size: 0.85rem;
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

@media (max-width: 768px) {
  .pagination-controls {
    flex-direction: column;
    gap: 16px;
  }
  
  .search-container {
    max-width: 100%;
  }
}

@media (max-width: 640px) {
  .tabs {
    overflow-x: auto;
    padding-bottom: 8px;
  }
  
  .page-numbers {
    display: none;
  }
}
</style>