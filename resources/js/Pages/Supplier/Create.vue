<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const form = useForm({
  company_name: "",
  email: "",
  phone_number: "",
  office_address: "",
  krapin: "",
  contact_person: "",
  created_by: 1
});

const submit = () => {
  form.post(route("suppliers.create"));
};
</script>

<template>
  <GuestLayout>
    <Head title="Supplier Registration" />

    <form @submit.prevent="submit">
      <div>
        <InputLabel for="company_name" value="Company Name" />
        <TextInput
          id="company_name"
          type="text"
          class="mt-1 block w-full"
          v-model="form.company_name"
          required
          autofocus
        />
        <InputError class="mt-2" :message="form.errors.company_name" />
      </div>

      <div class="mt-4">
        <InputLabel for="email" value="Business Email" />
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
        <InputLabel for="phone_number" value="Phone Number" />
        <TextInput
          id="phone_number"
          type="tel"
          class="mt-1 block w-full"
          v-model="form.phone_number"
          required
        />
        <InputError class="mt-2" :message="form.errors.phone_number" />
      </div>

      <div class="mt-4">
        <InputLabel for="address" value="Business Address" />
        <TextInput
          id="office_address"
          type="text"
          class="mt-1 block w-full"
          v-model="form.office_address"
          required
        />
        <InputError class="mt-2" :message="form.errors.office_address" />
      </div>

     

      <div class="mt-4">
        <InputLabel for="krapin" value="Tax Number" />
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
        <InputLabel for="contact_person" value="Contact Person Name" />
        <TextInput
          id="contact_person"
          type="text"
          class="mt-1 block w-full"
          v-model="form.contact_person"
          required
        />
        <InputError class="mt-2" :message="form.errors.contact_person" />
      </div>


      <div class="mt-4 flex items-center justify-end">
        <Link
          :href="route('login')"
          class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        >
         Cancel
        </Link>

        <PrimaryButton
          class="ms-4"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          Register Supplier
        </PrimaryButton>
      </div>
    </form>
  </GuestLayout>
</template>