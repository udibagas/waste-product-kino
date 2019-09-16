<template>
    <el-card>
        <h4>KONVERSI BERAT</h4>
        <hr>

        <el-form :inline="true" class="form-right" @submit.native.prevent="() => { return }">
            <el-form-item>
                <el-button @click="openForm" type="primary" icon="el-icon-upload">UPLOAD DATA KONVERSI BERAT</el-button>
            </el-form-item>
            <el-form-item>
                <el-button @click="() => { showForm = true; formModel = {}; }" type="primary" icon="el-icon-plus">TAMBAH KONVERSI BERAT</el-button>
            </el-form-item>
            <el-form-item style="margin-right:0;">
                <el-input placeholder="Search" prefix-icon="el-icon-search" v-model="keyword" @change="requestData" clearable>
                    <el-button @click="() => { page = 1; keyword = ''; requestData(); }" slot="append" icon="el-icon-refresh"></el-button>
                </el-input>
            </el-form-item>
        </el-form>

        <el-table :data="paginatedData.data" stripe
        height="calc(100vh - 330px)"
        :default-sort = "{prop: 'material_id', order: 'ascending'}"
        v-loading="loading"
        style="border-top:1px solid #eee;"
        @sort-change="sortChange">
            <el-table-column type="index" min-width="50" :index="paginatedData.from"> </el-table-column>
            <el-table-column prop="kategori_jual" label="Kategori Jual" sortable="custom" min-width="120px"></el-table-column>
            <el-table-column prop="finished_good" label="Finished Goods" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column prop="material_id" label="Material ID" min-width="170" sortable="custom"></el-table-column>
            <el-table-column prop="material_description" min-width="350" label="Material Description" sortable="custom"></el-table-column>
            <el-table-column prop="berat" label="Berat Rata - Rata (gr)" align="center" header-align="center" sortable="custom" min-width="170px"></el-table-column>
            <el-table-column prop="keterangan" label="Keterangan" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column fixed="right" width="40px">
                <template slot-scope="scope">
                    <el-dropdown>
                        <span class="el-dropdown-link">
                            <i class="el-icon-more"></i>
                        </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item @click.native.prevent="() => { showForm = true; formModel = JSON.parse(JSON.stringify(scope.row)); }"><i class="el-icon-edit-outline"></i> Edit</el-dropdown-item>
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

        <el-dialog center :visible.sync="showForm" :title="!!formModel.id ? 'EDIT KONVERSI BERAT' : 'TAMBAH KONVERSI BERAT'" width="450px" v-loading="loading" :close-on-click-modal="false">
            <el-alert type="error" title="ERROR"
                :description="error.message + '\n' + error.file + ':' + error.line"
                v-show="error.message"
                style="margin-bottom:15px;">
            </el-alert>

            <el-form label-width="150px" label-position="left">
                <el-form-item label="Kategori Jual" :class="formErrors.kategori_jual ? 'is-error' : ''">
                    <el-select placeholder="Kategori Jual" v-model="formModel.kategori_jual" style="width:100%;">
                        <el-option
                        v-for="k in $store.state.kategoriBarangList.filter(k => k.jenis == 'WP')"
                        :key="k.id"
                        :value="k.nama"
                        :label="k.nama"></el-option>
                    </el-select>
                    <div class="el-form-item__error" v-if="formErrors.kategori_jual">{{formErrors.kategori_jual[0]}}</div>
                </el-form-item>

                <el-form-item label="Finished Goods" :class="formErrors.finished_good ? 'is-error' : ''">
                    <el-input placeholder="Finished Goods" v-model="formModel.finished_good"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.finished_good">{{formErrors.finished_good[0]}}</div>
                </el-form-item>

                <el-form-item label="Material ID" :class="formErrors.material_id ? 'is-error' : ''">
                    <el-input placeholder="Material ID" v-model="formModel.material_id"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.material_id">{{formErrors.material_id[0]}}</div>
                </el-form-item>

                <el-form-item label="Material Description" :class="formErrors.material_description ? 'is-error' : ''">
                    <el-input placeholder="Material Description" v-model="formModel.material_description"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.material_description">{{formErrors.material_description[0]}}</div>
                </el-form-item>

                <el-form-item label="Berat Rata - Rata (gr)" :class="formErrors.berat ? 'is-error' : ''">
                    <el-input type="number" placeholder="Berat Rata - Rata (gr)" v-model="formModel.berat"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.berat">{{formErrors.berat[0]}}</div>
                </el-form-item>

                <el-form-item label="Keterangan">
                    <el-input placeholder="Keterangan" v-model="formModel.keterangan"></el-input>
                </el-form-item>
            </el-form>

            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="() => !!formModel.id ? update() : store()" icon="el-icon-success">SAVE</el-button>
                <el-button type="info" @click="showForm = false" icon="el-icon-error">CANCEL</el-button>
            </span>
        </el-dialog>

        <el-dialog
        :visible.sync="uploadFormDialog"
        title="Upload Data Konversi Berat"
        width="700px"
        v-loading="loading"
        :close-on-click-modal="false">

            Select File : <input type="file" id="file-upload">
            <div id="logs" class="logs-container">
                <span v-for="(log, i) in logs" :key="i" v-html="log"></span>
            </div>
        </el-dialog>
    </el-card>
</template>

<script>
import XLSX from 'xlsx'

