import '../css/app.css';
import './bootstrap';

import { createApp } from 'vue';
import router from './router';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const app = createApp({});

app.use(router)
   .use(ZiggyVue)
   .mount('#app');
