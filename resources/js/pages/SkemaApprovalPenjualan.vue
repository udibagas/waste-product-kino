<template>
    <el-card>
        <h4>SKEMA APPROVAL PENJUALAN</h4>
        <hr>

        <el-form :inline="true" class="form-right" @submit.native.prevent="() => { return }">
            <el-form-item>
                <el-button @click="addData" type="primary"><i class="el-icon-plus"></i> TAMBAH SKEMA APPROVAL</el-button>
            </el-form-item>
            <el-form-item style="margin-right:0;">
                <el-input placeholder="Search" prefix-icon="el-icon-search" v-model="keyword" @change="requestData" clearable>
                    <el-button @click="() => { page = 1; keyword = ''; requestData(); }" slot="append" icon="el-icon-refresh"></el-button>
                </el-input>
            </el-form-item>
        </el-form>

        <el-table :data="paginatedData.data" stripe
        height="calc(100vh - 330px)"
        :default-sort = "{prop: 'location_id', order: 'ascending'}"
        v-loading="loading"
        style="border-top:1px solid #eee;"
        @filter-change="filterChange"
        @sort-change="sortChange">
            <el-table-column type="index" width="50" :index="paginatedData.from"> </el-table-column>
            <el-table-column
            prop="location.plant"
            label="Plant - Location"
            column-key="location_id"
            :filters="$store.state.locationList.map(l => {return {text: l.plant + ' - ' + l.name, value: l.id}})">
                <template slot-scope="scope">
                    {{scope.row.location.plant}} - {{scope.row.location.name}}
                </template>
            </el-table-column>
            <el-table-column
            prop="level"
            column-key="level"
            :filters="[{text: 'Level 1', value: 1}, {text: 'Level 2', value: 2}]"
            label="Level"
            sortable="custom">
                <template slot-scope="scope">
                    Level {{scope.row.level}}
                </template>
            </el-table-column>
            <el-table-column prop="user.name" label="User"></el-table-column>
            <el-table-column prop="user.email" label="Email"></el-table-column>

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
                <el-form-item label="Plant" :class="formErrors.location_id ? 'is-error' : ''">
                    <el-select v-model="formModel.location_id" style="width:100%" placeholder="Location">
                        <el-option
                        v-for="item in $store.state.locationList"
                        :key="item.id"
                        :label="item.plant + ' - ' + item.name"
                        :value="item.id">
                        </el-option>
                    </el-select>
                    <div class="el-form-item__error" v-if="formErrors.location_id">{{formErrors.location_id[0]}}</div>
                </el-form-item>

                <el-form-item label="Level" :class="formErrors.level ? 'is-error' : ''">
                    <el-select v-model="formModel.level" style="width:100%" placeholder="Level">
                        <el-option label="Level 1" value="1"> </el-option>
                        <el-option label="Level 2" value="2"> </el-option>
                    </el-select>
                    <div class="el-form-item__error" v-if="formErrors.user_id">{{formErrors.user_id[0]}}</div>
                </el-form-item>

                <el-form-item label="User" :class="formErrors.user_id ? 'is-error' : ''">
                    <el-select filterable default-first-option v-model="formModel.user_id" style="width:100%" placeholder="User">
                        <el-option
                        v-for="item in $store.state.userList"
                        :key="item.id"
                        :label="item.name + ' (' + item.email + ')'"
                        :value="item.id">
                        </el-option>
                    </el-select>
                    <div class="el-form-item__error" v-if="formErrors.user_id">{{formErrors.user_id[0]}}</div>
                </el-form-item>
            </el-form>

            <span slot="footer" class="dialog-footer">
                <el-button type="primary" icon="el-icon-success" @click="() => !!formModel.id ? update() : store()">SAVE</el-button>
                <el-button type="info" icon="el-icon-error" @click="showForm = false">CANCEL</el-button>
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
            sort: 'location_id',
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
            axios.post('/skemaApprovalPenjualan', this.formModel)
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
            axios.put('/skemaApprovalPenjualan/' + this.formModel.id, this.formModel)
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
            this.formTitle = 'TAMBAH SKEMA APPROVAL'
            this.error = {}
            this.formErrors = {}
            this.formModel = {}
            this.showForm = true
        },
        editData: function(data) {
            this.formTitle = 'EDIT SKEMA APPROVAL'
            this.formModel = JSON.parse(JSON.stringify(data));
            this.error = {}
            this.formErrors = {}
            this.showForm = true
        },
        deleteData: function(id) {
            this.$confirm('Anda yakin akan menghapus data ini?', 'Warning', {
                type: 'warning',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then(() => {
                axios.delete('/skemaApprovalPenjualan/' + id).then(r => { this.requestData();
                    this.$message({
                        message: 'Data BERHASIL dihapus.',
                        type: 'success'
                    });
                }).catch(e => {
                    this.$message({
                        message: 'Data GAGAL dihapus. ' + e.response.data.message,
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

            axios.get('/skemaApprovalPenjualan', {params: Object.assign(params, this.filters)})
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
        this.$store.commit('getLocationList');
        this.$store.commit('getUserList');
    }
}
</script>

<style lang="css" scoped>

</style>
