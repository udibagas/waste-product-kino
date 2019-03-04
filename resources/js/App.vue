<template>
    <el-container>
        <el-header>
            <el-row>
                <el-col :span="12">
                    <div class="brand"> <i class="el-icon-tickets"></i> Business Contract and Asset Management System </div>
                </el-col>
                <el-col :span="12" class="text-right">
                    <span style="margin-right:15px;">Welcome, {{user.name}}!</span>
                    <el-dropdown>
                        <i class="el-icon-more text-white"></i>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item>My Profile</el-dropdown-item>
                            <el-dropdown-item @click="logout">Logout</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </el-col>
            </el-row>
        </el-header>
        <el-container>
            <el-aside width="auto" style="text-align:center;background-color: #324057;">
                <el-button icon="el-icon-document" circle></el-button><br><br>
                <el-button icon="el-icon-menu" circle></el-button>
            </el-aside>
            <el-aside width="auto">Aside</el-aside>
            <el-main> <router-view></router-view> </el-main>
        </el-container>
    </el-container>
</template>

<script>
export default {
    data() {
        return {
            user: USER
        }
    },
    methods: {
        logout() {
            document.getElementById('logout-form').submit();
        }
    }
}
</script>

<style lang="scss" scoped>
.brand {
    font-size: 24px;
    font-weight: bolder;
}

.el-header {
    background-color: #324057;
    color: #fff;
    line-height: 60px;
}

.el-aside {
    // background-color: #D3DCE6;
    background-color: #FFF;
    color: #333;
    height: calc(100vh - 60px);
    padding: 20px 10px 10px;
}

.el-main {
    background-color: #E5E9F2;
    color: #333;
    height: calc(100vh - 60px);
}

body > .el-container {
    margin-bottom: 40px;
}

.el-icon-more {
    transform: rotate(90deg);
    -webkit-transform: rotate(90deg);
    -moz-transform: rotate(90deg);
    -ms-transform: rotate(90deg);
    -o-transform: rotate(90deg);
}
</style>