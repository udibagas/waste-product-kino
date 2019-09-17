<template>
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Kategori</th>
                <th class="text-center">Stock</th>
                <th class="text-center">Pengajuan</th>
                <th class="text-center">Selisih</th>
                <th class="text-center">Satuan</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(item, index) in data.items_bb" :key="index">
                <td>{{index+1}}.</td>
                <td>{{item.kategori.kode}} - {{item.kategori.nama}}</td>
                <td class="text-center">{{item.stock_berat | formatNumber}}</td>
                <td class="text-center">{{item.timbangan_manual | formatNumber}}</td>
                <td class="text-center">{{item.stock_berat - item.timbangan_manual | formatNumber}}</td>
                <td class="text-center">{{item.kategori.unit}}</td>
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
