<template>
    <el-row :gutter="10">
        <el-col :span="12">
            <table class="table table-sm table-bordered table-striped" style="margin-bottom:0">
                <tbody>
                    <tr>
                        <td class="label">No. Surat Jalan</td>
                        <td>{{penjualan.no_sj}}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal</td>
                        <td>{{penjualan.tanggal | readableDate}}</td>
                    </tr>
                    <tr>
                        <td class="label">Plant</td>
                        <td>{{penjualan.location.plant}} - {{penjualan.location.name}}</td>
                    </tr>
                    <tr>
                        <td class="label">Pembeli</td>
                        <td>{{penjualan.pembeli.nama}}</td>
                    </tr>
                    <tr>
                        <td class="label">Kontak Pembeli</td>
                        <td> {{penjualan.pembeli.kontak}}</td>
                    </tr>
                    <tr>
                        <td class="label">TOP Date</td>
                        <td>{{penjualan.top_date | readableDate}}</td>
                    </tr>
                    <tr>
                        <td class="label">Value</td>
                        <td class="text-info">Rp {{penjualan.value | formatNumber}}</td>
                    </tr>
                    <tr>
                        <td class="label">Terbayar</td>
                        <td class="text-success">Rp {{penjualan.terbayar | formatNumber}}</td>
                    </tr>
                    <tr>
                        <td class="label">Outstanding</td>
                        <td class="text-danger">Rp {{penjualan.value - penjualan.terbayar | formatNumber}}</td>
                    </tr>
                </tbody>
            </table>
        </el-col>
        <el-col :span="12">
            <el-alert type="error" title="ERROR"
                :description="error.message + '\n' + error.file + ':' + error.line"
                v-show="error.message"
                style="margin-bottom:15px;">
            </el-alert>

            <el-form label-width="150px">
                <el-form-item label="Tanggal">
                    <el-date-picker v-model="formModel.tanggal" type="date" value-format="yyyy-MM-dd" placeholder="Tanggal" style="width:100%;"> </el-date-picker>
                    <div class="el-form-item__error" v-if="formErrors.tanggal">{{formErrors.tanggal[0]}}</div>
                </el-form-item>
                <el-form-item label="Pembayaran (Rp)">
                    <el-input type="number" placeholder="Pembayaran (Rp)" v-model="formModel.value"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.value">{{formErrors.value[0]}}</div>
                </el-form-item>
                <el-form-item label="Keterangan">
                    <el-input v-model="formModel.keterangan" type="textarea" rows="3" placeholder="Keterangan"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.keterangan">{{formErrors.keterangan[0]}}</div>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="save" icon="el-icon-success">SAVE</el-button>
                    <el-button type="info" @click="$emit('close-form')" icon="el-icon-error">CANCEL</el-button>
                </el-form-item>
            </el-form>
        </el-col>
    </el-row>
</template>

<script>
import moment from 'moment'

export default {
    props: ['penjualan'],
    data() {
        return {
            formModel: { tanggal: moment().format('YYYY-MM-DD') },
            formErrors: {},
            error: {}
        }
    },
    methods: {
        save() {
            if (this.formModel.value > this.penjualan.value - this.penjualan.terbayar) {
                this.$message({
                    message: 'Jumlah pembayaran melebihi outstanding',
                    type: 'error',
                    showClose: true,
                    duration: 5000
                })
                return
            }
            this.$confirm('Anda yakin?', 'Confirm').then(() => {
                this.formModel.penjualan_id = this.penjualan.id
                axios.post(BASE_URL + '/pembayaran', this.formModel).then(r => {
                    this.$emit('close-form');
                    this.$emit('reload-data');
                    this.$message({
                        message: 'Data pembayaran berhasil disimpan',
                        type: 'success',
                        showClose: true
                    })
                }).catch(e => {
                    if (e.response.status == 422) {
                        this.error = {}
                        this.formErrors = e.response.data.errors;
                    }

                    else {
                        this.formErrors = {}
                        this.error = e.response.data;
                    }
                })
            }).catch(e => console.log(e))
        }
    }
    
}
</script>

<style scoped>
td.label {
    width: 150px;
    font-weight: bold;
}
</style>