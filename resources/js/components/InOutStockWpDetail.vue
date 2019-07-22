<template>
    <div>
        <el-table :data="paginatedData.data" stripe
        :default-sort = "{prop: 'tanggal', order: 'descending'}"
        v-loading="loading"
        style="border-top:1px solid #eee;"
        @filter-change="filterChange"
        @sort-change="sortChange">
            <el-table-column type="index" width="50" :index="paginatedData.from"> </el-table-column>
            <el-table-column prop="tanggal" width="100" label="Tanggal" sortable="custom">
                <template slot-scope="scope">
                    {{ scope.row.tanggal | readableDate }}
                </template>
            </el-table-column>
            <el-table-column prop="no_aju" min-width="200" label="No. Pengajuan" sortable="custom"></el-table-column>
            <el-table-column prop="no_sj" min-width="200" label="No. Surat Jalan" sortable="custom"></el-table-column>

            <el-table-column prop="sloc" min-width="100" label="Sloc" sortable="custom"> </el-table-column>
            <el-table-column prop="mvt" min-width="150" label="MVT Type" sortable="custom"> </el-table-column>

            <el-table-column prop="qty_in" min-width="110" label="Qty In" sortable="custom" align="center" header-align="center">
                <template slot-scope="scope">
                    {{ scope.row.qty_in | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="qty_out" min-width="110" label="Qty Out" sortable="custom" align="center" header-align="center">
                <template slot-scope="scope">
                    {{ scope.row.qty_out | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="stock_in" min-width="150" label="Stock In (kg)" sortable="custom" align="center" header-align="center">
                <template slot-scope="scope">
                    {{ scope.row.stock_in | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="stock_out" min-width="150" label="Stock Out (kg)" sortable="custom" align="center" header-align="center">
                <template slot-scope="scope">
                    {{ scope.row.stock_out | formatNumber }}
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
    </div>
</template>

<script>
import moment from 'moment'

export default {
    props: ['date_range', 'material', 'plant'],
    data: function() {
        return {
            loading: false,
            page: 1,
            pageSize: 10,
            sort: 'tanggal',
            order: 'descending',
            paginatedData: {},
        }
    },
    methods: {
        requestData: function() {
            let params = {
                page: this.page,
                plant: this.plant,
                material: this.material,
                pageSize: this.pageSize,
                sort: this.sort,
                order: this.order,
                range: this.date_range
            }

            this.loading = true;
            axios.get('/inOutStockWp', { params: params }).then(r => {
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
