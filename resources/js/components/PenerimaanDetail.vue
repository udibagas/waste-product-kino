<template>
    <div>
        <table class="table table-sm table-bordered">
            <tbody>
                <tr><td class="td-label">Tanggal</td><td class="td-value">{{data.tanggal | readableDate}}</td></tr>
                <tr><td class="td-label">No. Surat Jalan</td><td class="td-value">{{data.no_sj_keluar}}</td></tr>
                <tr><td class="td-label">User</td><td class="td-value">{{data.user}}</td></tr>
                <tr><td class="td-label">Lokasi Asal</td><td class="td-value">{{data.lokasi_asal}}</td></tr>
                <tr><td class="td-label">Lokasi Terima</td><td class="td-value">{{data.lokasi_terima}}</td></tr>
                <tr><td class="td-label">Penerima</td><td class="td-value">{{data.penerima}}</td></tr>
                <tr><td class="td-label">Keterangan</td><td class="td-value">{{data.keterangan}}</td></tr>
                <tr><td class="td-label">Status</td><td class="td-value"><el-tag size="small" :type="statuses[data.status].type">{{statuses[data.status].label}}</el-tag></td></tr>
            </tbody>
        </table>
        <table class="table table-sm table-bordered" v-if="data.items.length > 0">
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Kategori</th>
                    <th class="text-center" style="width:100px;">Kirim</th>
                    <th class="text-center" style="width:100px;">Terima</th>
                    <th class="text-center" style="width:100px;">Selisih</th>
                    <th class="text-center">Satuan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in data.items" :key="index">
                    <td>{{index+1}}.</td>
                    <td>{{item.barang.kode}} - {{item.barang.nama}}</td>
                    <td class="text-center">{{item.timbangan_manual_kirim | formatNumber}}</td>
                    <td class="text-center">{{item.timbangan_manual_terima | formatNumber}}</td>
                    <td class="text-center">{{item.timbangan_manual_kirim - item.timbangan_manual_terima | formatNumber}}</td>
                    <td class="text-center">{{item.eun}}</td>
                    <td>{{item.keterangan}}</td>
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
                {type: 'success', label: 'Submitted', value: 1}
            ]
        }
    },
    mounted() {
        console.log(this.data);
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
