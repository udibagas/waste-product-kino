<template>
  <div>
    <el-form :inline="true" class="form-right" @submit.native.prevent="() => { return }">
      <el-form-item>
        <el-date-picker
          @change="requestData"
          v-model="dateRange"
          value-format="yyyy-MM-dd"
          type="daterange"
          range-separator="-"
          start-placeholder="Dari"
          end-placeholder="Sampai"
        ></el-date-picker>
      </el-form-item>
      <el-form-item style="margin-center:0;">
        <el-input
          placeholder="Search"
          prefix-icon="el-icon-search"
          v-model="keyword"
          @change="requestData"
          clearable
        >
          <el-button
            @click="() => { page = 1; keyword = ''; requestData(); }"
            slot="append"
            icon="el-icon-refresh"
          ></el-button>
        </el-input>
      </el-form-item>
    </el-form>

    <el-table
      :data="paginatedData.data"
      stripe
      :default-sort="{prop: 'tanggal', order: 'descending'}"
      height="calc(100vh - 385px)"
      v-loading="loading"
      style="border-top:1px solid #eee;"
      @filter-change="filterChange"
      @sort-change="sortChange"
    >
      <el-table-column type="index" width="50" :index="paginatedData.from"></el-table-column>
      <el-table-column prop="tanggal" width="100" label="Tanggal" sortable="custom">
        <template slot-scope="scope">{{ scope.row.tanggal | readableDate }}</template>
      </el-table-column>
      <el-table-column prop="no_aju" min-width="200" label="No. Pengajuan" sortable="custom"></el-table-column>
      <el-table-column prop="no_sj" min-width="200" label="No. Surat Jalan" sortable="custom"></el-table-column>

      <el-table-column
        prop="location.name"
        min-width="130"
        label="Lokasi"
        column-key="plant"
        :filters="$store.state.locationList.map(l => { return {value: l.plant, text: l.plant + ' - ' + l.name } })"
        sortable="custom"
      ></el-table-column>

      <el-table-column prop="sloc" min-width="100" label="Sloc" sortable="custom"></el-table-column>
      <el-table-column prop="mvt" min-width="150" label="MVT Type" sortable="custom"></el-table-column>
      <el-table-column prop="material" min-width="200" label="Material ID" sortable="custom"></el-table-column>
      <el-table-column
        prop="material_description"
        show-overflow-tooltip
        min-width="200"
        label="Material Description"
        sortable="custom"
      ></el-table-column>

      <el-table-column
        prop="qty_in"
        min-width="110"
        label="Qty In"
        sortable="custom"
        align="center"
        header-align="center"
      >
        <template slot-scope="scope">{{ scope.row.qty_in | formatNumber }}</template>
      </el-table-column>
      <el-table-column
        prop="qty_out"
        min-width="110"
        label="Qty Out"
        sortable="custom"
        align="center"
        header-align="center"
      >
        <template slot-scope="scope">{{ scope.row.qty_out | formatNumber }}</template>
      </el-table-column>
      <el-table-column
        prop="stock_in"
        min-width="150"
        label="Stock In (kg)"
        sortable="custom"
        align="center"
        header-align="center"
      >
        <template slot-scope="scope">{{ (scope.row.stock_in/1000).toFixed(4) | formatNumber }}</template>
      </el-table-column>
      <el-table-column
        prop="stock_out"
        min-width="150"
        label="Stock Out (kg)"
        sortable="custom"
        align="center"
        header-align="center"
      >
        <template slot-scope="scope">{{ (scope.row.stock_out/1000).toFixed(4) | formatNumber }}</template>
      </el-table-column>
    </el-table>

    <br />

    <el-pagination
      @current-change="(p) => { page = p; requestData(); }"
      @size-change="(s) => { pageSize = s; requestData(); }"
      :page-size="pageSize"
      background
      layout="prev, pager, next, sizes, total"
      :page-sizes="[10,25,50,100]"
      :total="paginatedData.total"
    ></el-pagination>
  </div>
</template>

<script>
import moment from "moment";

export default {
  data: function() {
    return {
      loading: false,
      keyword: "",
      page: 1,
      pageSize: 10,
      sort: "tanggal",
      order: "descending",
      filters: {},
      paginatedData: {},
      dateRange: [moment().format("YYYY-MM-01"), moment().format("YYYY-MM-DD")]
    };
  },
  methods: {
    sortChange: function(column) {
      if (this.sort !== column.prop || this.order !== column.order) {
        this.sort = column.prop;
        this.order = column.order;
        this.requestData();
      }
    },
    filterChange: function(f) {
      let column = Object.keys(f)[0];
      this.filters[column] = Object.values(f[column]);
      this.page = 1;
      this.requestData();
    },
    requestData: function() {
      let params = {
        page: this.page,
        keyword: this.keyword,
        pageSize: this.pageSize,
        sort: this.sort,
        order: this.order,
        range: this.dateRange
      };

      this.loading = true;
      axios
        .get("/inOutStockWp", { params: Object.assign(params, this.filters) })
        .then(r => {
          this.paginatedData = r.data;
        })
        .catch(e => {
          this.$message({
            message: e.response.data.message || e.response.message,
            type: "error",
            showClose: true
          });
        })
        .finally(() => {
          this.loading = false;
        });
    }
  },
  mounted: function() {
    this.requestData();
    this.$store.commit("getLocationList");
  }
};
</script>
