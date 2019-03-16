import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from './pages/Home'
import User from './pages/User'
import Location from './pages/Location'
import SkemaApprovalPenjualan from './pages/SkemaApprovalPenjualan'
import Pembeli from './pages/Pembeli'
import Penerimaan from './pages/Penerimaan'
import Pengeluaran from './pages/Pengeluaran'
import PenjualanBb from './pages/PenjualanBb'
import PengajuanPenjualanBb from './pages/PengajuanPenjualanBb'
// import PengajuanPenjualanWp from './pages/PengajuanPenjualanWp'
import InOutStockBb from './pages/InOutStockBb'
import StockBb from './pages/StockBb'
import ReportBb from './pages/ReportBb'
import KategoriBarang from './pages/KategoriBarang'
import KonversiBerat from './pages/KonversiBerat'
import ApprovalPengajuanPenjualan from './pages/ApprovalPengajuanPenjualan'
import Profile from './pages/Profile'

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
        name: 'profile',
        path: '/profile',
        component: Profile
    },
    {
        name: 'penjualanBb',
        path: '/penjualanBb',
        component: PenjualanBb
    },
    {
        name: 'pengajuanPenjualanBb',
        path: '/pengajuanPenjualanBb',
        component: PengajuanPenjualanBb,
    },
    // {
    //     name: 'pengajuanPenjualanWp',
    //     path: '/pengajuanPenjualanWp',
    //     component: PengajuanPenjualanWp,
    // },
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
    {
        name: 'konversiBerat',
        path: '/konversiBerat',
        component: KonversiBerat
    },
    {
        name: 'approvalPengajuanPenjualan',
        path: '/approvalPengajuanPenjualan/:id',
        component: ApprovalPengajuanPenjualan,
        props: true
    },
    {
        path: '*',
        component: Home
    }
]

export default new VueRouter({
    base: APP_BASE + 'app',
    mode: 'history',
    routes: routes
})