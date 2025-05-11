<template>
    <div>
        <div class="mb-8 flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">{{ $t('invoice.invoices') }}</h1>
            <div>
                <button 
                    @click="openAIModal"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 mr-3"
                >
                    {{ $t('invoice.aiGenerate') }}
                </button>
                <button 
                    @click="openAddModal"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    {{ $t('invoice.addInvoice') }}
                </button>
            </div>
        </div>

        <div v-if="loading" class="flex justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
        </div>
        
        <div v-else>
            <InvoiceTable 
                :invoices="invoices" 
                :pagination="pagination"
                @edit="openEditModal"
                @delete="confirmDelete"
                @page-change="fetchInvoices"
            />
        </div>

        <!-- Add/Edit Modal -->
        <Modal :show="isModalOpen" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ editingInvoice ? $t('invoice.editInvoice') : $t('invoice.addInvoice') }}
                </h2>
                
                <InvoiceForm
                    :invoice="editingInvoice"
                    @submit="saveInvoice"
                    @cancel="closeModal"
                />
            </div>
        </Modal>

        <!-- AI Invoice Modal -->
        <Modal :show="isAIModalOpen" @close="closeAIModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ $t('invoice.aiGenerate') }}
                </h2>
                
                <form @submit.prevent="generateAIInvoice" class="space-y-4">
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">{{ $t('invoice.description') }} (ภาษาไทย)</label>
                        <textarea
                            id="description"
                            v-model="aiDescription"
                            rows="5"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="ใส่รายละเอียดใบแจ้งหนี้เป็นภาษาไทย เช่น 'ค่าบริการที่ปรึกษาด้านบัญชี เดือนมกราคม 2566 สำหรับบริษัท กขค จำกัด จำนวน 10,000 บาท'"
                            required
                        ></textarea>
                    </div>
                    
                    <div class="flex justify-end pt-4">
                        <button
                            type="button"
                            @click="closeAIModal"
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3"
                        >
                            {{ $t('common.cancel') }}
                        </button>
                        <button
                            type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            :disabled="aiLoading"
                        >
                            {{ aiLoading ? $t('common.loading') : $t('invoice.aiGenerate') }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal :show="isDeleteModalOpen" @close="isDeleteModalOpen = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ $t('common.confirm') }}
                </h2>
                <p class="mb-4">{{ $t('invoice.deleteConfirm') }}</p>
                <div class="flex justify-end">
                    <button
                        @click="isDeleteModalOpen = false"
                        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3"
                    >
                        {{ $t('common.cancel') }}
                    </button>
                    <button
                        @click="deleteInvoice"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                        {{ $t('common.delete') }}
                    </button>
                </div>
            </div>
        </Modal>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import eventBus from '@/services/event-bus';
import InvoiceTable from '@/Components/InvoiceTable.vue';
import InvoiceForm from '@/Components/InvoiceForm.vue';
import Modal from '@/Components/Modal.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const invoices = ref([]);
const pagination = ref(null);
const loading = ref(true);
const isModalOpen = ref(false);
const isAIModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const editingInvoice = ref(null);
const invoiceToDelete = ref(null);
const aiDescription = ref('');
const aiLoading = ref(false);

// Fetch invoices from API
const fetchInvoices = async (page = 1) => {
    loading.value = true;
    try {
        const response = await api.invoices.getAll(page);
        invoices.value = response.data.data;
        delete response.data.data;
        pagination.value = response.data;
    } catch (error) {
        console.error('Error fetching invoices:', error);
        eventBus.toast.error(t('invoice.failedToLoad'));
    } finally {
        loading.value = false;
    }
};

// Modal functions
const openAddModal = () => {
    editingInvoice.value = null;
    isModalOpen.value = true;
};

const openEditModal = (invoice) => {
    editingInvoice.value = { ...invoice };
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    editingInvoice.value = null;
};

// AI Invoice functions
const openAIModal = () => {
    aiDescription.value = '';
    isAIModalOpen.value = true;
};

const closeAIModal = () => {
    isAIModalOpen.value = false;
    aiDescription.value = '';
};

const generateAIInvoice = async () => {
    aiLoading.value = true;
    try {
        await api.invoices.generateWithAI(aiDescription.value);
        
        await fetchInvoices();
        closeAIModal();
        eventBus.toast.success(t('invoice.createSuccess'));
    } catch (error) {
        console.error('Error generating AI invoice:', error);
        eventBus.toast.error(t('invoice.failedToSave') + ': ' + (error.response?.data?.error || error.message));
    } finally {
        aiLoading.value = false;
    }
};

// Save invoice (create or update)
const saveInvoice = async (invoiceData) => {
    try {
        if (editingInvoice.value) {
            await api.invoices.update(editingInvoice.value.id, invoiceData);
            eventBus.toast.success(t('invoice.updateSuccess'));
        } else {
            await api.invoices.create(invoiceData);
            eventBus.toast.success(t('invoice.createSuccess'));
        }
        
        await fetchInvoices();
        closeModal();
    } catch (error) {
        console.error('Error saving invoice:', error);
        eventBus.toast.error(t('invoice.failedToSave'));
    }
};

// Delete functions
const confirmDelete = (invoice) => {
    invoiceToDelete.value = invoice;
    isDeleteModalOpen.value = true;
};

const deleteInvoice = async () => {
    try {
        await api.invoices.delete(invoiceToDelete.value.id);
        eventBus.toast.success(t('invoice.deleteSuccess'));
        await fetchInvoices();
        isDeleteModalOpen.value = false;
    } catch (error) {
        console.error('Error deleting invoice:', error);
        eventBus.toast.error(t('invoice.failedToDelete'));
    }
};

// Load invoices on component mount
onMounted(() => {
    fetchInvoices();
});
</script> 