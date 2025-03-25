<template>
  <div class="custom-select-container">
    <div 
      class="custom-select-trigger" 
      @click="toggleDropdown"
      :class="{ 'active': isOpen }"
    >
      <span>{{ displayValue || placeholder }}</span>
      <svg 
        class="dropdown-arrow" 
        :class="{ 'open': isOpen }"
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
    
    <div class="custom-select-dropdown" v-show="isOpen">
      <div class="search-box">
        <input
          type="text"
          v-model="searchQuery"
          @input="filterItems"
          :placeholder="'Search...'"
          class="dropdown-search"
          @click.stop
        >
      </div>
      
      <div class="dropdown-options">
        <div
          v-for="item in filteredItems"
          :key="item"
          class="dropdown-option"
          @click="selectItem(item)"
        >
          {{ item }}
        </div>
        <div v-if="filteredItems.length === 0" class="no-results">
          No items match your search
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SearchableDropdown',
  props: {
    items: {
      type: Array,
      required: true
    },
    modelValue: {
      type: [String, Number],
      default: ''
    },
    placeholder: {
      type: String,
      default: 'Select an option'
    }
  },
  emits: ['update:modelValue'],
  data() {
    return {
      isOpen: false,
      searchQuery: '',
      filteredItems: []
    }
  },
  computed: {
    displayValue() {
      return this.modelValue || '';
    }
  },
  mounted() {
    this.filteredItems = [...this.items];
    document.addEventListener('click', this.closeDropdownOutside);
  },
  beforeDestroy() {
    document.removeEventListener('click', this.closeDropdownOutside);
  },
  methods: {
    toggleDropdown() {
      this.isOpen = !this.isOpen;
      
      if (this.isOpen) {
        this.searchQuery = '';
        this.filteredItems = [...this.items];
        
        this.$nextTick(() => {
          const trigger = document.querySelector('.custom-select-trigger');
          const dropdown = document.querySelector('.custom-select-dropdown');
          
          if (!trigger || !dropdown) return;
          
          const triggerRect = trigger.getBoundingClientRect();
          const viewportHeight = window.innerHeight;
          const spaceBelow = viewportHeight - triggerRect.bottom;
          const dropdownHeight = Math.min(250, this.filteredItems.length * 36 + 70);
          
          if (spaceBelow < dropdownHeight) {
            dropdown.classList.add('dropdown-top');
          } else {
            dropdown.classList.remove('dropdown-top');
          }
        });
      }
    },
    closeDropdownOutside(event) {
      const dropdown = document.querySelector('.custom-select-container');
      if (dropdown && !dropdown.contains(event.target)) {
        this.isOpen = false;
      }
    },
    filterItems() {
      if (this.searchQuery.trim() === '') {
        this.filteredItems = [...this.items];
      } else {
        const query = this.searchQuery.toLowerCase();
        this.filteredItems = this.items.filter(
          item => item.toLowerCase().includes(query)
        );
      }
    },
    selectItem(item) {
      this.$emit('update:modelValue', item);
      this.isOpen = false;
    }
  }
}
</script>

<style scoped>
.custom-select-container {
  position: relative;
  width: 100%;
}

.custom-select-trigger {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  cursor: pointer;
  background-color: white;
  transition: all 0.2s ease;
}

.custom-select-trigger:hover {
  border-color: #999;
}

.custom-select-trigger.active {
  border-color: #4a5568;
  box-shadow: 0 0 0 1px #4a5568;
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
  background: white;
  border: 1px solid #ddd;
  border-radius: 4px;
  margin-top: 4px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  max-height: 250px;
  overflow-y: auto;
}

.custom-select-dropdown.dropdown-top {
  top: auto;
  bottom: 100%;
  margin-top: 0;
  margin-bottom: 4px;
}

.search-box {
  padding: 8px;
  border-bottom: 1px solid #eee;
}

.dropdown-search {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  outline: none;
}

.dropdown-search:focus {
  border-color: #4a5568;
}

.dropdown-options {
  max-height: 200px;
  overflow-y: auto;
}

.dropdown-option {
  padding: 8px 12px;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.dropdown-option:hover {
  background-color: #f7fafc;
}

.no-results {
  padding: 12px;
  text-align: center;
  color: #718096;
  font-style: italic;
}
</style> 