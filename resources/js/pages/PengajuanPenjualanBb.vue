<template>
    <el-card>
        <h4>PENGAJUAN PENJUALAN BARANG BEKAS</h4>
        <hr>

        <el-form :inline="true" class="form-right" @submit.native.prevent="() => { return }">
            <el-form-item>
                <el-button @click="addData" type="primary"><i class="el-icon-plus"></i> INPUT PENGAJUAN PENJUALAN BARANG BEKAS</el-button>
            </el-form-item>
            <el-form-item style="margin-right:0;">
                <el-input placeholder="Search" prefix-icon="el-icon-search" v-model="keyword" @change="requestData" clearable>
                    <el-button @click="() => { page = 1; keyword = ''; requestData(); }" slot="append" icon="el-icon-refresh"></el-button>
                </el-input>
            </el-form-item>
        </el-form>

        <el-table :data="paginatedData.data" stripe
        @row-dblclick="(row, column, event) =>  { selectedData = row; showDetail = true }"
        height="calc(100vh - 330px)"
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
            <el-table-column prop="no_aju" label="No. Pengajuan" sortable="custom"></el-table-column>

            <el-table-column
            prop="location_id"
            label="Plant"
            column-key="location_id"
            :filters="$store.state.locationList.map(l => { return {value: l.id, text: l.plant + ' - ' + l.name } })"
            sortable="custom">
                <template slot-scope="scope">
                    {{scope.row.location.plant}} - {{scope.row.location.name}}
                </template>
            </el-table-column>

            <el-table-column prop="user.name" label="Yang Mengajukan"></el-table-column>

            <el-table-column
            prop="approval1_status"
            width="130"
            align="center"
            header-align="center"
            label="Approval 1"
            column-key="approval1_status"
            :filters="approval_statuses.map(s => { return {value: s.value, text: s.label} })"
            sortable="custom">
                <template slot-scope="scope">
                    <el-tag size="small" :type="approval_statuses[scope.row.approval1_status].type">
                        {{approval_statuses[scope.row.approval1_status].label}}
                    </el-tag>
                </template>
            </el-table-column>

            <el-table-column
            prop="approval2_status"
            width="130"
            align="center"
            header-align="center"
            label="Approval 2"
            column-key="approval2_status"
            :filters="approval_statuses.map(s => { return {value: s.value, text: s.label} })"
            sortable="custom">
                <template slot-scope="scope">
                    <el-tag size="small" :type="approval_statuses[scope.row.approval2_status].type">
                        {{approval_statuses[scope.row.approval2_status].label}}
                    </el-tag>
                </template>
            </el-table-column>

            <el-table-column
            prop="status"
            width="100"
            align="center"
            header-align="center"
            label="Status"
            column-key="status"
            :filters="statuses.map(s => { return {value: s.value, text: s.label} })"
            sortable="custom">
                <template slot-scope="scope">
                    <el-tag size="small" :type="statuses[scope.row.status].type">{{statuses[scope.row.status].label}}</el-tag>
                </template>
            </el-table-column>

            <el-table-column fixed="right" width="40px">
                <template slot-scope="scope">
                    <el-dropdown>
                        <span class="el-dropdown-link">
                            <i class="el-icon-more"></i>
                        </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item @click.native.prevent="() => { showDetail = true; selectedData = scope.row; }"><i class="el-icon-zoom-in"></i> Show Detail</el-dropdown-item>
                            <!-- bisa diedit kalau status draft atau rejected -->
                            <el-dropdown-item v-if="scope.row.status == 0 || scope.row.status == 3" @click.native.prevent="editData(scope.row)"><i class="el-icon-edit-outline"></i> Edit</el-dropdown-item>
                            <!-- bisa dihapus kalau status draft -->
                            <el-dropdown-item v-if="scope.row.status == 0" @click.native.prevent="deleteData(scope.row.id)"><i class="el-icon-delete"></i> Hapus</el-dropdown-item>
                            <!-- bisa diapprove kalau status submitted -->
                            <el-dropdown-item v-if="scope.row.status == 1" @click.native.prevent="approve(scope.row, 1)"><i class="el-icon-check"></i> Approve</el-dropdown-item>
                            <!-- bisa di reject kalau status submitted -->
                            <el-dropdown-item v-if="scope.row.status == 1" @click.native.prevent="approve(scope.row, 2)"><i class="el-icon-close"></i> Reject</el-dropdown-item>
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

        <!-- DETAIL DIALOG -->
        <el-dialog top="60px" width="750px" center title="DETAIL PENGAJUAN PENJUALAN BARANG BEKAS" :visible.sync="showDetail" v-if="!!selectedData.id">
            <table class="table table-sm table-bordered">
                <tbody>
                    <tr><td class="td-label">Tanggal</td><td class="td-value">{{selectedData.tanggal | readableDate}}</td></tr>
                    <tr><td class="td-label">No. Pengajuan</td><td class="td-value">{{selectedData.no_aju}}</td></tr>
                    <tr><td class="td-label">Plant</td><td class="td-value">{{selectedData.location.plant}} - {{selectedData.location.name}}</td></tr>
                    <tr><td class="td-label">User</td><td class="td-value">{{selectedData.user.name}}</td></tr>
                    <tr><td class="td-label">Approval 1</td><td class="td-value"><el-tag size="small" :type="approval_statuses[selectedData.approval1_status].type">{{approval_statuses[selectedData.approval1_status].label}}</el-tag></td></tr>
                    <tr><td class="td-label">Approval 2</td><td class="td-value"><el-tag size="small" :type="approval_statuses[selectedData.approval2_status].type">{{approval_statuses[selectedData.approval2_status].label}}</el-tag></td></tr>
                    <tr><td class="td-label">Status</td><td class="td-value"><el-tag size="small" :type="statuses[selectedData.status].type">{{statuses[selectedData.status].label}}</el-tag></td></tr>
                </tbody>
            </table>
            <el-tabs type="border-card">
                <el-tab-pane label="Items">
                    <PengajuanPenjualanItemBb :data="selectedData" />
                </el-tab-pane>
                <el-tab-pane label="Approval History">
                    <ApprovalHistory :pengajuan="selectedData.id" />
                </el-tab-pane>
            </el-tabs>
        </el-dialog>

        <el-dialog top="60px" center :visible.sync="showForm" :title="formTitle" width="650px" v-loading="loading" :close-on-click-modal="false">
            <el-alert type="error" title="ERROR"
                :description="error.message + '\n' + error.file + ':' + error.line"
                v-show="error.message"
                style="margin-bottom:15px;">
            </el-alert>

            <el-form label-width="130px" label-position="left">
                <el-form-item label="Tanggal" :class="formErrors.tanggal ? 'is-error' : ''">
                    <el-date-picker v-model="formModel.tanggal" type="date" format="dd-MMM-yyyy" value-format="yyyy-MM-dd" placeholder="Tanggal" style="width:100%;"> </el-date-picker>
                    <div class="el-form-item__error" v-if="formErrors.tanggal">{{formErrors.tanggal[0]}}</div>
                </el-form-item>

                <el-form-item label="Nomor Pengajuan" :class="formErrors.no_aju ? 'is-error' : ''">
                    <el-input disabled placeholder="Nomor Pengajuan" v-model="formModel.no_aju"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.no_aju">{{formErrors.no_aju[0]}}</div>
                </el-form-item>

                <el-form-item label="Plant" :class="formErrors.location_id ? 'is-error' : ''">
                    <el-select :disabled="user.role == 0 || !!formModel.id" v-model="formModel.location_id" style="width:100%" placeholder="Plant">
                        <el-option
                        v-for="item in $store.state.locationList"
                        :key="item.id"
                        :label="item.plant + ' - ' + item.name"
                        :value="item.id">
                        </el-option>
                    </el-select>
                    <div class="el-form-item__error" v-if="formErrors.location_id">{{formErrors.location_id[0]}}</div>
                </el-form-item>

                <p> <el-button type="primary" @click="searchCategory" icon="el-icon-search">SEARCH CATEGORY</el-button> </p>

                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kategori</th>
                            <th class="text-center" style="width:100px">Stock</th>
                            <th class="text-center" style="width:100px">Pengajuan</th>
                            <th class="text-center" style="width:100px">Selisih</th>
                            <th class="text-center">Satuan</th>
                            <th class="text-center"> </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in formModel.items_bb" :key="index">
                            <td>{{index+1}}.</td>
                            <td>{{item.kategori.kode}} - {{item.kategori.nama}}</td>
                            <td class="text-center">{{item.stock_berat | formatNumber}}</td>
                            <td><input type="number" v-model="item.timbangan_manual" class="my-input" placeholder="Berat"></td>
                            <td class="text-center">{{item.stock_berat - item.timbangan_manual | formatNumber}}</td>
                            <td class="text-center"> {{item.eun}} </td>
                            <td class="text-center">
                                <a href="#" @click="deleteItem(index)" class="icon-bg"><i class="el-icon-delete"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </el-form>

            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="save" icon="el-icon-success">SAVE</el-button>
                <el-button type="success" @click="saveSubmit" icon="el-icon-success">SAVE & SUBMIT</el-button>
                <el-button type="info" @click="showForm = false" icon="el-icon-error">CANCEL</el-button>
            </span>
        </el-dialog>

        <!-- DIALOG UNTUK SEARCH CATEGORY -->
        <el-dialog center
        append-to-body
        top="60px"
        :visible.sync="showCategoryList"
        title="Select Category"
        width="500px"
        v-loading="loading"
        :close-on-click-modal="false">
            <el-input prefix-icon="el-icon-search" v-model="categoryKeyword" placeholder="Search Category"></el-input>
            <br><br>
            <table class="table table-sm table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kategori</th>
                            <th class="text-right">Stock</th>
                            <th class="text-center">Satuan</th>
                            <th style="width:80px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(m, i) in filteredCategory.slice((categoryPage - 1) * 10, categoryPage * 10)" :key="i">
                            <td>{{(i + 1) + ((categoryPage - 1) * 10)}}.</td>
                            <td>{{ m.kategori.kode }} - {{ m.kategori.nama }}</td>
                            <td class="text-right">{{ m.stock | formatNumber }}</td>
                            <td class="text-center">{{ m.unit }}</td>
                            <td class="text-center"><input type="checkbox" :value="m" v-model="selectedCategory"></td>
                        </tr>
                    </tbody>
                </table>

                <el-row>
                    <el-col :span="12">
                        <el-pagination @current-change="(p) => categoryPage = p"
                            :page-size="10"
                            background
                            layout="prev, pager, next"
                            :total="filteredCategory.length">
                        </el-pagination>
                    </el-col>
                    <el-col :span="12" class="text-right">
                        <strong>Total: {{filteredCategory.length | formatNumber}} items</strong>
                    </el-col>
                </el-row>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="selectCategory" icon="el-icon-success">DONE</el-button>
                <el-button type="info" @click="showCategoryList = false" icon="el-icon-error">CANCEL</el-button>
            </span>
        </el-dialog>
    </el-card>
