<template>
    <el-card>
        <h4>KONVERSI BERAT</h4>
        <hr>

        <el-form :inline="true" class="form-right">
            <el-form-item>
                <el-upload :action="base_url + '/konversiBerat'"
                ref="upload"
                :on-success="(response, file, fileList) => { fileList = []; refreshData(); }"
                :on-progress="() => { loading = true }"
                :with-credentials="true"
                :show-file-list="false"
                :on-error="(err, file, fileList) => { loading = false; $message({type: 'error', message: err, showClose: true}); }"
                :multiple="false" :limit="1">
                    <el-button :disabled="loading" type="primary" icon="el-icon-upload">UPLOAD DATA KONVERSI BERAT</el-button>
                </el-upload>
            </el-form-item>
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
        :default-sort = "{prop: 'material_id', order: 'ascending'}"
        v-loading="loading"
        style="border-top:1px solid #eee;"
        @sort-change="sortChange">
            <el-table-column type="index" width="50" :index="paginatedData.from"> </el-table-column>
            <el-table-column prop="kategori_jual" label="Kategori Jual" sortable="custom"></el-table-column>
            <el-table-column prop="finished_good" label="Finished Goods" sortable="custom"></el-table-column>
            <el-table-column prop="material_id" label="Material ID" sortable="custom"></el-table-column>
            <el-table-column prop="material_description" label="Material Description" sortable="custom"></el-table-column>
            <el-table-column prop="berat" label="Berat Rata - Rata" sortable="custom"></el-table-column>
            <el-table-column prop="keterangan" label="Keterangan" sortable="custom"></el-table-column>
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
            base_url: BASE_URL,
            loading: false,
            keyword: '',
            page: 1,
            pageSize: 10,
            sort: 'material_id',
            order: 'ascending',
            paginatedData: {},
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

            axios.get(BASE_URL + '/konversiBerat', { params: params }).then(r => {
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
    }
}
</script>

<style lang="css" scoped>

</style>