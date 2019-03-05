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
        pengeluaranList: []
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
        }
    }
})