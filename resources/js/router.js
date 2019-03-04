import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from './pages/Home'
import User from './pages/User'
import Pembeli from './pages/Pembeli'
import Penerimaan from './pages/Penerimaan'
import Pengeluaran from './pages/Pengeluaran'
import Penjualan from './pages/Penjualan'
import KategoriBarang from './pages/KategoriBarang'

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
    {
        name: 'pembeli',
        path: '/pembeli',
        component: Pembeli
    },
    {
        name: 'pengeluaran',
        path: '/pengeluaran',
        component: Pengeluaran
    },
    {
        name: 'penerimaan',
        path: '/penerimaan',
        component: Penerimaan
    },
    {
        name: 'penjualan',
        path: '/penjualan',
        component: Penjualan
    },
    {
        name: 'kategoriBarang',
        path: '/kategoriBarang',
        component: KategoriBarang
    },
]

export default new VueRouter({
    base: 'app',
    mode: 'history',
    routes: routes
})