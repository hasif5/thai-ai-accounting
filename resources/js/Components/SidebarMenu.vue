<template>
    <nav class="mt-5 px-2">
        <router-link
            v-for="item in navigation"
            :key="item.name"
            :to="item.to"
            :class="[
                $route.name.startsWith(item.name)
                    ? 'bg-gray-100 text-gray-900'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                'group flex items-center px-2 py-2 text-base font-medium rounded-md'
            ]"
        >
            <component
                :is="item.icon"
                :class="[
                    $route.name.startsWith(item.name)
                        ? 'text-gray-500'
                        : 'text-gray-400 group-hover:text-gray-500',
                    'mr-4 flex-shrink-0 h-6 w-6'
                ]"
            />
            {{ item.label }}
        </router-link>
    </nav>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();

const navigation = computed(() => [
    {
        name: 'admin.dashboard',
        label: 'Dashboard',
        to: { name: 'admin.dashboard' },
        icon: 'HomeIcon'
    },
    {
        name: 'admin.customers',
        label: 'Customers',
        to: { name: 'admin.customers' },
        icon: 'UsersIcon'
    },
    {
        name: 'admin.invoices',
        label: 'Invoices',
        to: { name: 'admin.invoices' },
        icon: 'DocumentTextIcon'
    },
    {
        name: 'admin.purchases',
        label: 'Purchases',
        to: { name: 'admin.purchases' },
        icon: 'ShoppingCartIcon'
    },
    {
        name: 'admin.cashflow',
        label: 'Cash Flow',
        to: { name: 'admin.cashflow' },
        icon: 'CurrencyDollarIcon'
    }
]);
</script> 