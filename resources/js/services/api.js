import axios from 'axios';
import router from '../router';

// Create axios instance with custom config
const api = axios.create({
    baseURL: '/api',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json'
    }
});

// Add a response interceptor to handle authentication errors
api.interceptors.response.use(
    response => response,
    error => {
        // Redirect to login page on 401 Unauthorized errors
        if (error.response && error.response.status === 401) {
            // Check if we're already on a login page to prevent redirect loops
            const currentPath = window.location.pathname;
            if (!currentPath.includes('/login') && !currentPath.includes('/register')) {
                router.push({ name: 'client.login' });
            }
        }
        return Promise.reject(error);
    }
);

// Set token for client auth if available
const clientToken = localStorage.getItem('client_token');
if (clientToken) {
    api.defaults.headers.common['Authorization'] = `Bearer ${clientToken}`;
}

// API endpoints as methods
export default {
    // Customers
    customers: {
        getAll: (page = 1) => api.get(`/customers?page=${page}`),
        get: (id) => api.get(`/customers/${id}`),
        create: (data) => api.post('/customers', data),
        update: (id, data) => api.put(`/customers/${id}`, data),
        delete: (id) => api.delete(`/customers/${id}`)
    },

    // Invoices
    invoices: {
        getAll: (page = 1) => api.get(`/invoices?page=${page}`),
        get: (id) => api.get(`/invoices/${id}`),
        create: (data) => api.post('/invoices', data),
        update: (id, data) => api.put(`/invoices/${id}`, data),
        delete: (id) => api.delete(`/invoices/${id}`),
        generateWithAI: (description) => api.post('/ai/invoice', { description })
    },

    // Purchases
    purchases: {
        getAll: (page = 1) => api.get(`/purchases?page=${page}`),
        get: (id) => api.get(`/purchases/${id}`),
        create: (data) => api.post('/purchases', data),
        update: (id, data) => api.put(`/purchases/${id}`, data),
        delete: (id) => api.delete(`/purchases/${id}`)
    },

    // Auth
    auth: {
        login: (credentials) => api.post('/login', credentials),
        register: (userData) => api.post('/register', userData),
        logout: () => api.post('/logout'),
        getUser: () => api.get('/user')
    },
    
    // Client specific endpoints
    client: {
        invoices: {
            getAll: (page = 1) => api.get(`/client/invoices?page=${page}`),
            get: (id) => api.get(`/client/invoices/${id}`),
            getPaymentQR: (id) => api.get(`/client/invoices/${id}/payment`)
        },
        updateToken: (token) => {
            localStorage.setItem('client_token', token);
            api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        },
        clearToken: () => {
            localStorage.removeItem('client_token');
            delete api.defaults.headers.common['Authorization'];
        }
    }
}; 