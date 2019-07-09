<template>
    <el-card>
        <h4>PENGAJUAN PENJUALAN WASTE PRODUCT</h4>
        <hr>

        <el-form :inline="true" class="form-right">
            <el-form-item>
                <el-button @click="addData" type="primary"><i class="el-icon-plus"></i> INPUT PENGAJUAN PENJUALAN WASTE PRODUCT</el-button>
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
                    <PengajuanPenjualanDetailWp :data="scope.row" />
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
            <el-table-column prop="period_from" label="Periode" sortable="custom" width="200">
                <template slot-scope="scope">
                    {{scope.row.period_from | readableDate}} - {{scope.row.period_to | readableDate}}
                </template>
            </el-table-column>
            <!-- <el-table-column prop="jenis" label="Jenis" width="90" align="center" header-align="center" sortable="custom"></el-table-column> -->
            <el-table-column prop="mvt_type" label="MVT Type" sortable="custom"></el-table-column>
            <el-table-column prop="sloc" label="Sloc" sortable="custom"></el-table-column>

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

        <!-- DIALOG UNTUK FORM -->
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
                            <el-input disabled placeholder="Nomor Pengajuan" v-model="formModel.no_aju"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.no_aju">{{formErrors.no_aju[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Period From">
                            <el-date-picker v-model="formModel.period_from" type="date" value-format="yyyy-MM-dd" placeholder="Period From" style="width:100%;"> </el-date-picker>
                            <div class="el-form-item__error" v-if="formErrors.period_from">{{formErrors.period_from[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Period To">
                            <el-date-picker v-model="formModel.period_to" type="date" value-format="yyyy-MM-dd" placeholder="Period To" style="width:100%;"> </el-date-picker>
                            <div class="el-form-item__error" v-if="formErrors.period_to">{{formErrors.period_to[0]}}</div>
                        </el-form-item>
                    </el-col>

                    <el-col :span="12">
                        <el-form-item label="Plant">
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

                        <el-form-item label="Sloc">
                            <el-select :disabled="!!formModel.id" v-model="formModel.sloc" filterable style="width:100%" placeholder="Sloc">
                                <el-option
                                v-for="(item, id) in $store.state.slocList"
                                :key="id"
                                :label="item"
                                :value="item">
                                </el-option>
                            </el-select>
                            <div class="el-form-item__error" v-if="formErrors.sloc">{{formErrors.sloc[0]}}</div>
                        </el-form-item>

                        <el-form-item label="MVT Type">
                            <el-select :disabled="!!formModel.id" v-model="formModel.mvt_type" filterable default-first-option style="width:100%" placeholder="MVT Type" multiple>
                                <el-option
                                v-for="(item, id) in $store.state.mvtList"
                                :key="id"
                                :label="item"
                                :value="item">
                                </el-option>
                            </el-select>
                            <div class="el-form-item__error" v-if="formErrors.mvt_type">{{formErrors.mvt_type[0]}}</div>
                        </el-form-item>

                    </el-col>
                </el-row>

                <p> <el-button type="primary" @click="searchMaterial" icon="el-icon-search">SEARCH MATERIAL</el-button> </p>

                <table class="table table-sm table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Material ID</th>
                            <th>Material Name</th>
                            <!-- <th class="text-center">Unit</th> -->
                            <!-- <th class="text-center">Qty</th> -->
                            <th class="text-right">Stock</th>
                            <th class="text-center">Diajukan (kg)</th>
                            <th>Price/Unit (Rp)</th>
                            <th>Value</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(m, i) in formModel.items_wp" :key="i">
                            <td>{{i + 1}}.</td>
                            <td>{{m.material}}</td>
                            <td>{{m.material_description}}</td>
                            <!-- <td class="text-center">{{m.bun}}</td> -->
                            <!-- <td class="text-center">{{m.quantity}}</td> -->
                            <td class="text-right">{{(m.stock / 1000).toFixed(4) | formatNumber}} kg</td>
                            <td><input type="number" step="any" class="my-input" v-model="m.diajukan" placeholder="Diajukan"></td>
                            <td><input type="number" class="my-input" v-model="m.price_per_unit" placeholder="Price/Unit"></td>
                            <td class="text-right">Rp {{ (m.diajukan * m.price_per_unit).toFixed(0) | formatNumber }}</td>
                            <td><a href="#" @click="deleteItem(i)" class="icon-bg"><i class="el-icon-delete"></i></a></td>
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

        <!-- DIALOG UNTUK SEARCH MATERIAL -->
        <el-dialog center
        :visible.sync="showMaterialList"
        title="Select Material"
        width="900px"
        v-loading="loading"
        :close-on-click-modal="false">
            <el-input prefix-icon="el-icon-search" v-model="materialKeyword" placeholder="Search Material"></el-input>
            <br><br>
            <table class="table table-sm table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Material ID</th>
                            <th>Material Name</th>
                            <!-- <th class="text-center">Unit</th> -->
                            <!-- <th class="text-center">Qty</th> -->
                            <th class="text-right">Stock (kg)</th>
                            <th style="width:80px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(m, i) in filteredMaterial.slice((materialPage - 1) * 10, materialPage * 10)" :key="i">
                            <td>{{(i + 1) + ((materialPage - 1) * 10)}}.</td>
                            <td>{{m.material}}</td>
                            <td>{{m.material_description}}</td>
                            <!-- <td class="text-center">{{m.bun}}</td> -->
                            <!-- <td class="text-center">{{m.quantity}}</td> -->
                            <td class="text-right">{{(m.stock / 1000).toFixed(4) | formatNumber}}</td>
                            <td class="text-center"><input type="checkbox" :value="m" v-model="selectedMaterial"></td>
                        </tr>
                    </tbody>
                </table>

                <el-row>
                    <el-col :span="12">
                        <el-pagination @current-change="(p) => materialPage = p"
                            :page-size="10"
                            background
                            layout="prev, pager, next"
                            :total="filteredMaterial.length">
                        </el-pagination>
                    </el-col>
                    <el-col :span="12" class="text-right">
                        <strong>Total: {{filteredMaterial.length | formatNumber}} items</strong>
                    </el-col>
                </el-row>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="selectMaterial" icon="el-icon-success">DONE</el-button>
                <el-button type="info" @click="showMaterialList = false" icon="el-icon-error">CANCEL</el-button>
            </span>
        </el-dialog>
    </el-card>
</template>

<script>
import moment from 'moment'
import PengajuanPenjualanDetailWp from '../components/PengajuanPenjualanDetailWp'

export default {
    components: { PengajuanPenjualanDetailWp },
    watch: {
        keyword: function(v, o) {
            this.requestData()
        },
        pageSize: function(v, o) {
            this.requestData()
        },
        'formModel.location_id'(v, o) {
            if (v) {
                this.formModel.plant = this.$store.state.locationList.find(l => l.id == v).plant;
            }

            if (v != o && !this.formModel.id) {
                this.formModel.items_wp = []
            }
        },
        'formModel.sloc'(v, o) {
            if (v != o && !this.formModel.id) {
                this.formModel.items_wp = []
            }
        },
        'formModel.mvt_type'(v, o) {
            if (v != o && !this.formModel.id) {
                this.formModel.items_wp = []
            }
        }
    },
    computed: {
        filteredMaterial() {
            let keyword = this.materialKeyword.toLowerCase();
            return this.materials
                .filter(m => m.stock > 0 && (m.material.toLowerCase().includes(keyword) || m.material_description.toLowerCase().includes(keyword)))
                .filter(m => this.formModel.items_wp.findIndex(i => i.material == m.material) == -1)
        }
    },
    data: function() {
        return {
            number: '0001',
            loading: false,
            showForm: false,
            showMaterialList: false,
            formTitle: '',
            formErrors: {},
            error: {},
            formModel: {
                jenis: 'WP',
                items_wp: []
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
                {type: 'danger', label: 'Rejected', value: 3},
                {type: '', label: 'Processed', value: 4},
            ],
            approval_statuses: [
                {type: 'info', label: 'Pending', value: 0},
                {type: 'success', label: 'Approved', value: 1},
                {type: 'danger', label: 'Rejected', value: 2},
            ],
            materials: [],
            selectedMaterial: [],
            materialKeyword: '',
            materialPage: 1
        }
    },
    methods: {
        searchMaterial() {
            if (!this.formModel.location_id) {
                this.$message({ message: 'Mohon pilih Plant', showClose: true, type: 'error' });
                return
            }

            if (!this.formModel.sloc) {
                this.$message({ message: 'Mohon pilih Sloc', showClose: true, type: 'error' });
                return
            }

            let params = {
                plant: this.formModel.plant,
                mvt_type: this.formModel.mvt_type,
                sloc: this.formModel.sloc
            }
            axios.get(BASE_URL + '/stockWp/getList', { params: params }).then(r => {
                this.materials = r.data
                this.showMaterialList = true
            }).catch(e => console.log(e))
        },
        selectMaterial() {
            this.showMaterialList = false
            this.selectedMaterial.forEach(m => {
                let exists = this.formModel.items_wp.find(i => i.material == m.material)
                if (!exists) {
                    this.formModel.items_wp.push({
                        diajukan: (m.stock / 1000).toFixed(4),
                        price_per_unit: 0,
                        material: m.material,
                        material_description: m.material_description,
                        stock: m.stock
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
        deleteItem(index) {
            let items = this.formModel.items_wp

            if (!items[index].id) {
                items.splice(index, 1)
                return
            }

            this.$confirm('Anda yakin akan menghapus item ini?', 'Warning', {
                type: 'warning',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then(() => {
                axios.delete(BASE_URL + '/pengajuanPenjualanItemWp/' + items[index].id).then(r => {
                    this.$message({ message: 'Item berhasil dihapus', showClose: true, type: 'success' });
                    items.splice(index, 1);
                    this.requestData()
                }).catch(e => {
                    this.$message({ message: 'Item gagal dihapus', showClose: true, type: 'error' });
                    console.log(e);
                })
            }).catch(e => console.log(e))
        },
        save() {
            let invalid = this.formModel.items_wp.filter(i => i.diajukan > i.stock / 1000)
            if (invalid.length > 0) {
                this.$message({
                    message: 'Jumlah diajukan melebihi stock.',
                    type: 'error',
                    showClose: true
                });
                return
            }

            let invalid_price = this.formModel.items_wp.filter(i => !i.price_per_unit)
            if (invalid_price.length > 0) {
                this.$message({
                    message: 'Masukkan Price/Unit.',
                    type: 'error',
                    showClose: true
                });
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
                    type: 'success',
                    showClose: true
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
                    type: 'success',
                    showClose: true
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
            this.formTitle = 'INPUT PENGAJUAN PENJUALAN WASTE PRODUCT'
            this.error = {}
            this.formErrors = {}
            this.formModel = {
                no_aju: this.number + '/' + moment().format('MM') + '/' + moment().format('YYYY') + '/AJU',
                jenis: 'WP',
                tanggal: moment().format('YYYY-MM-DD'),
                status: 0,
                items_wp: []
            }

            axios.get(BASE_URL + '/pengajuanPenjualan/getLastRecord', {
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
        editData: function(data) {
            this.formTitle = 'EDIT PENGAJUAN PENJUALAN WASTE PRODUCT'
            this.formModel = JSON.parse(JSON.stringify(data));
            this.formModel.mvt_type = data.mvt_type.split(',')
            this.formModel.items_wp = data.items_wp.map(i => {
                return {
                    id: i.id,
                    material: i.material_id,
                    material_description: i.material_description,
                    diajukan: i.berat,
                    stock: i.stock,
                    price_per_unit: i.price_per_unit,
                    bun: i.unit
                }
            })
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
                        type: 'success',
                        showClose: true
                    });
                }).catch(e => {
                    this.$message({
                        message: 'Data GAGAL dihapus. ' + e.response.data.message,
                        type: 'error',
                        showClose: true
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
                jenis: 'WP'
            }

            this.loading = true;

            axios.get(BASE_URL + '/pengajuanPenjualan', {params: Object.assign(params, this.filters)}).then(r => {
                this.loading = false;
                this.paginatedData = r.data
            }).catch(e => {
                this.loading = false;
                this.$message({
                    message: e.response.data.message || e.response.message,
                    type: 'error',
                    showClose: true
                });
            })
        }
    },
    created: function() {
        this.requestData();
        this.$store.commit('getLocationList');
        this.$store.commit('getSlocList');
        this.$store.commit('getMvtList');
    }
}
</script>

<style lang="css" scoped>
.my-input {
    border: none;
    width: 100px;
    padding: 5px;
    background-color: #eee;
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
