<template>
    <el-card>
        <h4>PENGAJUAN PENJUALAN BARANG BEKAS</h4>
        <hr>

        <el-form :inline="true" class="form-right">
            <el-form-item>
                <el-button @click="addData" type="primary"><i class="el-icon-plus"></i> INPUT PENGAJUAN PENJUALAN BARANG BEKAS</el-button>
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
        :default-sort = "{prop: 'tanggal', order: 'descending'}"
        v-loading="loading"
        style="border-top:1px solid #eee;"
        @filter-change="filterChange"
        @sort-change="sortChange">
            <el-table-column type="index" width="50" :index="paginatedData.from"> </el-table-column>
            <el-table-column type="expand" width="20px">
                <template slot-scope="scope">
                    <el-tabs type="border-card">
                        <el-tab-pane label="Items">
                            <PengajuanPenjualanItemBb :data="scope.row" />
                        </el-tab-pane>
                        <el-tab-pane label="Approval History">
                            <ApprovalHistory :pengajuan="scope.row.id" />
                        </el-tab-pane>
                    </el-tabs>
                </template>
            </el-table-column>
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
                    <el-dropdown v-if="scope.row.approval2_status != 1">
                        <span class="el-dropdown-link">
                            <i class="el-icon-more"></i>
                        </span>
                        <el-dropdown-menu slot="dropdown">
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

        <el-dialog center :visible.sync="showForm" :title="formTitle" width="900px" v-loading="loading" :close-on-click-modal="false">
            <el-alert type="error" title="ERROR"
                :description="error.message + '\n' + error.file + ':' + error.line"
                v-show="error.message"
                style="margin-bottom:15px;">
            </el-alert>

            <el-form label-width="170px">
                <el-form-item label="Tanggal">
                    <el-date-picker v-model="formModel.tanggal" type="date" value-format="yyyy-MM-dd" placeholder="Tanggal" style="width:100%;"> </el-date-picker>
                    <div class="el-form-item__error" v-if="formErrors.tanggal">{{formErrors.tanggal[0]}}</div>
                </el-form-item>

                <el-form-item label="Nomor Pengajuan">
                    <el-input placeholder="Nomor Pengajuan" v-model="formModel.no_aju"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.no_aju">{{formErrors.no_aju[0]}}</div>
                </el-form-item>

                <el-form-item label="Plant">
                    <el-select :disabled="user.role == 0" v-model="formModel.location_id" style="width:100%" placeholder="Plant">
                        <el-option
                        v-for="item in $store.state.locationList"
                        :key="item.id"
                        :label="item.plant + ' - ' + item.name"
                        :value="item.id">
                        </el-option>
                    </el-select>
                    <div class="el-form-item__error" v-if="formErrors.location_id">{{formErrors.location_id[0]}}</div>
                </el-form-item>
                
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2">#</th>
                            <th rowspan="2">Kategori</th>
                            <th colspan="2" class="text-center">Stock</th>
                            <th rowspan="2" class="text-center">Jumlah Diajukan</th>
                            <th rowspan="2" class="text-center">Selisih</th>
                            <th rowspan="2" class="text-center">Eun</th>
                            <th rowspan="2">Timbangan Manual (kg)</th>
                            <th rowspan="2" class="text-center">
                                <a href="#" @click="addItem" class="icon-bg"><i class="el-icon-circle-plus-outline"></i></a>
                            </th>
                        </tr>
                        <tr>
                            <th style="width:80px" class="text-center">Qty</th>
                            <th style="width:80px" class="text-center">Berat (kg)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in formModel.items_bb" :key="index">
                            <td>{{index+1}}.</td>
                            <td>
                                <select name="kat" placeholder="Kategori" :disabled="!!item.id" v-model="item.kategori_barang_id" class="my-input" @change="updateStockInfo($event, index)">
                                    <option value="">-- Pilih Kategori --</option>
                                    <option v-for="(k, i) in $store.state.kategoriBarangList.filter(k => k.status == 2 && k.jenis == 'BB')" :key="i" :value="k.id">
                                        {{k.kode}} - {{k.nama}}
                                    </option>
                                </select>
                            </td>
                            <td class="text-center">{{item.stock_qty | formatNumber}}</td>
                            <td class="text-center">{{item.stock_berat | formatNumber}}</td>
                            <td><input type="number" v-model="item.jumlah" class="my-input" placeholder="Jumlah Diajukan"></td>
                            <td class="text-center">{{item.stock_qty - item.jumlah | formatNumber}}</td>
                            <td class="text-center"> {{item.eun}} </td>
                            <td><input type="number" v-model="item.timbangan_manual" class="my-input" placeholder="Timbangan Manual"></td>
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
    </el-card>
</template>

<script>
import moment from 'moment'
import PengajuanPenjualanItemBb from '../components/PengajuanPenjualanItemBb'
import ApprovalHistory from '../components/ApprovalHistory'

