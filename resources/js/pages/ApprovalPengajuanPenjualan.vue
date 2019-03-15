<template>
    <el-card>
        <h4>Approval Pengajuan Penjualan</h4>
        <hr>
        <div class="panel" v-loading="submitting">
            <table>
                <tbody>
                    <tr><th class="label">No. Pengajuan</th><td>: {{data.no_aju}}</td></tr>
                    <tr><th class="label">Tanggal</th><td>: {{data.tanggal}}</td></tr>
                    <tr><th class="label">Plant</th><td>: {{data.location.plant}} - {{data.location.name}}</td></tr>
                    <tr><th class="label">Periode</th><td>: {{data.period_from}} - {{data.period_to}}</td></tr>
                    <tr><th class="label">Jenis</th><td>: {{data.jenis}}</td></tr>
                    <tr>
                        <th class="label">Approval 1</th>
                        <td>: <el-tag :type="statuses[data.approval1_status].type" size="small">{{statuses[data.approval1_status].label}}</el-tag></td>
                    </tr>
                    <tr>
                        <th class="label">Approval 2</th>
                        <td>: <el-tag :type="statuses[data.approval2_status].type" size="small">{{statuses[data.approval2_status].label}}</el-tag></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br>
        <PengajuanPenjualanDetailBb :data="data" />

        <br>
        <el-button v-if="!submitted" :disabled="submitting" type="primary" icon="el-icon-success" @click="approve(1)">APPROVE</el-button>
        <el-button v-if="!submitted" :disabled="submitting" type="danger" icon="el-icon-error" @click="approve(2)">REJECT</el-button>
    </el-card>
</template>

<script>
import PengajuanPenjualanDetailBb from '../components/PengajuanPenjualanDetailBb'

export default {
    components: { PengajuanPenjualanDetailBb },
    props: ['id'],
    data() {
        return {
            params: this.$route.query,
            data: {},
            submitting: false,
            submitted: false,
            statuses: [
                {type: 'info', label: 'Pending'},
                {type: 'success', label: 'Approved'},
                {type: 'danger', label: 'Rejected'},
            ]
        }
    },
    methods: {
        requestData() {
            axios.get(BASE_URL + '/pengajuanPenjualan/' + this.id).then(r => {
                this.data = r.data
            }).catch(e => {
                this.$message({
                    message: 'Error fetching data : ' + e.response.data.message,
                    type: 'error',
                    showClose: true
                });
            })
        },
        approve(status) {
            this.$confirm('Anda yakin?', 'Warning', {
                type: 'warning',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then(() => {
                this.submitting = true
                let data = {
                    status: status,
                    level: this.params.level
                }

                axios.put(BASE_URL + '/pengajuanPenjualan/' + this.id + '/approve', data).then(r => {
                    this.submitting = false
                    this.submitted = true
                    this.requestData()
                    this.$message({
                        message: 'Approval berhasil',
                        type: 'success',
                        showClose: true
                    });
                }).catch(e => {
                    this.submitting = false
                    this.$message({
                        message: 'Error : ' + e.response.data.message,
                        type: 'error',
                        showClose: true
                    });
                })
            }).catch(() => {})
        }
    },
    mounted() {
        this.requestData()
    }
}
</script>

<style scoped>
th.label {
    width: 120px;
}

/* .panel {
    padding: 15px;
    background-color: #eee;
} */
</style>