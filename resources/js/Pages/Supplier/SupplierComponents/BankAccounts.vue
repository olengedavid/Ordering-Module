<template>
  <div>
    <div class="table-controls">
      <div class="table-title">Bank Accounts</div>
      <button @click="openAddBankAccountModal" class="add-btn">
        <span class="plus-icon">+</span>
        Add Bank Account
      </button>
    </div>
    
    <div class="table-container">
      <div class="table-wrapper">
        <table class="data-table">
          <thead>
            <tr>
              <th @click="sortBy('bankName')" class="sortable">
                Bank Name <i :class="getSortIcon('bankName')"></i>
              </th>
              <th @click="sortBy('branch')" class="sortable">
                Branch <i :class="getSortIcon('branch')"></i>
              </th>
              <th @click="sortBy('accountName')" class="sortable">
                Account Name <i :class="getSortIcon('accountName')"></i>
              </th>
              <th @click="sortBy('accountNumber')" class="sortable">
                Account Number <i :class="getSortIcon('accountNumber')"></i>
              </th>
              <th>Primary Account</th>
              <th class="actions-column">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="account in sortedBankAccounts" :key="account.id" class="data-row">
              <td>{{ account.bank_name || account.bankName }}</td>
              <td>{{ account.branch }}</td>
              <td>{{ account.account_name || account.accountName }}</td>
              <td>{{ account.account_number || account.accountNumber }}</td>
              <td class="primary-column">
                <div v-if="account.is_primary || account.isPrimary" class="primary-badge">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                </div>
                <button 
                  v-else 
                  @click="setPrimaryAccount(account.id)" 
                  class="set-primary-btn"
                >
                  Set as Primary
                </button>
              </td>
              <td class="actions-column">
                <button @click="editBankAccount(account)" class="action-btn edit-btn">
                  Edit
                </button>
                <button @click="deleteBankAccount(account.id)" class="action-btn delete-btn">
                  Delete
                </button>
              </td>
            </tr>
            <tr v-if="bankAccounts.length === 0">
              <td colspan="6" class="empty-state">
                No bank accounts added yet
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- Add Bank Account Modal -->
    <div v-if="showBankAccountModal" class="modal-overlay" @click.self="closeBankAccountModal">
      <div class="modal-container">
        <div class="modal-header">
          <h2>{{ editingBankAccount ? 'Edit Bank Account' : 'Add New Bank Account' }}</h2>
          <button class="close-btn" @click="closeBankAccountModal">&times;</button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="saveBankAccount">
            <div class="form-group">
              <label for="bankName">Bank Name <span class="required">*</span></label>
              
              <!-- Custom searchable dropdown for banks -->
              <div class="custom-select-container">
                <div 
                  class="custom-select-trigger" 
                  @click="toggleBankDropdown"
                  :class="{ 'active': isBankDropdownOpen }"
                >
                  <span>{{ newBankAccount.bankName || 'Select a bank' }}</span>
                  <svg 
                    class="dropdown-arrow" 
                    :class="{ 'open': isBankDropdownOpen }"
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
                
                <div class="custom-select-dropdown" v-show="isBankDropdownOpen">
                  <div class="search-box">
                    <input
                      type="text"
                      v-model="bankSearch"
                      @input="filterBanks"
                      placeholder="Search bank..."
                      class="dropdown-search"
                      @click.stop
                    >
                  </div>
                  
                  <div class="dropdown-options">
                    <div
                      v-for="bank in filteredBanks"
                      :key="bank"
                      class="dropdown-option"
                      @click="selectBank(bank)"
                    >
                      {{ bank }}
                    </div>
                    <div v-if="filteredBanks.length === 0" class="no-results">
                      No banks match your search
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="branch">Branch <span class="required">*</span></label>
              <input type="text" id="branch" v-model="newBankAccount.branch" required placeholder="Enter branch name">
            </div>
            <div class="form-group">
              <label for="accountName">Account Name <span class="required">*</span></label>
              <input type="text" id="accountName" v-model="newBankAccount.accountName" required placeholder="Enter account name">
            </div>
            <div class="form-group">
              <label for="accountNumber">Account Number <span class="required">*</span></label>
              <input type="text" id="accountNumber" v-model="newBankAccount.accountNumber" required placeholder="Enter account number">
            </div>
            <div class="form-actions">
              <button type="button" class="cancel-btn" @click="closeBankAccountModal">Cancel</button>
              <button type="submit" class="submit-btn">Save Bank Account</button>
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
  name: 'BankAccounts',
  props: {
    supplier: {
      type: Object,
      required: true
    },
    bankAccounts: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      sortKey: 'bankName',
      sortDir: 'asc',
      showBankAccountModal: false,
      newBankAccount: {
        bankName: '',
        branch: '',
        accountName: '',
        accountNumber: '',
        isPrimary: false
      },
      editingBankAccount: false,
      editingBankAccountId: null,
      isBankDropdownOpen: false,
      bankSearch: '',
      filteredBanks: [],
      banks: [
        'Absa Bank Kenya',
        'Access Bank Kenya',
        'Bank of Africa',
        'Bank of Baroda',
        'Bank of India',
        'Consolidated Bank of Kenya',
        'Co-operative Bank',
        'Credit Bank',
        'Development Bank of Kenya',
        'Diamond Trust Bank',
        'DIB Bank Kenya',
        'Ecobank Kenya',
        'Equity Bank',
        'Family Bank',
        'First Community Bank',
        'Guaranty Trust Bank',
        'Guardian Bank',
        'Gulf African Bank',
        'Housing Finance Company of Kenya',
        'I&M Bank',
        'Jamii Bora Bank',
        'KCB Bank Kenya',
        'Kenya Commercial Bank (KCB)',
        'Mayfair Bank',
        'Middle East Bank Kenya',
        'National Bank of Kenya',
        'NCBA Bank Kenya',
        'Paramount Bank',
        'Prime Bank (Kenya)',
        'SBM Bank Kenya',
        'Sidian Bank',
        'Spire Bank',
        'Stanbic Bank Kenya',
        'Standard Chartered Kenya',
        'Victoria Commercial Bank'
      ]
    }
  },
  computed: {
    sortedBankAccounts() {
      const accounts = [...this.bankAccounts];
      accounts.sort((a, b) => {
        let modifier = this.sortDir === 'asc' ? 1 : -1;
        // Handle different property name formats
        let aValue, bValue;
        
        // Convert sortKey to both formats
        const camelKey = this.sortKey; // e.g. 'bankName'
        const snakeKey = this.sortKey.replace(/[A-Z]/g, letter => `_${letter.toLowerCase()}`); // e.g. 'bank_name'
        
        // Try to get value in both formats
        aValue = a[camelKey] !== undefined ? a[camelKey] : a[snakeKey];
        bValue = b[camelKey] !== undefined ? b[camelKey] : b[snakeKey];
        
        // Handle undefined or null values
        if (aValue === undefined || aValue === null) aValue = '';
        if (bValue === undefined || bValue === null) bValue = '';
        
        // Sort based on value types
        if (typeof aValue === 'number' && typeof bValue === 'number') {
          return aValue < bValue ? -1 * modifier : 1 * modifier;
        } else {
          return aValue.toString().localeCompare(bValue.toString()) * modifier;
        }
      });
      
      return accounts;
    },
  },
  mounted() {
    // Initialize the filteredBanks with all banks
    this.filteredBanks = [...this.banks];
    
    // Add event listener for closing dropdowns when clicking outside
    document.addEventListener('click', this.closeBankDropdownOutside);
  },
  beforeDestroy() {
    // Remove event listener when component is destroyed
    document.removeEventListener('click', this.closeBankDropdownOutside);
  },
  methods: {
    openAddBankAccountModal() {
      this.editingBankAccount = false;
      this.editingBankAccountId = null;
      this.newBankAccount = {
        bankName: '',
        branch: '',
        accountName: this.supplier ? this.supplier.companyName : '',
        accountNumber: '',
        isPrimary: this.bankAccounts.length === 0 // Set as primary if it's the first account
      };
      this.showBankAccountModal = true;
      this.isBankDropdownOpen = false;
    },
    closeBankAccountModal() {
      this.showBankAccountModal = false;
      this.editingBankAccount = false;
      this.editingBankAccountId = null;
      this.newBankAccount = {
        bankName: '',
        branch: '',
        accountName: '',
        accountNumber: '',
        isPrimary: false
      };
      this.isBankDropdownOpen = false;
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
    editBankAccount(account) {
      // Basic validation to prevent errors
      if (!account || !account.id) {
        alert('Cannot edit: Invalid bank account');
        return;
      }
      
      this.editingBankAccount = true;
      this.editingBankAccountId = account.id;
      
      try {
        const normalizedAccount = this.normalizeBankAccount(account);
        this.newBankAccount = {
          uuid: normalizedAccount.uuid,
          bankName: normalizedAccount.bankName,
          branch: normalizedAccount.branch,
          accountName: normalizedAccount.accountName,
          accountNumber: normalizedAccount.accountNumber,
          isPrimary: normalizedAccount.isPrimary
        };
        this.showBankAccountModal = true;
      } catch (error) {
        console.error('Error preparing bank account for editing:', error);
        alert('An error occurred while preparing the bank account for editing');
        this.editingBankAccount = false;
        this.editingBankAccountId = null;
      }
    },
    saveBankAccount() {
      try {
        // Prepare the bank account data to send to the server
        const bankAccountData = {
          bank_name: this.newBankAccount.bankName,
          branch: this.newBankAccount.branch,
          account_name: this.newBankAccount.accountName,
          account_number: this.newBankAccount.accountNumber,
          is_primary: this.newBankAccount.isPrimary,
          company_uuid: this.supplier.uuid,
          created_by: this.supplier.created_by || 1 // Default to 1 if missing
        };

        // Basic validation to ensure we have all required fields
        if (!bankAccountData.bank_name || !bankAccountData.branch || 
            !bankAccountData.account_name || !bankAccountData.account_number || 
            !bankAccountData.company_uuid) {
          // If any required field is missing, don't proceed
          alert('Please fill in all required fields');
          return;
        }

        if (this.editingBankAccount) {
          // If editing an existing bank account, include its UUID
          bankAccountData.uuid = this.newBankAccount.uuid;
          
          // Use the update endpoint
          router.put(route('supplier.bank-accounts.update'), bankAccountData, {
            preserveScroll: true,
            onSuccess: () => {
              this.closeBankAccountModal();
            },
            onError: (errors) => {
              alert('Error updating bank account');
              console.error(errors);
            }
          });
        } else {
          // Use the store endpoint to save new bank account
          router.post(route('supplier.bank-accounts.store'), bankAccountData, {
            preserveScroll: true,
            onSuccess: () => {
              this.closeBankAccountModal();
            },
            onError: (errors) => {
              alert('Error creating bank account');
              console.error(errors);
            }
          });
        }
      } catch (error) {
        console.error('Error in saveBankAccount:', error);
        alert('An unexpected error occurred when saving the bank account');
      }
    },
    setPrimaryAccount(accountId) {
      // Get the account object to find its UUID
      const account = this.bankAccounts.find(acc => acc.id === accountId);
      if (!account || !account.uuid) {
        alert('Could not find bank account to set as primary');
        return;
      }
      
      // Use the update endpoint to set this account as primary
      router.put(route('supplier.bank-accounts.update'), {
        uuid: account.uuid,
        bank_name: account.bank_name || account.bankName,
        branch: account.branch,
        account_name: account.account_name || account.accountName,
        account_number: account.account_number || account.accountNumber,
        is_primary: true
      }, {
        preserveScroll: true,
        onError: (errors) => {
          // Handle errors to prevent unresponsiveness
          alert('Error setting bank account as primary');
          console.error(errors);
        }
      });
    },
    deleteBankAccount(accountId) {
      // Get the account object to find its UUID
      const account = this.bankAccounts.find(acc => acc.id === accountId);
      if (!account || !account.uuid) {
        alert('Could not find bank account to delete');
        return;
      }
      
      // Use the delete endpoint to remove the bank account
      router.delete(route('supplier.bank-accounts.destroy'), {
        data: { uuid: account.uuid },
        preserveScroll: true,
        onError: (errors) => {
          // Handle errors to prevent unresponsiveness
          alert('Error deleting bank account');
          console.error(errors);
        }
      });
    },
    // Bank dropdown methods
    toggleBankDropdown() {
      this.isBankDropdownOpen = !this.isBankDropdownOpen;
      
      if (this.isBankDropdownOpen) {
        // Reset search when opening
        this.bankSearch = '';
        this.filteredBanks = [...this.banks];
        
        // Position dropdown after Vue has updated the DOM
        this.$nextTick(() => {
          const trigger = document.querySelector('.custom-select-trigger');
          const dropdown = document.querySelector('.custom-select-dropdown');
          
          if (!trigger || !dropdown) return;
          
          // Get element positions
          const triggerRect = trigger.getBoundingClientRect();
          const viewportHeight = window.innerHeight;
          
          // Calculate available space below
          const spaceBelow = viewportHeight - triggerRect.bottom;
          
          // Dropdown height (with some buffer)
          const dropdownHeight = Math.min(250, this.filteredBanks.length * 36 + 70);
          
          // Determine if dropdown should open upward or downward
          if (spaceBelow < dropdownHeight) {
            // Not enough space below
            dropdown.classList.add('dropdown-top');
          } else {
            // Default: show below
            dropdown.classList.remove('dropdown-top');
          }
        });
      }
    },
    closeBankDropdownOutside(event) {
      // Close the dropdown if clicking outside of it
      const dropdown = document.querySelector('.custom-select-container');
      if (dropdown && !dropdown.contains(event.target)) {
        this.isBankDropdownOpen = false;
      }
    },
    filterBanks() {
      if (this.bankSearch.trim() === '') {
        // If search is empty, show all banks
        this.filteredBanks = [...this.banks];
      } else {
        // Filter banks based on search input
        const query = this.bankSearch.toLowerCase();
        this.filteredBanks = this.banks.filter(
          bank => bank.toLowerCase().includes(query)
        );
      }
    },
    selectBank(bank) {
      this.newBankAccount.bankName = bank;
      this.isBankDropdownOpen = false;
    },
    normalizeBankAccount(account) {
      return {
        id: account.id,
        uuid: account.uuid,
        bankName: account.bank_name || account.bankName,
        branch: account.branch,
        accountName: account.account_name || account.accountName,
        accountNumber: account.account_number || account.accountNumber,
        isPrimary: account.is_primary || account.isPrimary
      };
    }
  }
}
</script>

