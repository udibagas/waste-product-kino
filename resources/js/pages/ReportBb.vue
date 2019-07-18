<template>
    <el-card>
        <h4>REPORT SUMMARY MUTASI BB</h4>
        <hr>

        <el-form :inline="true" class="form-right">
            <el-form-item>
                <el-select @change="requestData" v-model="location_id" style="width:100%" placeholder="Lokasi">
                    <el-option
                    v-for="item in $store.state.locationList"
                    :key="item.id"
                    :label="item.plant + ' - ' + item.name"
                    :value="item.id">
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-date-picker
                @change="requestData"
                v-model="dateRange"
                value-format="yyyy-MM-dd"
                type="daterange"
                range-separator="-"
                start-placeholder="Dari"
                end-placeholder="Sampai">
                </el-date-picker>
            </el-form-item>
            <el-form-item style="margin-right:0;">
                <el-button @click="exportToExcel" type="primary"><i class="el-icon-download"></i> EXPORT KE EXCEL</el-button>
            </el-form-item>
        </el-form>

        <el-table :data="report" stripe v-loading="loading" style="border-top:1px solid #eee;" height="calc(100vh - 275px)">
            <el-table-column type="index" width="50"> </el-table-column>
            <el-table-column label="Kategori Barang">
                <template slot-scope="scope">
                    {{ getKategori(scope.row.kategori_id) }}
                </template>
            </el-table-column>
            <el-table-column prop="stock_in" width="110" label="Stock In" align="center" header-align="center">
                <template slot-scope="scope">
                    {{ scope.row.stock_in | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="stock_out" width="110" label="Stock Out" align="center" header-align="center">
                <template slot-scope="scope">
                    {{ scope.row.stock_out | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column width="100" label="Unit" align="center" header-align="center">
                <template slot-scope="scope">
                    {{ getUnit(scope.row.kategori_id) }}
                </template>
            </el-table-column>
            <el-table-column width="110" label="Selisih" align="center" header-align="center">
                <template slot-scope="scope">
                    {{ (scope.row.stock_in - scope.row.stock_out).toFixed(4) | formatNumber }}
                </template>
            </el-table-column>
        </el-table>
    </el-card>
</template>

<script>
import moment from 'moment'
import exportFromJSON from 'export-from-json'

export default {
    data() {
        return {
            loading: false,
            report: [],
            location_id: null,
            dateRange: [
                moment().format('YYYY-MM-01'),
                moment().format('YYYY-MM-DD'),
            ]
        }
    },
    methods: {
        exportToExcel() {
            let data = this.report.map(r => {
                return {
                    "Kategori Barang": this.getKategori(r.kategori_id),
                    "Stock In": r.stock_in,
                    "Stock Out": r.stock_out,
                    "Unit": this.getKategori(r.kategori_id),
                    "Selisih": r.stock_in - r.stock_out
                }
            })

            exportFromJSON({ data: data, fileName: 'report-summary-mutasi-bb', exportType: 'xls' })
        },
        getKategori(id) {
            let kategori = this.$store.state.kategoriBarangList.find(k => k.id == id)
            if (!kategori) {
                return 'Unregistered Kategori : ' + id
            }

            return kategori.jenis + ' : ' + kategori.kode + ' - ' + kategori.nama
        },
        getUnit(id) {
            let kategori = this.$store.state.kategoriBarangList.find(k => k.id == id)
            if (!kategori) {
                return '-'
            }

            return kategori.unit
        },
        requestData() {
            let params = {
                start: this.dateRange[0],
                end: this.dateRange[1],
                location_id: this.location_id
            }

            this.loading = true;

            axios.get('/report/bb', {params: params}).then(r => {
                    this.report = r.data
            }).catch(e => {
                this.$message({
                    message: e.response.data.message || e.response.message,
                    type: 'error',
                    showClose: true
                });
            }).finally(() => {
                this.loading = false
            })
        }
    },
    mounted() {
        this.requestData();
        this.$store.commit('getLocationList')
        this.$store.commit('getKategoriBarangList')
    }
}
</script>

<style lang="css" scoped>

</style>
