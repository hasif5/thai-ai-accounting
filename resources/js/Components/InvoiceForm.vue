<template>
    <div>
        <form @submit="onSubmit" class="space-y-4">
            <!-- Customer -->
            <div>
                <label for="customer_id" class="block text-sm font-medium text-gray-700">Customer</label>
                <select
                    id="customer_id"
                    v-model="values.customer_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    :class="{ 'border-red-500': errors.customer_id }"
                >
                    <option value="">Select a customer</option>
                    <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                        {{ customer.name }}
                    </option>
                </select>
                <div v-if="errors.customer_id" class="mt-1 text-sm text-red-600">{{ errors.customer_id }}</div>
            </div>

            <!-- Issue Date -->
            <div>
                <label for="issue_date" class="block text-sm font-medium text-gray-700">Issue Date</label>
                <input
                    id="issue_date"
                    v-model="values.issue_date"
                    type="date"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    :class="{ 'border-red-500': errors.issue_date }"
                />
                <div v-if="errors.issue_date" class="mt-1 text-sm text-red-600">{{ errors.issue_date }}</div>
            </div>

            <!-- Due Date -->
            <div>
                <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                <input
                    id="due_date"
                    v-model="values.due_date"
                    type="date"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    :class="{ 'border-red-500': errors.due_date }"
                />
                <div v-if="errors.due_date" class="mt-1 text-sm text-red-600">{{ errors.due_date }}</div>
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
                    {{ invoice ? 'Update' : 'Create' }}
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { useForm } from 'vee-validate';
import * as yup from 'yup';
import axios from 'axios';

const props = defineProps({
    invoice: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['submit', 'cancel']);
const customers = ref([]);

// Get today's date in YYYY-MM-DD format
const today = new Date().toISOString().split('T')[0];

// Define validation schema
const schema = yup.object({
    customer_id: yup.number().required('Customer is required'),
    issue_date: yup.date().required('Issue date is required'),
    due_date: yup.date()
        .required('Due date is required')
        .min(yup.ref('issue_date'), 'Due date must be after or equal to issue date'),
    notes: yup.string().nullable()
});

// Initialize form with vee-validate
const { values, errors, handleSubmit, isSubmitting, resetForm } = useForm({
    validationSchema: schema,
    initialValues: props.invoice || {
        customer_id: '',
        issue_date: today,
        due_date: today,
        notes: ''
    }
});

// Update form values when invoice prop changes
watch(() => props.invoice, (newInvoice) => {
    if (newInvoice) {
        resetForm({
            values: {
                customer_id: newInvoice.customer_id || '',
                issue_date: newInvoice.issue_date ? new Date(newInvoice.issue_date).toISOString().split('T')[0] : today,
                due_date: newInvoice.due_date ? new Date(newInvoice.due_date).toISOString().split('T')[0] : today,
                notes: newInvoice.notes || ''
            }
        });
    } else {
        resetForm({
            values: {
                customer_id: '',
                issue_date: today,
                due_date: today,
                notes: ''
            }
        });
    }
}, { deep: true });

// Fetch customers
const fetchCustomers = async () => {
    try {
        const response = await axios.get('/api/customers');
        customers.value = response.data.data || response.data;
    } catch (error) {
        console.error('Error fetching customers:', error);
    }
};

// Submit handler
const onSubmit = handleSubmit((formValues) => {
    emit('submit', formValues);
});

// Load customers when component mounts
onMounted(() => {
    fetchCustomers();
});
</script> 