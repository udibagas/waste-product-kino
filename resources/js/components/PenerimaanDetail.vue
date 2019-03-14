<template>
    <div>
        <table class="table table-sm table-bordered" v-if="data.items.length > 0">
            <thead>
                <tr>
                    <th rowspan="2" style="width:40px">#</th>
                    <th rowspan="2">Kategori</th>
                    <th colspan="3" class="text-center">Qty</th>
                    <th rowspan="2" class="text-center" style="width:50px">Eun</th>
                    <th colspan="3" class="text-center">Timbangan Manual (kg)</th>
                    <th rowspan="2">Keterangan</th>
                </tr>
                <tr>
                    <th class="text-center" style="width:90px;">Kirim</th>
                    <th class="text-center" style="width:90px;">Terima</th>
                    <th class="text-center" style="width:90px;">Selisih</th>
                    <th class="text-center" style="width:90px;">Kirim</th>
                    <th class="text-center" style="width:90px;">Terima</th>
                    <th class="text-center" style="width:90px;">Selisih</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in data.items" :key="index">
                    <td>{{index+1}}.</td>
                    <td>{{item.barang.kode}} - {{item.barang.nama}}</td>
                    <td class="text-center">{{item.qty_kirim | formatNumber}}</td>
                    <td class="text-center">{{item.qty_terima | formatNumber}}</td>
                    <td class="text-center">{{item.qty_kirim - item.qty_terima | formatNumber}}</td>
                    <td class="text-center">{{item.eun}}</td>
                    <td class="text-center">{{item.timbangan_manual_kirim | formatNumber}}</td>
                    <td class="text-center">{{item.timbangan_manual_terima | formatNumber}}</td>
                    <td class="text-center">{{item.timbangan_manual_kirim - item.timbangan_manual_terima | formatNumber}}</td>
                    <td>{{item.keterangan}}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    props: ['data'],
    mounted() {
        console.log(this.data);
    }
}
</script>

<style scoped>

</style>