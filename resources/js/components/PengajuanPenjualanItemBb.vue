<template>
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th rowspan="2">#</th>
                <th rowspan="2">Kategori</th>
                <th colspan="2" class="text-center">Stock</th>
                <th colspan="2" class="text-center">Pengajuan</th>
                <th colspan="2" class="text-center">Selisih</th>
            </tr>
            <tr>
                <th style="width:80px" class="text-center">Qty (pcs)</th>
                <th style="width:80px" class="text-center">Berat (kg)</th>
                <th style="width:80px" class="text-center">Qty (pcs)</th>
                <th style="width:80px" class="text-center">Berat (kg)</th>
                <th style="width:80px" class="text-center">Qty (pcs)</th>
                <th style="width:80px" class="text-center">Berat (kg)</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(item, index) in data.items_bb" :key="index">
                <td>{{index+1}}.</td>
                <td>{{item.kategori.kode}} - {{item.kategori.nama}}</td>
                <td class="text-center">{{item.stock_qty | formatNumber}}</td>
                <td class="text-center">{{item.stock_berat | formatNumber}}</td>
                <td class="text-center">{{item.jumlah | formatNumber}}</td>
                <td class="text-center">{{item.timbangan_manual | formatNumber}}</td>
                <td class="text-center">{{item.stock_qty - item.jumlah | formatNumber}}</td>
                <td class="text-center">{{item.stock_berat - item.timbangan_manual | formatNumber}}</td>
            </tr>
        </tbody>
    </table>
</template>

<script>
export default {
    props: ['data']
}
</script>

<style scoped>
</style>
