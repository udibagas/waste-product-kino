<template>
    <el-container>
        <el-header>
            <el-row>
                <el-col :span="12">
                    <a href="#" @click.prevent="collapse = !collapse" style="margin:0 30px 0 3px">
                        <!-- <font-awesome-icon :icon="collapse ? 'chevron-right' : 'chevron-left'"></font-awesome-icon> -->
                        <font-awesome-icon icon="bars" style="color:#fff"></font-awesome-icon>
                    </a>
                    <span class="brand">
                        <img :src="baseURL + '/images/logo.png'" style="height:30px;margin-right:15px;" />
                        Waste Product Management
                    </span>
                </el-col>
                <el-col :span="12" class="text-right">
                    <el-dropdown @command="handleCommand">
                        <span class="el-dropdown-link">Welcome, {{user.name}}!</span>
                        <!-- <i class="el-icon-more text-white"></i> -->
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item command="profile">My Profile</el-dropdown-item>
                            <el-dropdown-item command="logout">Logout</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </el-col>
            </el-row>
        </el-header>
        
        <el-container>
            <el-aside width="auto">
                <el-menu
                :collapse="collapse"
                default-active="1"
                background-color="#324057"
                text-color="#fff"
                class="sidebar"
                active-text-color="#ffd04b">
                    <el-menu-item v-if="m.children.length == 0" v-for="(m, i) in menus" :index="(++i).toString()" :key="i" @click="$router.push(m.url)">
                        <font-awesome-icon :icon="m.icon" style="margin-right:5px;"></font-awesome-icon>
                        <span slot="title">{{m.label}}</span>
                    </el-menu-item>
                    <el-submenu v-else :index="(++i).toString()">
                        <template slot="title">
                            <font-awesome-icon :icon="m.icon" style="margin-right:5px;"></font-awesome-icon>
                            <span>{{m.label}}</span>
                        </template>
                        <el-menu-item v-for="(sm, ii) in m.children" :index="(i).toString() + '-' + ++ii" :key="ii" @click="$router.push(sm.url)">
                            <font-awesome-icon :icon="sm.icon" style="margin-right:5px;"></font-awesome-icon> 
                            <span slot="title">{{sm.label}}</span>
                        </el-menu-item>
                    </el-submenu>
                </el-menu>
            </el-aside>
            <el-main>
                <el-collapse-transition>
                    <router-view></router-view>
                </el-collapse-transition>
            </el-main>
        </el-container>
    </el-container>
</template>

<script>
import { library } from '@fortawesome/fontawesome-svg-core'
import { 
    faHome, 
    faUserLock, 
    faChevronRight, 
    faChevronLeft, 
    faBoxes, 
    faDatabase, 
    faUsers, 
    faTags, 
    faMarker, 
    faCogs, 
    faRecycle, 
    faBars, 
    faProjectDiagram, 
    faBuilding,
faCity,
faSlidersH,
faFileInvoiceDollar,
faChartBar,
faShare,
faExchangeAlt,
faWarehouse,
faReply,
faMoneyBill,
faFileContract,
faBalanceScale, } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(
    faHome,
    faUserLock,
    faChevronRight,
    faChevronLeft,
    faBoxes,
    faDatabase,
    faUsers,
    faTags,
    faMarker,
    faCogs,
    faRecycle,
    faBars,
    faProjectDiagram,
    faBuilding,
    faCity,
    faSlidersH,
    faFileInvoiceDollar,
    faChartBar,
    faShare,
    faExchangeAlt,
    faWarehouse,
    faReply,
    faMoneyBill,
    faFileContract,
    faBalanceScale
)

export default {
    name: 'App',
    components: { FontAwesomeIcon },
    data() {
        return {
            collapse: true,
            user: USER,
            baseURL: BASE_URL,
            menus: []
        }
    },
    methods: {
        handleCommand(command) {
            if (command === 'logout') {
                document.getElementById('logout-form').submit();
            }
        },
        getMenu() {
            axios.get(BASE_URL + '/navigation')
                .then(r => this.menus = r.data)
                .catch(e => console.log(e))
        }

    },
    created() {
        this.getMenu()
    }
}
</script>

<style lang="scss" scoped>
.brand {
    font-size: 22px;
    // font-weight: bolder;
}

.el-header {
    background-color: #324057;
    color: #fff;
    line-height: 60px;
}

.sidebar {
    background-color: #324057;
    border-color: #324057;
    height: calc(100vh - 60px);
}

.sidebar:not(.el-menu--collapse) {
    width: 250px;
}

.el-aside {
    height: calc(100vh - 60px);
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

.el-dropdown-link {
    color: #fff;
}
</style>