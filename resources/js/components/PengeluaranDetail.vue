<template>
    <div>
        <table class="table table-sm table-bordered">
            <tbody>
                <tr><td class="td-label">Tanggal</td><td class="td-value">{{data.tanggal | readableDate}}</td></tr>
                <tr><td class="td-label">No. Surat Jalan</td><td class="td-value">{{data.no_sj}}</td></tr>
                <tr><td class="td-label">User</td><td class="td-value">{{data.user}}</td></tr>
                <tr><td class="td-label">Lokasi Asal</td><td class="td-value">{{data.lokasi_asal}}</td></tr>
                <tr><td class="td-label">Lokasi Terima</td><td class="td-value">{{data.lokasi_terima}}</td></tr>
                <tr><td class="td-label">Penerima</td><td class="td-value">{{data.penerima}}</td></tr>
                <tr><td class="td-label">Jembatan Timbang</td><td class="td-value">{{data.jembatan_timbang | formatNumber}} KG</td></tr>
                <tr><td class="td-label">Status</td><td class="td-value"><el-tag size="small" :type="statuses[data.status].type">{{statuses[data.status].label}}</el-tag></td></tr>
            </tbody>
        </table>
        <table class="table table-sm table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kategori</th>
                    <th class="text-center">Stock</th>
                    <th class="text-center">Dikeluarkan</th>
                    <th class="text-center">Selisih</th>
                    <th class="text-center">Unit</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in data.items" :key="index">
                    <td>{{index+1}}.</td>
                    <td>{{ item.barang.kode }} - {{ item.barang.nama }}</td>
                    <td class="text-center">{{item.stock_berat | formatNumber}}</td>
                    <td class="text-center">{{item.timbangan_manual}}</td>
                    <td class="text-center">{{item.stock_berat - item.timbangan_manual | formatNumber}}</td>
                    <td class="text-center">{{ item.eun }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    props: ['data'],
    data() {
        return {
            statuses: [
                {type: 'info', label: 'Draft', value: 0},
                {type: 'warning', label: 'Submitted', value: 1},
                {type: 'success', label: 'Received', value: 2},
            ]
        }
    },
    created() {
        axios.get('/stockBb/getStockList').then(r => {
            this.data.items.forEach(i => {
                let stock = r.data.find(d => d.kategori_barang_id == i.kategori_barang_id && d.location_id == this.data.lokasi_asal_id)
                if (stock) {
                    i.stock_jumlah = stock.qty
                    i.stock_berat = stock.stock
                    i.kategori = stock.kategori
                } else {
                    i.stock_jumlah = 0
                    i.stock_berat = 0
                    i.kategori = this.$store.state.kategoriBarangList.find(k => k.id == i.kategori_barang_id)
                }
            })
            this.$forceUpdate()
        }).catch(e => {
            this.$message({
                message: 'Failed to get stock data.' + e,
                type: 'error',
                showClose: true
            });
        })
    }
}
</script>

<style scoped>
.td-label {
    width: 150px;
    background-color: #333c58;
    font-weight: bold;
    color: #fff;
}

.td-value {
    background-color: #efefef;
}
</style>
