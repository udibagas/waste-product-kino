<template>
    <el-card>
        <h4>REPORT SUMMARY MUTASI BB</h4>
        <hr>

        <el-form :inline="true" class="form-right">
            <el-form-item>
                <el-select v-model="lokasi" style="width:100%" placeholder="Lokasi">
                    <el-option
                    v-for="item in $store.state.locationList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id">
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item style="margin-right:0;">
                <el-date-picker
                v-model="dateRange"
                value-format="yyyy-MM-dd"
                type="daterange"
                range-separator="-"
                start-placeholder="Dari"
                end-placeholder="Sampai">
                </el-date-picker>
            </el-form-item>
        </el-form>

        <el-table :data="report" stripe v-loading="loading" style="border-top:1px solid #eee;">
            <el-table-column type="index" width="50"> </el-table-column>
            <el-table-column prop="kategori" label="Kategori Barang"></el-table-column>
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
            <el-table-column width="110" label="Selisih" align="center" header-align="center">
                <template slot-scope="scope">
                    {{ scope.row.stock_in - scope.row.stock_out | formatNumber }}
                </template>
            </el-table-column>
        </el-table>
    </el-card>
</template>

<script>
import moment from 'moment'

export default {
    watch: {
        dateRange(v, o) {
            this.requestData()
        },
        lokasi(v, o) {
            this.requestData()
        }
    },
    data: function() {
        return {
            loading: false,
            report: {},
            lokasi: '',
            dateRange: [
                moment().format('YYYY-MM-01'),
                moment().format('YYYY-MM-DD'),
            ]
        }
    },
    methods: {
        requestData: function() {
            let params = {
                start: this.dateRange[0],
                end: this.dateRange[1],
                lokasi: this.lokasi
            }

            this.loading = true;

            axios.get(BASE_URL + '/report/bb', {params: params})
                .then(r => {
                    this.loading = false;
                    this.report = r.data
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
        this.$store.commit('getLocationList')
    }
}
</script>

<style lang="css" scoped>

</style>