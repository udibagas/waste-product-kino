<template>
    <el-dialog
    v-loading="loading"
    title="MY PROFILE"
    :visible.sync="visible"
    :close-on-click-modal="false"
    :close-on-press-escape="false"
    width="800px"
    :show-close="false" center>
        <el-form label-width="150px">
            <el-row :gutter="15">
                <el-col :span="12">
                    <el-form-item label="Name" :class="formErrors.name ? 'is-error' : ''">
                        <el-input placeholder="Username" v-model="formModel.name"></el-input>
                        <div class="el-form-item__error" v-if="formErrors.name">{{formErrors.name[0]}}</div>
                    </el-form-item>

                    <el-form-item label="Email" :class="formErrors.email ? 'is-error' : ''">
                        <el-input placeholder="Email" v-model="formModel.email"></el-input>
                        <div class="el-form-item__error" v-if="formErrors.email">{{formErrors.email[0]}}</div>
                    </el-form-item>

                    <el-form-item label="No. Karyawan" :class="formErrors.no_karyawan ? 'is-error' : ''">
                        <el-input placeholder="No. Karyawan" v-model="formModel.no_karyawan"></el-input>
                        <div class="el-form-item__error" v-if="formErrors.no_karyawan">{{formErrors.no_karyawan[0]}}</div>
                    </el-form-item>

                    <el-form-item label="Password" :class="formErrors.password ? 'is-error' : ''">
                        <el-input type="password" placeholder="Password" v-model="formModel.password"></el-input>
                        <div class="el-form-item__error" v-if="formErrors.password">{{formErrors.password[0]}}</div>
                    </el-form-item>

                    <el-form-item label="Konfirmasi Password">
                        <el-input type="password" placeholder="Konfirmasi Password" v-model="formModel.password_confirmation"></el-input>
                    </el-form-item>

                </el-col>
                <el-col :span="12">
                    <el-form-item label="Role">
                        <el-input disabled :value="formModel.role == 1 ? 'Admin' : 'User'"></el-input>
                    </el-form-item>

                    <el-form-item label="Plant" v-if="formModel.location">
                        <el-input disabled placeholder="Plant" v-model="formModel.location.plant"></el-input>
                    </el-form-item>

                    <el-form-item label="Lokasi" v-if="formModel.location">
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
        </el-form>

        <span slot="footer">
            <el-button type="primary" @click="save" icon="el-icon-success">SAVE</el-button>
            <el-button type="info" @click="$emit('close-profile-dialog')" icon="el-icon-error">CLOSE</el-button>
        </span>
    </el-dialog>
</template>

<script>
export default {
    props: ['visible'],
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
                axios.put('/user/' + this.formModel.id, this.formModel).then(r => {
                    this.$message({
                        message: 'Data berhasil diupdate',
                        type: 'success',
                        showClose: true
                    })
                    this.$emit('close-profile-dialog')
                    this.getData()
                }).catch(e => {
                    this.$message({
                        message: 'Data gagal diupdate. ' + e.response.data.message,
                        type: 'error',
                        showClose: true
                    })
                }).finally(() => {
                    this.loading = false
                })
            }).catch(e => console.log(e))
        },
        getData() {
            this.loading = true
            axios.get('/user/' + USER.id).then(r => {
                this.formModel = r.data
            }).catch(e => {
                this.$message({
                    message: 'Gagal mengambil data. ' + e.response.data.message,
                    type: 'error',
                    showClose: true
                })
            }).finally(() => {
                this.loading = false
            })
        }
    },
    mounted() {
        this.getData()
    }
}
</script>

<style scoped>

</style>
