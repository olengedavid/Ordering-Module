<template>
  <div>
    <div class="message-container">
      <SuccessMessage v-if="successMessage" @close="successMessage = ''" v-slot>{{ successMessage }}</SuccessMessage>
      <ErrorMessage v-if="errorMessage" @close="errorMessage = ''" v-slot>{{ errorMessage }}</ErrorMessage>
    </div>
    
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
            <tr v-if="localBankAccounts.length === 0">
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
import SuccessMessage from '@/Components/SuccessMessage.vue';
import ErrorMessage from '@/Components/ErrorMessage.vue';

export default {
  name: 'BankAccounts',
  components: {
    SuccessMessage,
    ErrorMessage
  },
  props: {
    supplier: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      successMessage: '',
      errorMessage: '',
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
      localBankAccounts: [],
      loading: false,
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
      const accounts = [...this.localBankAccounts];
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
    
    // Load bank accounts if not provided through props
    this.loadBankAccounts();
  },
  beforeDestroy() {
    // Remove event listener when component is destroyed
    document.removeEventListener('click', this.closeBankDropdownOutside);
  },
  methods: {
    getBankAccounts() {
      // Only use local data since we're fully independent now
      return this.localBankAccounts;
    },
    loadBankAccounts(forceRefresh = false) {
      // If bank accounts already provided by props and we're not forcing a refresh, just process them
      if (!forceRefresh && this.localBankAccounts && this.localBankAccounts.length > 0) {
        this.processReceivedAccounts(this.localBankAccounts);
        return;
      }
      
      // Otherwise, fetch the bank accounts
      if (!this.supplier || !this.supplier.uuid) {
        console.error('Cannot load bank accounts: No supplier UUID provided');
        return;
      }
      
      this.loading = true;
      
      // Use fetch API to get the bank accounts data
      fetch(route('supplier.bank-accounts.list') + '?company_uuid=' + this.supplier.uuid)
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
          }
          return response.json();
        })
        .then(data => {
          if (data.bankAccounts) {
            this.processReceivedAccounts(data.bankAccounts);
          }
          this.loading = false;
        })
        .catch(error => {
          console.error('Error fetching bank accounts:', error);
          this.loading = false;
        });
    },
    processReceivedAccounts(accounts) {
      try {
        if (accounts && Array.isArray(accounts) && accounts.length > 0) {
          // Make a deep copy to avoid mutating props directly
          const normalizedAccounts = accounts.map(account => this.normalizeBankAccount(account));
          
          // Assign IDs if missing
          normalizedAccounts.forEach((account, index) => {
            if (!account.id) {
              account.id = index + 1;
            }
          });
          
          this.localBankAccounts = normalizedAccounts;
        }
      } catch (error) {
        console.error('Error processing bank accounts:', error);
      }
    },
    openAddBankAccountModal() {
      this.editingBankAccount = false;
      this.editingBankAccountId = null;
      this.newBankAccount = {
        bankName: '',
        branch: '',
        accountName: this.supplier ? (this.supplier.company_name || this.supplier.companyName || '') : '',
        accountNumber: '',
        isPrimary: this.getBankAccounts().length === 0 // Set as primary if it's the first account
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
        this.errorMessage = 'Cannot edit: Invalid bank account';
        this.autoDismissMessage('error');
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
        this.errorMessage = 'An error occurred while preparing the bank account for editing';
        this.editingBankAccount = false;
        this.editingBankAccountId = null;
        this.autoDismissMessage('error');
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
          this.errorMessage = 'Please fill in all required fields';
          this.autoDismissMessage('error');
          return;
        }

        if (this.editingBankAccount) {
          // If editing an existing bank account, include its UUID
          bankAccountData.uuid = this.newBankAccount.uuid;
          
          // Use the update endpoint
          router.put(route('supplier.bank-accounts.update'), bankAccountData, {
            preserveScroll: true,
            onSuccess: () => {
              this.successMessage = 'Bank account updated successfully';
              this.closeBankAccountModal();
              this.loadBankAccounts(true);
              this.autoDismissMessage('success');
            },
            onError: (errors) => {
              this.errorMessage = 'Error updating bank account';
              console.error(errors);
              this.autoDismissMessage('error');
            }
          });
        } else {
          // Use the store endpoint to save new bank account
          router.post(route('supplier.bank-accounts.store'), bankAccountData, {
            preserveScroll: true,
            onSuccess: () => {
              this.successMessage = 'Bank account added successfully';
              this.closeBankAccountModal();
              this.loadBankAccounts(true);
              this.autoDismissMessage('success');
            },
            onError: (errors) => {
              this.errorMessage = 'Error creating bank account';
              console.error(errors);
              this.autoDismissMessage('error');
            }
          });
        }
      } catch (error) {
        console.error('Error in saveBankAccount:', error);
        this.errorMessage = 'An unexpected error occurred when saving the bank account';
        this.autoDismissMessage('error');
      }
    },
    setPrimaryAccount(accountId) {
      // Get the account object to find its UUID
      const account = this.getBankAccounts().find(acc => acc.id === accountId);
      if (!account || !account.uuid) {
        this.errorMessage = 'Could not find bank account to set as primary';
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
        onSuccess: () => {
          this.successMessage = 'Primary bank account updated successfully';
          this.loadBankAccounts(true);
          this.autoDismissMessage('success');
        },
        onError: (errors) => {
          // Handle errors to prevent unresponsiveness
          this.errorMessage = 'Error setting bank account as primary';
          console.error(errors);
          this.autoDismissMessage('error');
        }
      });
    },
    deleteBankAccount(accountId) {
      // Get the account object to find its UUID
      const account = this.getBankAccounts().find(acc => acc.id === accountId);
      if (!account || !account.uuid) {
        this.errorMessage = 'Could not find bank account to delete';
        return;
      }
      
      // Use the delete endpoint to remove the bank account
      router.delete(route('supplier.bank-accounts.destroy'), {
        data: { uuid: account.uuid },
        preserveScroll: true,
        onSuccess: () => {
          this.successMessage = 'Bank account deleted successfully';
          this.loadBankAccounts(true);
          this.autoDismissMessage('success');
        },
        onError: (errors) => {
          // Handle errors to prevent unresponsiveness
          this.errorMessage = 'Error deleting bank account';
          console.error(errors);
          this.autoDismissMessage('error');
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
    },
    autoDismissMessage(type) {
      setTimeout(() => {
        if (type === 'success') {
          this.successMessage = '';
        } else if (type === 'error') {
          this.errorMessage = '';
        }
      }, 3000);
    }
  }
}
</script>

<style scoped>
@import './SupplierSharedStyles.css';
</style> 