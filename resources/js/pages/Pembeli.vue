<template>
    <el-card>
        <h4>KELOLA PEMBELI</h4>
        <hr>

        <el-form :inline="true" class="form-right" @submit.native.prevent="() => { return }">
            <el-form-item>
                <el-button @click="addData" type="primary"><i class="el-icon-plus"></i> TAMBAH PEMBELI</el-button>
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
            <el-table-column prop="nama" label="Nama" sortable="custom"></el-table-column>
            <el-table-column prop="kontak" label="Kontak" sortable="custom"></el-table-column>
            <el-table-column prop="alamat" label="Alamat" sortable="custom">
                <template slot-scope="scope">
                   <span v-html="scope.row.alamat.replace(/(?:\r\n|\r|\n)/g, '<br>')"></span>
                </template>
            </el-table-column>
            <el-table-column prop="bank" label="Bank" sortable="custom"></el-table-column>
            <el-table-column prop="nomor_rekening" label="Nomor Rekeing" sortable="custom"></el-table-column>
            <el-table-column prop="pemegang_rekening" label="Pemegang Rekeing" sortable="custom"></el-table-column>

            <el-table-column fixed="right" width="40px">
                <template slot-scope="scope">
                    <el-dropdown>
                        <span class="el-dropdown-link">
                            <i class="el-icon-more"></i>
                        </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item @click.native.prevent="editData(scope.row)"><i class="el-icon-edit-outline"></i> Edit</el-dropdown-item>
                            <el-dropdown-item @click.native.prevent="deleteData(scope.row.id)"><i class="el-icon-delete"></i> Hapus</el-dropdown-item>
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

        <el-dialog center :visible.sync="showForm" :title="formTitle" width="600px" v-loading="loading" :close-on-click-modal="false">
            <el-alert type="error" title="ERROR"
                :description="error.message + '\n' + error.file + ':' + error.line"
                v-show="error.message"
                style="margin-bottom:15px;">
            </el-alert>

            <el-form label-width="180px">
                <el-form-item label="Nama" :class="formErrors.nama ? 'is-error' : ''">
                    <el-input placeholder="Nama" v-model="formModel.nama"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.nama">{{formErrors.nama[0]}}</div>
                </el-form-item>

                <el-form-item label="Kontak" :class="formErrors.kontak ? 'is-error' : ''">
                    <el-input placeholder="Kontak" v-model="formModel.kontak"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.kontak">{{formErrors.kontak[0]}}</div>
                </el-form-item>

                <el-form-item label="Alamat" :class="formErrors.alamat ? 'is-error' : ''">
                    <el-input type="textarea" rows="5" placeholder="Alamat" v-model="formModel.alamat"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.alamat">{{formErrors.alamat[0]}}</div>
                </el-form-item>

                <el-form-item label="Bank" :class="formErrors.bank ? 'is-error' : ''">
                    <el-input placeholder="Bank" v-model="formModel.bank"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.bank">{{formErrors.bank[0]}}</div>
                </el-form-item>

                <el-form-item label="Nomor Rekening" :class="formErrors.nomor_rekening ? 'is-error' : ''">
                    <el-input placeholder="Nomor Rekening" v-model="formModel.nomor_rekening"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.nomor_rekening">{{formErrors.nomor_rekening[0]}}</div>
                </el-form-item>

                <el-form-item label="Pemegang Rekening" :class="formErrors.pemegang_rekening ? 'is-error' : ''">
                    <el-input placeholder="Pemegang Rekening" v-model="formModel.pemegang_rekening"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.pemegang_rekening">{{formErrors.pemegang_rekening[0]}}</div>
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
    data: function() {
        return {
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
            this.page = 1
            this.requestData();
        },
        store: function() {
            this.loading = true;
            axios.post('/pembeli', this.formModel)
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
            axios.put('/pembeli/' + this.formModel.id, this.formModel)
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
            this.formTitle = 'TAMBAH DATA PEMBELI'
            this.error = {}
            this.formErrors = {}
            this.formModel = {}
            this.showForm = true
        },
        editData: function(data) {
            this.formTitle = 'EDIT DATA PEMBELI'
            this.formModel = JSON.parse(JSON.stringify(data));
            this.error = {}
            this.formErrors = {}
            this.showForm = true
        },
        deleteData: function(id) {
            this.$confirm('Anda yakin akan menghapus user ini?')
                .then(() => {
                    axios.delete('/pembeli/' + id)
                        .then(r => {
                            this.requestData();
                            this.$message({
                                message: 'User BERHASIL dihapus.',
                                type: 'success'
                            });
                        })
                        .catch(e => {
                            this.$message({
                                message: 'User GAGAL dihapus. ' + e.response.data.message,
                                type: 'error'
                            });
                        })
                })
                .catch(() => {

                });
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

            axios.get('/pembeli', {params: Object.assign(params, this.filters)})
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
