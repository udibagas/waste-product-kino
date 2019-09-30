<template>
    <div>
        <el-row :gutter="15">
            <el-col :span="12">
                <table class="table table-sm table-bordered">
                    <tbody>
                        <tr><td class="td-label">Tanggal</td><td class="td-value">{{data.tanggal | readableDate}}</td></tr>
                        <tr><td class="td-label">No. Surat Jalan</td><td class="td-value">{{data.no_sj}}</td></tr>
                        <tr><td class="td-label">No. Pengajuan</td><td class="td-value">{{data.no_aju}}</td></tr>
                        <tr><td class="td-label">User</td><td class="td-value">{{data.user}}</td></tr>
                        <tr><td class="td-label">Plant</td><td class="td-value">{{data.location.plant}} - {{data.location.name}}</td></tr>
                        <tr><td class="td-label">Pembeli</td><td class="td-value">{{data.pembeli.nama}} - {{data.pembeli.kontak}}</td></tr>
                        <tr><td class="td-label">TOP Date</td><td class="td-value">{{data.top_date | readableDate}}</td></tr>
                    </tbody>
                </table>
            </el-col>
            <el-col :span="12">
                <table class="table table-sm table-bordered">
                    <tbody>
                        <tr><td class="td-label">Jembatan Timbang</td><td class="td-value">{{data.jembatan_timbang | formatNumber}} Kg</td></tr>
                        <tr><td class="td-label">Value</td><td class="td-value">Rp {{data.value | formatNumber}}</td></tr>
                        <tr><td class="td-label">Terbayar</td><td class="td-value">Rp {{data.terbayar | formatNumber}}</td></tr>
                        <tr><td class="td-label">Outstanding</td><td class="td-value">Rp {{data.value - data.terbayar | formatNumber}}</td></tr>
                        <tr>
                            <td class="td-label">Status</td>
                            <td class="td-value">
                                <el-tag size="small" :type="statuses[data.status].type">{{statuses[data.status].label}}</el-tag>
                            </td>
                        </tr>
                        <tr>
                            <td class="td-label"> Status Pembayaran</td>
                            <td class="td-value">
                                <el-tag size="small" :type="statuses_pembayaran[data.status_pembayaran].type">
                                    {{statuses_pembayaran[data.status_pembayaran].label}}
                                </el-tag>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </el-col>
        </el-row>

        <el-tabs type="border-card">
            <el-tab-pane label="SUMMARY ITEM">
                <table class="table table-sm table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th class="text-center">Kategori</th>
                            <th class="text-center">Berat</th>
                            <th class="text-center">Terjual</th>
                            <th class="text-center">Sisa</th>
                            <th class="text-center">Dijual</th>
                            <th class="text-center">Price Per Kg</th>
                            <th class="text-center">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in data.summaryItems" :key="index">
                            <td>{{index+1}}.</td>
                            <td>{{item.kategori}}</td>
                            <td class="text-right">{{item.berat.toFixed(4) | formatNumber}} kg</td>
                            <td class="text-right">{{item.terjual.toFixed(4) | formatNumber}} kg</td>
                            <td class="text-right">{{(item.berat - item.terjual).toFixed(4) | formatNumber}} kg</td>
                            <td class="text-right">{{item.berat_dijual.toFixed(4) | formatNumber}} kg</td>
                            <td class="text-right">Rp {{item.price_per_unit | formatNumber}}</td>
                            <td class="text-right">Rp {{(item.berat_dijual * item.price_per_unit).toFixed(0) | formatNumber}}</td>
                        </tr>
                    </tbody>
                </table>
            </el-tab-pane>
            <el-tab-pane label="DETAIL ITEM">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th class="text-center">Kategori</th>
                            <th class="text-center">Material</th>
                            <th class="text-center">Material Description</th>
                            <th class="text-center">Berat Diajukan (kg)</th>
                            <th class="text-center">Berat Terjual (kg)</th>
                            <th class="text-center">Sisa (kg)</th>
                            <th class="text-center">Berat Dijual (kg)</th>
                            <th class="text-center">Price Per Unit (Rp)</th>
                            <th class="text-center">Value (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in data.items_wp" :key="index">
                            <td>{{index+1}}.</td>
                            <td>{{item.kategori}}</td>
                            <td>{{item.material_id}}</td>
                            <td>{{item.material_description}}</td>
                            <td class="text-right">{{item.berat | formatNumber}} kg</td>
                            <td class="text-right">{{item.terjual | formatNumber}} kg</td>
                            <td class="text-right">{{item.berat - item.terjual | formatNumber}} kg</td>
                            <td class="text-right">{{item.berat_dijual | formatNumber}} kg</td>
                            <td class="text-right">Rp {{item.price_per_unit | formatNumber}}</td>
                            <td class="text-right">Rp {{item.value.toFixed(0) | formatNumber}}</td>
                        </tr>
                    </tbody>
                </table>
            </el-tab-pane>
            <el-tab-pane label="PEMBAYARAN">
                <Pembayaran :data="data.pembayaran" />
            </el-tab-pane>
        </el-tabs>
    </div>
</template>

<script>
import Pembayaran from './Pembayaran'

export default {
    components: { Pembayaran },
    props: ['data'],
    data() {
        return {
            statuses: [
                {type: 'info', label: 'Draft', value: 0},
                {type: 'warning', label: 'Submitted', value: 1},
                {type: 'success', label: 'Approved', value: 2},
            ],
            statuses_pembayaran: [
                {type: 'info', label: 'No Payment', value: 0},
                {type: 'warning', label: 'Partial', value: 1},
                {type: 'success', label: 'Paid', value: 2},
            ]
        }
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
