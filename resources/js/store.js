import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        kategoriBarangList: [],
        pengeluaranList: [],
        locationList: [],
        userList: [],
        menuList: [],
        pembeliList: [],
        pengajuanPenjualanList: [],
        slocList: [],
        mvtList: [],
        matList: []
    },
    mutations: {
        getKategoriBarangList(state) {
            axios
                .get("/kategoriBarang/getList")
                .then(r => (state.kategoriBarangList = r.data))
                .catch(e => console.log(e));
        },
        getPengeluaranList(state) {
            axios
                .get("/pengeluaran/getList")
                .then(r => (state.pengeluaranList = r.data))
                .catch(e => console.log(e));
        },
        getLocationList(state) {
            axios
                .get("/location/getList")
                .then(r => (state.locationList = r.data))
                .catch(e => console.log(e));
        },
        getUserList(state) {
            axios
                .get("/user/getList")
                .then(r => (state.userList = r.data))
                .catch(e => console.log(e));
        },
        getPembeliList(state) {
            axios
                .get("/pembeli/getList")
                .then(r => (state.pembeliList = r.data))
                .catch(e => console.log(e));
        },
        getPengajuanPenjualanList(state, jenis) {
            axios
                .get("/pengajuanPenjualan/getList", { params: { jenis: jenis }})
                .then(r => (state.pengajuanPenjualanList = r.data))
                .catch(e => console.log(e));
        },
        getMenuList(state) {
            axios
                .get("/navigation")
                .then(r => (state.menuList = r.data))
                .catch(e => console.log(e));
        },
        getSlocList(state) {
            axios
                .get("/getSlocList")
                .then(r => (state.slocList = r.data))
                .catch(e => console.log(e));
        },
        getMvtList(state) {
            axios
                .get("/getMvtList")
                .then(r => (state.mvtList = r.data))
                .catch(e => console.log(e));
        },
        getMatList(state) {
            axios
                .get("/getMatList")
                .then(r => (state.matList = r.data))
                .catch(e => console.log(e));
        }
    }
});
