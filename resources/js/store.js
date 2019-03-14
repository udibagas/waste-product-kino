import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        pagerOptions: [
            { value: 10, label: 10 },
            { value: 25, label: 25 },
            { value: 50, label: 50 },
            { value: 100, label: 100 },
        ],
        kategoriBarangList: [],
        pengeluaranList: [],
        locationList: [],
        userList: [],
        menuList: []
    },
    mutations: {
        getKategoriBarangList(state) {
            axios.get(BASE_URL + '/kategoriBarang/getList')
                .then(r => state.kategoriBarangList = r.data)
                .catch(e => console.log(e))
        },
        getPengeluaranList(state) {
            axios.get(BASE_URL + '/pengeluaran/getList')
                .then(r => state.pengeluaranList = r.data)
                .catch(e => console.log(e))
        },
        getLocationList(state) {
            axios.get(BASE_URL + '/location/getList')
                .then(r => state.locationList = r.data)
                .catch(e => console.log(e))
        },
        getUserList(state) {
            axios.get(BASE_URL + '/user/getList')
                .then(r => state.userList = r.data)
                .catch(e => console.log(e))
        },
        getMenuList(state) {
            axios.get(BASE_URL + '/navigation')
                .then(r => state.menuList = r.data)
                .catch(e => console.log(e))
        }
    }
})