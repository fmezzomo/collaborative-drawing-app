import './bootstrap';
import { createApp } from 'vue';
import store from './store'; 
import DrawingCanvas from './components/DrawingCanvas.vue';


const app = createApp({});

import App from './components/App.vue';
//app.component('App', App);
app.component('drawing-canvas', DrawingCanvas);
app.use(store);

app.mount('#app');
