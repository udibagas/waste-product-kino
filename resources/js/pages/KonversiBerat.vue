<template>
    <el-card>
        <h4>KONVERSI BERAT</h4>
        <hr>

        <el-form :inline="true" class="form-right">
            <el-form-item>
                <el-button @click="openForm" type="primary" icon="el-icon-upload">UPLOAD DATA KONVERSI BERAT</el-button>
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
        :default-sort = "{prop: 'material_id', order: 'ascending'}"
        v-loading="loading"
        style="border-top:1px solid #eee;"
        @sort-change="sortChange">
            <el-table-column type="index" width="50" :index="paginatedData.from"> </el-table-column>
            <el-table-column prop="kategori_jual" label="Kategori Jual" sortable="custom"></el-table-column>
            <el-table-column prop="finished_good" label="Finished Goods" sortable="custom"></el-table-column>
            <el-table-column prop="material_id" label="Material ID" width="170" sortable="custom"></el-table-column>
            <el-table-column prop="material_description" width="350" label="Material Description" sortable="custom"></el-table-column>
            <el-table-column prop="berat" label="Berat Rata - Rata (gr)" align="center" header-align="center" sortable="custom"></el-table-column>
            <el-table-column prop="keterangan" label="Keterangan" sortable="custom"></el-table-column>
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
            keyword: '',
            page: 1,
            pageSize: 10,
            sort: 'material_id',
            order: 'ascending',
            paginatedData: {},
            uploadFormDialog: false,
            logs: ['Please select file...<br>']
        }
    },
    methods: {
        openForm() {
            $('#file-upload').val('');
            this.logs = ['Please select file...<br>']
            this.uploadFormDialog = true
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

            axios.post(BASE_URL + '/konversiBerat', { rows: data }).then(r => {
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

                let dataToImport = res.map(r => {
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
        goToPage: function(p) {
            this.page = p;
            this.requestData();
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

            axios.get(BASE_URL + '/konversiBerat', { params: params }).then(r => {
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