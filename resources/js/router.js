import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from './pages/Home'
import User from './pages/User'

Vue.use(VueRouter);

const routes = [
    {
        name: 'home',
        path: '/home',
        component: Home
    },
    {
        name: 'user',
        path: '/user',
        component: User
    },
]

export default new VueRouter({
    base: 'app',
    mode: 'history',
    routes: routes
})