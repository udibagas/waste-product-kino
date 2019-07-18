<template>
    <el-card>
        <h4>KELOLA USERS</h4>
        <hr>

        <el-form :inline="true" class="form-right" @submit.native.prevent="() => { return }">
            <el-form-item>
                <el-button @click="addData" type="primary"><i class="el-icon-plus"></i> TAMBAH USER</el-button>
            </el-form-item>
            <el-form-item style="margin-right:0;">
                <el-input placeholder="Search" prefix-icon="el-icon-search" v-model="keyword" @change="requestData" clearable>
                    <el-button @click="() => { page = 1; keyword = ''; requestData(); }" slot="append" icon="el-icon-refresh"></el-button>
                </el-input>
            </el-form-item>
        </el-form>

        <el-table :data="paginatedData.data" stripe
        height="calc(100vh - 330px)"
        :default-sort = "{prop: 'name', order: 'ascending'}"
        v-loading="loading"
        style="border-top:1px solid #eee;"
        @filter-change="filterChange"
        @sort-change="sortChange">
            <el-table-column type="index" min-width="50" :index="paginatedData.from"> </el-table-column>
            <el-table-column prop="name" label="Name" sortable="custom" min-width="180"></el-table-column>
            <el-table-column prop="no_karyawan" label="No. Karyawan" sortable="custom" min-width="130"></el-table-column>
            <el-table-column prop="email" label="Email" sortable="custom" min-width="220"></el-table-column>
            <el-table-column label="Lokasi" width="200">
                <template slot-scope="scope" v-if="scope.row.location">
                    {{scope.row.location.plant}} - {{scope.row.location.name}}
                </template>
            </el-table-column>
            <el-table-column prop="department" label="Departemen" sortable="custom" min-width="180"></el-table-column>
            <el-table-column prop="role" label="Role" sortable="custom" min-width="120"
            column-key="role"
            :filters="[{value: 0, text: 'Member'},{value: 1, text: 'User'}, {value: 9, text: 'Admin'}]">
                <template slot-scope="scope">
                    {{roles[scope.row.role]}}
                </template>
            </el-table-column>
            <el-table-column prop="last_login" label="Last Login" sortable="custom" min-width="180"></el-table-column>
            <el-table-column prop="login" label="Login" sortable="custom" min-width="80" header-align="center" align="center"></el-table-column>

            <el-table-column prop="status" label="Status" sortable="custom" column-key="status" min-width="100" align="center" header-align="center"
            :filters="[{value: 0, text: 'Inactive'},{value: 1, text: 'Active'}]">
                <template slot-scope="scope">
                    <el-tag size="small" :type="scope.row.status ? 'success' : 'danger'">{{scope.row.status ? 'Active' : 'Inactive'}}</el-tag>
                </template>
            </el-table-column>

            <el-table-column prop="allow_approve_kategori" label="Allow Approve Category" min-width="200" align="center" header-align="center">
                <template slot-scope="scope">
                    <el-tag size="small" :type="scope.row.allow_approve_kategori ? 'success' : 'danger'">{{scope.row.allow_approve_kategori ? 'Yes' : 'No'}}</el-tag>
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

        <el-dialog center :visible.sync="showForm" :title="formTitle" width="900px" v-loading="loading" :close-on-click-modal="false">
            <el-alert type="error" title="ERROR"
                :description="error.message + '\n' + error.file + ':' + error.line"
                v-show="error.message"
                style="margin-bottom:15px;">
            </el-alert>

            <el-form label-width="170px">
                <el-row :gutter="15">
                    <el-col :span="12">
                        <el-form-item label="Name" :class="formErrors.name ? 'is-error' : ''">
                            <el-input placeholder="Username" v-model="formModel.name"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.name">{{formErrors.name[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Email" :class="formErrors.email ? 'is-error' : ''">
                            <el-input placeholder="Email" v-model="formModel.email"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.email">{{formErrors.email[0]}}</div>
                        </el-form-item>

                        <el-form-item label="No. Karyawan" :class="formErrors.no_karyawan ? 'is-error' : ''">
                            <el-input placeholder="No. Karyawan" v-model="formModel.no_karyawan"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.no_karyawan">{{formErrors.no_karyawan[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Password" :class="formErrors.password ? 'is-error' : ''">
                            <el-input type="password" placeholder="Password" v-model="formModel.password"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.password">{{formErrors.password[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Konfirmasi Password" :class="formErrors.password ? 'is-error' : ''">
                            <el-input type="password" placeholder="Konfirmasi Password" v-model="formModel.password_confirmation"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Lokasi">
                            <el-select v-model="formModel.location_id" style="width:100%" placeholder="Lokasi">
                                <el-option
                                v-for="item in $store.state.locationList"
                                :key="item.id"
                                :label="item.plant + ' - ' + item.name"
                                :value="item.id">
                                </el-option>
                            </el-select>
                            <div class="el-form-item__error" v-if="formErrors.location_id">{{formErrors.location_id[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Departemen">
                            <el-input placeholder="Departemen" v-model="formModel.department"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.department">{{formErrors.department[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Role">
                            <el-radio v-model="formModel.role" :label="0">User</el-radio>
                            <el-radio v-model="formModel.role" :label="1">Admin</el-radio>
                            <div class="el-form-item__error" v-if="formErrors.role">{{formErrors.role[0]}}</div>
                        </el-form-item>

                        <el-form-item v-show="formModel.role == 0" label="Allow Approve Category">
                            <el-checkbox v-model="formModel.allow_approve_kategori">Yes</el-checkbox>
                        </el-form-item>

                        <el-form-item label="Status">
                            <el-radio v-model="formModel.status" :label="0">Inactive</el-radio>
                            <el-radio v-model="formModel.status" :label="1">Active</el-radio>
                            <div class="el-form-item__error" v-if="formErrors.status">{{formErrors.status[0]}}</div>
                        </el-form-item>
                    </el-col>
                </el-row>
            </el-form>

            <div v-show="formModel.role == 0">
                <h3>Hak Akses</h3>
                <hr>
                <el-tree
                :data="$store.state.menuList"
                :props="{ children: 'children', label: 'label' }"
                show-checkbox
                default-expand-all
                node-key="id"
                ref="tree"
                @check="(node, checked) => { rights = checked.checkedKeys }"></el-tree>
            </div>

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
            roles: { 0: 'User', 1: 'Admin' },
            loading: false,
            showForm: false,
            formTitle: '',
            formErrors: {},
            error: {},
            formModel: { rights: [] },
            keyword: '',
            page: 1,
            pageSize: 10,
            sort: 'name',
            order: 'ascending',
            filters: {},
            paginatedData: {},
            rights: []
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
        save() {
            this.formModel.rights = this.rights.map(r => {
                return { menu_id: r }
            })

            if (!!this.formModel.id) {
                this.update()
            } else {
                this.store()
            }
        },
        store: function() {
            this.loading = true;
            axios.post('/user', this.formModel)
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
            axios.put('/user/' + this.formModel.id, this.formModel)
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
            this.formTitle = 'TAMBAH USER'
            this.error = {}
            this.formErrors = {}
            this.formModel = {
                role: 0,
                status: 1,
                allow_approve_kategori: false,
                rights: []
            }
            this.showForm = true
            let _this = this
            setTimeout(function() {
                _this.$refs.tree.setCheckedKeys([]);
            }, 10)
        },
        editData: function(data) {
            this.formTitle = 'EDIT USER'
            this.formModel = JSON.parse(JSON.stringify(data));
            this.error = {}
            this.formErrors = {}
            this.showForm = true
            let _this = this

            setTimeout(function() {
                _this.$refs.tree.setCheckedKeys(_this.formModel.rights.map(r => { return r.menu_id }));
            }, 10)
        },
        deleteData: function(id) {
            this.$confirm('Anda yakin akan menghapus user ini?', 'Warning', {
                type: 'warning',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then(() => {
                axios.delete('/user/' + id).then(r => {
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
        requestData: function() {
            let params = {
                page: this.page,
                keyword: this.keyword,
                pageSize: this.pageSize,
                sort: this.sort,
                order: this.order,
            }
            this.loading = true;

            axios.get('/user', {params: Object.assign(params, this.filters)}).then(r => {
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
        this.$store.commit('getMenuList');
    }
}
</script>

<style lang="css" scoped>
</style>
