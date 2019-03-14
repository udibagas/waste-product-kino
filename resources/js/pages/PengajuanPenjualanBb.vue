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
                    <PengajuanPenjualanDetailBb :data="scope.row" />
                </template>
            </el-table-column>
            <el-table-column prop="tanggal" width="100" label="Tanggal" sortable="custom">
                <template slot-scope="scope">
                    {{ scope.row.tanggal | readableDate }}
                </template>
            </el-table-column>
            <el-table-column prop="no_aju" label="No. Pengajuan" sortable="custom"></el-table-column>
            <el-table-column prop="plant" label="Plant" sortable="custom">
                <template slot-scope="scope">
                    {{scope.row.plant}} - {{scope.row.location.name}}
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

                        <el-form-item label="Nomor Pengajuan">
                            <el-input placeholder="Nomor Pengajuan" v-model="formModel.no_aju"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.no_aju">{{formErrors.no_aju[0]}}</div>
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
                    </el-col>
                </el-row>
                
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th>Jumlah Diterima</th>
                            <th class="text-center">Eun</th>
                            <th>Timbangan Manual (kg)</th>
                            <th class="text-center">Selisih</th>
                            <th class="text-center">
                                <a href="#" @click="addItem" class="icon-bg"><i class="el-icon-circle-plus-outline"></i></a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in formModel.items_bb" :key="index">
                            <td>{{index+1}}.</td>
                            <td>
                                <select name="kat" placeholder="Kategori" v-model="formModel.items_bb[index].kategori_barang_id" class="my-input" @change="updateEun($event, index)">
                                    <option value="">-- Pilih Kategori --</option>
                                    <option v-for="(k, i) in $store.state.kategoriBarangList.filter(k => k.status == 1)" :key="i" :value="k.id">
                                        {{k.jenis}} : {{k.kode}} - {{k.nama}}
                                    </option>
                                </select>
                            </td>
                            <td><input type="number" v-model="formModel.items_bb[index].jumlah" class="my-input" placeholder="Jumlah"> </td>
                            <td><input type="number" v-model="formModel.items_bb[index].jumlah_terima" class="my-input" placeholder="Jumlah Diterima"></td>
                            <td class="text-center"> {{formModel.items_bb[index].eun}} </td>
                            <td><input type="number" v-model="formModel.items_bb[index].timbangan_manual" class="my-input" placeholder="Timbangan Manual"></td>
                            <td>{{item.jumlah - item.jumlah_terima | formatNumber}}</td>
                            <td class="text-center">
                                <a v-if="index > 0" href="#" @click="deleteItem(index)" class="icon-bg"><i class="el-icon-delete"></i></a>
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
import PengajuanPenjualanDetailBb from '../components/PengajuanPenjualanDetailBb'

export default {
    components: { PengajuanPenjualanDetailBb },
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
            loading: false,
            showForm: false,
            formTitle: '',
            formErrors: {},
            error: {},
            formModel: {
                jenis: 'BB',
                items_bb: [{
                    kategori_barang_id: '',
                    jumlah: '',
                    jumlah_terima: '',
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
                {type: 'info', label: 'Draft'},
                {type: 'warning', label: 'Submitted'},
                {type: 'success', label: 'Approved'},
            ]
        }
    },
    methods: {
        updateEun(event, index) {
            if (this.formModel.jenis == 'BB') {
                let selectedKategori = this.$store.state.kategoriBarangList.find(k => k.id == event.target.value)
                this.formModel.items_bb[index].eun = selectedKategori.unit;
            }
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
                    jumlah: '',
                    jumlah_terima: '',
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
            let invalid = this.formModel.items_bb.filter(i => !i.kategori_barang_id || !i.jumlah || !i.jumlah_terima || !i.timbangan_manual).length
            if (invalid) {
                this.$message({ message: 'Mohon lengkapi data barang.', showClose: true, type: 'error' });
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
        store: function() {
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
        update: function() {
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
        addData: function() {
            this.formTitle = 'INPUT PENGAJUAN PENJUALAN BARANG BEKAS'
            this.error = {}
            this.formErrors = {}
            this.formModel = {
                tanggal: moment().format('YYYY-MM-DD'),
                status: 0,
                items_bb: [{
                    kategori_barang_id: '',
                    jumlah: '',
                    jumlah_terima: '',
                    eun: '',
                    timbangan_manual: ''
                }]
            }

            this.showForm = true
        },
        editData: function(data) {
            this.formTitle = 'EDIT PENGAJUAN PENJUALAN BARANG BEKAS'
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
    created: function() {
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
    padding: 5px;
    /* background-color: #eee; */
    background-color: transparent;
}

.my-input:hover, .my-input:focus {
    border: none;
}

.icon-bg {
    font-size: 20px;
}
</style>
