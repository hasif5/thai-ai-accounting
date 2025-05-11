<template>
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
        <p class="mt-1 text-sm text-gray-600">Welcome to your client dashboard.</p>

        <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Outstanding Invoices Card -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Outstanding Invoices</dt>
                                <dd>
                                    <div class="text-lg font-medium text-gray-900">
                                        {{ loading ? 'Loading...' : `${summaryData.outstanding_count} (${formatCurrency(summaryData.outstanding_total)})` }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <router-link :to="{ name: 'client.invoices' }" class="font-medium text-blue-700 hover:text-blue-900">
                            View all
                        </router-link>
                    </div>
                </div>
            </div>

            <!-- Last Payment Card -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Last Payment</dt>
                                <dd>
                                    <div class="text-lg font-medium text-gray-900">
                                        {{ loading ? 'Loading...' : 
                                           (summaryData.last_payment ? 
                                            `${formatCurrency(summaryData.last_payment.amount)} on ${formatDate(summaryData.last_payment.date)}` : 
                                            'No payments yet') }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <span class="font-medium text-gray-500">
                            {{ summaryData.last_payment ? `Invoice #${summaryData.last_payment.invoice_number}` : '' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const loading = ref(true);
const summaryData = ref({
    outstanding_count: 0,
    outstanding_total: 0,
    last_payment: null
});

onMounted(async () => {
    try {
        const response = await fetch('/api/client/dashboard', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Authorization': `Bearer ${localStorage.getItem('client_token')}`
            }
        });

        if (!response.ok) {
            throw new Error('Failed to load dashboard data');
        }

        summaryData.value = await response.json();
    } catch (error) {
        console.error('Error loading dashboard data:', error);
    } finally {
        loading.value = false;
    }
});

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
</script> 