<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const accountsReceivable = ref(0);
const accountsPayable = ref(0);
const cashFlow = ref(0);
const loading = ref(true);

const fetchMetrics = async () => {
  try {
    const [arResponse, apResponse, cfResponse] = await Promise.all([
      axios.get('/api/metrics/accounts-receivable'),
      axios.get('/api/metrics/accounts-payable'),
      axios.get('/api/metrics/cash-flow')
    ]);

    accountsReceivable.value = arResponse.data.total;
    accountsPayable.value = apResponse.data.total;
    cashFlow.value = cfResponse.data.net;
    loading.value = false;
  } catch (error) {
    console.error('Error fetching metrics:', error);
    loading.value = false;
  }
};

onMounted(() => {
  fetchMetrics();
});
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Accounts Receivable Tile -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900">Accounts Receivable</h3>
              <div v-if="loading" class="mt-2 text-gray-600">
                Loading...
              </div>
              <div v-else class="mt-2">
                <span class="text-2xl font-bold text-green-600">฿{{ accountsReceivable.toLocaleString() }}</span>
              </div>
            </div>
          </div>

          <!-- Accounts Payable Tile -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900">Accounts Payable</h3>
              <div v-if="loading" class="mt-2 text-gray-600">
                Loading...
              </div>
              <div v-else class="mt-2">
                <span class="text-2xl font-bold text-red-600">฿{{ accountsPayable.toLocaleString() }}</span>
              </div>
            </div>
          </div>

          <!-- Cash Flow Tile -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900">Net Cash Flow</h3>
              <div v-if="loading" class="mt-2 text-gray-600">
                Loading...
              </div>
              <div v-else class="mt-2">
                <span 
                  :class="['text-2xl font-bold', cashFlow >= 0 ? 'text-green-600' : 'text-red-600']"
                >฿{{ cashFlow.toLocaleString() }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
