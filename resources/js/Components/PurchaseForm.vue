<template>
    <div>
        <form @submit="onSubmit" class="space-y-4">
            <!-- Supplier -->
            <div>
                <label for="supplier_id" class="block text-sm font-medium text-gray-700">Supplier</label>
                <select
                    id="supplier_id"
                    v-model="values.supplier_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    :class="{ 'border-red-500': errors.supplier_id }"
                >
                    <option value="">Select a supplier</option>
                    <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                        {{ supplier.name }}
                    </option>
                </select>
                <div v-if="errors.supplier_id" class="mt-1 text-sm text-red-600">{{ errors.supplier_id }}</div>
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
                    {{ purchase ? 'Update' : 'Create' }}
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
    purchase: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['submit', 'cancel']);
const suppliers = ref([]);

// Get today's date in YYYY-MM-DD format
const today = new Date().toISOString().split('T')[0];

// Define validation schema
const schema = yup.object({
    supplier_id: yup.number().required('Supplier is required'),
    issue_date: yup.date().required('Issue date is required'),
    due_date: yup.date()
        .required('Due date is required')
        .min(yup.ref('issue_date'), 'Due date must be after or equal to issue date'),
    notes: yup.string().nullable()
});

// Initialize form with vee-validate
const { values, errors, handleSubmit, isSubmitting, resetForm } = useForm({
    validationSchema: schema,
    initialValues: props.purchase || {
        supplier_id: '',
        issue_date: today,
        due_date: today,
        notes: ''
    }
});

// Update form values when purchase prop changes
watch(() => props.purchase, (newPurchase) => {
    if (newPurchase) {
        resetForm({
            values: {
                supplier_id: newPurchase.supplier_id || '',
                issue_date: newPurchase.issue_date ? new Date(newPurchase.issue_date).toISOString().split('T')[0] : today,
                due_date: newPurchase.due_date ? new Date(newPurchase.due_date).toISOString().split('T')[0] : today,
                notes: newPurchase.notes || ''
            }
        });
    } else {
        resetForm({
            values: {
                supplier_id: '',
                issue_date: today,
                due_date: today,
                notes: ''
            }
        });
    }
}, { deep: true });

// Fetch suppliers
const fetchSuppliers = async () => {
    try {
        const response = await axios.get('/api/suppliers');
        suppliers.value = response.data.data || response.data;
    } catch (error) {
        console.error('Error fetching suppliers:', error);
    }
};

// Submit handler
const onSubmit = handleSubmit((formValues) => {
    emit('submit', formValues);
});

// Load suppliers when component mounts
onMounted(() => {
    fetchSuppliers();
});
</script>