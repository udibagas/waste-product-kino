<template>
    <el-card>
        <h4>KELOLA KATEGORI BARANG</h4>
        <hr>

        <el-form :inline="true" class="form-right" @submit.native.prevent="() => { return }">
            <el-form-item>
                <el-button @click="addData" type="primary"><i class="el-icon-plus"></i> TAMBAH KATEGORI BARANG</el-button>
            </el-form-item>
            <el-form-item style="margin-right:0;">
                <el-input placeholder="Search" prefix-icon="el-icon-search" v-model="keyword" @change="requestData" clearable>
                    <el-button @click="() => { page = 1; keyword = ''; requestData(); }" slot="append" icon="el-icon-refresh"></el-button>
                </el-input>
            </el-form-item>
        </el-form>

        <el-table :data="paginatedData.data" stripe
        height="calc(100vh - 330px)"
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
                    <el-tag size="mini" :type="statuses[scope.row.status].type">{{statuses[scope.row.status].label}}</el-tag>
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

        <el-pagination
        @current-change="(p) => { page = p; requestData(); }"
        @size-change="(s) => { pageSize = s; requestData(); }"
        :page-size="pageSize"
        background
        layout="prev, pager, next, sizes, total"
        :page-sizes="[10,25,50,100]"
        :total="paginatedData.total">
        </el-pagination>

        <el-dialog center :visible.sync="showForm" :title="formTitle" width="400px" v-loading="loading" :close-on-click-modal="false">
            <el-alert type="error" title="ERROR"
                :description="error.message + '\n' + error.file + ':' + error.line"
                v-show="error.message"
                style="margin-bottom:15px;">
            </el-alert>

            <el-form label-width="100px">
                <el-form-item label="Jenis" :class="formErrors.jenis ? 'is-error' : ''">
                    <el-select placeholder="Jenis" v-model="formModel.jenis" style="width:100%;">
                        <el-option value="BB" label="BB"></el-option>
                        <el-option value="WP" label="WP"></el-option>
                    </el-select>
                    <div class="el-form-item__error" v-if="formErrors.jenis">{{formErrors.jenis[0]}}</div>
                </el-form-item>

                <el-form-item label="Kode" :class="formErrors.kode ? 'is-error' : ''">
                    <el-input placeholder="Kode" v-model="formModel.kode"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.kode">{{formErrors.kode[0]}}</div>
                </el-form-item>

                <el-form-item label="Nama" :class="formErrors.nama ? 'is-error' : ''">
                    <el-input placeholder="Nama" v-model="formModel.nama"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.nama">{{formErrors.nama[0]}}</div>
                </el-form-item>

                <el-form-item label="Unit" :class="formErrors.unit ? 'is-error' : ''">
                    <el-select placeholder="Jenis" v-model="formModel.unit" style="width:100%;">
                        <el-option value="PCS" label="PCS"></el-option>
                        <el-option value="KG" label="KG"></el-option>
                    </el-select>
                    <div class="el-form-item__error" v-if="formErrors.unit">{{formErrors.unit[0]}}</div>
                </el-form-item>

                <el-form-item label="Harga" :class="formErrors.harga ? 'is-error' : ''">
                    <el-input type="number" placeholder="Harga" v-model="formModel.harga"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.harga">{{formErrors.harga[0]}}</div>
                </el-form-item>
            </el-form>

            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="() => !!formModel.id ? update() : store()" icon="el-icon-success">SAVE</el-button>
                <el-button type="info" @click="showForm = false" icon="el-icon-error">CANCEL</el-button>
            </span>
        </el-dialog>
    </el-card>
</template>

<script>
export default {
    data() {
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
        sortChange(column) {
            if (this.sort !== column.prop || this.order !== column.order) {
                this.sort = column.prop;
                this.order = column.order;
                this.requestData();
            }
        },
        filterChange(f) {
            let column = Object.keys(f)[0];
            this.filters[column] = Object.values(f[column]);
            this.page = 1
            this.requestData();
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
        store() {
            this.loading = true;
            axios.post('/kategoriBarang', this.formModel).then(r => {
                this.showForm = false;
                this.$message({
                    message: 'Data BERHASIL disimpan.',
                    type: 'success'
                });
                this.requestData();
            }).catch(e => {
                if (e.response.status == 422) {
                    this.error = {}
                    this.formErrors = e.response.data.errors;
                }

                if (e.response.status == 500) {
                    this.formErrors = {}
                    this.error = e.response.data;
                }
            }).finally(() => {
                this.loading = false
            })
        },
        update() {
            this.loading = true;
            axios.put('/kategoriBarang/' + this.formModel.id, this.formModel).then(r => {
                this.showForm = false
                this.$message({
                    message: 'Data BERHASIL disimpan.',
                    type: 'success'
                });
                this.requestData()
            }).catch(e => {
                if (e.response.status == 422) {
                    this.error = {}
                    this.formErrors = e.response.data.errors;
                }

                if (e.response.status == 500) {
                    this.formErrors = {}
                    this.error = e.response.data;
                }
            }).finally(() => {
                this.loading = false
            })
        },
        addData() {
            this.formTitle = 'TAMBAH KATEGORI BARANG'
            this.error = {}
            this.formErrors = {}
            this.formModel = {}
            this.showForm = true
        },
        editData(data) {
            this.formTitle = 'EDIT KATEGORI BARANG'
            this.formModel = JSON.parse(JSON.stringify(data));
            this.error = {}
            this.formErrors = {}
            this.showForm = true
        },
        deleteData(id) {
            this.$confirm('Anda yakin akan menghapus data ini?', 'Warning', {
                type: 'warning',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then(() => {
                axios.delete('/kategoriBarang/' + id).then(r => {
                    this.requestData();
                    this.$message({
                        message: 'Kategori BERHASIL dihapus.',
                        type: 'success',
                        showClose: true
                    });
                }).catch(e => {
                    this.$message({
                        message: 'Kategori GAGAL dihapus. ' + e.response.data.message,
                        type: 'error',
                        showClose: true
                    });
                })
            }).catch(() => { });
        },
        requestData() {
            let params = {
                page: this.page,
                keyword: this.keyword,
                pageSize: this.pageSize,
                sort: this.sort,
                order: this.order,
            }

            this.loading = true;
            axios.get('/kategoriBarang', {params: Object.assign(params, this.filters)}).then(r => {
                this.paginatedData = r.data
            }).catch(e => {
                this.$message({
                    message: e.response.data.message || e.response.message,
                    type: 'error'
                });
            }).finally(() => {
                this.loading = false
            })
        }
    },
    created() {
        this.requestData();
    }
}
</script>
