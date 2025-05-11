<template>
    <div>
        <div class="mb-8 flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Customers</h1>
            <button 
                @click="openAddModal"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                Add Customer
            </button>
        </div>

        <div v-if="loading" class="flex justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
        </div>
        
        <div v-else>
            <CustomerTable 
                :customers="customers" 
                :pagination="pagination"
                @edit="openEditModal"
                @delete="confirmDelete"
                @page-change="fetchCustomers"
            />
        </div>

        <!-- Add/Edit Modal -->
        <Modal :show="isModalOpen" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ editingCustomer ? 'Edit Customer' : 'Add New Customer' }}
                </h2>
                
                <CustomerForm
                    :customer="editingCustomer"
                    @submit="saveCustomer"
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
                <p class="mb-4">Are you sure you want to delete this customer?</p>
                <div class="flex justify-end">
                    <button
                        @click="isDeleteModalOpen = false"
                        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteCustomer"
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
import CustomerTable from '@/Components/CustomerTable.vue';
import CustomerForm from '@/Components/CustomerForm.vue';
import Modal from '@/Components/Modal.vue';

const customers = ref([]);
const pagination = ref(null);
const loading = ref(true);
const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const editingCustomer = ref(null);
const customerToDelete = ref(null);

// Fetch customers from API
const fetchCustomers = async (page = 1) => {
    loading.value = true;
    try {
        const response = await axios.get(`/api/customers?page=${page}`);
        customers.value = response.data.data;
        delete response.data.data;
        pagination.value = response.data;
    } catch (error) {
        console.error('Error fetching customers:', error);
        alert('Failed to load customers');
    } finally {
        loading.value = false;
    }
};

// Modal functions
const openAddModal = () => {
    editingCustomer.value = null;
    isModalOpen.value = true;
};

const openEditModal = (customer) => {
    editingCustomer.value = { ...customer };
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    editingCustomer.value = null;
};

// Save customer (create or update)
const saveCustomer = async (customerData) => {
    try {
        if (editingCustomer.value) {
            await axios.put(`/api/customers/${editingCustomer.value.id}`, customerData);
        } else {
            await axios.post('/api/customers', customerData);
        }
        
        await fetchCustomers();
        closeModal();
    } catch (error) {
        console.error('Error saving customer:', error);
        alert('Failed to save customer');
    }
};

// Delete functions
const confirmDelete = (customer) => {
    customerToDelete.value = customer;
    isDeleteModalOpen.value = true;
};

const deleteCustomer = async () => {
    try {
        await axios.delete(`/api/customers/${customerToDelete.value.id}`);
        await fetchCustomers();
        isDeleteModalOpen.value = false;
    } catch (error) {
        console.error('Error deleting customer:', error);
        alert('Failed to delete customer');
    }
};

// Load customers on component mount
onMounted(() => {
    fetchCustomers();
});
</script> 