<template>
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th rowspan="2" class="text-center">#</th>
                <th rowspan="2">Kategori</th>
                <th rowspan="2" class="text-center">Stock</th>
                <th rowspan="2" class="text-center">Satuan</th>
                <th colspan="3" class="text-center">Pengajuan</th>
                <th colspan="3" class="text-center">Aktual</th>
            </tr>
            <tr>
                <th class="text-center">Berat/Qty</th>
                <th class="text-center">Harga/Satuan Sistem</th>
                <th class="text-center">Value Jual Sistem</th>
                <th class="text-center">Berat/Qty</th>
                <th class="text-center">Harga/Satuan Aktual</th>
                <th class="text-center">Value Jual Aktual</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(item, index) in data.items_bb" :key="index">
                <td class="text-center">{{index+1}}</td>
                <td>{{item.kategori.kode}} - {{item.kategori.nama}}</td>
                <td class="text-center">{{item.stock_berat | formatNumber}}</td>
                <td class="text-center">{{item.kategori.unit}}</td>
                <td class="text-center">{{item.timbangan_manual | formatNumber}}</td>
                <td class="text-center">Rp {{item.kategori.harga | formatNumber}}</td>
                <td class="text-center">Rp {{item.kategori.harga * item.timbangan_manual | formatNumber}}</td>
                <td class="text-center">{{item.jembatan_timbang | formatNumber}}</td>
                <td class="text-center">Rp {{item.price_per_kg | formatNumber}}</td>
                <td class="text-center">Rp {{item.jembatan_timbang * item.price_per_kg | formatNumber}}</td>
            </tr>
        </tbody>
    </table>
</template>

<script>
export default {
    props: ['data']
}
</script>

<style lang="scss" scoped>

</style>
