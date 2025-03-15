<script setup>
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import { useForm, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = page.props.auth.user;
const props = defineProps({
    supplierUuid: {
        type: String,
        required: true
    },
    bankAccounts: {
        type: Array,
        required: true
    }
});

const showingModal = ref(false);

const form = useForm({
    bank_name: '',
    branch: '',
    account_name: '',
    account_number: '',
    is_primary: false,
    company_uuid: props.supplierUuid,
    created_by: user.id,
});

const submit = () => {
    form.post(route('supplier.bank-accounts.store'), {
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
</script>

<template>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-medium text-gray-900">Bank Accounts</h3>
                <button
                    @click="showingModal = true"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                >
                    Add Bank Account
                </button>
            </div>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Bank Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Branch
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Account Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Account Number
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Primary Account
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="account in bankAccounts" :key="account.id">
                        <td class="px-6 py-4 whitespace-nowrap">{{ account.bank_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ account.branch }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ account.account_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ account.account_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span v-if="account.is_primary" class="text-green-600">✓</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                            <button class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Bank Account Modal -->
    <Modal :show="showingModal" @close="closeModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                Add Bank Account
            </h2>

            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Bank Name</label>
                        <input
                            type="text"
                            v-model="form.bank_name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            required
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Branch</label>
                        <input
                            type="text"
                            v-model="form.branch"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            required
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Account Name</label>
                        <input
                            type="text"
                            v-model="form.account_name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            required
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Account Number</label>
                        <input
                            type="text"
                            v-model="form.account_number"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            required
                        />
                    </div>

                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            v-model="form.is_primary"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm"
                        />
                        <label class="ml-2 text-sm text-gray-700">Set as Primary Account</label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button
                        type="button"
                        class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900"
                        @click="closeModal"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                        :disabled="form.processing"
                    >
                        Save Account
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>