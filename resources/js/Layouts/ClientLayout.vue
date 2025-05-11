<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Toast Notification Component -->
        <Toast />
        
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-xl font-medium text-gray-900">
                        <slot name="header">{{ $t('client.portal') }}</slot>
                    </h1>
                    <div class="flex items-center space-x-4">
                        <slot name="header-right"></slot>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex">
            <!-- Sidebar -->
            <aside class="w-48 bg-white shadow-sm h-screen sticky top-0">
                <nav class="mt-5 px-2">
                    <router-link
                        v-for="item in navigation"
                        :key="item.name"
                        :to="item.to"
                        :class="[
                            $route.name.startsWith(item.name)
                                ? 'bg-gray-50 text-gray-900'
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                            'group flex items-center px-2 py-2 text-sm font-medium rounded-md'
                        ]"
                    >
                        <component
                            :is="item.icon"
                            :class="[
                                $route.name.startsWith(item.name)
                                    ? 'text-gray-500'
                                    : 'text-gray-400 group-hover:text-gray-500',
                                'mr-3 flex-shrink-0 h-5 w-5'
                            ]"
                        />
                        {{ $t(item.label) }}
                    </router-link>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 p-6">
                <slot></slot>
            </main>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();

const navigation = computed(() => [
    {
        name: 'client.home',
        label: 'client.home',
        to: { name: 'client.home' },
        icon: 'HomeIcon'
    },
    {
        name: 'client.invoices',
        label: 'invoice.invoices',
        to: { name: 'client.invoices' },
        icon: 'DocumentTextIcon'
    }
]);
</script> 