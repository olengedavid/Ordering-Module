<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineProps({
  products: {
    type: Array,
    required: true,
  },
});


const getPrimaryImagePath = (product) => {
  const images = typeof product.images === 'string' 
    ? JSON.parse(product.images) 
    : product.images;
    
  return images?.find(img => img.type === 'primary')?.path || '';
};
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Products" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold text-gray-800">Products</h2>
              <Link
                :href="route('products.create')"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
              >
                Add Product
              </Link>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div
                v-for="product in products"
                :key="product.id"
                class="bg-white rounded-lg shadow overflow-hidden"
              >
                <div class="aspect-w-16 aspect-h-9">
                  <img
                    :src="'/storage/' + getPrimaryImagePath(product)"
                    :alt="product.name"
                    class="w-full h-48 object-cover"
                  />
                </div>
                <div class="p-4">
                  <div class="flex justify-between items-start">
                    <div>
                      <h3 class="text-lg font-semibold text-gray-900">
                        {{ product.name }}
                      </h3>
                      <p class="text-sm text-gray-500">SKU: {{ product.sku_number }}</p>
                    </div>
                    <span
                      :class="{
                        'px-2 py-1 text-xs rounded-full': true,
                        'bg-green-100 text-green-800': product.status === 'active',
                        'bg-red-100 text-red-800': product.status === 'inactive'
                      }"
                    >
                      {{ product.status }}
                    </span>
                  </div>
                  <div class="mt-4 space-y-2">
                    <p class="text-sm text-gray-600">
                      <span class="font-medium">Category:</span> {{ product.category }}
                    </p>
                    <p class="text-sm text-gray-600">
                      <span class="font-medium">Unit:</span>
                      {{ product.unit_of_measure }}
                    </p>
                    <p class="text-sm text-gray-600">
                      <span class="font-medium">Manufacturer:</span>
                      {{ product.manufucturer }}
                    </p>
                  </div>
                  <div class="mt-4 flex justify-end space-x-2">
                    <!-- <Link
                      :href="route('products.edit', product.id)"
                      class="text-sm text-indigo-600 hover:text-indigo-900"
                    >
                      Edit
                    </Link> -->
                    <button
                      @click="deleteProduct(product.id)"
                      class="text-sm text-red-600 hover:text-red-900"
                    >
                      Delete
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="products.length === 0" class="text-center py-12">
              <p class="text-gray-500">No products found</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>