</template>

<script>
import moment from 'moment'
import PengajuanPenjualanItemBb from '../components/PengajuanPenjualanItemBb'
import ApprovalHistory from '../components/ApprovalHistory'

export default {
    components: { PengajuanPenjualanItemBb, ApprovalHistory },
    computed: {
        filteredCategory() {
            let keyword = this.categoryKeyword.toLowerCase();
            return this.stock
                .filter(m => m.stock > 0 && (m.kategori.nama.toLowerCase().includes(keyword) || m.kategori.kode.toLowerCase().includes(keyword)))
                .filter(m => this.formModel.items_bb.findIndex(i => i.kategori_barang_id == m.kategori_barang_id) == -1)
        }
    },
    watch: {
        'formModel.location_id'(v, o) {
            if (v) {
                this.formModel.lokasi_asal = this.$store.state.locationList.find(l => l.id == v).name;
                let params = { location_id: v }
                axios.get('/stockBb/getStockList', { params: params }).then(r => {
                    this.stock = r.data
                }).catch(e => console.log(e))
            }
        },
    },
    data: function() {
        return {
            number: '0001',
            stock: [],
            showCategoryList: false,
            categoryKeyword: '',
            categoryPage: 1,
            selectedCategory: [],
            user: USER,
            loading: false,
            showForm: false,
            formTitle: '',
            formErrors: {},
            error: {},
            formModel: {
                jenis: 'BB',
                items_bb: []
            },
            keyword: '',
            page: 1,
            pageSize: 10,
            sort: 'tanggal',
            order: 'descending',
            filters: {},
            paginatedData: {},
            showDetail: false,
            selectedData: {},
            statuses: [
                {type: 'info', label: 'Draft', value: 0},
                {type: 'warning', label: 'Submitted', value: 1},
                {type: 'success', label: 'Approved', value: 2},
                {type: 'danger', label: 'Rejected', value: 3},
                {type: '', label: 'Processed', value: 4},
            ],
            approval_statuses: [
                {type: 'info', label: 'Pending', value: 0},
                {type: 'success', label: 'Approved', value: 1},
                {type: 'danger', label: 'Rejected', value: 2},
            ]
        }
    },
    methods: {
        searchCategory() {
            if (!this.formModel.location_id) {
                this.$message({ message: 'Mohon pilih plant', showClose: true, type: 'error', duration: 10000 });
                return
            }

            this.showCategoryList = true
        },
        selectCategory() {
            this.showCategoryList = false
            this.selectedCategory.forEach(c => {
                let exists = this.formModel.items_bb.find(i => i.kategori_barang_id == c.kategori_barang_id)
                if (!exists && c.stock > 0) {
                    this.formModel.items_bb.push({
                    kategori: c.kategori,
                    kategori_barang_id: c.kategori_barang_id,
                    stock_berat: c.stock,
                    eun: c.kategori.unit,
                    timbangan_manual: 0
                })
                }
            })
        },
        approve(data, status) {
            // kalau reject harus pake note
            if (status == 2) {
                this.$prompt('Mohon masukkan note').then(({value}) => {
                    this.doApprove(data, status, value)
                }).catch((e) => console.log(e))
            } else if (status == 1) {
                this.$confirm('Anda yakin?', 'Warning', {
                    type: 'warning',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Tidak'
                }).then(() => {
                    this.doApprove(data, status, '')
                }).catch(e => console.log(e));
            }
        },
        doApprove(data, status, note) {
            if (data.approval1_status == 0 || data.approval1_status == 2) {
                var approval_data = { level: 1, status: status, note: note }
            }

            else if (data.approval2_status == 0 || data.approval2_status == 2) {
                var approval_data = { level: 2, status: status, note: note }
            }

            else {
                return;
            }

            axios.put('/pengajuanPenjualan/' + data.id + '/approve', approval_data).then(r => {
                this.requestData();
                this.$message({
                    message: 'Approval berhasil.',
                    type: 'success',
                    showClose: true
                });
            }).catch(e => {
                this.$message({
                    message: 'Approval gagal. ' + e.response.data.message,
                    type: 'error',
                    showClose: true
                });
            })
        },
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
        deleteItem(index) {
            let items = this.formModel.items_bb

            if (!items[index].id) {
                items.splice(index, 1)
                return
            }

            this.$confirm('Anda yakin akan menghapus item ini?', 'Warning', {
                type: 'warning',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then(() => {
                axios.delete('/pengajuanPenjualanItemBb/' + items[index].id).then(r => {
                    this.$message({ message: 'Item berhasil dihapus', showClose: true, type: 'success' });
                    items.splice(index, 1);
                }).catch(e => {
                    this.$message({ message: 'Item gagal dihapus', showClose: true, type: 'error' });
                    console.log(e);
                })
            }).catch(() => {})
        },
        save() {
            if (this.formModel.items_bb.length == 0) {
                this.$message({ message: 'Mohon masukkan item', showClose: true, type: 'error' });
                return
            }

            let invalid = this.formModel.items_bb.filter(i => !i.timbangan_manual).length
            if (invalid) {
                this.$message({ message: 'Mohon lengkapi data barang.', showClose: true, type: 'error' });
                return
            }

            let invalid_berat = this.formModel.items_bb.filter(i => parseFloat(i.timbangan_manual) > parseFloat(i.stock_berat)).length
            if (invalid_berat) {
                this.$message({ message: 'Timbangan pengajuan melebihi stock', showClose: true, type: 'error' });
                return
            }

            if (!!this.formModel.id) {
                this.update()
            } else {
                this.store()
            }
        },
        saveSubmit() {
            this.$confirm('Anda yakin?', 'Warning', {
                type: 'warning',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then(() => {
                this.formModel.status = 1
                this.save()
            }).catch(() => {})
        },
        store() {
            this.loading = true;
            axios.post('/pengajuanPenjualan', this.formModel).then(r => {
                this.loading = false;
                this.showForm = false;
                this.$message({
                    message: 'Data BERHASIL disimpan.',
                    type: 'success'
                });
                this.requestData();
            }).catch(e => {
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
        update() {
            this.loading = true;
            axios.put('/pengajuanPenjualan/' + this.formModel.id, this.formModel).then(r => {
                this.loading = false;
                this.showForm = false
                this.$message({
                    message: 'Data BERHASIL disimpan.',
                    type: 'success'
                });
                this.requestData()
            }).catch(e => {
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
        addData() {
            this.formTitle = 'INPUT PENGAJUAN PENJUALAN BARANG BEKAS'
            this.error = {}
            this.formErrors = {}
            this.formModel = {
                no_aju: this.number + '/' + moment().format('MM') + '/' + moment().format('YYYY') + '/AJU',
                tanggal: moment().format('YYYY-MM-DD'),
                status: 0,
                jenis: 'BB',
                location_id: this.user.role == 0 ? this.user.location_id : '',
                items_bb: []
            }

            axios.get('/pengajuanPenjualan/getLastRecord', {
                params: { tahun: moment().format('YYYY')}
            }).then(r => {
                if (r.data) {
                    let int_number = parseInt(r.data.no_aju.slice(0, 4)) + 1;
                    this.number = int_number.toString().padStart(4, '0');
                    this.formModel.no_aju = this.number + '/' + moment().format('MM') + '/' + moment().format('YYYY') + '/AJU'
                }
                this.showForm = true
            }).catch(e => {
                this.$message({
                    message: 'Failed to fetch last record' + e,
                    type: 'error',
                    showClose: true
                });
            })
        },
        editData(data) {
            this.formTitle = 'EDIT PENGAJUAN PENJUALAN BARANG BEKAS'
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
                axios.delete('/pengajuanPenjualan/' + id).then(r => {
                    this.requestData();
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
        requestData() {
            let params = {
                page: this.page,
                keyword: this.keyword,
                pageSize: this.pageSize,
                sort: this.sort,
                order: this.order,
                jenis: 'BB'
            }

            this.loading = true;

            axios.get('/pengajuanPenjualan', {params: Object.assign(params, this.filters)}).then(r => {
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
    created() {
        this.requestData();
        this.$store.commit('getKategoriBarangList');
        this.$store.commit('getLocationList');
    }
}
</script>

<style lang="css" scoped>
.my-input {
    border: none;
    width: 100%;
    padding: 5px 0 5px 10px;
    background-color: #eee;
    /* background-color: transparent; */
}

input[type=checkbox] {
    width: 15px;
    height: 15px;
}

.icon-bg {
    font-size: 20px;
}

.td-label {
    width: 150px;
    background-color: #333c58;
    font-weight: bold;
    color: #fff;
}

.td-value {
    background-color: #efefef;
}
</style>
