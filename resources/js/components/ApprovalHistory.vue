<template>
    <table class="table table-sm table-striped table-hover table-bordered">
        <thead>
            <tr>
                <th class="text-center">Time</th>
                <th class="text-center">Level</th>
                <th>Note</th>
                <th>Approver</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="a in approval_histories" :key="a.id">
                <td class="text-center">{{a.created_at}}</td>
                <td class="text-center">{{a.level}}</td>
                <td>{{a.note}}</td>
                <td>{{a.user.name}}</td>
                <td class="text-center">
                    <el-tag size="small" :type="statuses[a.status].type">
                        {{statuses[a.status].label}}
                    </el-tag>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script>
export default {
    props: ['pengajuan'],
    data() {
        return {
            approval_histories: [],
            statuses: [
                {type: 'info', label: 'Pending'},
                {type: 'success', label: 'Approved'},
                {type: 'danger', label: 'Rejected'},
            ]
        }
    },
    methods: {
        getApprovalHistory() {
            axios.get('/pengajuanPenjualan/' + this.pengajuan + '/getApprovalHistory').then(r => {
                this.approval_histories = r.data
            }).then(e => console.log(e))
        }
    },
    created() {
        this.getApprovalHistory()
    }
}
</script>
