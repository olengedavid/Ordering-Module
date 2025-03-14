<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ref } from "vue";

const showingModal = ref(false);

const form = useForm({
  name: "",
  address: "",
  email: "",
  krapin: "",
  contact_person: "",
  phone_number: "",
  status: "active",
  company_id: 1,
});

const createWarehouse = () => {
  form.post(route("supplierwarehouse.create"), {
    preserveScroll: true,
    onSuccess: () => {
      closeModal();
      form.reset();
    },
  });
};

const closeModal = () => {
  showingModal.value = false;
  form.reset();
};

defineProps({
  warehouses: {
    type: Array,
    required: true,
  },
});
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Supplier Warehouses" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold text-gray-800">Warehouses</h2>
              <button
                @click="showingModal = true"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
              >
                Add Warehouse
              </button>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Warehouse Name
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Location
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Contact Person
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Phone Number
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Status
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="warehouse in warehouses" :key="warehouse.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ warehouse.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ warehouse.location }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ warehouse.contact_person }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ warehouse.phone_number }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        :class="{
                          'px-2 py-1 text-xs rounded-full': true,
                          'bg-green-100 text-green-800':
                            warehouse.status === 'active',
                          'bg-red-100 text-red-800':
                            warehouse.status === 'inactive',
                        }"
                      >
                        {{ warehouse.status }}
                      </span>
                    </td>
                  </tr>
                  <tr v-if="warehouses.length === 0">
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                      No warehouses found
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Add Warehouse Modal -->
            <Modal :show="showingModal" @close="closeModal">
              <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                  Add New Warehouse
                </h2>

                <form @submit.prevent="createWarehouse">
                  <div>
                    <InputLabel for="name" value="Warehouse Name" />
                    <TextInput
                      id="name"
                      type="text"
                      class="mt-1 block w-full"
                      v-model="form.name"
                      required
                      autofocus
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                  </div>

                  <div class="mt-4">
                    <InputLabel for="address" value="Address" />
                    <TextInput
                      id="address"
                      type="text"
                      class="mt-1 block w-full"
                      v-model="form.address"
                      required
                    />
                    <InputError class="mt-2" :message="form.errors.address" />
                  </div>

                  <div class="mt-4">
                    <InputLabel for="email" value="Email" />
                    <TextInput
                      id="email"
                      type="email"
                      class="mt-1 block w-full"
                      v-model="form.email"
                      required
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                  </div>

                  <div class="mt-4">
                    <InputLabel for="krapin" value="KRA PIN" />
                    <TextInput
                      id="krapin"
                      type="text"
                      class="mt-1 block w-full"
                      v-model="form.krapin"
                      required
                    />
                    <InputError class="mt-2" :message="form.errors.krapin" />
                  </div>

                  <div class="mt-4">
                    <InputLabel for="contact_person" value="Contact Person" />
                    <TextInput
                      id="contact_person"
                      type="text"
                      class="mt-1 block w-full"
                      v-model="form.contact_person"
                      required
                    />
                    <InputError
                      class="mt-2"
                      :message="form.errors.contact_person"
                    />
                  </div>

                  <div class="mt-4">
                    <InputLabel for="phone_number" value="Phone Number" />
                    <TextInput
                      id="phone_number"
                      type="tel"
                      class="mt-1 block w-full"
                      v-model="form.phone_number"
                      required
                    />
                    <InputError
                      class="mt-2"
                      :message="form.errors.phone_number"
                    />
                  </div>

                  <div class="mt-6 flex justify-end">
                    <button
                      type="button"
                      class="mr-3 px-4 py-2 text-sm text-gray-700 hover:text-gray-900"
                      @click="closeModal"
                    >
                      Cancel
                    </button>
                    <PrimaryButton :disabled="form.processing">
                      Create Warehouse
                    </PrimaryButton>
                  </div>
                </form>
              </div>
            </Modal>
            <!-- End of Modal -->
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>