<style scoped>
/* Table Controls */
.table-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.table-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1e293b;
}

.add-btn {
  display: flex;
  align-items: center;
  padding: 10px 16px;
  background-color: #0E64A5;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 0.95rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.add-btn:hover {
  background-color: #0a4f83;
}

.plus-icon {
  font-size: 1.2rem;
  margin-right: 8px;
  font-weight: bold;
}

/* Table styles with horizontal scroll */
.table-container {
  position: relative;
  width: 100%;
  background-color: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  margin-bottom: 24px;
}

.table-wrapper {
  width: 100%;
  overflow-x: auto;
  /* Enable smooth scrolling */
  scroll-behavior: smooth;
  /* Add some padding for better appearance on mobile */
  padding-bottom: 6px;
  /* Hide scrollbar for WebKit browsers */
  scrollbar-width: thin;
}

/* Customize scrollbar for Webkit browsers */
.table-wrapper::-webkit-scrollbar {
  height: 8px;
}

.table-wrapper::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}

.table-wrapper::-webkit-scrollbar-thumb {
  background-color: #cbd5e1;
  border-radius: 4px;
}

.table-wrapper::-webkit-scrollbar-thumb:hover {
  background-color: #94a3b8;
}

/* Make sure the table takes full width of its container */
.data-table {
  width: 100%;
  min-width: 750px; /* Minimum width to ensure horizontal scroll appears when needed */
  table-layout: auto; /* Allow table to adjust column width based on content */
  border-collapse: separate;
  border-spacing: 0;
  font-size: 0.95rem;
}

