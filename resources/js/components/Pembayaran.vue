<template>
    <table class="table table-sm table-striped table-bordered table-hover" style="width: 500px">
        <thead>
            <tr>
                <th class="text-center">Date</th>
                <th class="text-center">Value</th>
                <th class="text-center">User</th>
                <th>Keterangan</th>
                <!-- <th class="text-center">Status</th> -->
            </tr>
        </thead>
        <tbody>
            <tr v-for="d in data" :key="d.id">
                <td class="text-center">{{d.tanggal | readableDate}}</td>
                <td class="text-center">Rp {{d.value | formatNumber}}</td>
                <td class="text-center">{{d.user}}</td>
                <td>{{d.keterangan}}</td>
                <!-- <td class="text-center">
                    <el-tag size="small" :type="statuses[d.status].type">
                        {{statuses[d.status].label}}
                    </el-tag>
                </td> -->
            </tr>
        </tbody>
    </table>
</template>

<script>
export default {
    props: ['data'],
    data() {
        return {
            statuses: [
                {type: 'info', label: 'Draft'},
                {type: 'success', label: 'Submitted'},
            ]
        }
    }
}
</script>

<style lang="scss" scoped>

</style>