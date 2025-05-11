<template>
    <div>
        <div class="pb-5 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
            <h1 class="text-2xl font-semibold text-gray-900">
                Invoice #{{ invoice ? invoice.invoice_number : 'Loading...' }}
            </h1>
            <div class="mt-3 flex sm:mt-0 sm:ml-4">
                <router-link 
                    :to="{ name: 'client.invoices' }" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    Back to Invoices
                </router-link>
            </div>
        </div>

        <div v-if="loading" class="mt-6 text-center text-gray-500">
            Loading invoice details...
        </div>
        
        <div v-else-if="error" class="mt-6 bg-red-50 p-4 rounded-md">
            <p class="text-sm font-medium text-red-800">{{ error }}</p>
        </div>
        
        <div v-else-if="invoice" class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Invoice Details -->
            <div class="lg:col-span-2 bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Invoice Details</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            Issued on {{ formatDate(invoice.issue_date) }}
                        </p>
                    </div>
                    <span 
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                        :class="getStatusClass(invoice.status)"
                    >
                        {{ getStatusLabel(invoice.status) }}
                    </span>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Invoice Number</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ invoice.invoice_number }}</dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Issue Date</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ formatDate(invoice.issue_date) }}</dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ formatDate(invoice.due_date) }}</dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-bold">{{ formatCurrency(invoice.total) }}</dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Notes</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ invoice.notes || 'No notes' }}</dd>
                        </div>
                    </dl>
                </div>
                
                <!-- Invoice Lines -->
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Invoice Items</h3>
                </div>
                <div class="border-t border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="line in invoice.lines" :key="line.id">
                                <td class="px-6 py-4 whitespace-normal text-sm text-gray-900">{{ line.description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ line.quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ formatCurrency(line.unit_price) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">{{ formatCurrency(line.amount) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Subtotal:</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">{{ formatCurrency(invoice.subtotal) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Tax (7%):</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">{{ formatCurrency(invoice.tax) }}</td>
                            </tr>
                            <tr class="bg-gray-50">
                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">Total:</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">{{ formatCurrency(invoice.total) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Payment Section -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Payment</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        {{ invoice.status === 'paid' ? 'This invoice has been paid.' : 'Pay using PromptPay QR code' }}
                    </p>
                </div>
                
                <div v-if="invoice.status !== 'paid'" class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <div v-if="loading" class="flex justify-center">
                        <p class="text-gray-500">Loading payment information...</p>
                    </div>
                    <div v-else-if="qrError" class="bg-red-50 p-4 rounded-md">
                        <p class="text-sm font-medium text-red-800">{{ qrError }}</p>
                    </div>
                    <div v-else-if="qrCode" class="flex flex-col items-center">
                        <img :src="qrCode" alt="PromptPay QR Code" class="w-48 h-48 object-contain mb-4" />
                        <p class="text-sm text-gray-600 text-center mb-2">
                            Scan this QR code with your banking app to pay
                        </p>
                        <p class="text-lg font-bold text-gray-900 mb-4">
                            {{ formatCurrency(invoice.total) }}
                        </p>
                        <button 
                            @click="refreshPaymentStatus" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Check Payment Status
                        </button>
                    </div>
                </div>
                
                <div v-else class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <div class="flex flex-col items-center">
                        <div class="rounded-full bg-green-100 p-3">
                            <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="mt-2 text-sm font-medium text-gray-900">
                            Payment completed
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Thank you for your payment.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '@/services/api';
import eventBus from '@/services/event-bus';

const route = useRoute();
const loading = ref(true);
const invoice = ref(null);
const error = ref('');
const qrCode = ref('');
const qrError = ref('');

onMounted(async () => {
    await getInvoice();
    
    if (invoice.value && invoice.value.status !== 'paid') {
        await getPaymentQR();
    }
});

async function getInvoice() {
    loading.value = true;
    try {
        const response = await api.client.invoices.get(route.params.id);
        invoice.value = response.data;
    } catch (err) {
        error.value = err.message || 'An error occurred while loading the invoice';
        console.error('Error loading invoice:', err);
        eventBus.toast.error('Failed to load invoice details');
    } finally {
        loading.value = false;
    }
}

async function getPaymentQR() {
    try {
        const response = await api.client.invoices.getPaymentQR(route.params.id);
        qrCode.value = response.data.qr_image_url;
    } catch (err) {
        qrError.value = err.message || 'An error occurred while generating the payment QR';
        console.error('Error getting payment QR:', err);
        eventBus.toast.error('Failed to generate payment QR code');
    }
}

async function refreshPaymentStatus() {
    eventBus.toast.info('Checking payment status...');
    await getInvoice();
    
    if (invoice.value && invoice.value.status === 'paid') {
        eventBus.toast.success('Payment confirmed!');
    } else if (invoice.value && invoice.value.status !== 'paid') {
        eventBus.toast.info('Payment not yet confirmed. Please try again later.');
        await getPaymentQR();
    }
}

function formatCurrency(amount) {
    if (amount === undefined || amount === null) return '฿0.00';
    return new Intl.NumberFormat('th-TH', {
        style: 'currency',
        currency: 'THB'
    }).format(amount);
}

function formatDate(dateString) {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
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
            return 'Paid';
        case 'issued':
            return 'Outstanding';
        case 'void':
            return 'Voided';
        case 'draft':
            return 'Draft';
        default:
            return status;
    }
}
</script> 