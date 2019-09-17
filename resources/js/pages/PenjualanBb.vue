<template>
    <el-card>
        <h4>PENJUALAN BARANG BEKAS</h4>
        <hr>

        <el-form :inline="true" class="form-right" @submit.native.prevent="() => { return }">
            <el-form-item>
                <el-button @click="addData" type="primary"><i class="el-icon-plus"></i> INPUT PENJUALAN BARANG BEKAS</el-button>
            </el-form-item>
            <el-form-item style="margin-right:0;">
                <el-input placeholder="Search" prefix-icon="el-icon-search" v-model="keyword" @change="requestData" clearable>
                    <el-button @click="() => { page = 1; keyword = ''; requestData(); }" slot="append" icon="el-icon-refresh"></el-button>
                </el-input>
            </el-form-item>
        </el-form>

        <el-table :data="paginatedData.data" stripe
        height="calc(100vh - 330px)"
        :default-sort = "{prop: 'tanggal', order: 'descending'}"
        v-loading="loading"
        style="border-top:1px solid #eee;"
        @row-dblclick="(row, column, event) =>  { selectedData = row; showDetail = true }"
        @filter-change="filterChange"
        @sort-change="sortChange">
            <el-table-column type="index" width="50" :index="paginatedData.from"> </el-table-column>
            <el-table-column prop="tanggal" width="100" label="Tanggal" sortable="custom">
                <template slot-scope="scope">
                    {{ scope.row.tanggal | readableDate }}
                </template>
            </el-table-column>
            <el-table-column prop="user" label="User" min-width="150"></el-table-column>
            <el-table-column min-width="150px" prop="no_sj" label="No. Surat Jalan" sortable="custom"></el-table-column>
            <el-table-column min-width="150px" prop="no_aju" label="No. Pengajuan" sortable="custom"></el-table-column>

            <el-table-column
            prop="plant"
            label="Plant"
            sortable="custom"
            column-key="location_id"
            :filters="$store.state.locationList.map(l => { return {value: l.id, text: l.plant + ' - ' + l.name } })"
            width="120">
                <template slot-scope="scope">
                    {{scope.row.location.plant}} - {{scope.row.location.name}}
                </template>
            </el-table-column>

            <el-table-column
            prop="pembeli_id"
            label="Pembeli"
            sortable="custom"
            column-key="pembeli_id"
            :filters="$store.state.pembeliList.map(l => { return {value: l.id, text: l.nama } })"
            min-width="200">
                <template slot-scope="scope">
                    {{scope.row.pembeli.nama}} ({{scope.row.pembeli.kontak}})
                </template>
            </el-table-column>

            <el-table-column prop="jembatan_timbang" label="Jembatan Timbang" min-width="200" align="center" header-align="center" sortable="custom">
                <template slot-scope="scope">
                    {{scope.row.jembatan_timbang | formatNumber}} kg
                </template>
            </el-table-column>

            <el-table-column prop="value" label="Value" width="120" align="center" header-align="center" sortable="custom">
                <template slot-scope="scope">
                    <span class="text-info">Rp {{scope.row.value | formatNumber}}</span>
                </template>
            </el-table-column>

            <el-table-column label="Terbayar" width="120" align="center" header-align="center">
                <template slot-scope="scope">
                    <span class="text-success">Rp {{scope.row.terbayar | formatNumber}}</span>
                </template>
            </el-table-column>

            <el-table-column label="Outstanding" width="120" align="center" header-align="center">
                <template slot-scope="scope">
                    <span class="text-danger">Rp {{scope.row.value - scope.row.terbayar | formatNumber}}</span>
                </template>
            </el-table-column>

            <el-table-column label="TOP Date" width="100" align="center" header-align="center">
                <template slot-scope="scope">
                    {{scope.row.top_date | readableDate}}
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

            <el-table-column
            prop="status_pembayaran"
            width="170"
            align="center"
            header-align="center"
            label="Status Pembayaran"
            column-key="status_pembayaran"
            :filters="statuses_pembayaran.map(s => { return {value: s.value, text: s.label} })"
            sortable="custom">
                <template slot-scope="scope">
                    <el-tag size="small" :type="statuses_pembayaran[scope.row.status_pembayaran].type">{{statuses_pembayaran[scope.row.status_pembayaran].label}}</el-tag>
                </template>
            </el-table-column>

            <el-table-column fixed="right" width="40px">
                <template slot-scope="scope">
                    <el-dropdown>
                        <span class="el-dropdown-link">
                            <i class="el-icon-more"></i>
                        </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item @click.native.prevent="() => { selectedData = scope.row; showDetail = true; }"><i class="el-icon-zoom-in"></i> Show Detail</el-dropdown-item>
                            <el-dropdown-item v-if="scope.row.status === 0" @click.native.prevent="editData(scope.row)"><i class="el-icon-edit-outline"></i> Edit</el-dropdown-item>
                            <el-dropdown-item v-if="scope.row.status === 0" @click.native.prevent="deleteData(scope.row.id)"><i class="el-icon-delete"></i> Hapus</el-dropdown-item>
                            <el-dropdown-item v-if="scope.row.status === 1" @click.native.prevent="printSlipJual(scope.row.id)"><i class="el-icon-printer"></i> Print Slip Jual</el-dropdown-item>
                            <el-dropdown-item v-if="scope.row.status === 1 && scope.row.status_pembayaran !== 2" @click.native.prevent="inputPembayaran(scope.row)"><i class="el-icon-check"></i> Input Pembayaran</el-dropdown-item>
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

        <el-dialog top="60px" width="95%" center title="DETAIL PENJUALAN" :visible.sync="showDetail" v-if="!!selectedData.id">
            <el-row>
                <el-col :span="12">
                    <table class="table table-sm table-bordered">
                        <tbody>
                            <tr><td class="td-label">Tanggal</td><td class="td-value">{{selectedData.tanggal | readableDate}}</td></tr>
                            <tr><td class="td-label">No. Surat Jalan</td><td class="td-value">{{selectedData.no_sj}}</td></tr>
                            <tr><td class="td-label">No. Pengajuan</td><td class="td-value">{{selectedData.no_aju}}</td></tr>
                            <tr><td class="td-label">User</td><td class="td-value">{{selectedData.user}}</td></tr>
                            <tr><td class="td-label">Plant</td><td class="td-value">{{selectedData.location.plant}} - {{selectedData.location.name}}</td></tr>
                            <tr><td class="td-label">Pembeli</td><td class="td-value">{{selectedData.pembeli.nama}} ({{selectedData.pembeli.kontak}})</td></tr>
                        </tbody>
                    </table>
                </el-col>
                <el-col :span="12">
                    <table class="table table-sm table-bordered">
                        <tbody>
                            <tr><td class="td-label">Value</td><td class="td-value">Rp {{selectedData.value | formatNumber}}</td></tr>
                            <tr><td class="td-label">Terbayar</td><td class="td-value">Rp {{selectedData.terbayar | formatNumber}}</td></tr>
                            <tr><td class="td-label">Outstanding</td><td class="td-value">Rp {{selectedData.value - selectedData.terbayar | formatNumber}}</td></tr>
                            <tr><td class="td-label">TOP Date</td><td class="td-value">{{selectedData.top_date | readableDate}}</td></tr>
                            <tr><td class="td-label">Status</td><td class="td-value"><el-tag size="mini" :type="statuses[selectedData.status].type">{{statuses[selectedData.status].label}}</el-tag></td></tr>
                            <tr><td class="td-label">Status Pembayaran</td><td class="td-value"><el-tag size="mini" :type="statuses_pembayaran[selectedData.status].type">{{statuses_pembayaran[selectedData.status].label}}</el-tag></td></tr>
                        </tbody>
                    </table>
                </el-col>
            </el-row>

            <el-tabs type="border-card">
                <el-tab-pane label="Items">
                    <PenjualanItemBb :data="selectedData" />
                </el-tab-pane>
                <el-tab-pane label="Pembayaran">
                    <Pembayaran :data="selectedData.pembayaran" />
                </el-tab-pane>
            </el-tabs>
        </el-dialog>

        <!-- FORM PEMBAYARAN -->
        <el-dialog
        top="60px"
        center
        :visible.sync="showFormPembayaran"
        :title="!!formModelPembayaran.id ? 'Input Pembayaran' : 'Edit Pembayaran'"
        v-loading="loading"
        width="900px"
        :close-on-click-modal="false">
            <FormPembayaran
            @close-form="showFormPembayaran = false"
            @reload-data="requestData"
            :penjualan="formModelPembayaran" />
        </el-dialog>

        <!-- FORM INPUT/EDIT PENJUALAN -->
        <el-dialog top="60px" center :visible.sync="showForm" :title="formTitle" width="90%" v-loading="loading" :close-on-click-modal="false">
            <el-alert type="error" title="ERROR"
                :description="error.message + '\n' + error.file + ':' + error.line"
                v-show="error.message"
                style="margin-bottom:15px;">
            </el-alert>

            <el-form label-width="170px" label-position="left">
                <el-row :gutter="30">
                    <el-col :span="12">
                        <el-form-item label="Tanggal" :class="formErrors.tanggal ? 'is-error' : ''">
                            <el-date-picker v-model="formModel.tanggal" type="date" format="dd-MMM-yyyy" value-format="yyyy-MM-dd" placeholder="Tanggal" style="width:100%;"> </el-date-picker>
                            <div class="el-form-item__error" v-if="formErrors.tanggal">{{formErrors.tanggal[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Nomor Surat Jalan" :class="formErrors.no_sj ? 'is-error' : ''">
                            <el-input disabled placeholder="Nomor Surat Jalan" v-model="formModel.no_sj"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.no_sj">{{formErrors.no_sj[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Plant" :class="formErrors.location_id ? 'is-error' : ''">
                            <el-select :disabled="!!formModel.id" v-model="formModel.location_id" style="width:100%" placeholder="Plant">
                                <el-option
                                v-for="item in $store.state.locationList"
                                :key="item.id"
                                :label="item.plant + ' - ' + item.name"
                                :value="item.id">
                                </el-option>
                            </el-select>
                            <div class="el-form-item__error" v-if="formErrors.location_id">{{formErrors.location_id[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Nomor Pengajuan" :class="formErrors.no_aju ? 'is-error' : ''">
                            <el-select :disabled="!!formModel.id" v-model="formModel.no_aju" style="width:100%" placeholder="Nomor Pengajuan">
                                <el-option
                                v-for="item in $store.state.pengajuanPenjualanList.filter(p => p.location_id == formModel.location_id)"
                                :key="item.id"
                                :label="item.no_aju"
                                :value="item.no_aju">
                                </el-option>
                            </el-select>
                            <div class="el-form-item__error" v-if="formErrors.no_aju">{{formErrors.no_aju[0]}}</div>
                        </el-form-item>
                    </el-col>

                    <el-col :span="12">
                        <el-form-item label="Nama Pembeli" :class="formErrors.pembeli_id ? 'is-error' : ''">
                            <el-select v-model="formModel.pembeli_id" style="width:100%" placeholder="Nama Pembeli">
                                <el-option
                                v-for="item in $store.state.pembeliList"
                                :key="item.id"
                                :label="item.nama"
                                :value="item.id">
                                </el-option>
                            </el-select>
                            <div class="el-form-item__error" v-if="formErrors.pembeli_id">{{formErrors.pembeli_id[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Kontak Pembeli" :class="formErrors.kontak_pembeli ? 'is-error' : ''">
                            <el-input disabled placeholder="Kontak Pembeli" v-model="formModel.kontak_pembeli"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.kontak_pembeli">{{formErrors.kontak_pembeli[0]}}</div>
                        </el-form-item>

                        <el-form-item label="TOP Date" :class="formErrors.top_date ? 'is-error' : ''">
                            <el-date-picker placeholder="TOP Date" v-model="formModel.top_date" format="dd-MMM-yyyy" value-format="yyyy-MM-dd" style="width:100%;"></el-date-picker>
                            <div class="el-form-item__error" v-if="formErrors.top_date">{{formErrors.top_date[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Jembatan Timbang (KG)" :class="formErrors.jembatan_timbang ? 'is-error' : ''">
                            <el-input type="number" placeholder="Jembatan Timbang (KG)" v-model="formModel.jembatan_timbang"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.jembatan_timbang">{{formErrors.jembatan_timbang[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Value Penjualan (Rp)" :class="formErrors.value ? 'is-error' : ''">
                            <el-input type="number" placeholder="Value Penjualan (Rp)" v-model="formModel.value"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.value">{{formErrors.value[0]}}</div>
                        </el-form-item>

                    </el-col>
                </el-row>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered" v-if="formModel.items_bb.length > 0">
                        <thead>
                            <tr>
                                <th rowspan="2" class="text-center">#</th>
                                <th rowspan="2" class="text-center">Kategori</th>
                                <th rowspan="2" class="text-center">Stock</th>
                                <th rowspan="2" class="text-center">Satuan</th>
                                <th colspan="3" class="text-center">Pengajuan</th>
                                <th colspan="3" class="text-center">Aktual</th>
                            </tr>
                            <tr>
                                <th class="text-center">Berat/Qty</th>
                                <th class="text-center">Harga/Satuan Sistem</th>
                                <th class="text-center">Value Jual Sistem</th>
                                <th class="text-center">Berat/Qty</th>
                                <th class="text-center">Harga/Satuan Aktual (Rp)</th>
                                <th class="text-center">Value Jual Aktual</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in formModel.items_bb" :key="index">
                                <td class="text-center">{{index+1}}</td>
                                <td class="text-center">{{item.kategori.kode}} - {{item.kategori.nama}}</td>
                                <td class="text-center">{{item.stock_berat | formatNumber}}</td>
                                <td class="text-center">{{item.kategori.unit}}</td>
                                <td class="text-center">{{item.timbangan_manual | formatNumber}}</td>
                                <td class="text-center">Rp. {{item.kategori.harga | formatNumber}}</td>
                                <td class="text-center">Rp. {{item.kategori.harga * item.timbangan_manual | formatNumber}}</td>
                                <td class="text-center"><input type="text" v-model="item.jembatan_timbang" class="my-input" placeholder="Jembatan Timbang"></td>
                                <td class="text-center"><input type="text" v-model="item.price_per_kg" class="my-input" placeholder="Harga per Kg"></td>
                                <td class="text-center">Rp {{item.jembatan_timbang * item.price_per_kg | formatNumber}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
import PenjualanItemBb from '../components/PenjualanItemBb'
import Pembayaran from '../components/Pembayaran'
import FormPembayaran from '../components/FormPembayaran'

export default {
    components: { PenjualanItemBb, Pembayaran, FormPembayaran },
    watch: {
        'formModel.pembeli_id'(v, o) {
            let pembeli = this.$store.state.pembeliList.find(p => p.id == v)
            if (!!pembeli) {
                this.formModel.kontak_pembeli = pembeli.kontak
            }
        },
        'formModel.location_id'(v, o) {
            if (!this.formModel.id) {
                this.formModel.items_bb = []
            }
        },
        'formModel.no_aju'(v, o) {
            if (!!this.formModel.id) {
                return
            }

            let pengajuan = this.$store.state.pengajuanPenjualanList.find(p => p.no_aju == v)

            if (!!pengajuan) {
                this.formModel.items_bb = pengajuan.items_bb.map(i => {
                    return {
                        stock_berat: i.stock_berat,
                        stock_qty: i.stock_qty,
                        kategori_barang_id: i.kategori_barang_id,
                        qty: i.jumlah,
                        jembatan_timbang: i.timbangan_manual,
                        timbangan_manual: i.timbangan_manual,
                        kategori: i.kategori,
                        price_per_kg: i.kategori.harga,
                        value: i.kategori.harga * i.timbangan_manual
                    }
                })
                // console.log(this.formModel.items_bb)
            }

        }
    },
    data: function() {
        return {
            loading: false,
            showForm: false,
            showFormPembayaran: false,
            formTitle: '',
            formErrors: {},
            error: {},
            formModel: { jenis: 'BB', items_bb: [] },
            number: '0001',
            formModelPembayaran: {},
            keyword: '',
            page: 1,
            pageSize: 10,
            sort: 'tanggal',
            order: 'descending',
            filters: {},
            paginatedData: {},
            selectedData: {},
            showDetail: false,
            statuses: [
                {type: 'info', label: 'Draft', value: 0},
                {type: 'warning', label: 'Submitted', value: 1},
                {type: 'success', label: 'Approved', value: 2},
            ],
            statuses_pembayaran: [
                {type: 'info', label: 'No Payment', value: 0},
                {type: 'warning', label: 'Partial', value: 1},
                {type: 'success', label: 'Paid', value: 2},
            ]
        }
    },
    methods: {
        printSlipJual(id) {
            window.open(BASE_URL + '/penjualan/' + id + '/printSlip', '_blank')
        },
        inputPembayaran(data) {
            this.formModelPembayaran = data
            this.showFormPembayaran = true
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
        save() {
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
                this.$store.commit('getPengajuanPenjualanList', 'BB');
            }).catch(() => {})
        },
        store: function() {
            this.loading = true;
            axios.post('/penjualan', this.formModel).then(r => {
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
        update: function() {
            this.loading = true;
            axios.put('/penjualan/' + this.formModel.id, this.formModel).then(r => {
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
        addData: function() {
            this.formTitle = 'INPUT PENJUALAN BARANG BEKAS'
            this.error = {}
            this.formErrors = {}
            this.formModel = {
                no_sj: this.number + '/' + moment().format('MM') + '/' + moment().format('YYYY') + '/BB',
                jenis: 'BB',
                tanggal: moment().format('YYYY-MM-DD'),
                top_date: moment().add(30, 'days').format('YYYY-MM-DD'),
                status: 0,
                items_bb: []
            }

            axios.get('/penjualan/getLastRecord', {
                params: { tahun: moment().format('YYYY')}
            }).then(r => {
                if (r.data) {
                    let int_number = parseInt(r.data.no_sj.slice(0, 4)) + 1;
                    this.number = int_number.toString().padStart(4, '0');
                    this.formModel.no_sj = this.number + '/' + moment().format('MM') + '/' + moment().format('YYYY') + '/BB'
                }
                this.showForm = true
            }).catch(e => {
                this.$message({
                    message: 'Failed to fetch last record',
                    type: 'error',
                    showClose: true
                });
            })
        },
        editData: function(data) {
            this.formTitle = 'EDIT PENJUALAN BARANG BEKAS'
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
                axios.delete('/penjualan/' + id).then(r => {
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
            }).catch(e => console.log(e));
        },
        requestData: function() {
            let params = {
                page: this.page,
                keyword: this.keyword,
                jenis: 'BB',
                pageSize: this.pageSize,
                sort: this.sort,
                order: this.order,
            }

            this.loading = true;

            axios.get('/penjualan', {params: Object.assign(params, this.filters)}).then(r => {
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
        this.$store.commit('getKategoriBarangList');
        this.$store.commit('getLocationList');
        this.$store.commit('getPembeliList');
        this.$store.commit('getPengajuanPenjualanList', 'BB');
    }
}
</script>

<style lang="css" scoped>
.my-input {
    border: none;
    width: 100%;
    padding: 5px;
    background-color: #eee;
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