export default {
    components: { PengajuanPenjualanItemBb, ApprovalHistory },
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
            loading: false,
            showForm: false,
            formTitle: '',
            formErrors: {},
            error: {},
            formModel: {
                jenis: 'BB',
                items_bb: [{
                    kategori_barang_id: '',
                    jumlah: 0,
                    stock_qty: 0,
                    stock_berat: 0,
                    eun: '',
                    timbangan_manual: ''
                }]
            },
            keyword: '',
            page: 1,
            pageSize: 10,
            sort: 'tanggal',
            order: 'descending',
            filters: {},
            paginatedData: {},
            statuses: [
                {type: 'info', label: 'Draft', value: 0},
                {type: 'warning', label: 'Submitted', value: 1},
                {type: 'success', label: 'Approved', value: 2},
                {type: 'danger', label: 'Rejected', value: 3},
            ],
            approval_statuses: [
                {type: 'info', label: 'Pending', value: 0},
                {type: 'success', label: 'Approved', value: 1},
                {type: 'danger', label: 'Rejected', value: 2},
            ]
        }
    },
    methods: {
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

            axios.put(BASE_URL + '/pengajuanPenjualan/' + data.id + '/approve', approval_data).then(r => {
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
        updateStockInfo(event, index) {
            if (!this.formModel.location_id) {
                this.$message({
                    message: 'Mohon pilih plant terlebih dahulu',
                    type: 'warning',
                    showClose: true
                })

                this.formModel.items_bb[index].kategori_barang_id = ''
                return
            }

            let selectedKategori = this.$store.state.kategoriBarangList.find(k => k.id == event.target.value)
            this.formModel.items_bb[index].eun = selectedKategori.unit;
            
            let params = {
                location_id: this.formModel.location_id,
                kategori_barang_id: event.target.value
            }

            this.formModel.items_bb[index].stock_qty = 0
            this.formModel.items_bb[index].stock_berat = 0

            axios.get(BASE_URL + '/stockBb/getStock', { params: params }).then(r => {
                if (!!r.data && r.data.stock > 0) {
                    this.formModel.items_bb[index].stock_qty = r.data.qty
                    this.formModel.items_bb[index].stock_berat = r.data.stock
                    this.$forceUpdate()
                }
                
                else {
                    this.$message({
                        message: 'Tidak ada stock untuk kategori barang terkait',
                        type: 'warning',
                        showClose: true
                    })

                    this.formModel.items_bb[index].kategori_barang_id = ''
                }
            }).catch(e => {
                console.log(e);
            });
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
            this.refreshData();
        },
        goToPage: function(p) {
            this.page = p;
            this.requestData();
        },
        addItem() {
            if (this.formModel.jenis == 'BB') {
                this.formModel.items_bb.push({
                    kategori_barang_id: '',
                    jumlah: 0,
                    stock_qty: 0,
                    stock_berat: 0,
                    eun: '',
                    timbangan_manual: ''
                })
            }
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
                axios.delete(BASE_URL + '/pengajuanPenjualanItemBb/' + items[index].id).then(r => {
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

            let invalid = this.formModel.items_bb.filter(i => !i.kategori_barang_id || !i.jumlah || !i.timbangan_manual).length
            if (invalid) {
                this.$message({ message: 'Mohon lengkapi data barang.', showClose: true, type: 'error' });
                return
            }

            // pengajuan melebihi stock
            let invalid_qty = this.formModel.items_bb.filter(i => i.jumlah > i.stock_qty).length
            if (invalid_qty) {
                this.$message({ message: 'Jumlah pengajuan melebihi stock', showClose: true, type: 'error' });
                return
            }

            let invalid_berat = this.formModel.items_bb.filter(i => i.timbangan_manual > i.stock_berat).length
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
            axios.post(BASE_URL + '/pengajuanPenjualan', this.formModel).then(r => {
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
            axios.put(BASE_URL + '/pengajuanPenjualan/' + this.formModel.id, this.formModel).then(r => {
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
                tanggal: moment().format('YYYY-MM-DD'),
                status: 0,
                jenis: 'BB',
                location_id: this.user.role == 0 ? this.user.location_id : '',
                items_bb: [{
                    kategori_barang_id: '',
                    jumlah: '',
                    stock_qty: 0,
                    stock_berat: 0,
                    eun: '',
                    timbangan_manual: ''
                }]
            }

            this.showForm = true
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
                axios.delete(BASE_URL + '/pengajuanPenjualan/' + id).then(r => {
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
        refreshData() {
            this.keyword = '';
            this.page = 1;
            this.requestData();
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

            axios.get(BASE_URL + '/pengajuanPenjualan', {params: Object.assign(params, this.filters)}).then(r => {
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

.icon-bg {
    font-size: 20px;
}
</style>
