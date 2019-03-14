<template>
    <el-card>
        <h3>Welcome {{user.name}}!</h3>
        
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

<style scoped>
.el-card {
    padding: 200px 0;
}

h1, h3 {
    text-align: center;
}
</style>