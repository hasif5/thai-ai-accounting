<template>
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tax ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-if="customers.length === 0">
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            No customers found
                        </td>
                    </tr>
                    <tr v-for="customer in customers" :key="customer.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ customer.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ customer.tax_id || '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ customer.email || '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ customer.phone || '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button 
                                @click="$emit('edit', customer)" 
                                class="text-indigo-600 hover:text-indigo-900 mr-4"
                            >
                                Edit
                            </button>
                            <button 
                                @click="$emit('delete', customer)" 
                                class="text-red-600 hover:text-red-900"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div v-if="pagination" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="hidden sm:block">
                    <p class="text-sm text-gray-700">
                        Showing
                        <span class="font-medium">{{ pagination.from || 0 }}</span>
                        to
                        <span class="font-medium">{{ pagination.to || 0 }}</span>
                        of
                        <span class="font-medium">{{ pagination.total }}</span>
                        results
                    </p>
                </div>
                <div class="flex-1 flex justify-between sm:justify-end">
                    <button
                        v-if="pagination.prev_page_url"
                        @click="$emit('page-change', pagination.current_page - 1)"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                    >
                        Previous
                    </button>
                    <button
                        v-if="pagination.next_page_url"
                        @click="$emit('page-change', pagination.current_page + 1)"
                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    customers: {
        type: Array,
        required: true
    },
    pagination: {
        type: Object,
        default: null
    }
});

defineEmits(['edit', 'delete', 'page-change']);
</script> 