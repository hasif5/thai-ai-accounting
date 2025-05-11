<template>
    <div>
        <form @submit="onSubmit" class="space-y-4">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Company Name</label>
                <input
                    id="name"
                    v-model="values.name"
                    type="text"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    :class="{ 'border-red-500': errors.name }"
                />
                <div v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</div>
            </div>

            <!-- Tax ID -->
            <div>
                <label for="tax_id" class="block text-sm font-medium text-gray-700">Tax ID</label>
                <input
                    id="tax_id"
                    v-model="values.tax_id"
                    type="text"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    :class="{ 'border-red-500': errors.tax_id }"
                />
                <div v-if="errors.tax_id" class="mt-1 text-sm text-red-600">{{ errors.tax_id }}</div>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input
                    id="email"
                    v-model="values.email"
                    type="email"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    :class="{ 'border-red-500': errors.email }"
                />
                <div v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</div>
            </div>

            <!-- Phone -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                <input
                    id="phone"
                    v-model="values.phone"
                    type="text"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    :class="{ 'border-red-500': errors.phone }"
                />
                <div v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone }}</div>
            </div>

            <!-- Address -->
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                <textarea
                    id="address"
                    v-model="values.address"
                    rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    :class="{ 'border-red-500': errors.address }"
                ></textarea>
                <div v-if="errors.address" class="mt-1 text-sm text-red-600">{{ errors.address }}</div>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea
                    id="notes"
                    v-model="values.notes"
                    rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    :class="{ 'border-red-500': errors.notes }"
                ></textarea>
                <div v-if="errors.notes" class="mt-1 text-sm text-red-600">{{ errors.notes }}</div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end pt-4">
                <button
                    type="button"
                    @click="$emit('cancel')"
                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    :disabled="isSubmitting"
                >
                    {{ customer ? 'Update' : 'Create' }}
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useForm } from 'vee-validate';
import * as yup from 'yup';

const props = defineProps({
    customer: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['submit', 'cancel']);

// Define validation schema
const schema = yup.object({
    name: yup.string().required('Name is required'),
    tax_id: yup.string().nullable(),
    email: yup.string().email('Must be a valid email').nullable(),
    phone: yup.string().nullable(),
    address: yup.string().nullable(),
    notes: yup.string().nullable()
});

// Initialize form with vee-validate
const { values, errors, handleSubmit, isSubmitting, resetForm } = useForm({
    validationSchema: schema,
    initialValues: props.customer || {
        name: '',
        tax_id: '',
        email: '',
        phone: '',
        address: '',
        notes: ''
    }
});

// Update form values when customer prop changes
watch(() => props.customer, (newCustomer) => {
    if (newCustomer) {
        resetForm({
            values: {
                name: newCustomer.name || '',
                tax_id: newCustomer.tax_id || '',
                email: newCustomer.email || '',
                phone: newCustomer.phone || '',
                address: newCustomer.address || '',
                notes: newCustomer.notes || ''
            }
        });
    } else {
        resetForm({
            values: {
                name: '',
                tax_id: '',
                email: '',
                phone: '',
                address: '',
                notes: ''
            }
        });
    }
}, { deep: true });

// Submit handler
const onSubmit = handleSubmit((formValues) => {
    emit('submit', formValues);
});
</script> 