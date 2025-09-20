import './bootstrap';
import '../css/app.css';
import { createApp } from 'vue';
import CurrencyConverter from './Components/CurrencyConverter.vue';

const app = createApp({});
app.component('currency-converter', CurrencyConverter);
app.mount('#app');
