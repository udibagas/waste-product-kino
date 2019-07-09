<template>
    <el-card>
        <h4>KELOLA KATEGORI BARANG</h4>
        <hr>

        <el-form :inline="true" class="form-right">
            <el-form-item>
                <el-button @click="addData" type="primary"><i class="el-icon-plus"></i> TAMBAH KATEGORI BARANG</el-button>
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
        :default-sort = "{prop: 'nama', order: 'ascending'}"
        v-loading="loading"
        style="border-top:1px solid #eee;"
        @filter-change="filterChange"
        @sort-change="sortChange">
            <el-table-column type="index" width="50" :index="paginatedData.from"> </el-table-column>
            <el-table-column prop="jenis" label="Jenis" sortable="custom"></el-table-column>
            <el-table-column prop="kode" label="Kode" sortable="custom"></el-table-column>
            <el-table-column prop="nama" label="Nama" sortable="custom"></el-table-column>
            <el-table-column prop="unit" label="Unit" sortable="custom"></el-table-column>
            <el-table-column prop="harga" label="Harga" sortable="custom" align="right" header-align="right">
                <template slot-scope="scope">
                    Rp {{scope.row.harga | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="status" label="Status" sortable="custom" width="100" align="center" header-align="center">
                <template slot-scope="scope">
                    <el-tag :type="statuses[scope.row.status].type">{{statuses[scope.row.status].label}}</el-tag>
                </template>
            </el-table-column>

            <el-table-column fixed="right" width="40px">
                <template slot-scope="scope">
                    <el-dropdown>
                        <span class="el-dropdown-link">
                            <i class="el-icon-more"></i>
                        </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item @click.native.prevent="editData(scope.row)"><i class="el-icon-edit-outline"></i> Edit</el-dropdown-item>
                            <el-dropdown-item @click.native.prevent="deleteData(scope.row.id)"><i class="el-icon-delete"></i> Hapus</el-dropdown-item>
                            <el-dropdown-item v-if="scope.row.status != 2 && (user.allow_approve_kategori || user.role == 1)" @click.native.prevent="approve(scope.row, 2)"><i class="el-icon-check"></i> Approve</el-dropdown-item>
                            <el-dropdown-item v-if="scope.row.status != 3 && (user.allow_approve_kategori || user.role == 1)" @click.native.prevent="approve(scope.row, 3)"><i class="el-icon-close"></i> Reject</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
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

        <el-dialog center :visible.sync="showForm" :title="formTitle" width="600px" v-loading="loading" :close-on-click-modal="false">
            <el-alert type="error" title="ERROR"
                :description="error.message + '\n' + error.file + ':' + error.line"
                v-show="error.message"
                style="margin-bottom:15px;">
            </el-alert>

            <el-form label-width="180px">
                <el-form-item label="Jenis">
                    <el-select placeholder="Jenis" v-model="formModel.jenis" style="width:100%;">
                        <el-option value="BB" label="BB"></el-option>
                        <el-option value="WP" label="WP"></el-option>
                    </el-select>
                    <div class="el-form-item__error" v-if="formErrors.jenis">{{formErrors.jenis[0]}}</div>
                </el-form-item>

                <el-form-item label="Kode">
                    <el-input placeholder="Kode" v-model="formModel.kode"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.kode">{{formErrors.kode[0]}}</div>
                </el-form-item>

                <el-form-item label="Nama">
                    <el-input placeholder="Nama" v-model="formModel.nama"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.nama">{{formErrors.nama[0]}}</div>
                </el-form-item>

                <el-form-item label="Unit">
                    <el-input placeholder="Unit" v-model="formModel.unit"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.unit">{{formErrors.unit[0]}}</div>
                </el-form-item>

                <el-form-item label="Harga">
                    <el-input type="number" placeholder="Harga" v-model="formModel.harga"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.harga">{{formErrors.harga[0]}}</div>
                </el-form-item>
            </el-form>

            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="save" icon="el-icon-success">SAVE</el-button>
                <el-button type="info" @click="showForm = false" icon="el-icon-error">CANCEL</el-button>
            </span>
        </el-dialog>
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
            user: USER,
            statuses: [
                {type: 'info', label: 'Draft'},
                {type: 'warning', label: 'Updated'},
                {type: 'success', label: 'Approved'},
                {type: 'danger', label: 'Rejected'},
            ],
            loading: false,
            showForm: false,
            formTitle: '',
            formErrors: {},
            error: {},
            formModel: {},
            keyword: '',
            page: 1,
            pageSize: 10,
            sort: 'nama',
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
        save() {
            if (!!this.formModel.id) {
                this.update()
            } else {
                this.store()
            }
        },
        approve(data, status) {
            this.$confirm('Anda yakin?', 'Warning', {
                type: 'warning',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then(() => {
                this.formModel = data
                this.formModel.status = status
                this.update()
            }).catch(() => {})
        },
        store: function() {
            this.loading = true;
            axios.post(BASE_URL + '/kategoriBarang', this.formModel)
                .then(r => {
                    this.loading = false;
                    this.showForm = false;
                    this.$message({
                        message: 'Data BERHASIL disimpan.',
                        type: 'success'
                    });
                    this.requestData();
                })
                .catch(e => {
                    this.loading = false;
                    if (e.response.status == 422) {
                        this.error = {}
                        this.formErrors = e.response.data.errors;
                    }

                    if (e.response.status == 500) {
                        this.formErrors = {}
                        this.error = e.response.data;
                    }
                })
        },
        update: function() {
            this.loading = true;
            axios.put(BASE_URL + '/kategoriBarang/' + this.formModel.id, this.formModel)
                .then(r => {
                    this.loading = false;
                    this.showForm = false
                    this.$message({
                        message: 'Data BERHASIL disimpan.',
                        type: 'success'
                    });
                    this.requestData()
                })
                .catch(e => {
                    this.loading = false;
                    if (e.response.status == 422) {
                        this.error = {}
                        this.formErrors = e.response.data.errors;
                    }

                    if (e.response.status == 500) {
                        this.formErrors = {}
                        this.error = e.response.data;
                    }
                })
        },
        addData: function() {
            this.formTitle = 'TAMBAH KATEGORI BARANG'
            this.error = {}
            this.formErrors = {}
            this.formModel = {}
            this.showForm = true
        },
        editData: function(data) {
            this.formTitle = 'EDIT KATEGORI BARANG'
            this.formModel = JSON.parse(JSON.stringify(data));
            this.error = {}
            this.formErrors = {}
            this.showForm = true
        },
        deleteData: function(id) {
            this.$confirm('Anda yakin akan menghapus user ini?', 'Warning', {
                type: 'warning',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then(() => {
                axios.delete(BASE_URL + '/kategoriBarang/' + id).then(r => {
                    this.requestData();
                    this.$message({
                        message: 'User BERHASIL dihapus.',
                        type: 'success'
                    });
                }).catch(e => {
                    this.$message({
                        message: 'User GAGAL dihapus. ' + e.response.data.message,
                        type: 'error'
                    });
                })
            }).catch(() => { });
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

            axios.get(BASE_URL + '/kategoriBarang', {params: Object.assign(params, this.filters)})
                .then(r => {
                    this.loading = false;
                    this.paginatedData = r.data
                })
                .catch(e => {
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
