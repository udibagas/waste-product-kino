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
import PenjualanWp from './pages/PenjualanWp'
import PengajuanPenjualanBb from './pages/PengajuanPenjualanBb'
import PengajuanPenjualanWp from './pages/PengajuanPenjualanWp'
import InOutStockBb from './pages/InOutStockBb'
import InOutStockWp from './pages/InOutStockWp'
import StockBb from './pages/StockBb'
import StockWp from './pages/StockWp'
import ReportBb from './pages/ReportBb'
import ReportWp from './pages/ReportWp'
import KategoriBarang from './pages/KategoriBarang'
import KonversiBerat from './pages/KonversiBerat'
import ApprovalPengajuanPenjualan from './pages/ApprovalPengajuanPenjualan'
import { Message } from 'element-ui';

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
        name: 'penjualanBb',
        path: '/penjualanBb',
        component: PenjualanBb
    },
    {
        name: 'penjualanWp',
        path: '/penjualanWp',
        component: PenjualanWp
    },
    {
        name: 'pengajuanPenjualanBb',
        path: '/pengajuanPenjualanBb',
        component: PengajuanPenjualanBb,
    },
    {
        name: 'pengajuanPenjualanWp',
        path: '/pengajuanPenjualanWp',
        component: PengajuanPenjualanWp,
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
        name: 'inOutStockWp',
        path: '/inOutStockWp',
        component: InOutStockWp
    },
    {
        name: 'stockBb',
        path: '/stockBb',
        component: StockBb
    },
    {
        name: 'stockWp',
        path: '/stockWp',
        component: StockWp
    },
    {
        name: 'reportBb',
        path: '/reportBb',
        component: ReportBb
    },
    {
        name: 'reportWp',
        path: '/reportWp',
        component: ReportWp
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

const router = new VueRouter({
    base: APP_BASE + 'app',
    mode: 'history',
    routes: routes
})

router.beforeEach((to, from, next) => {
    let params = { route: to.path }
    axios.get('/checkAuth', { params: params }).then(r => {
        next()
    }).catch(e => {
        Message({
            message: 'You have no right to access this menu or your session is expired. Please refresh your browser.',
            type: 'error',
            showClose: true,
            duration: 10000
        })
        next(false)
    })
});

export default router
