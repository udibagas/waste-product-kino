<template>
    <el-card>
        <table class="table table-sm table-bordered" style="width:900px">
            <thead>
                <tr>
                    <th rowspan="2">#</th>
                    <th rowspan="2">Kategori</th>
                    <th colspan="2" class="text-center">Stock</th>
                    <th colspan="2" class="text-center">Dikeluarkan</th>
                    <th colspan="2" class="text-center">Selisih</th>
                </tr>
                <tr>
                    <th style="width:100px" class="text-center">Jumlah</th>
                    <th style="width:100px" class="text-center">Berat</th>
                    <th style="width:100px" class="text-center">Jumlah</th>
                    <th style="width:100px" class="text-center">Berat</th>
                    <th style="width:100px" class="text-center">Jumlah</th>
                    <th style="width:100px" class="text-center">Berat</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in data.items" :key="index">
                    <td>{{index+1}}.</td>
                    <td>{{ item.barang.kode }} - {{ item.barang.nama }}</td>
                    <td class="text-center">{{item.stock_jumlah | formatNumber}} {{item.barang.unit}}</td>
                    <td class="text-center">{{item.stock_berat | formatNumber}} KG</td>
                    <td class="text-center">{{item.qty}} {{item.barang.unit}}</td>
                    <td class="text-center">{{item.timbangan_manual}} KG</td>
                    <td class="text-center">{{item.stock_jumlah - item.qty | formatNumber}} {{item.barang.unit}}</td>
                    <td class="text-center">{{item.stock_berat - item.timbangan_manual | formatNumber}} KG</td>
                </tr>
            </tbody>
        </table>
    </el-card>
</template>

<script>
export default {
    props: ['data'],
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
