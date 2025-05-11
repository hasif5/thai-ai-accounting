<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Toast Notification Component -->
        <Toast />
        
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-semibold text-gray-900">
                        <slot name="header">{{ $t('admin.dashboard') }}</slot>
                    </h1>
                    <div class="flex items-center space-x-4">
                        <slot name="header-right"></slot>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex">
            <!-- Sidebar -->
            <aside class="w-64 bg-white shadow-sm h-screen sticky top-0">
                <SidebarMenu />
            </aside>

            <!-- Main Content -->
            <main class="flex-1 p-6">
                <slot></slot>
            </main>
        </div>
    </div>
</template>

<script setup>
import SidebarMenu from '@/components/SidebarMenu.vue';
</script> 