<template>
    <el-card v-loading="loading">
        <h4>MY PROFILE</h4>
        <hr>
        <el-form label-width="170px">
            <el-row :gutter="15">
                <el-col :span="12">
                    <el-form-item label="Name">
                        <el-input placeholder="Username" v-model="formModel.name"></el-input>
                        <div class="el-form-item__error" v-if="formErrors.name">{{formErrors.name[0]}}</div>
                    </el-form-item>

                    <el-form-item label="Email">
                        <el-input placeholder="Email" v-model="formModel.email"></el-input>
                        <div class="el-form-item__error" v-if="formErrors.email">{{formErrors.email[0]}}</div>
                    </el-form-item>

                    <el-form-item label="No. Karyawan">
                        <el-input placeholder="No. Karyawan" v-model="formModel.no_karyawan"></el-input>
                        <div class="el-form-item__error" v-if="formErrors.no_karyawan">{{formErrors.no_karyawan[0]}}</div>
                    </el-form-item>

                    <el-form-item label="Password">
                        <el-input type="password" placeholder="Password" v-model="formModel.password"></el-input>
                        <div class="el-form-item__error" v-if="formErrors.password">{{formErrors.password[0]}}</div>
                    </el-form-item>
                    
                    <el-form-item label="Konfirmasi Password">
                        <el-input type="password" placeholder="Konfirmasi Password" v-model="formModel.password_confirmation"></el-input>
                    </el-form-item>

                </el-col>
                <el-col :span="12">
                    <el-form-item label="Role">
                        <el-input disabled v-model="formModel.role"></el-input>
                    </el-form-item>
                    
                    <el-form-item label="Plant">
                        <el-input disabled placeholder="Plant" v-model="formModel.location.plant"></el-input>
                    </el-form-item>

                    <el-form-item label="Lokasi">
                        <el-input disabled placeholder="Lokasi" v-model="formModel.location.name"></el-input>
                    </el-form-item>

                    <el-form-item label="Departemen">
                        <el-input disabled placeholder="Departemen" v-model="formModel.department"></el-input>
                    </el-form-item>

                    <el-form-item label="Status">
                        <el-tag :type="formModel.status ? 'success' : 'danger'">
                            {{formModel.status ? 'Active' : 'Nonactive'}}
                        </el-tag>
                    </el-form-item>
                </el-col>
            </el-row>
            <hr>
            <el-form-item>
                <el-button type="primary" @click="save" icon="el-icon-success">SAVE</el-button>
            </el-form-item>
        </el-form>
    </el-card>
</template>

<script>
export default {
    data() {
        return {
            formModel: USER,
            loading: false,
            formErrors: {},
        }
    },
    methods: {
        save() {
            this.$confirm('Anda yakin?', 'Warning', {
                type: 'warning',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then(() => {
                this.loading = true
                axios.put(BASE_URL + '/user/' + this.formModel.id, this.formModel).then(r => {
                    this.loading = false
                    this.$message({
                        message: 'Data berhasil diupdate',
                        type: 'success',
                        showClose: true
                    })
                    this.getData()
                }).catch(e => {
                    this.loading = false
                    this.$message({
                        message: 'Data gagal diupdate. ' + e.response.data.message,
                        type: 'error',
                        showClose: true
                    })
                })
            }).catch(e => console.log(e))
        },
        getData() {
            this.loading = true
            axios.get(BASE_URL + '/user/' + USER.id).then(r => {
                this.loading = false
                this.formModel = r.data
            }).catch(e => {
                this.loading = false
                this.$message({
                    message: 'Gagal mengambil data. ' + e.response.data.message,
                    type: 'error',
                    showClose: true
                })
            })
        }
    },
    created() {
        this.getData()
    }
}
</script>

<style scoped>

</style>