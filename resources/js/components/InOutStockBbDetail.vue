<template>
    <el-card>
        <el-table :data="paginatedData.data" stripe
        :default-sort = "{prop: 'tanggal', order: 'ascending'}"
        v-loading="loading"
        style="border-top:1px solid #eee;">
            <el-table-column type="index" width="50" :index="paginatedData.from"> </el-table-column>
            <el-table-column prop="tanggal" width="100" label="Tanggal">
                <template slot-scope="scope">
                    {{ scope.row.tanggal | readableDate }}
                </template>
            </el-table-column>
            <el-table-column prop="no_sj" min-width="200" label="No. Surat Jalan"></el-table-column>

            <el-table-column prop="qty_in" min-width="110" label="Qty In" align="right" header-align="right">
                <template slot-scope="scope">
                    {{ scope.row.qty_in | formatNumber }} {{scope.row.eun}}
                </template>
            </el-table-column>
            <el-table-column prop="qty_out" min-width="110" label="Qty Out" align="right" header-align="right">
                <template slot-scope="scope">
                    {{ scope.row.qty_out | formatNumber }} {{scope.row.eun}}
                </template>
            </el-table-column>
            <el-table-column prop="stock_in" min-width="110" label="Stock In" align="right" header-align="right">
                <template slot-scope="scope">
                    {{ scope.row.stock_in | formatNumber }} kg
                </template>
            </el-table-column>
            <el-table-column prop="stock_out" min-width="110" label="Stock Out" align="right" header-align="right">
                <template slot-scope="scope">
                    {{ scope.row.stock_out | formatNumber }} kg
                </template>
            </el-table-column>
        </el-table>

        <br>

        <el-pagination
        @current-change="(p) => { page = p; requestData(); }"
        @size-change="(s) => { pageSize = s; requestData(); }"
        :page-size="pageSize"
        background
        layout="prev, pager, next, sizes, total"
        :page-sizes="[10,25,50,100]"
        :total="paginatedData.total">
        </el-pagination>
    </el-card>
</template>

<script>
import moment from 'moment'

export default {
    props: ['date_range', 'kategori_id', 'location_id'],
    data: function() {
        return {
            loading: false,
            page: 1,
            pageSize: 10,
            sort: 'tanggal',
            order: 'ascending',
            paginatedData: {},
        }
    },
    methods: {
        requestData: function() {
            let params = {
                page: this.page,
                pageSize: this.pageSize,
                sort: this.sort,
                order: this.order,
                range: this.date_range,
                kategori_barang_id: this.kategori_id,
                location_id: this.location_id
            }

            this.loading = true;
            axios.get('/inOutStockBb', { params: params }).then(r => {
                this.paginatedData = r.data
            }).catch(e => {
                this.$message({
                    message: e.response.data.message || e.response.message,
                    type: 'error',
                    showClose: true
                });
            }).finally(() => {
                this.loading = false
            })
        }
    },
    mounted: function() {
        this.requestData();
    }
}
</script>
