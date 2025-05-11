import '../css/app.css';
import './bootstrap';

import { createApp } from 'vue';
import router from './router';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Toast from './Components/Toast.vue';
import i18n, { formatDate, formatCurrency } from './i18n';

const app = createApp({});

// Add global components
app.component('Toast', Toast);

// Add global methods
app.config.globalProperties.$formatDate = formatDate;
app.config.globalProperties.$formatCurrency = formatCurrency;

app.use(router)
   .use(ZiggyVue)
   .use(i18n)
   .mount('#app');
