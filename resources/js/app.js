require('./bootstrap');

import Vue from 'vue'
import ElementUI from 'element-ui';
import locale from 'element-ui/lib/locale/lang/en'

Vue.use(ElementUI, { locale })

import App from './App'
import router from './router'
import store from './store'

const app = new Vue({
    el: '#app',
    components: { App },
    router,
    store
});
