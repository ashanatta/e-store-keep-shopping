import axios from 'axios'

// Configure Axios at the very top
axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL
axios.defaults.withCredentials = true
axios.defaults.withXSRFToken = true
axios.defaults.xsrfCookieName = 'XSRF-TOKEN'
axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import reveal from './directives/reveal'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import 'bootstrap-icons/font/bootstrap-icons.css'
// Design system must load after Bootstrap so its overrides win
import './assets/main.css'

createApp(App)
  .use(router)
  .directive('reveal', reveal)
  .mount('#app')
