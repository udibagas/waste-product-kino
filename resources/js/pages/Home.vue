<template>
    <el-card>
        <h3 style="margin-top:20vh">Welcome {{user.name}}!</h3>

        <h1>Waste Product Management System</h1>
    </el-card>
</template>

<script>
export default {
   data() {
       return {
           user: USER
       }
   }
}
</script>

<style lang="scss" scoped>
.el-card {
    height: calc(100vh - 100px)
}

h1, h3 {
    text-align: center;
}
</style>
