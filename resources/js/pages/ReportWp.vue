<template>
    <el-card>
        <h4>REPORT WASTE PRODUCT</h4>
        <hr>

        <el-tabs type="card">
            <el-tab-pane label="SUMMARY MUTASI" lazy>
                <el-form :inline="true" class="form-right">
                    <el-form-item>
                        <el-select @change="requestData" v-model="plant" style="width:100%" placeholder="Lokasi">
                            <el-option
                            v-for="item in $store.state.locationList"
                            :key="item.id"
                            :label="item.plant + ' - ' + item.name"
                            :value="item.plant">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-date-picker
                        @change="requestData"
                        v-model="dateRange"
                        value-format="yyyy-MM-dd"
                        format="dd-MMM-yyyy"
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

                <el-table :data="report" stripe v-loading="loading" style="border-top:1px solid #eee;" height="calc(100vh - 330px)">
                    <el-table-column type="index" width="50"> </el-table-column>
                    <el-table-column type="expand" width="40">
                        <template slot-scope="scope">
                            <InOutStockWpDetail :plant="plant" :date_range="dateRange" :material="scope.row.material" />
                        </template>
                    </el-table-column>
                    <el-table-column prop="material" label="Material ID" min-width="150px" show-overflow-tooltip> </el-table-column>
                    <el-table-column prop="material_description" label="Material Description" min-width="150px" show-overflow-tooltip> </el-table-column>
                    <el-table-column label="Qty (pcs)" header-align="center">
                        <el-table-column prop="qty_in" min-width="100" label="IN" align="center" header-align="center">
                            <template slot-scope="scope">
                                {{ scope.row.qty_in | formatNumber }}
                            </template>
                        </el-table-column>
                        <el-table-column prop="qty_out" min-width="100" label="OUT" align="center" header-align="center">
                            <template slot-scope="scope">
                                {{ scope.row.qty_out | formatNumber }}
                            </template>
                        </el-table-column>
                        <el-table-column min-width="100" label="Selisih" align="center" header-align="center">
                            <template slot-scope="scope">
                                {{ (scope.row.qty_in - scope.row.qty_out) | formatNumber }}
                            </template>
                        </el-table-column>
                    </el-table-column>

                    <el-table-column label="Berat (kg)" header-align="center">
                        <el-table-column prop="stock_in" min-width="100" label="IN" align="center" header-align="center">
                            <template slot-scope="scope">
                                {{ scope.row.stock_in | formatNumber }}
                            </template>
                        </el-table-column>
                        <el-table-column prop="stock_out" min-width="100" label="OUT" align="center" header-align="center">
                            <template slot-scope="scope">
                                {{ scope.row.stock_out | formatNumber }}
                            </template>
                        </el-table-column>
                        <el-table-column min-width="100" label="Selisih" align="center" header-align="center">
                            <template slot-scope="scope">
                                {{ (scope.row.stock_in - scope.row.stock_out).toFixed(4) | formatNumber }}
                            </template>
                        </el-table-column>
                    </el-table-column>
                </el-table>
            </el-tab-pane>
            <el-tab-pane label="IN OUT STOCK" lazy><InOutStockWp /></el-tab-pane>
        </el-tabs>
    </el-card>
</template>

<script>
import moment from 'moment'
import exportFromJSON from 'export-from-json'
import InOutStockWp from './InOutStockWp'
import InOutStockWpDetail from '../components/InOutStockWpDetail'

export default {
    components: { InOutStockWp, InOutStockWpDetail },
    data() {
        return {
            loading: false,
            report: [],
            plant: null,
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
                    "Material ID ": r.material,
                    "Material Description ": r.material_description,
                    "Qty In": r.qty_in,
                    "Qty Out": r.qty_out,
                    "Qty Selisih": r.qty_in - r.qty_out,
                    "Berat In": r.stock_in,
                    "Berat Out": r.stock_out,
                    "Berat Selisih": r.stock_in - r.stock_out,
                }
            })

            exportFromJSON({ data: data, fileName: 'report-summary-mutasi-wp', exportType: 'xls' })
        },
        requestData() {
            let params = {
                start: this.dateRange[0],
                end: this.dateRange[1],
                plant: this.plant
            }

            this.loading = true;

            axios.get('/report/wp', {params: params}).then(r => {
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
    }
}
</script>
