<template>
    <el-card>
        <h4>STOCK BB</h4>
        <hr>

        <el-form :inline="true" class="form-right" @submit.native.prevent="() => { return }">
            <el-form-item style="margin-right:0;">
                <el-input placeholder="Search" prefix-icon="el-icon-search" v-model="keyword" @change="requestData" clearable>
                    <el-button @click="() => { page = 1; keyword = ''; requestData(); }" slot="append" icon="el-icon-refresh"></el-button>
                </el-input>
            </el-form-item>
        </el-form>

        <el-table :data="paginatedData.data" stripe
        height="calc(100vh - 330px)"
        :default-sort = "{prop: 'lokasi', order: 'ascending'}"
        v-loading="loading"
        style="border-top:1px solid #eee;"
        @filter-change="filterChange"
        @sort-change="sortChange">
            <el-table-column type="index" width="50" :index="paginatedData.from"> </el-table-column>
            <el-table-column
            min-width="150"
            prop="location_id"
            label="Plant - Location"
            column-key="location_id"
            sortable="custom"
            :filters="$store.state.locationList.map(l => {return {text: l.plant + ' - ' + l.name, value: l.id}})">
                <template slot-scope="scope">
                    {{scope.row.location.plant}} - {{scope.row.location.name}}
                </template>
            </el-table-column>
            <el-table-column
            min-width="150"
            prop="kategori_barang_id"
            label="Kategori Barang"
            sortable="custom"
            column-key="kategori_barang_id"
            :filters="$store.state.kategoriBarangList.map(l => {return {text: l.kode + ' - ' + l.nama, value: l.id}})">
                <template slot-scope="scope">
                    {{ scope.row.kategori.kode }} - {{ scope.row.kategori.nama }}
                </template>
            </el-table-column>
            <el-table-column prop="qty" min-width="200" label="Jumlah" sortable="custom" align="right" header-align="right">
                <template slot-scope="scope">
                    {{ scope.row.qty | formatNumber }} {{scope.row.unit}}
                </template>
            </el-table-column>
            <el-table-column prop="stock" min-width="200" label="Berat" sortable="custom" align="right" header-align="right">
                <template slot-scope="scope">
                    {{ scope.row.stock | formatNumber }} KG
                </template>
            </el-table-column>
            <el-table-column prop="stock_bbs.updated_at" min-width="150" label="Waktu Update" sortable="custom" align="center" header-align="center">
                <template slot-scope="scope">
                    {{ scope.row.updated_at | readableDateTime }}
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
export default {
    data: function() {
        return {
            loading: false,
            showForm: false,
            keyword: '',
            page: 1,
            pageSize: 10,
            sort: 'lokasi',
            order: 'ascending',
            filters: {},
            paginatedData: {}
        }
    },
    methods: {
        sortChange: function(column) {
            if (this.sort !== column.prop || this.order !== column.order) {
                this.sort = column.prop;
                this.order = column.order;
                this.requestData();
            }
        },
        filterChange: function(f) {
            let column = Object.keys(f)[0];
            this.filters[column] = Object.values(f[column]);
            this.page = 1
            this.requestData();
        },
        requestData: function() {
            let params = {
                page: this.page,
                keyword: this.keyword,
                pageSize: this.pageSize,
                sort: this.sort,
                order: this.order,
            }
            this.loading = true;

            axios.get('/stockBb', {params: Object.assign(params, this.filters)}).then(r => {
                this.loading = false;
                this.paginatedData = r.data
            }).catch(e => {
                this.loading = false;
                this.$message({
                    message: e.response.data.message || e.response.message,
                    type: 'error'
                });
            })
        }
    },
    created: function() {
        this.requestData();
        this.$store.commit('getLocationList');
        this.$store.commit('getKategoriBarangList');
    }
}
</script>

<style lang="css" scoped>

</style>
