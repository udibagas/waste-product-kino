<template>
    <div>
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Kategori</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-center">Jumlah Diajukan</th>
                    <th class="text-center">Selisih</th>
                    <th class="text-center">Timbangan Manual (kg)</th>
                    <th class="text-center">Eun</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in data.items_bb" :key="index">
                    <td>{{index+1}}.</td>
                    <td>{{item.kategori.kode}} - {{item.kategori.nama}}</td>
                    <td class="text-center">{{item.jumlah | formatNumber}}</td>
                    <td class="text-center">{{item.jumlah_terima | formatNumber}}</td>
                    <td class="text-center">{{item.jumlah - item.jumlah_terima | formatNumber}}</td>
                    <td class="text-center">{{item.timbangan_manual | formatNumber}}</td>
                    <td class="text-center">{{item.eun}}</td>
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