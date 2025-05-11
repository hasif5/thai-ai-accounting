<template>
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">{{ $t('invoice.invoices') }}</h1>
        <p class="mt-1 text-sm text-gray-600">{{ $t('invoice.details') }}</p>

        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-md">
            <div v-if="loading" class="p-4 text-center text-gray-500">
                {{ $t('common.loading') }}
            </div>
            <div v-else-if="invoices.length === 0" class="p-4 text-center text-gray-500">
                {{ $t('invoice.noInvoices') }}
            </div>
            <ul v-else role="list" class="divide-y divide-gray-200">
                <li v-for="invoice in invoices" :key="invoice.id">
                    <div class="block hover:bg-gray-50">
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <p class="text-sm font-medium text-blue-600 truncate">
                                        {{ $t('invoice.invoiceNumber') }} #{{ invoice.invoice_number }}
                                    </p>
                                    <div class="ml-2 flex-shrink-0 flex">
                                        <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                           :class="getStatusClass(invoice.status)">
                                            {{ getStatusLabel(invoice.status) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="ml-2 flex-shrink-0 flex">
                                    <router-link 
                                        :to="{ name: 'client.invoices.show', params: { id: invoice.id }}" 
                                        class="font-medium text-blue-600 hover:text-blue-500 mr-4"
                                    >
                                        {{ $t('common.view') }}
                                    </router-link>
                                    <button 
                                        v-if="invoice.status !== 'paid'" 
                                        @click="payInvoice(invoice.id)"
                                        type="button"
                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                    >
                                        {{ $t('invoice.pay') }}
                                    </button>
                                </div>
                            </div>
                            <div class="mt-2 sm:flex sm:justify-between">
                                <div class="sm:flex">
                                    <p class="flex items-center text-sm text-gray-500">
                                        <span class="truncate">{{ $t('invoice.amount') }}: {{ $formatCurrency(invoice.total) }}</span>
                                    </p>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                    <p>
                                        {{ $t('invoice.issueDate') }}: {{ $formatDate(invoice.issue_date) }} · 
                                        {{ $t('invoice.dueDate') }}: {{ $formatDate(invoice.due_date) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        
        <!-- Pagination -->
        <div v-if="pagination && pagination.last_page > 1" class="mt-4 flex justify-center">
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <a 
                    v-if="pagination.current_page > 1"
                    href="#" 
                    @click.prevent="getInvoices(pagination.current_page - 1)"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                >
                    Previous
                </a>
                <a 
                    v-if="pagination.current_page < pagination.last_page"
                    href="#" 
                    @click.prevent="getInvoices(pagination.current_page + 1)"
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                >
                    Next
                </a>
            </nav>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';
import eventBus from '@/services/event-bus';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const router = useRouter();
const loading = ref(true);
const invoices = ref([]);
const pagination = ref(null);

onMounted(() => {
    getInvoices();
});

async function getInvoices(page = 1) {
    loading.value = true;
    try {
        const response = await api.client.invoices.getAll(page);
        invoices.value = response.data.data;
        delete response.data.data;
        pagination.value = response.data;
    } catch (error) {
        console.error('Error loading invoices:', error);
        eventBus.toast.error(t('invoice.failedToLoad'));
    } finally {
        loading.value = false;
    }
}

function payInvoice(id) {
    router.push({ name: 'client.invoices.show', params: { id } });
}

function getStatusClass(status) {
    switch (status) {
        case 'paid':
            return 'bg-green-100 text-green-800';
        case 'issued':
            return 'bg-yellow-100 text-yellow-800';
        case 'void':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

function getStatusLabel(status) {
    switch (status) {
        case 'paid':
            return t('invoice.paid');
        case 'issued':
            return t('invoice.pending');
        case 'void':
            return t('invoice.cancelled');
        case 'draft':
            return t('invoice.draft');
        default:
            return status;
    }
}
</script> 