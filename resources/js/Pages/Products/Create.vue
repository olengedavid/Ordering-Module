<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const page = usePage();
const user = page.props.auth.user;

const form = useForm({
  name: "",
  sku_number: "",
  category: "",
  unit_of_measure: "",
  description: "",
  manufucturer: "",
  primary_image: null,
  secondary_images: [],
  created_by: user.id,
  company_id: user.company_id
});

const submit = () => {
  form.post(route("products.store"), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      window.location.href = route("products.index");
    },
  });
};

const handleSecondaryImages = (event) => {
  form.secondary_images = Array.from(event.target.files);
};
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Create Product" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <form @submit.prevent="submit" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <InputLabel for="name" value="Product Name" />
                  <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                  />
                  <InputError :message="form.errors.name" />
                </div>

                <div>
                  <InputLabel for="sku_number" value="SKU Number" />
                  <TextInput
                    id="sku_number"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.sku_number"
                    required
                  />
                  <InputError :message="form.errors.sku_number" />
                </div>

                <div>
                  <InputLabel for="category" value="Category" />
                  <TextInput
                    id="category"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.category"
                    required
                  />
                  <InputError :message="form.errors.category" />
                </div>

                <div>
                  <InputLabel for="unit_of_measure" value="Unit of Measure" />
                  <TextInput
                    id="unit_of_measure"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.unit_of_measure"
                    required
                  />
                  <InputError :message="form.errors.unit_of_measure" />
                </div>

                <div>
                  <InputLabel for="manufucturer" value="Manufacturer" />
                  <TextInput
                    id="manufucturer"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.manufucturer"
                    required
                  />
                  <InputError :message="form.errors.manufucturer" />
                </div>
              </div>

              <div>
                <InputLabel for="description" value="Description" />
                <textarea
                  id="description"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                  v-model="form.description"
                  rows="4"
                ></textarea>
                <InputError :message="form.errors.description" />
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <InputLabel for="primary_image" value="Primary Image" />
                  <input
                    type="file"
                    id="primary_image"
                    @input="form.primary_image = $event.target.files[0]"
                    accept="image/*"
                    class="mt-1 block w-full"
                    required
                  />
                  <InputError :message="form.errors.primary_image" />
                </div>

                <div>
                  <InputLabel for="secondary_images" value="Secondary Images" />
                  <input
                    type="file"
                    id="secondary_images"
                    @input="handleSecondaryImages"
                    accept="image/*"
                    multiple
                    class="mt-1 block w-full"
                  />
                  <InputError :message="form.errors.secondary_images" />
                </div>
              </div>

              <div class="flex items-center justify-end mt-6">
                <PrimaryButton
                  :class="{ 'opacity-25': form.processing }"
                  :disabled="form.processing"
                >
                  Create Product
                </PrimaryButton>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>