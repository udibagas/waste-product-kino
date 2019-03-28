<template>
    <el-card>
        <h4>STOCK WASTE PRODUCT</h4>
        <hr>

        <el-form :inline="true" class="form-right">
            <el-form-item>
                <el-button @click="openForm" type="primary" icon="el-icon-upload">UPLOAD DATA MB51</el-button>
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
        :default-sort = "{prop: 'plant', order: 'ascending'}"
        v-loading="loading"
        style="border-top:1px solid #eee;"
        @sort-change="sortChange">
            <el-table-column type="index" width="50" :index="paginatedData.from"> </el-table-column>
            <el-table-column prop="plant" label="Plant" sortable="custom"></el-table-column>
            <el-table-column prop="sloc" label="Sloc" sortable="custom"></el-table-column>
            <el-table-column prop="mvt" label="Mvt" sortable="custom"></el-table-column>
            <el-table-column prop="posting_date" label="Posting Date" sortable="custom"></el-table-column>
            <el-table-column prop="mat_doc" label="Mat. Doc" sortable="custom"></el-table-column>
            <el-table-column prop="material" label="Material" sortable="custom"></el-table-column>
            <el-table-column prop="material_description" label="Material Description" sortable="custom"></el-table-column>
            <el-table-column prop="doc_date" label="Doc. Date" sortable="custom"></el-table-column>
            <el-table-column prop="entry_date" label="Entry Date" sortable="custom"></el-table-column>
            <el-table-column prop="time" label="Time" sortable="custom"></el-table-column>
            <el-table-column prop="bun" label="Bun" sortable="custom"></el-table-column>
            <el-table-column prop="quantity" label="Quantity" sortable="custom"></el-table-column>
            <el-table-column prop="stock" label="Stock" sortable="custom"></el-table-column>
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
        title="Upload MB51" 
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
            sort: 'plant',
            order: 'ascending',
            paginatedData: {},
            uploadFormDialog: false,
            logs: ['Please select file...<br>']
        }
    },
    methods: {
        excelTimeToStr(v) {
            let num = parseFloat(v) * 24
            return ('0' + Math.floor(num) % 24).slice(-2) + ':' + ((num % 1)*60 + '0').slice(0, 2);
        },
        openForm() {
            $('#file-upload').val('');
            this.logs = ['Please select file...<br>']
            this.uploadFormDialog = true
        },
        save(data) {
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

            axios.post(BASE_URL + '/stockWp', { rows: data }).then(r => {
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
                var data = e.target.result;
                data = new Uint8Array(data);
                var workbook = XLSX.read(data, {type: 'array'});
                console.log(workbook);
                var result = {};
                // ini kalau mau baca semua sheet
                // workbook.SheetNames.forEach(function (sheetName) {
                //     var roa = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName], {header: 1});
                //     if (roa.length) result[sheetName] = roa;
                // });
                // see the result, caution: it works after reader event is done.
                var res = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]], {header: 1});
                _this.logs.push('Reading file completed. Found ' + (res.length - 1) + ' rows <br>')
                _this.logs.push('Importing data. This may take long moment. Don\'t close this window. Please wait ... <br>')

                var dataToImport = res.map(r => {
                    return {
                        plant: r[0],
                        sloc: r[1],
                        mvt: r[2],
                        posting_date: r[3],
                        mat_doc: r[4],
                        material: r[5],
                        material_description: r[6],
                        doc_date: r[7],
                        entry_date: r[8],
                        time: _this.excelTimeToStr(r[9]),
                        bun: r[10],
                        quantity: r[11]
                    }
                });
                
                // buang headernya
                _this.save(dataToImport.splice(1));
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

            axios.get(BASE_URL + '/stockWp', { params: params }).then(r => {
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