/* Ensure the table headers and data cells have proper min-width */
.data-table th, 
.data-table td {
  white-space: nowrap; /* Prevent text wrapping */
  min-width: 120px; /* Minimum width for each column */
}

/* Actions column should be narrower and fixed */
.actions-column {
  min-width: 110px;
  width: 110px;
}

/* Add a visible indicator for horizontal scroll on mobile */
@media (max-width: 768px) {
  .table-container::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 20px;
    height: 100%;
    background: linear-gradient(to right, rgba(255, 255, 255, 0), rgba(230, 230, 230, 0.3));
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.3s;
  }

  .table-container:hover::after {
    opacity: 1;
  }
}

.data-table th {
  background-color: #f8fafc;
  color: #64748b;
  font-weight: 600;
  text-align: left;
  padding: 14px 15px;
  border-bottom: 1px solid #e5e7eb;
}

.data-table th.sortable {
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

.data-table td {
  padding: 14px 15px;
  border-bottom: 1px solid #e5e7eb;
  color: #334155;
  text-align: left;
}

.data-table tr:last-child td {
  border-bottom: none;
}

.data-row:hover td {
  background-color: #f8fafc;
}

.primary-column {
  text-align: center;
}

.primary-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  background-color: rgba(16, 185, 129, 0.1);
  color: #10b981;
  border-radius: 50%;
}

