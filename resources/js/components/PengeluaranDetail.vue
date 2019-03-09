<template>
    <div>
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th class="text-center">Jenis</th>
                    <th class="text-center">Kode Kategori</th>
                    <th>Kategori</th>
                    <th class="text-center">Qty</th>
                    <th class="text-center">Eun</th>
                    <th class="text-center">Timbangan Manual (kg)</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in data.items" :key="index">
                    <td>{{index+1}}.</td>
                    <td class="text-center">{{item.barang.jenis}}</td>
                    <td class="text-center">{{item.barang.kode}}</td>
                    <td>{{item.barang.nama}}</td>
                    <td class="text-center">{{item.qty | formatNumber}}</td>
                    <td class="text-center">{{item.eun}}</td>
                    <td class="text-center">{{item.timbangan_manual | formatNumber}}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    props: ['data']
}
</script>

<style scoped>

</style>