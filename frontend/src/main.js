import './assets/main.css'

import { createApp } from 'vue'
import App from './views/Home.vue'
import Header from './components/Header.vue';
import router from './router'

const app = createApp(App)
app.use(router)
app.mount('#app')

app.component('Header', Header);