.set-primary-btn {
  background: none;
  border: 1px solid #cbd5e1;
  border-radius: 4px;
  padding: 6px 10px;
  font-size: 0.85rem;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s;
}

.set-primary-btn:hover {
  border-color: #2563eb;
  color: #2563eb;
  background-color: rgba(37, 99, 235, 0.05);
}

.action-btn {
  background: none;
  border: 1px solid #e5e7eb;
  border-radius: 4px;
  padding: 6px 10px;
  font-size: 0.85rem;
  margin-right: 6px;
  cursor: pointer;
  transition: all 0.2s;
}

.edit-btn {
  color: #0E64A5;
  border-color: #0E64A5;
}

.edit-btn:hover {
  background-color: rgba(14, 100, 165, 0.05);
}

.delete-btn {
  color: #ef4444;
  border-color: #ef4444;
}

.delete-btn:hover {
  background-color: rgba(239, 68, 68, 0.05);
}

.empty-state {
  text-align: center;
  color: #64748b;
  padding: 30px;
  font-style: italic;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-container {
  background-color: white;
  border-radius: 8px;
  width: 500px;
  max-width: 90%;
  max-height: 90vh;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
  display: flex;
  flex-direction: column;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  margin: 0;
  font-size: 1.25rem;
  color: #1e293b;
  font-weight: 600;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #64748b;
  cursor: pointer;
}

.modal-body {
  padding: 24px;
  text-align: left;
  overflow-y: auto;
  max-height: 70vh;
}

.form-group {
  margin-bottom: 16px;
  text-align: left;
  width: 100%;
  box-sizing: border-box;
  position: relative;
}

.form-group label {
  display: block;
  margin-bottom: 6px;
  font-size: 0.9rem;
  font-weight: 500;
  color: #334155;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  font-size: 0.95rem;
  color: #1e293b;
  background-color: white;
  transition: border-color 0.2s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
}

.required {
  color: #ef4444;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 24px;
  gap: 12px;
}

.cancel-btn {
  padding: 10px 16px;
  background-color: white;
  color: #64748b;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  font-size: 0.95rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.cancel-btn:hover {
  background-color: #f1f5f9;
}

.submit-btn {
  padding: 10px 16px;
  background-color: #0E64A5;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 0.95rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.submit-btn:hover {
  background-color: #0a4f83;
}

/* Custom Select Styles */
.custom-select-container {
  position: relative;
  width: 100%;
}

.custom-select-trigger {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  padding: 10px 14px;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  background-color: white;
  cursor: pointer;
  transition: all 0.2s;
}

.custom-select-trigger.active {
  border-color: #2563eb;
  box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
}

.dropdown-arrow {
  transition: transform 0.2s;
}

.dropdown-arrow.open {
  transform: rotate(180deg);
}

.custom-select-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  width: 100%;
  background-color: white;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  margin-top: 5px;
  z-index: 10;
  max-height: 300px;
  overflow-y: auto;
}

.custom-select-dropdown.dropdown-top {
  top: auto;
  bottom: 100%;
  margin-top: 0;
  margin-bottom: 5px;
}

.search-box {
  padding: 10px 12px;
  border-bottom: 1px solid #e5e7eb;
}

.dropdown-search {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #cbd5e1;
  border-radius: 4px;
  font-size: 0.9rem;
}

.dropdown-search:focus {
  outline: none;
  border-color: #2563eb;
}

.dropdown-options {
  max-height: 220px;
  overflow-y: auto;
}

.dropdown-option {
  padding: 10px 14px;
  cursor: pointer;
  transition: background-color 0.2s;
}

.dropdown-option:hover {
  background-color: #f1f5f9;
}

.no-results {
  padding: 14px;
  text-align: center;
  color: #64748b;
  font-style: italic;
}
</style> 