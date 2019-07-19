<template>
    <el-card>
        <h4>PENGELUARAN BARANG BEKAS</h4>
        <hr>

        <el-form :inline="true" class="form-right" @submit.native.prevent="() => { return }">
            <el-form-item>
                <el-button @click="addData" type="primary"><i class="el-icon-plus"></i> INPUT PENGELUARAN BARANG BEKAS</el-button>
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
                    <PengeluaranDetail :data="scope.row" />
                </template>
            </el-table-column>
            <el-table-column prop="tanggal" width="100" label="Tanggal" sortable="custom">
                <template slot-scope="scope">
                    {{ scope.row.tanggal | readableDate }}
                </template>
            </el-table-column>
            <el-table-column prop="no_sj" label="No. Surat Jalan" min-width="100" sortable="custom"></el-table-column>

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
            <el-table-column prop="jembatan_timbang" label="Jembatan Timbang" sortable="custom" align="right" header-align="right">
                <template slot-scope="scope">
                    {{ scope.row.jembatan_timbang | formatNumber }} kg
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
                        <el-form-item label="Tanggal" :class="formErrors.tanggal ? 'is-error' : ''">
                            <el-date-picker v-model="formModel.tanggal" type="date" format="dd-MMM-yyyy" value-format="yyyy-MM-dd" placeholder="Tanggal" style="width:100%;"> </el-date-picker>
                            <div class="el-form-item__error" v-if="formErrors.tanggal">{{formErrors.tanggal[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Nomor Surat Jalan" :class="formErrors.no_sj ? 'is-error' : ''">
                            <el-input disabled placeholder="Nomor Surat Jalan" v-model="formModel.no_sj"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.no_sj">{{formErrors.no_sj[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Jembatan Timbang (kg)" :class="formErrors.jembatan_timbang ? 'is-error' : ''">
                            <el-input type="number" placeholder="Jembatan Timbang (kg)" v-model="formModel.jembatan_timbang"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.jembatan_timbang">{{formErrors.jembatan_timbang[0]}}</div>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Lokasi Asal" :class="formErrors.lokasi_asal_id ? 'is-error' : ''">
                            <el-select :disabled="!!formModel.id" v-model="formModel.lokasi_asal_id" style="width:100%" placeholder="Lokasi Asal">
                                <el-option
                                v-for="item in $store.state.locationList"
                                :key="item.id"
                                :label="item.plant + ' - ' + item.name"
                                :value="item.id">
                                </el-option>
                            </el-select>
                            <div class="el-form-item__error" v-if="formErrors.lokasi_asal">{{formErrors.lokasi_asal[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Lokasi Terima" :class="formErrors.lokasi_terima_id ? 'is-error' : ''">
                            <el-select v-model="formModel.lokasi_terima_id" style="width:100%" placeholder="Lokasi Terima">
                                <el-option
                                v-for="item in $store.state.locationList.filter(l => !l.is_dummy)"
                                :key="item.id"
                                :label="item.plant + ' - ' + item.name"
                                :value="item.id">
                                </el-option>
                            </el-select>
                            <div class="el-form-item__error" v-if="formErrors.lokasi_terima">{{formErrors.lokasi_terima[0]}}</div>
                        </el-form-item>
                    </el-col>
                </el-row>

                <p> <el-button type="primary" @click="searchCategory" icon="el-icon-search">SEARCH CATEGORY</el-button> </p>

                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2">#</th>
                            <th rowspan="2">Kategori</th>
                            <th colspan="2" class="text-center">Stock</th>
                            <th colspan="2" class="text-center">Dikeluarkan</th>
                            <th colspan="2" class="text-center">Selisih</th>
                            <th rowspan="2" class="text-center">
                            </th>
                        </tr>
                        <tr>
                            <th style="width:100px" class="text-center">Jumlah</th>
                            <th style="width:100px" class="text-center">Berat</th>
                            <th style="width:100px" class="text-center">Jumlah (PCS)</th>
                            <th style="width:100px" class="text-center">Berat (KG)</th>
                            <th style="width:100px" class="text-center">Jumlah (PCS)</th>
                            <th style="width:100px" class="text-center">Berat (KG)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in formModel.items" :key="index">
                            <td>{{index+1}}.</td>
                            <td>{{ item.kategori.kode }} - {{ item.kategori.nama }}</td>
                            <td class="text-right">{{item.stock_jumlah | formatNumber}} {{item.kategori.unit}}</td>
                            <td class="text-right">{{item.stock_berat | formatNumber}} KG</td>
                            <td> <input type="number" v-model="item.qty" class="my-input" placeholder="Jumlah"> </td>
                            <td> <input type="number" v-model="item.timbangan_manual" class="my-input" placeholder="Timbangan Manual"> </td>
                            <td class="text-right">{{item.stock_jumlah - item.qty | formatNumber}} {{item.kategori.unit}}</td>
                            <td class="text-right">{{item.stock_berat - item.timbangan_manual | formatNumber}} KG</td>
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
        :visible.sync="showCategoryList"
        title="Select Category"
        width="900px"
        v-loading="loading"
        :close-on-click-modal="false">
            <el-input prefix-icon="el-icon-search" v-model="categoryKeyword" placeholder="Search Category"></el-input>
            <br><br>
            <table class="table table-sm table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kategori</th>
                            <th class="text-right">Jumlah</th>
                            <th class="text-right">Berat</th>
                            <th style="width:80px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(m, i) in filteredCategory.slice((categoryPage - 1) * 10, categoryPage * 10)" :key="i">
                            <td>{{(i + 1) + ((categoryPage - 1) * 10)}}.</td>
                            <td>{{ m.kategori.kode }} - {{ m.kategori.nama }}</td>
                            <td class="text-right">{{ m.qty | formatNumber }} {{m.unit}}</td>
                            <td class="text-right">{{ m.stock | formatNumber }} KG</td>
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
import PengeluaranDetail from '../components/PengeluaranDetail'

export default {
    components: { PengeluaranDetail },
    computed: {
        filteredCategory() {
            let keyword = this.categoryKeyword.toLowerCase();
            return this.stock
                .filter(m => m.kategori.nama.toLowerCase().includes(keyword) || m.kategori.kode.toLowerCase().includes(keyword))
        }
    },
    watch: {
        'formModel.lokasi_asal_id'(v, o) {
            if (v) {
                this.formModel.lokasi_asal = this.$store.state.locationList.find(l => l.id == v).name;
                let params = { location_id: v }
                axios.get('/stockBb/getStockList', { params: params }).then(r => {
                    this.stock = r.data
                }).catch(e => console.log(e))
            }
        },
        'formModel.lokasi_terima_id'(v, o) {
            if (v) {
                this.formModel.lokasi_terima = this.$store.state.locationList.find(l => l.id == v).name;
            }
        }
    },
    data: function() {
        return {
            number: '00015',
            stock: [],
            showCategoryList: false,
            categoryKeyword: '',
            categoryPage: 1,
            selectedCategory: [],
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
                {type: 'warning', label: 'Submitted', value: 1},
                {type: 'success', label: 'Received', value: 2},
            ]
        }
    },
    methods: {
        searchCategory() {
            if (!this.formModel.lokasi_asal_id) {
                this.$message({ message: 'Mohon pilih lokasi asal', showClose: true, type: 'error', duration: 10000 });
                return
            }

            this.showCategoryList = true
        },
        selectCategory() {
            this.showCategoryList = false
            this.selectedCategory.forEach(c => {
                let exists = this.formModel.items.find(i => i.kategori_barang_id == c.kategori_barang_id)
                if (!exists) {
                    this.formModel.items.push({
                        kategori_barang_id: c.kategori_barang_id,
                        kategori: c.kategori,
                        eun: c.kategori.unit,
                        stock_jumlah: c.qty,
                        stock_berat: c.stock,
                        qty: 0,
                        timbangan_manual: 0
                    })
                }
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
            if (!this.formModel.items[index].id) {
                this.formModel.items.splice(index, 1)
                return
            }

            this.$confirm('Anda yakin akan menghapus item ini?', 'Warning', {
                type: 'warning',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then(() => {
                axios.delete('/pengeluaranItem/' + this.formModel.items[index].id)
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
            if (this.formModel.items.length == 0) {
                this.$message({ message: 'Mohon isi data barang.', showClose: true, type: 'error' });
                return
            }

            let invalid = this.formModel.items.filter(i => !i.qty || !i.timbangan_manual).length

            if (invalid) {
                this.$message({ message: 'Mohon lengkapi data barang.', showClose: true, type: 'error' });
                return
            }

            if (this.$store.state.locationList.find(l => l.id == this.formModel.lokasi_asal_id).is_dummy == false) {
                let invalid2 = this.formModel.items.filter(i => i.qty > i.stock_jumlah).length

                if (invalid2) {
                    this.$message({ message: 'Jumlah melebihi stock', showClose: true, type: 'error' });
                    return
                }

                let invalid3 = this.formModel.items.filter(i => i.timbangan_manual > i.stock_berat).length

                if (invalid3) {
                    this.$message({ message: 'Berat melebihi stock', showClose: true, type: 'error' });
                    return
                }
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
            axios.post('/pengeluaran', this.formModel)
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
            axios.put('/pengeluaran/' + this.formModel.id, this.formModel).then(r => {
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
            this.formTitle = 'INPUT PENGELUARAN BARANG BEKAS'
            this.error = {}
            this.formErrors = {}

            this.formModel = {
                no_sj: this.number + '/' + moment().format('MM') + '/' + moment().format('YYYY') + '/OUT/BB',
                tanggal: moment().format('YYYY-MM-DD'),
                status: 0,
                items: []
            }

            axios.get('/pengeluaran/getLastRecord', {
                params: { tahun: moment().format('YYYY')}
            }).then(r => {
                if (r.data) {
                    let int_number = parseInt(r.data.no_sj.slice(0, 4)) + 1;
                    this.number = int_number.toString().padStart(4, '0');
                    this.formModel.no_sj = this.number + '/' + moment().format('MM') + '/' + moment().format('YYYY') + '/OUT/BB'
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
            this.formTitle = 'EDIT PENGELUARAN BARANG BEKAS'
            this.formModel = JSON.parse(JSON.stringify(data));
            this.formModel.items = this.formModel.items.map(i => {
                i.kategori = {};
                return i;
            });
            this.error = {}
            this.formErrors = {}

            let params = { location_id: this.formModel.lokasi_asal_id }
            axios.get('/stockBb/getStockList', { params: params }).then(r => {
                this.stock = r.data
            }).catch(e => console.log(e))

            axios.get('/stockBb/getStockList').then(r => {
                this.formModel.items.forEach(i => {
                    let stock = r.data.find(d => d.kategori_barang_id == i.kategori_barang_id && d.location_id == this.formModel.lokasi_asal_id)
                    if (stock) {
                        i.stock_jumlah = stock.qty
                        i.stock_berat = stock.stock
                        i.kategori = stock.kategori
                    } else {
                        i.stock_jumlah = 0
                        i.stock_berat = 0
                        i.kategori = this.$store.state.kategoriBarangList.find(k => k.id == i.kategori_barang_id)
                    }
                })
                this.showForm = true
            }).catch(e => {
                this.$message({
                    message: 'Failed to get stock data.' + e,
                    type: 'error',
                    showClose: true
                });
            })
        },
        deleteData: function(id) {
            this.$confirm('Anda yakin akan menghapus data ini?', 'Warning', {
                type: 'warning',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then(() => {
                axios.delete('/pengeluaran/' + id).then(r => {
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

            axios.get('/pengeluaran', {params: Object.assign(params, this.filters)})
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
    /* background-color: transparent; */
}

input[type=checkbox] {
    width: 15px;
    height: 15px;
}

.my-input:hover, .my-input:focus {
    border: none;
}

.icon-bg {
    font-size: 20px;
}
</style>
