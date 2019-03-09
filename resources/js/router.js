import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from './pages/Home'
import User from './pages/User'
import Location from './pages/Location'
import SkemaApprovalPenjualan from './pages/SkemaApprovalPenjualan'
import Pembeli from './pages/Pembeli'
import Penerimaan from './pages/Penerimaan'
import Pengeluaran from './pages/Pengeluaran'
import Penjualan from './pages/Penjualan'
import PengajuanPenjualan from './pages/PengajuanPenjualan'
import InOutStockBb from './pages/InOutStockBb'
import StockBb from './pages/StockBb'
import ReportBb from './pages/ReportBb'
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
        name: 'pengajuanPenjualan',
        path: '/pengajuanPenjualan',
        component: PengajuanPenjualan
    },
    {
        name: 'location',
        path: '/location',
        component: Location
    },
    {
        name: 'inOutStockBb',
        path: '/inOutStockBb',
        component: InOutStockBb
    },
    {
        name: 'stockBb',
        path: '/stockBb',
        component: StockBb
    },
    {
        name: 'reportBb',
        path: '/reportBb',
        component: ReportBb
    },
    {
        name: 'skemaApprovalPenjualan',
        path: '/skemaApprovalPenjualan',
        component: SkemaApprovalPenjualan
    },
    {
        name: 'kategoriBarang',
        path: '/kategoriBarang',
        component: KategoriBarang
    },
]

export default new VueRouter({
    base: APP_BASE + 'app',
    mode: 'history',
    routes: routes
})