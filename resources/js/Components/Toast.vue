<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="transform opacity-0 scale-95"
        enter-to-class="transform opacity-100 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="transform opacity-100 scale-100"
        leave-to-class="transform opacity-0 scale-95"
    >
        <div 
            v-if="visible"
            class="fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg max-w-sm"
            :class="toastClasses"
        >
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <!-- Success Icon -->
                    <svg v-if="toast.type === 'success'" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <!-- Error Icon -->
                    <svg v-else-if="toast.type === 'error'" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <!-- Info Icon -->
                    <svg v-else-if="toast.type === 'info'" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <!-- Warning Icon -->
                    <svg v-else-if="toast.type === 'warning'" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ toast.message }}</p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button
                            @click="close"
                            class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2"
                            :class="closeButtonClasses"
                        >
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import eventBus from '../services/event-bus';

const toast = ref({ type: 'info', message: '' });
const visible = ref(false);
const timeout = ref(null);

const toastClasses = computed(() => {
    switch (toast.value.type) {
        case 'success':
            return 'bg-green-50 text-green-800';
        case 'error':
            return 'bg-red-50 text-red-800';
        case 'warning':
            return 'bg-yellow-50 text-yellow-800';
        case 'info':
        default:
            return 'bg-blue-50 text-blue-800';
    }
});

const closeButtonClasses = computed(() => {
    switch (toast.value.type) {
        case 'success':
            return 'text-green-500 hover:bg-green-100 focus:ring-green-400 focus:ring-offset-green-50';
        case 'error':
            return 'text-red-500 hover:bg-red-100 focus:ring-red-400 focus:ring-offset-red-50';
        case 'warning':
            return 'text-yellow-500 hover:bg-yellow-100 focus:ring-yellow-400 focus:ring-offset-yellow-50';
        case 'info':
        default:
            return 'text-blue-500 hover:bg-blue-100 focus:ring-blue-400 focus:ring-offset-blue-50';
    }
});

const show = (newToast) => {
    // Clear any existing timeout
    if (timeout.value) {
        clearTimeout(timeout.value);
    }
    
    // Set the toast data
    toast.value = newToast;
    visible.value = true;
    
    // Auto-hide after 5 seconds
    timeout.value = setTimeout(() => {
        close();
    }, 5000);
};

const close = () => {
    visible.value = false;
    
    if (timeout.value) {
        clearTimeout(timeout.value);
        timeout.value = null;
    }
};

// Event listeners
onMounted(() => {
    eventBus.on('toast', show);
});

onUnmounted(() => {
    eventBus.off('toast', show);
    
    if (timeout.value) {
        clearTimeout(timeout.value);
    }
});
</script> 