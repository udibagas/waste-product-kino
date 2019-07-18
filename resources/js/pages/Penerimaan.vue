<template>
    <el-card>
        <h4>PENERIMAAN BARANG BEKAS</h4>
        <hr>

        <el-form :inline="true" class="form-right" @submit.native.prevent="() => { return }">
            <el-form-item>
                <el-button @click="addData" type="primary"><i class="el-icon-plus"></i> INPUT PENERIMAAN BARANG BEKAS</el-button>
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
        @filter-change="filterChange"
        @sort-change="sortChange">
            <el-table-column type="index" width="50" :index="paginatedData.from"> </el-table-column>
            <el-table-column type="expand" width="20px">
                <template slot-scope="scope">
                    <PenerimaanDetail :data="scope.row" />
                </template>
            </el-table-column>
            <el-table-column prop="tanggal" min-width="100" label="Tanggal" sortable="custom">
                <template slot-scope="scope">
                    {{ scope.row.tanggal | readableDate }}
                </template>
            </el-table-column>
            <el-table-column prop="no_sj_keluar" label="No. Surat Jalan" sortable="custom"></el-table-column>

            <el-table-column
            prop="lokasi_asal"
            label="Lokasi Asal"
            sortable="custom"
            column-key="lokasi_asal_id"
            :filters="$store.state.locationList.map(l => { return {value: l.id, text: l.plant + ' - ' + l.name } })" >
            </el-table-column>

            <el-table-column
            prop="lokasi_terima"
            label="Lokasi Terima"
            sortable="custom"
            column-key="lokasi_terima_id"
            :filters="$store.state.locationList.map(l => { return {value: l.id, text: l.plant + ' - ' + l.name } })" >
            </el-table-column>

            <el-table-column prop="penerima" label="Penerima" sortable="custom"></el-table-column>

            <el-table-column prop="keterangan" label="Keterangan" sortable="custom"></el-table-column>

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
                    <el-dropdown v-if="scope.row.status == 0">
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

        <el-dialog center :visible.sync="showForm" :title="formTitle" width="1000px" v-loading="loading" :close-on-click-modal="false">
            <el-alert type="error" title="ERROR"
                :description="error.message + '\n' + error.file + ':' + error.line"
                v-show="error.message"
                style="margin-bottom:15px;">
            </el-alert>

            <el-form label-width="170px">

                <el-row :gutter="15">
                    <el-col :span="12">
                        <el-form-item label="Tanggal" :class="formErrors.tanggal ? 'is-error' : ''">
                            <el-date-picker v-model="formModel.tanggal" type="date" value-format="yyyy-MM-dd" placeholder="Tanggal" style="width:100%;"> </el-date-picker>
                            <div class="el-form-item__error" v-if="formErrors.tanggal">{{formErrors.tanggal[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Nomor Surat Jalan Keluar" :class="formErrors.no_sj_keluar ? 'is-error' : ''">
                            <el-select :disabled="!!formModel.id" v-model="formModel.no_sj_keluar" style="width:100%" filterable default-first-option placeholder="Nomor Surat Jalan Keluar">
                                <el-option
                                v-for="item in $store.state.pengeluaranList"
                                :key="item.id"
                                :label="item.no_sj"
                                :value="item.no_sj">
                                </el-option>
                            </el-select>
                            <div class="el-form-item__error" v-if="formErrors.no_sj_keluar">{{formErrors.no_sj_keluar[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Keterangan" :class="formErrors.keterangan ? 'is-error' : ''">
                            <el-input type="textarea" rows="3" placeholder="Keterangan" v-model="formModel.keterangan"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.keterangan">{{formErrors.keterangan[0]}}</div>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Lokasi Asal" :class="formErrors.lokasi_asal ? 'is-error' : ''">
                            <el-input disabled placeholder="Lokasi Asal" v-model="formModel.lokasi_asal"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.lokasi_asal">{{formErrors.lokasi_asal[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Lokasi Terima" :class="formErrors.lokasi_terima ? 'is-error' : ''">
                            <el-input disabled placeholder="Lokasi Terima" v-model="formModel.lokasi_terima"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.lokasi_terima">{{formErrors.lokasi_terima[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Nama Penerima" :class="formErrors.penerima ? 'is-error' : ''">
                            <el-input placeholder="Nama Penerima" v-model="formModel.penerima"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.penerima">{{formErrors.penerima[0]}}</div>
                        </el-form-item>
                    </el-col>
                </el-row>

                <table class="table table-sm table-bordered" v-if="formModel.items.length > 0">
                    <thead>
                        <tr>
                            <th rowspan="2">#</th>
                            <th rowspan="2">Kategori</th>
                            <th colspan="3" class="text-center">Qty</th>
                            <th rowspan="2" class="text-center">Eun</th>
                            <th colspan="3" class="text-center">Timbangan Manual (kg)</th>
                            <th rowspan="2">Keterangan</th>
                        </tr>
                        <tr>
                            <th class="text-center" style="width:90px;">Kirim</th>
                            <th class="text-center" style="width:90px;">Terima</th>
                            <th class="text-center" style="width:90px;">Selisih</th>
                            <th class="text-center" style="width:90px;">Kirim</th>
                            <th class="text-center" style="width:90px;">Terima</th>
                            <th class="text-center" style="width:90px;">Selisih</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in formModel.items" :key="index">
                            <td>{{index+1}}.</td>
                            <td>{{formModel.items[index].barang.jenis}} : {{formModel.items[index].barang.kode}} - {{formModel.items[index].barang.nama}}</td>
                            <td class="text-center">{{formModel.items[index].qty_kirim | formatNumber}}</td>
                            <td><input type="number" v-model="formModel.items[index].qty_terima" class="my-input" placeholder="Terima"></td>
                            <td class="text-center">{{formModel.items[index].qty_kirim - formModel.items[index].qty_terima | formatNumber}}</td>
                            <td class="text-center">{{formModel.items[index].eun}}</td>
                            <td class="text-center">{{formModel.items[index].timbangan_manual_kirim | formatNumber}}</td>
                            <td><input type="number" v-model="formModel.items[index].timbangan_manual_terima" class="my-input" placeholder="Terima"> </td>
                            <td class="text-center">{{formModel.items[index].timbangan_manual_kirim - formModel.items[index].timbangan_manual_terima | formatNumber}}</td>
                            <td><input v-model="formModel.items[index].keterangan" class="my-input" placeholder="Keterangan"></td>
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
import PenerimaanDetail from '../components/PenerimaanDetail'

export default {
    components: { PenerimaanDetail },
    watch: {
        'formModel.no_sj_keluar'(v, o) {
            if (!!this.formModel.id) {
                return
            }

            let pengeluaran = this.$store.state.pengeluaranList.find(p => p.no_sj == v)
            if (pengeluaran) {
                this.formModel.lokasi_asal = pengeluaran.lokasi_asal
                this.formModel.lokasi_terima = pengeluaran.lokasi_terima
                this.formModel.lokasi_asal_id = pengeluaran.lokasi_asal_id
                this.formModel.lokasi_terima_id = pengeluaran.lokasi_terima_id
                this.formModel.items = pengeluaran.items.map(i => {
                    return {
                        kategori_barang_id: i.kategori_barang_id,
                        qty_kirim: i.qty,
                        qty_terima: i.qty,
                        eun: i.eun,
                        timbangan_manual_kirim: i.timbangan_manual,
                        timbangan_manual_terima: i.timbangan_manual,
                        barang: i.barang
                    }
                })
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
            formModel: { items: [] },
            keyword: '',
            page: 1,
            pageSize: 10,
            sort: 'tanggal',
            order: 'descending',
            filters: {},
            paginatedData: {},
            statuses: [
                {type: 'info', label: 'Draft', value: 0},
                {type: 'success', label: 'Submitted', value: 1}
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
            this.page = 1
            this.requestData();
        },
        save() {
            // validasi item
            let invalid = this.formModel.items.filter(i => !i.qty_terima || !i.timbangan_manual_terima).length

            if (invalid) {
                this.$message({ message: 'Mohon lengkapi data barang.', showClose: true, type: 'error' });
                return
            }

            let invalid2 = this.formModel.items.filter(i => i.qty_terima > i.qty_kirim).length

            if (invalid2) {
                this.$message({ message: 'Jumlah terima melebihi jumlah kirim', showClose: true, type: 'error' });
                return
            }

            let invalid3 = this.formModel.items.filter(i => i.timbangan_manual_terima > i.timbangan_manual_kirim).length

            if (invalid3) {
                this.$message({ message: 'Berat terima melebihi berat kirim', showClose: true, type: 'error' });
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
                this.$store.commit('getPengeluaranList');
            }).catch(() => {})
        },
        store: function() {
            this.loading = true;
            axios.post(BASE_URL + '/penerimaan', this.formModel).then(r => {
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
            axios.put(BASE_URL + '/penerimaan/' + this.formModel.id, this.formModel).then(r => {
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
            this.formTitle = 'INPUT PENERIMAAN BARANG BEKAS'
            this.error = {}
            this.formErrors = {}

            this.formModel = {
                tanggal: moment().format('YYYY-MM-DD'),
                status: 0,
                items: []
            }

            this.showForm = true
        },
        editData: function(data) {
            this.formTitle = 'EDIT PENERIMAAN BARANG BEKAS'
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
                axios.delete(BASE_URL + '/penerimaan/' + id).then(r => {
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
        requestData: function() {
            let params = {
                page: this.page,
                keyword: this.keyword,
                pageSize: this.pageSize,
                sort: this.sort,
                order: this.order,
            }
            this.loading = true;

            axios.get(BASE_URL + '/penerimaan', {params: Object.assign(params, this.filters)})
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
        this.$store.commit('getPengeluaranList');
        this.$store.commit('getLocationList');
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

.my-input:hover, .my-input:focus {
    border: none;
}

.icon-bg {
    font-size: 20px;
}
</style>
