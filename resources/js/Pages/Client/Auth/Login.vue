<template>
    <AuthLayout>
        <template #title>Client Login</template>
        <template #subtitle>Sign in to your client portal</template>

        <form @submit.prevent="login" class="space-y-6">
            <div v-if="errorMessage" class="bg-red-50 p-4 rounded-md">
                <p class="text-sm text-red-800">{{ errorMessage }}</p>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                <div class="mt-1">
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        autocomplete="email"
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    />
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="mt-1">
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        autocomplete="current-password"
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    />
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input
                        id="remember"
                        v-model="form.remember"
                        type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    />
                    <label for="remember" class="ml-2 block text-sm text-gray-900"> Remember me </label>
                </div>

                <div class="text-sm">
                    <router-link :to="{ name: 'client.register' }" class="font-medium text-blue-600 hover:text-blue-500">
                        Need an account?
                    </router-link>
                </div>
            </div>

            <div>
                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    <span v-if="loading">Signing in...</span>
                    <span v-else>Sign in</span>
                </button>
            </div>
        </form>
    </AuthLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import AuthLayout from '@/Layouts/AuthLayout.vue';

const router = useRouter();
const loading = ref(false);
const errorMessage = ref('');

const form = reactive({
    email: '',
    password: '',
    remember: false
});

async function login() {
    loading.value = true;
    errorMessage.value = '';

    try {
        const response = await fetch('/api/client/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(form)
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Login failed');
        }

        // Store the token
        localStorage.setItem('client_token', data.token);
        
        // Redirect to client dashboard
        router.push({ name: 'client.home' });
    } catch (error) {
        errorMessage.value = error.message || 'An error occurred during login';
    } finally {
        loading.value = false;
    }
}
</script> 