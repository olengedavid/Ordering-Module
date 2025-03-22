<script setup>
const props = defineProps({
  currentPage: {
    type: Number,
    required: true
  },
  lastPage: {
    type: Number,
    required: true
  },
  perPage: {
    type: Number,
    required: true
  },
  perPageOptions: {
    type: Array,
    default: () => [10, 25, 50, 100],
  },
});

const emit = defineEmits(['update:perPage', 'page-changed']);

const handlePerPageChange = (event) => {
  emit('update:perPage', Number(event.target.value));
};

const changePage = (page) => {
  if (page >= 1 && page <= props.lastPage) {
    emit('page-changed', page);
  }
};
</script>

<template>
  <div class="flex items-center justify-between px-4 py-3 sm:px-6">
    <div class="flex items-center">
      <span class="mr-2 text-sm text-gray-700">Show</span>
      <select
        class="rounded border-gray-300 text-sm"
        @change="handlePerPageChange"
        :value="perPage"
      >
        <option v-for="option in perPageOptions" :key="option" :value="option">
          {{ option }}
        </option>
      </select>
      <span class="ml-2 text-sm text-gray-700">per page</span>
    </div>

    <div class="flex items-center space-x-2">
      <button
        @click="changePage(currentPage - 1)"
        :disabled="currentPage === 1"
        class="px-3 py-1 text-sm rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Previous
      </button>

      <span class="px-3 py-1 text-sm bg-primary-blue text-white rounded">
        {{ currentPage }}
      </span>

      <button
        @click="changePage(currentPage + 1)"
        :disabled="currentPage === lastPage"
        class="px-3 py-1 text-sm rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Next
      </button>
    </div>
  </div>
</template>