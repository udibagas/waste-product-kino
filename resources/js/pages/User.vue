<template>
    <el-card>
        <h4>KELOLA USERS</h4>
        <hr>

        <el-form :inline="true" class="form-right">
            <el-form-item>
                <el-button @click="addData" type="primary"><i class="el-icon-plus"></i> TAMBAH USER</el-button>
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
        :default-sort = "{prop: 'name', order: 'ascending'}"
        v-loading="loading"
        style="border-top:1px solid #eee;"
        @filter-change="filterChange"
        @sort-change="sortChange">
            <el-table-column type="index" width="50" :index="paginatedData.from"> </el-table-column>
            <el-table-column prop="name" label="Name" sortable="custom"></el-table-column>
            <el-table-column prop="email" label="Email" sortable="custom"></el-table-column>
            <el-table-column prop="role" label="Role" sortable="custom" 
            column-key="role"
            :filters="[{value: 0, text: 'Member'},{value: 1, text: 'User'}, {value: 9, text: 'Admin'}]">
                <template slot-scope="scope">
                    {{roles[scope.row.role]}}
                </template>
            </el-table-column>
            <el-table-column prop="status" label="Status" sortable="custom" column-key="status"
            :filters="[{value: 0, text: 'Inactive'},{value: 1, text: 'Active'}]">
                <template slot-scope="scope">
                    <el-tag :type="scope.row.status ? 'success' : 'danger'">{{scope.row.status ? 'Active' : 'Inactive'}}</el-tag>
                </template>
            </el-table-column>

            <el-table-column prop="last_login" label="Last Login" sortable="custom"></el-table-column>
            <el-table-column prop="login" label="Login" sortable="custom"></el-table-column>

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

        <el-dialog :visible.sync="showForm" :title="formTitle" width="600px" v-loading="loading" :close-on-click-modal="false">
            <el-alert type="error" title="ERROR"
                :description="error.message + '\n' + error.file + ':' + error.line"
                v-show="error.message"
                style="margin-bottom:15px;">
            </el-alert>

            <el-form label-width="180px">
                <el-form-item label="Name">
                    <el-input placeholder="Username" v-model="formModel.name"></el-input>
                    <div class="error-feedback" v-if="formErrors.name">{{formErrors.name[0]}}</div>
                </el-form-item>

                <el-form-item label="Email">
                    <el-input placeholder="Email" v-model="formModel.email"></el-input>
                    <div class="error-feedback" v-if="formErrors.email">{{formErrors.email[0]}}</div>
                </el-form-item>

                <el-form-item label="Role">
                    <el-select placeholder="Role" v-model="formModel.role" style="width:100%;">
                        <el-option :value="0" label="Member"></el-option>
                        <el-option :value="1" label="User"></el-option>
                        <el-option :value="9" label="Admin"></el-option>
                    </el-select>
                    <div class="error-feedback" v-if="formErrors.role">{{formErrors.role[0]}}</div>
                </el-form-item>

                <el-form-item label="Password">
                    <el-input type="password" placeholder="Password" v-model="formModel.password"></el-input>
                    <div class="error-feedback" v-if="formErrors.password">{{formErrors.password[0]}}</div>
                </el-form-item>
                
                <el-form-item label="Konfirmasi Password">
                    <el-input type="password" placeholder="Konfirmasi Password" v-model="formModel.password_confirmation"></el-input>
                </el-form-item>

                <el-form-item label="Status">
                    <el-select placeholder="Status" v-model="formModel.status" style="width:100%;">
                        <el-option :value="0" label="Inactive"></el-option>
                        <el-option :value="1" label="Active"></el-option>
                    </el-select>
                    <div class="error-feedback" v-if="formErrors.status">{{formErrors.status[0]}}</div>
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
            roles: { 0: 'Member', 1: 'User', 9: 'Admin' },
            loading: false,
            showForm: false,
            formTitle: '',
            formErrors: {},
            error: {},
            formModel: {},
            keyword: '',
            page: 1,
            pageSize: 10,
            sort: 'name',
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
        store: function() {
            this.loading = true;
            axios.post(BASE_URL + '/user', this.formModel)
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
            axios.put(BASE_URL + '/user/' + this.formModel.id, this.formModel)
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
            this.formTitle = 'Tambah User'
            this.error = {}
            this.formErrors = {}
            this.formModel = {}
            this.showForm = true
        },
        editData: function(data) {
            this.formTitle = 'Edit User'
            this.formModel = JSON.parse(JSON.stringify(data));
            this.error = {}
            this.formErrors = {}
            this.showForm = true
        },
        deleteData: function(id) {
            this.$confirm('Anda yakin akan menghapus user ini?')
                .then(() => {
                    axios.delete(BASE_URL + '/user/' + id)
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

            axios.get(BASE_URL + '/user', {params: Object.assign(params, this.filters)})
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
