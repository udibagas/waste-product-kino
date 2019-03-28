<template>
    <el-card>
        <h4>PENJUALAN BARANG BEKAS</h4>
        <hr>

        <el-form :inline="true" class="form-right">
            <el-form-item>
                <el-button @click="addData" type="primary"><i class="el-icon-plus"></i> INPUT PENJUALAN BARANG BEKAS</el-button>
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
                    {{scope.row.id}}
                </template>
            </el-table-column>
            <el-table-column prop="tanggal" width="100" label="Tanggal" sortable="custom">
                <template slot-scope="scope">
                    {{ scope.row.tanggal | readableDate }}
                </template>
            </el-table-column>
            <el-table-column prop="no_sj" label="No. Surat Jalan" sortable="custom"></el-table-column>
            <el-table-column prop="plant" label="Plant" sortable="custom" width="120">
                <template slot-scope="scope">
                    {{scope.row.location.plant}} - {{scope.row.location.name}}
                </template>
            </el-table-column>
            <el-table-column prop="pembeli_id" label="Pembeli" sortable="custom" width="200">
                <template slot-scope="scope">
                    {{scope.row.pembeli.nama}}
                </template>
            </el-table-column>
            <el-table-column prop="value" label="Value" width="90" align="center" header-align="center" sortable="custom">
                <template slot-scope="scope">
                    {{scope.row.value | formatNumber}}
                </template>
            </el-table-column>
            <el-table-column label="TOP Date" width="90" align="center" header-align="center">
                <template slot-scope="scope">
                    {{scope.row.top_date}}
                </template>
            </el-table-column>
            <el-table-column prop="status" width="100" align="center" header-align="center" label="Status" sortable="custom">
                <template slot-scope="scope">
                    <el-tag size="small" :type="statuses[scope.row.status].type">{{statuses[scope.row.status].label}}</el-tag>
                </template>
            </el-table-column>

            <el-table-column fixed="right" width="40px">
                <template slot-scope="scope">
                    <el-dropdown v-if="scope.row.status === 0">
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

        <el-dialog center :visible.sync="showForm" :title="formTitle" width="900px" v-loading="loading" :close-on-click-modal="false">
            <el-alert type="error" title="ERROR"
                :description="error.message + '\n' + error.file + ':' + error.line"
                v-show="error.message"
                style="margin-bottom:15px;">
            </el-alert>

            <el-form label-width="170px">
                <el-row :gutter="15">
                    <el-col :span="12">
                        <el-form-item label="Tanggal">
                            <el-date-picker v-model="formModel.tanggal" type="date" value-format="yyyy-MM-dd" placeholder="Tanggal" style="width:100%;"> </el-date-picker>
                            <div class="el-form-item__error" v-if="formErrors.tanggal">{{formErrors.tanggal[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Nomor Surat Jalan">
                            <el-input placeholder="Nomor Surat Jalan" v-model="formModel.no_sj"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.no_sj">{{formErrors.no_sj[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Plant">
                            <el-select v-model="formModel.location_id" style="width:100%" placeholder="Plant">
                                <el-option
                                v-for="item in $store.state.locationList"
                                :key="item.id"
                                :label="item.plant + ' - ' + item.name"
                                :value="item.id">
                                </el-option>
                            </el-select>
                            <div class="el-form-item__error" v-if="formErrors.location_id">{{formErrors.location_id[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Nomor Pengajuan">
                            <el-select v-model="formModel.no_aju" style="width:100%" placeholder="Nomor Pengajuan">
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
                        <el-form-item label="Nama Pembeli">
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

                        <el-form-item label="Kontak Pembeli">
                            <el-input disabled placeholder="Kontak Pembeli" v-model="formModel.kontak_pembeli"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.kontak_pembeli">{{formErrors.kontak_pembeli[0]}}</div>
                        </el-form-item>

                        <el-form-item label="TOP Date">
                            <el-date-picker placeholder="TOP Date" v-model="formModel.top_date" value-format="yyyy-MM-dd" style="width:100%;"></el-date-picker>
                            <div class="el-form-item__error" v-if="formErrors.top_date">{{formErrors.top_date[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Jembatan Timbang">
                            <el-input type="number" placeholder="Jembatan Timbang" v-model="formModel.jembatan_timbang"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.jembatan_timbang">{{formErrors.jembatan_timbang[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Value Penjualan">
                            <el-input type="number" placeholder="Value Penjualan" v-model="formModel.value"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.value">{{formErrors.value[0]}}</div>
                        </el-form-item>

                    </el-col>
                </el-row>
                
                <table class="table table-sm table-bordered" v-if="formModel.items_bb.length > 0">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Kategori</th>
                            <th class="text-center">Stock (kg)</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Timbangan Manual (kg)</th>
                            <th class="text-center">Jembatan Timbang (kg)</th>
                            <th class="text-center">Harga/Kg Sistem (Rp)</th>
                            <th class="text-center">Harga/Kg Aktual (Rp)</th>
                            <th class="text-center">Value Jual Sistem (Rp)</th>
                            <th class="text-center">Value Jual Aktual (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in formModel.items_bb" :key="index">
                            <td class="text-center">{{index+1}}</td>
                            <td class="text-center">{{item.kategori.kode}} - {{item.kategori.nama}}</td>
                            <td class="text-center">{{item.stock_berat | formatNumber}}</td>
                            <td class="text-center">{{item.qty | formatNumber}}</td>
                            <td class="text-center">{{item.berat | formatNumber}}</td>
                            <td class="text-center"><input type="text" v-model="item.jembatan_timbang" class="my-input" placeholder="Jembatan Timbang"></td>
                            <td class="text-center">{{item.kategori.harga | formatNumber}}</td>
                            <td class="text-center"><input type="text" v-model="item.price_per_kg" class="my-input" placeholder="Harga per Kg"></td>
                            <td class="text-center">{{item.kategori.harga * item.berat | formatNumber}}</td>
                            <td class="text-center"><input type="text" v-model="item.value" class="my-input" placeholder="Value Jual"></td>
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

export default {
    watch: {
        keyword: function(v, o) {
            this.requestData()
        },
        pageSize: function(v, o) {
            this.requestData()
        },
        'formModel.pembeli_id'(v, o) {
            let pembeli = this.$store.state.pembeliList.find(p => p.id == v)
            if (!!pembeli) {
                this.formModel.kontak_pembeli = pembeli.kontak
            }
        },
        'formModel.no_aju'(v, o) {
            if (!!this.formModel.id) {
                return
            }

            let pengajuan = this.$store.state.pengajuanPenjualanList.find(p => p.no_aju == v)
            // this.formModel.jembatan_timbang = pengajuan.jembatan_timbang

            if (!!pengajuan) {
                this.formModel.items_bb = pengajuan.items_bb.map(i => {
                    return {
                        stock_berat: i.stock_berat,
                        kategori_barang_id: i.kategori_barang_id,
                        qty: i.jumlah,
                        jembatan_timbang: i.timbangan_manual,
                        berat: i.timbangan_manual,
                        kategori: i.kategori,
                        price_per_kg: i.kategori.harga
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
            formTitle: '',
            formErrors: {},
            error: {},
            formModel: { jenis: 'BB', items_bb: [] },
            keyword: '',
            page: 1,
            pageSize: 10,
            sort: 'tanggal',
            order: 'descending',
            filters: {},
            paginatedData: {},
            statuses: [
                {type: 'info', label: 'Draft'},
                {type: 'warning', label: 'Submitted'},
                {type: 'success', label: 'Approved'},
            ]
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
        store: function() {
            this.loading = true;
            axios.post(BASE_URL + '/penjualan', this.formModel).then(r => {
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
            axios.put(BASE_URL + '/penjualan/' + this.formModel.id, this.formModel).then(r => {
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
                jenis: 'BB',
                tanggal: moment().format('YYYY-MM-DD'),
                status: 0,
                items_bb: []
            }

            this.showForm = true
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
                axios.delete(BASE_URL + '/penjualan/' + id).then(r => {
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

            axios.get(BASE_URL + '/penjualan', {params: Object.assign(params, this.filters)}).then(r => {
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
</style>