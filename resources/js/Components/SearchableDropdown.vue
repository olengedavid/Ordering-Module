<template>
  <div class="custom-select-container">
    <div class="custom-select-trigger" @click="toggleDropdown" :class="{ 'active': isOpen }">
      <span :data-has-value="!!modelValue">{{ modelValue || placeholder }}</span>
      <svg class="dropdown-arrow" :class="{ 'open': isOpen }" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="6 9 12 15 18 9"></polyline>
      </svg>
    </div>
    
    <div class="custom-select-dropdown" v-show="isOpen">
      <div class="search-container">
        <input
          type="text"
          v-model="searchQuery"
          :placeholder="`Search ${placeholder.toLowerCase()}...`"
          class="dropdown-search"
          @click.stop
        >
      </div>
      <div class="options-container">
        <div
          v-for="option in filteredOptions"
          :key="option"
          class="dropdown-option"
          @click="selectOption(option)"
        >
          {{ option }}
        </div>
        <div v-if="filteredOptions.length === 0" class="no-results">
          No results found
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted} from 'vue';

const props = defineProps({
  modelValue: String,
  options: {
    type: Array,
    required: true
  },
  placeholder: {
    type: String,
    default: 'Select option'
  }
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const searchQuery = ref('');

const filteredOptions = computed(() => {
  if (!searchQuery.value) return props.options;
  const query = searchQuery.value.toLowerCase();
  return props.options.filter(option => 
    option.toLowerCase().includes(query)
  );
});

const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
  if (isOpen.value) searchQuery.value = '';
};

const selectOption = (option) => {
  emit('update:modelValue', option);
  isOpen.value = false;
  searchQuery.value = '';
};

// Close dropdown when clicking outside
const closeDropdown = (e) => {
  if (!e.target.closest('.custom-select-container')) {
    isOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', closeDropdown);
});

onUnmounted(() => {
  document.removeEventListener('click', closeDropdown);
});
</script>

<style scoped>
.custom-select-container {
  position: relative;
  width: 100%;
}

.custom-select-trigger {
  padding: 10px 16px;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: #1e293b;  /* Darker text color */
  font-weight: 500;  /* Medium weight for text */
}

.custom-select-trigger span[data-has-value="false"] {
  color: #64748b;  /* Lighter color for placeholder */
  font-weight: normal;
}

.custom-select-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  margin-top: 4px;
  z-index: 50;
  display: flex;
  flex-direction: column;
  max-height: 250px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.search-container {
  position: sticky;
  top: 0;
  background: white;
  z-index: 1;
  padding: 8px;
  border-radius: 6px 6px 0 0;
}

.dropdown-search {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  font-size: 0.95rem;
  color: #1e293b;
}

.dropdown-search:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.options-container {
  overflow-y: auto;
  max-height: 210px;
}

.dropdown-option {
  padding: 10px 16px;
  cursor: pointer;
  color: #1e293b;
}

.dropdown-option:hover {
  background-color: #f8fafc;
}

.no-results {
  padding: 10px 16px;
  color: #64748b;
  font-style: italic;
}
</style>