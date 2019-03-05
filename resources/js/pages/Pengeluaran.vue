<template>
    <el-card>
        <h4>PENGELUARAN BARANG</h4>
        <hr>

        <el-form :inline="true" class="form-right">
            <el-form-item>
                <el-button @click="addData" type="primary"><i class="el-icon-plus"></i> INPUT PENGELUARAN BARANG</el-button>
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
            <el-table-column prop="tanggal" label="Tanggal" sortable="custom">
                <template slot-scope="scope">
                    {{ scope.row.tanggal | readableDate }}
                </template>
            </el-table-column>
            <el-table-column prop="no_sj" label="Nomor Surat Jalan" sortable="custom"></el-table-column>
            <el-table-column prop="lokasi_asal" label="Lokasi Asal" sortable="custom"></el-table-column>
            <el-table-column prop="lokasi_terima" label="Lokasi Terima" sortable="custom"></el-table-column>
            <el-table-column prop="penerima" label="Penerima" sortable="custom"></el-table-column>
            <el-table-column prop="jembatan_timbang" label="Jembatan Timbang" sortable="custom" align="right" header-align="right">
                <template slot-scope="scope">
                    {{ scope.row.jembatan_timbang | formatNumber }} kg
                </template>
            </el-table-column>
            <el-table-column prop="status" label="Status" sortable="custom">
                <template slot-scope="scope">
                    <el-tag :type="statuses[scope.row.status].type">{{statuses[scope.row.status].label}}</el-tag>
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

                        <el-form-item label="Jembatan Timbang (kg)">
                            <el-input type="number" placeholder="Jembatan Timbang (kg)" v-model="formModel.jembatan_timbang"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.jembatan_timbang">{{formErrors.jembatan_timbang[0]}}</div>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Lokasi Asal">
                            <el-input placeholder="Lokasi Asal" v-model="formModel.lokasi_asal"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.lokasi_asal">{{formErrors.lokasi_asal[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Lokasi Terima">
                            <el-input placeholder="Lokasi Terima" v-model="formModel.lokasi_terima"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.lokasi_terima">{{formErrors.lokasi_terima[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Nama Penerima">
                            <el-input placeholder="Nama Penerima" v-model="formModel.penerima"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.penerima">{{formErrors.penerima[0]}}</div>
                        </el-form-item>
                    </el-col>
                </el-row>
                
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kategori</th>
                            <th>Qty</th>
                            <th>Eun</th>
                            <th>Timbangan Manual (kg)</th>
                            <th>
                                <a href="#" @click="addItem" class="icon-bg"><i class="el-icon-circle-plus-outline"></i></a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in formModel.items" :key="index">
                            <td>{{index+1}}.</td>
                            <td>
                                <select name="kat" placeholder="Kategori" v-model="formModel.items[index].kategori_barang_id" class="my-input" @change="updateEun($event, index)">
                                    <option value="">-- Pilih Kategori --</option>
                                    <option v-for="(k, i) in $store.state.kategoriBarangList" :key="i" :value="k.id">
                                        {{k.jenis}} : {{k.kode}} - {{k.nama}}
                                    </option>
                                </select>
                            </td>
                            <td> <input type="number" v-model="formModel.items[index].qty" class="my-input" placeholder="Qty"> </td>
                            <td> {{formModel.items[index].eun}} </td>
                            <td> <input type="number" v-model="formModel.items[index].timbangan_manual" class="my-input" placeholder="Timbangan Manual"> </td>
                            <td>
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
            loading: false,
            showForm: false,
            formTitle: '',
            formErrors: {},
            error: {},
            formModel: {
                items: [{
                    kategori_barang_id: '',
                    qty: '',
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
                {type: 'primary', label: 'Submitted'},
                {type: 'success', label: 'Received'},
            ]
        }
    },
    methods: {
        updateEun(event, index) {
            let selectedKategori = this.$store.state.kategoriBarangList.find(k => k.id == event.target.value)
            this.formModel.items[index].eun = selectedKategori.unit;
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
            this.formModel.items.push({
                kategori_barang_id: '',
                qty: '',
                eun: '',
                timbangan_manual: ''
            })
        },
        deleteItem(index) {
            if (!this.formModel.items[index].id) {
                this.formModel.items.splice(index, 1)
                return
            }

            this.$confirm('Anda yakin akan menghapus item ini?', 'Warning', {
                type: 'warning',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then(() => {
                axios.delete(BASE_URL + '/pengeluaranItem/' + this.formModel.items[index].id)
                .then(r => {
                    this.$message({ message: 'Item berhasil dihapus', showClose: true, type: 'success' });
                    this.formModel.items.splice(index, 1);
                })
                .catch(e => {
                    this.$message({ message: 'Item gagal dihapus', showClose: true, type: 'error' });
                    console.log(e);
                })
            }).catch(() => {})
        },
        save() {
            // validasi item
            let invalid = this.formModel.items.filter(i => !i.kategori_barang_id || !i.qty || !i.timbangan_manual).length
            
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
            axios.post(BASE_URL + '/pengeluaran', this.formModel)
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
            axios.put(BASE_URL + '/pengeluaran/' + this.formModel.id, this.formModel).then(r => {
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
            this.formTitle = 'INPUT PENGELUARAN BARANG'
            this.error = {}
            this.formErrors = {}
            
            this.formModel = {
                tanggal: moment().format('YYYY-MM-DD'),
                items: [{
                    kategori_barang_id: '',
                    qty: '',
                    eun: '',
                    timbangan_manual: ''
                }]
            }

            this.showForm = true
        },
        editData: function(data) {
            this.formTitle = 'EDIT PENGELUARAN BARANG'
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
                axios.delete(BASE_URL + '/pengeluaran/' + id).then(r => {
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

            axios.get(BASE_URL + '/pengeluaran', {params: Object.assign(params, this.filters)})
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
        this.$store.commit('getKategoriBarangList');
    }
}
</script>

<style lang="css" scoped>
.my-input {
    border: none;
    width: 100%;
    padding: 5px;
    background-color: transparent;
}

.my-input:hover, .my-input:focus {
    border: none;
}

.icon-bg {
    font-size: 20px;
}
</style>
