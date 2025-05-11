<template>
    <AuthLayout>
        <template #title>Create Your Account</template>
        <template #subtitle>Sign up for the client portal</template>

        <form @submit.prevent="register" class="space-y-6">
            <div v-if="errorMessage" class="bg-red-50 p-4 rounded-md">
                <p class="text-sm text-red-800">{{ errorMessage }}</p>
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                <div class="mt-1">
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        autocomplete="name"
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    />
                </div>
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
                <label for="tax_id" class="block text-sm font-medium text-gray-700">Tax ID (Optional)</label>
                <div class="mt-1">
                    <input
                        id="tax_id"
                        v-model="form.tax_id"
                        type="text"
                        autocomplete="off"
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    />
                </div>
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <div class="mt-1">
                    <input
                        id="phone"
                        v-model="form.phone"
                        type="tel"
                        required
                        autocomplete="tel"
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
                        autocomplete="new-password"
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    />
                </div>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <div class="mt-1">
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    />
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="text-sm">
                    <router-link :to="{ name: 'client.login' }" class="font-medium text-blue-600 hover:text-blue-500">
                        Already have an account?
                    </router-link>
                </div>
            </div>

            <div>
                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    <span v-if="loading">Creating account...</span>
                    <span v-else>Register</span>
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
    name: '',
    email: '',
    tax_id: '',
    phone: '',
    password: '',
    password_confirmation: ''
});

async function register() {
    loading.value = true;
    errorMessage.value = '';

    try {
        const response = await fetch('/api/client/register', {
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
            throw new Error(data.message || 'Registration failed');
        }

        // Store the token
        localStorage.setItem('client_token', data.token);
        
        // Redirect to client dashboard
        router.push({ name: 'client.home' });
    } catch (error) {
        errorMessage.value = error.message || 'An error occurred during registration';
    } finally {
        loading.value = false;
    }
}
</script> 