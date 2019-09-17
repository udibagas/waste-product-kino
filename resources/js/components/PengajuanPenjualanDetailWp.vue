<template>
    <div>
        <el-row :gutter="15">
            <el-col :span="12">
                <table class="table table-sm table-bordered">
                    <tbody>
                        <tr><td class="td-label">Tanggal</td><td class="td-value">{{data.tanggal | readableDate}}</td></tr>
                        <tr><td class="td-label">No. Pengajuan</td><td class="td-value">{{data.no_aju}}</td></tr>
                        <tr><td class="td-label">Plant</td><td class="td-value">{{data.location.plant}} - {{data.location.name}}</td></tr>
                        <tr><td class="td-label">Periode</td><td class="td-value">{{data.period_from | readableDate}} - {{data.period_to | readableDate}}</td></tr>
                        <tr><td class="td-label">MVT Type</td><td class="td-value">{{data.mvt_type}}</td></tr>
                    </tbody>
                </table>
            </el-col>
            <el-col :span="12">
                <table class="table table-sm table-bordered">
                    <tbody>
                        <tr><td class="td-label">Sloc</td><td class="td-value">{{data.sloc}}</td></tr>
                        <tr><td class="td-label">Yang mengajukan</td><td class="td-value">{{data.user.name}}</td></tr>
                        <tr>
                            <td class="td-label"> Approval 1</td>
                            <td class="td-value">
                                <el-tag size="small" :type="approval_statuses[data.approval1_status].type">
                                    {{approval_statuses[data.approval1_status].label}}
                                </el-tag>
                            </td>
                        </tr>
                        <tr>
                            <td class="td-label"> Approval 2</td>
                            <td class="td-value">
                                <el-tag size="small" :type="approval_statuses[data.approval2_status].type">
                                    {{approval_statuses[data.approval2_status].label}}
                                </el-tag>
                            </td>
                        </tr>
                        <tr>
                            <td class="td-label">Status</td>
                            <td class="td-value">
                                <el-tag size="small" :type="statuses[data.status].type">{{statuses[data.status].label}}</el-tag>
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
                            <th class="text-center">Stock</th>
                            <th class="text-center">Diajukan</th>
                            <th class="text-center">Selisih</th>
                            <th class="text-center">Price Per Kg</th>
                            <th class="text-center">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in data.summaryItems" :key="index">
                            <td>{{index+1}}.</td>
                            <td>{{item.kategori}}</td>
                            <td class="text-right">{{parseFloat(item.stock).toFixed(4) | formatNumber}} kg</td>
                            <td class="text-right">{{parseFloat(item.berat).toFixed(4) | formatNumber}} kg</td>
                            <td class="text-right">{{(item.stock - item.berat).toFixed(4) | formatNumber}} kg</td>
                            <td class="text-right">Rp {{item.price_per_unit | formatNumber}}</td>
                            <td class="text-right">Rp {{item.value | formatNumber}}</td>
                        </tr>
                    </tbody>
                </table>
            </el-tab-pane>
            <el-tab-pane label="DETAIL ITEM">
                <table class="table table-sm table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th class="text-center">Kategori</th>
                            <th class="text-center">Material</th>
                            <th class="text-center">Material Description</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Diajukan</th>
                            <th class="text-center">Selisih</th>
                            <th class="text-center">Price Per Kg</th>
                            <th class="text-center">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in data.items_wp" :key="index">
                            <td>{{index+1}}.</td>
                            <td>{{item.kategori}}</td>
                            <td>{{item.material_id}}</td>
                            <td>{{item.material_description}}</td>
                            <td class="text-right">{{(item.stock/1000).toFixed(4) | formatNumber}} kg</td>
                            <td class="text-right">{{item.berat | formatNumber}} kg</td>
                            <td class="text-right">{{((item.stock/1000) - item.berat).toFixed(4) | formatNumber}} kg</td>
                            <td class="text-right">Rp {{item.price_per_unit | formatNumber}}</td>
                            <td class="text-right">Rp {{item.value | formatNumber}}</td>
                        </tr>
                    </tbody>
                </table>
            </el-tab-pane>
            <el-tab-pane label="APPROVAL HISTORY">
                <ApprovalHistory :pengajuan="data.id" />
            </el-tab-pane>
        </el-tabs>

    </div>
</template>

<script>
import ApprovalHistory from './ApprovalHistory'

export default {
    components: { ApprovalHistory },
    props: ['data'],
    data() {
        return {
            statuses: [
                {type: 'info', label: 'Draft'},
                {type: 'warning', label: 'Submitted'},
                {type: 'success', label: 'Approved'},
                {type: 'danger', label: 'Rejected', value: 3},
                {type: '', label: 'Processed', value: 4},
            ],
            approval_statuses: [
                {type: 'info', label: 'Pending', value: 0},
                {type: 'success', label: 'Approved', value: 1},
                {type: 'danger', label: 'Rejected', value: 2},
            ],
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
