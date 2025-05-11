import { createRouter, createWebHistory } from 'vue-router';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/admin',
            component: () => import('@/Layouts/AdminLayout.vue'),
            children: [
                {
                    path: '',
                    name: 'admin.dashboard',
                    component: () => import('@/Pages/Admin/Dashboard.vue')
                },
                {
                    path: 'customers',
                    name: 'admin.customers',
                    component: () => import('@/Pages/Admin/Customers.vue')
                },
                {
                    path: 'invoices',
                    name: 'admin.invoices',
                    component: () => import('@/Pages/Admin/Invoices.vue')
                },
                {
                    path: 'purchases',
                    name: 'admin.purchases',
                    component: () => import('@/Pages/Admin/Purchases.vue')
                },
                {
                    path: 'cashflow',
                    name: 'admin.cashflow',
                    component: () => import('@/Pages/Admin/Cashflow.vue')
                }
            ]
        },
        {
            path: '/client/login',
            name: 'client.login',
            component: () => import('@/Pages/Client/Auth/Login.vue')
        },
        {
            path: '/client/register',
            name: 'client.register',
            component: () => import('@/Pages/Client/Auth/Register.vue')
        },
        {
            path: '/client',
            component: () => import('@/Layouts/ClientLayout.vue'),
            children: [
                {
                    path: '',
                    name: 'client.home',
                    component: () => import('@/Pages/Client/Home.vue')
                },
                {
                    path: 'invoices',
                    name: 'client.invoices',
                    component: () => import('@/Pages/Client/Invoices.vue')
                },
                {
                    path: 'invoices/:id',
                    name: 'client.invoices.show',
                    component: () => import('@/Pages/Client/InvoiceShow.vue'),
                    props: true
                }
            ]
        }
    ]
});

export default router; 