<template>
    <el-card>
        <h4>STOCK BB</h4>
        <hr>

        <el-form :inline="true" class="form-right">
            <el-form-item>
                <el-select class="pager-options" v-model="pageSize" placeholder="Page Size">
                    <el-option v-for="item in $store.state.pagerOptions" :key="item.value" :label="item.label" :value="item.value"> </el-option>
                </el-select>
            </el-form-item>
            <el-form-item style="margin-right:0;">
                <el-input placeholder="Search" prefix-icon="el-icon-search" v-model="keyword">
                    <el-button @click="refreshData" slot="append" icon="el-icon-refresh"></el-button>
                </el-input>
            </el-form-item>
        </el-form>

        <el-table :data="paginatedData.data" stripe
        :default-sort = "{prop: 'lokasi', order: 'ascending'}"
        v-loading="loading"
        style="border-top:1px solid #eee;"
        @filter-change="filterChange"
        @sort-change="sortChange">
            <el-table-column type="index" width="50" :index="paginatedData.from"> </el-table-column>
            <el-table-column 
            prop="location.plant" 
            label="Plant - Location" 
            column-key="location_id" 
            sortable="custom"
            :filters="$store.state.locationList.map(l => {return {text: l.plant + ' - ' + l.name, value: l.id}})">
                <template slot-scope="scope">
                    {{scope.row.location.plant}} - {{scope.row.location.name}}
                </template>
            </el-table-column>
            <el-table-column 
            prop="kategori.nama" 
            label="Kategori Barang" 
            sortable="custom"
            column-key="kategori_barang_id" 
            :filters="$store.state.kategoriBarangList.map(l => {return {text: l.kode + ' - ' + l.nama, value: l.id}})">
                <template slot-scope="scope">
                    {{ scope.row.kategori.kode }} - {{ scope.row.kategori.nama }}
                </template>
            </el-table-column>
            <el-table-column prop="qty" width="110" label="Jumlah" sortable="custom" align="center" header-align="center"></el-table-column>
            <el-table-column prop="stock" width="110" label="Stock (kg)" sortable="custom" align="center" header-align="center">
                <template slot-scope="scope">
                    {{ scope.row.stock | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="unit" width="70" label="Unit" sortable="custom"></el-table-column>
            <el-table-column prop="updated_at" width="150" label="Waktu Update" sortable="custom" align="center" header-align="center">
                <template slot-scope="scope">
                    {{ scope.row.updated_at | readableDateTime }}
                </template>
            </el-table-column>
        </el-table>

        <br>

        <el-row>
            <el-col :span="12">
                <el-pagination @current-change="goToPage"
                    :page-size="pageSize"
                    background
                    layout="prev, pager, next"
                    :total="paginatedData.total">
                </el-pagination>
            </el-col>
            <el-col :span="12" style="text-align:right">
                {{ paginatedData.from }} - {{ paginatedData.to }} of {{ paginatedData.total }} items
            </el-col>
        </el-row>
    </el-card>
</template>

<script>
export default {
    watch: {
        keyword: function(v, o) {
            this.requestData()
        },
        pageSize: function(v, o) {
            this.requestData()
        }
    },
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
            this.refreshData();
        },
        goToPage: function(p) {
            this.page = p;
            this.requestData();
        },
        refreshData: function() {
            this.keyword = '';
            this.page = 1;
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

            axios.get(BASE_URL + '/stockBb', {params: Object.assign(params, this.filters)}).then(r => {
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