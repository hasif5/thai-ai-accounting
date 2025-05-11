import '../css/app.css';
import './bootstrap';

import { createApp } from 'vue';
import router from './router';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Toast from './Components/Toast.vue';

const app = createApp({});

// Add global components
app.component('Toast', Toast);

app.use(router)
   .use(ZiggyVue)
   .mount('#app');
