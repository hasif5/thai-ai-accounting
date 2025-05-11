<template>
    <div>
        <div class="mb-8 flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Purchases</h1>
            <button 
                @click="openAddModal"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                Add Purchase
            </button>
        </div>

        <div v-if="loading" class="flex justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
        </div>
        
        <div v-else>
            <PurchaseTable 
                :purchases="purchases" 
                :pagination="pagination"
                @edit="openEditModal"
                @delete="confirmDelete"
                @page-change="fetchPurchases"
            />
        </div>

        <!-- Add/Edit Modal -->
        <Modal :show="isModalOpen" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ editingPurchase ? 'Edit Purchase' : 'Add New Purchase' }}
                </h2>
                
                <PurchaseForm
                    :purchase="editingPurchase"
                    @submit="savePurchase"
                    @cancel="closeModal"
                />
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal :show="isDeleteModalOpen" @close="isDeleteModalOpen = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    Confirm Delete
                </h2>
                <p class="mb-4">Are you sure you want to delete this purchase?</p>
                <div class="flex justify-end">
                    <button
                        @click="isDeleteModalOpen = false"
                        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deletePurchase"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </Modal>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import PurchaseTable from '@/Components/PurchaseTable.vue';
import PurchaseForm from '@/Components/PurchaseForm.vue';
import Modal from '@/Components/Modal.vue';

const purchases = ref([]);
const pagination = ref(null);
const loading = ref(true);
const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const editingPurchase = ref(null);
const purchaseToDelete = ref(null);

// Fetch purchases from API
const fetchPurchases = async (page = 1) => {
    loading.value = true;
    try {
        const response = await axios.get(`/api/purchases?page=${page}`);
        purchases.value = response.data.data;
        delete response.data.data;
        pagination.value = response.data;
    } catch (error) {
        console.error('Error fetching purchases:', error);
        alert('Failed to load purchases');
    } finally {
        loading.value = false;
    }
};

// Modal functions
const openAddModal = () => {
    editingPurchase.value = null;
    isModalOpen.value = true;
};

const openEditModal = (purchase) => {
    editingPurchase.value = { ...purchase };
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    editingPurchase.value = null;
};

// Save purchase (create or update)
const savePurchase = async (purchaseData) => {
    try {
        if (editingPurchase.value) {
            await axios.put(`/api/purchases/${editingPurchase.value.id}`, purchaseData);
        } else {
            await axios.post('/api/purchases', purchaseData);
        }
        
        await fetchPurchases();
        closeModal();
    } catch (error) {
        console.error('Error saving purchase:', error);
        alert('Failed to save purchase');
    }
};

// Delete functions
const confirmDelete = (purchase) => {
    purchaseToDelete.value = purchase;
    isDeleteModalOpen.value = true;
};

const deletePurchase = async () => {
    try {
        await axios.delete(`/api/purchases/${purchaseToDelete.value.id}`);
        await fetchPurchases();
        isDeleteModalOpen.value = false;
    } catch (error) {
        console.error('Error deleting purchase:', error);
        alert('Failed to delete purchase');
    }
};

// Load purchases on component mount
onMounted(() => {
    fetchPurchases();
});
</script> 