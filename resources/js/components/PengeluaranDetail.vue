<template>
    <el-card>
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th style="width:30px">#</th>
                    <th>Kategori</th>
                    <th class="text-center" style="width:70px">Qty</th>
                    <th class="text-center" style="width:150px">Timbangan Manual (kg)</th>
                    <th class="text-center" style="width:70px">Eun</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in data.items" :key="index">
                    <td>{{index+1}}.</td>
                    <td>{{item.barang.kode}} - {{item.barang.nama}}</td>
                    <td class="text-center">{{item.qty | formatNumber}}</td>
                    <td class="text-right">{{item.timbangan_manual | formatNumber}}</td>
                    <td class="text-center">{{item.eun}}</td>
                </tr>
            </tbody>
        </table>
    </el-card>
</template>

<script>
export default {
    props: ['data']
}
</script>

<style scoped>

</style>