export default {
    data: function() {
        return {
            loading: false,
            keyword: '',
            page: 1,
            pageSize: 10,
            sort: 'material_id',
            order: 'ascending',
            paginatedData: {},
            uploadFormDialog: false,
            logs: ['Please select file...<br>'],
            formModel: {},
            formErrors: {},
            error: {},
            showForm: false
        }
    },
    methods: {
        openForm() {
            $('#file-upload').val('');
            this.logs = ['Please select file...<br>']
            this.uploadFormDialog = true
        },
        deleteData(id) {
            this.$confirm('Anda yakin akan menghapus data ini?', 'Warning', {
                type: 'warning',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then(() => {
                axios.delete('/konversiBerat/' + id).then(r => {
                    this.requestData();
                    this.$message({
                        message: 'Data berhasil dihapus.',
                        type: 'success',
                        showClose: true
                    });
                }).catch(e => {
                    this.$message({
                        message: 'Data gagal dihapus. ' + e.response.data.message,
                        type: 'error',
                        showClose: true
                    });
                })
            }).catch(e => console.log(e));
        },
        store() {
            this.loading = true;
            axios.post('/konversiBerat', this.formModel).then(r => {
                this.showForm = false;
                this.$message({
                    message: 'Data berhasil disimpan.',
                    type: 'success'
                });
                this.requestData();
            }).catch(e => {
                if (e.response.status == 422) {
                    this.error = {}
                    this.formErrors = e.response.data.errors;
                }

                if (e.response.status == 500) {
                    this.formErrors = {}
                    this.error = e.response.data;
                }
            }).finally(() => {
                this.loading = false
            })
        },
        update() {
            this.loading = true;
            axios.put('/konversiBerat/' + this.formModel.id, this.formModel).then(r => {
                this.showForm = false
                this.$message({
                    message: 'Data berhasil disimpan.',
                    type: 'success'
                });
                this.requestData()
            }).catch(e => {
                if (e.response.status == 422) {
                    this.error = {}
                    this.formErrors = e.response.data.errors;
                }

                if (e.response.status == 500) {
                    this.formErrors = {}
                    this.error = e.response.data;
                }
            }).finally(() => {
                this.loading = false
            })
        },
        save(data) {
            // console.log(data)
            // return;
            let progressCount = 0;
            let progress = setInterval(() => {
                if (progressCount == 90) {
                    this.logs.push('<br>')
                    progressCount = 0
                } else {
                    this.logs.push('#')
                    progressCount++
                }

                var elem = document.getElementById('logs');
                elem.scrollTop = elem.scrollHeight;
            }, 1000)

            axios.post('/konversiBerat', { rows: data }).then(r => {
                this.logs.push('<br>')
                this.logs.push(r.data);
                this.requestData()
            }).catch(e => {
                this.logs.push('<br>')
                if (e.response.data) {
                    this.logs.push(e.response.data.message)
                }
                console.log(e)
            }).finally(() => {
                $('#file-upload').val('');
                var elem = document.getElementById('logs');
                elem.scrollTop = elem.scrollHeight;
                clearInterval(progress)
            })
        },
        readFile(oEvent) {
            this.logs.push('File selected. Reading file. Please wait..<br>')

            var _this = this
            var oFile = oEvent.target.files[0];
            var sFilename = oFile.name;

            var reader = new FileReader();
            var result = {};

            reader.onload = function (e) {
                let data = new Uint8Array(e.target.result);
                let workbook = XLSX.read(data, {type: 'array'});
                let res = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]], {header: 1});

                _this.logs.push('Reading file completed. Found ' + (res.length - 1) + ' rows. <br>')

                let dataToImport = res
                    .filter(r => !!r[1]) // ambil yg finished good tidak kosong
                    .map(r => {
                        return {
                            kategori_jual: r[0],
                            finished_good: r[1],
                            material_id: r[2],
                            material_description: r[3],
                            berat: r[4] ? r[4] : 0,
                            keterangan: r[5]
                        }
                    });

                _this.logs.push('Filtering data...<br>')
                dataToImport.splice(0, 1)
                let uniqueMaterial = [... new Set(dataToImport.map(d => d.material_id))]
                let finalData = []
                const map = new Map();

                for (const item of dataToImport) {
                    if(!map.has(item.material_id)){
                        map.set(item.material_id, true);    // set any value to Map
                        finalData.push(item);
                    }
                }

                _this.logs.push('Found ' + uniqueMaterial.length + ' unique Material ID. <br>');
                _this.logs.push('Importing data. This may take long moment. Don\'t close this window. Please wait ... <br>')
                _this.save(finalData);
            };

            reader.readAsArrayBuffer(oFile);
        },
        sortChange: function(column) {
            if (this.sort !== column.prop || this.order !== column.order) {
                this.sort = column.prop;
                this.order = column.order;
                this.requestData();
            }
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

            axios.get('/konversiBerat', { params: params }).then(r => {
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
    mounted() {
        this.requestData();
        this.$store.commit('getKategoriBarangList')
        let _this = this
        $('body').on('change', '#file-upload', function(ev) {
            _this.readFile(ev);
        });
    }
}
</script>

<style lang="css" scoped>
.logs-container {
    margin-top: 20px;
    padding: 10px;color:#fff;
    background:#333;
    max-height:300px;
    overflow:auto;
}
</style>
