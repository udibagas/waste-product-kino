<template>
  <el-card>
    <h4>STOCK WASTE PRODUCT</h4>
    <hr />

    <el-form :inline="true" class="form-right" @submit.native.prevent="() => { return }">
      <el-form-item>
        <el-button @click="openForm" type="primary" icon="el-icon-upload">UPLOAD DATA MB51</el-button>
      </el-form-item>
      <el-form-item style="margin-right:0;">
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
      height="calc(100vh - 330px)"
      :default-sort="{prop: 'plant', order: 'ascending'}"
      v-loading="loading"
      style="border-top:1px solid #eee;"
      @filter-change="filterChange"
      @sort-change="sortChange"
    >
      <el-table-column type="index" width="50" :index="paginatedData.from"></el-table-column>

      <el-table-column
        prop="plant"
        label="Plant"
        min-width="90"
        column-key="plant"
        :filters="$store.state.locationList.map(l => { return {value: l.plant, text: l.plant + ' - ' + l.name } })"
        sortable="custom"
      ></el-table-column>

      <el-table-column
        prop="sloc"
        label="Sloc"
        min-width="90"
        column-key="sloc"
        :filters="$store.state.slocList.map(l => { return {value: l, text: l } })"
        sortable="custom"
      ></el-table-column>

      <el-table-column
        prop="mvt"
        label="Mvt"
        min-width="90"
        column-key="mvt"
        :filters="$store.state.mvtList.map(l => { return {value: l, text: l } })"
        sortable="custom"
      ></el-table-column>

      <!-- <el-table-column prop="posting_date" label="Posting Date" width="120" sortable="custom"></el-table-column> -->
      <!-- <el-table-column prop="mat_doc" label="Mat. Doc" width="100" sortable="custom"></el-table-column> -->

      <el-table-column
        prop="kategori"
        label="Kategori"
        min-width="120"
        column-key="kategori"
        :filters="$store.state.kategoriBarangList.filter(k => k.jenis == 'WP').map(l => { return {value: l.nama, text: l.nama } })"
        sortable="custom"
      ></el-table-column>

      <el-table-column prop="material" label="Material" width="170" sortable="custom"></el-table-column>
      <el-table-column
        prop="material_description"
        show-overflow-tooltip
        min-width="250"
        label="Material Description"
        sortable="custom"
      ></el-table-column>
      <!-- <el-table-column
            prop="mat"
            label="Mat"
            min-width="90"
            column-key="mat"
            :filters="$store.state.matList.map(l => { return {value: l, text: l } })"
            sortable="custom">
      </el-table-column>-->

      <!-- <el-table-column prop="doc_date" label="Doc. Date" width="110" sortable="custom"></el-table-column> -->
      <!-- <el-table-column prop="entry_date" label="Entry Date" width="110" sortable="custom"></el-table-column> -->
      <!-- <el-table-column prop="time" label="Time" width="75" sortable="custom"></el-table-column> -->
      <!-- <el-table-column prop="bun" label="Bun" width="70" sortable="custom"></el-table-column> -->
      <!-- <el-table-column prop="quantity" label="Qty" width="70" sortable="custom" align="right" header-align="right">
                <template slot-scope="scope">
                    {{ scope.row.quantity | formatNumber }}
                </template>
      </el-table-column>-->
      <el-table-column
        prop="stock"
        label="Stock (kg)"
        width="120"
        sortable="custom"
        align="right"
        header-align="right"
      >
        <template slot-scope="scope">{{ (scope.row.stock/1000).toFixed(4) | formatNumber }}</template>
      </el-table-column>

      <el-table-column
        align="right"
        header-align="right"
        prop="price_per_unit"
        label="Price Per Unit"
        width="120"
      >
        <template slot-scope="scope">Rp {{ scope.row.price_per_unit | formatNumber }}</template>
      </el-table-column>

      <el-table-column align="right" header-align="right" label="Value" width="120">
        <template
          slot-scope="scope"
        >Rp {{ (scope.row.stock / 1000 * scope.row.price_per_unit).toFixed(0) | formatNumber }}</template>
      </el-table-column>
    </el-table>

    <br />

    <el-pagination
      @current-change="(p) => { page = p; requestData(); }"
      @size-change="(s) => { pageSize = s; requestData(); }"
      :page-size="pageSize"
      background
      layout="prev, pager, next, sizes, total"
      :sizes="[10, 25, 50, 100]"
      :total="paginatedData.total"
    ></el-pagination>

    <el-dialog
      :visible.sync="uploadFormDialog"
      title="Upload MB51"
      width="700px"
      v-loading="loading"
      :close-on-click-modal="false"
    >
      Select File :
      <input type="file" id="file-upload" />
      <div id="logs" class="logs-container">
        <span v-for="(log, i) in logs" :key="i" v-html="log"></span>
      </div>
    </el-dialog>
  </el-card>
</template>

<script>
import XLSX from "xlsx";

export default {
  data: function() {
    return {
      loading: false,
      keyword: "",
      page: 1,
      pageSize: 10,
      sort: "plant",
      order: "ascending",
      paginatedData: {},
      uploadFormDialog: false,
      logs: ["Please select file...<br>"],
      plants: [],
      filters: {}
    };
  },
  methods: {
    getPlant() {
      axios
        .get("/location/getList")
        .then(r => {
          this.plants = r.data.map(d => {
            return parseInt(d.plant);
          });
        })
        .then(e => console.log(e));
    },
    excelTimeToStr(v) {
      let num = parseFloat(v) * 24;
      return (
        ("0" + (Math.floor(num) % 24)).slice(-2) +
        ":" +
        ((num % 1) * 60 + "0").slice(0, 2)
      );
    },
    openForm() {
      $("#file-upload").val("");
      this.logs = ["Please select file...<br>"];
      this.uploadFormDialog = true;
    },
    save(data) {
      let progressCount = 0;
      let progress = setInterval(() => {
        if (progressCount == 90) {
          this.logs.push("<br>");
          progressCount = 0;
        } else {
          this.logs.push("#");
          progressCount++;
        }

        var elem = document.getElementById("logs");
        elem.scrollTop = elem.scrollHeight;
      }, 1000);

      axios
        .post("/stockWp", { rows: data })
        .then(r => {
          this.logs.push("<br>");
          this.logs.push(r.data);
          this.requestData();
          this.$store.commit("getSlocList");
          this.$store.commit("getMvtList");
          this.$store.commit("getMatList");
        })
        .catch(e => {
          this.logs.push("<br>");
          if (e.response.data) {
            this.logs.push(e.response.data.message);
          }
          console.log(e);
        })
        .finally(() => {
          $("#file-upload").val("");
          var elem = document.getElementById("logs");
          elem.scrollTop = elem.scrollHeight;
          clearInterval(progress);
        });
    },
    readFile(oEvent) {
      this.logs.push("File selected. Reading file. Please wait..<br>");

      var _this = this;
      var oFile = oEvent.target.files[0];
      var sFilename = oFile.name;

      var reader = new FileReader();
      var result = {};

      reader.onload = function(e) {
        var data = e.target.result;
        data = new Uint8Array(data);
        var workbook = XLSX.read(data, { type: "array" });
        // console.log(workbook);
        var result = {};
        // ini kalau mau baca semua sheet
        // workbook.SheetNames.forEach(function (sheetName) {
        //     var roa = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName], {header: 1});
        //     if (roa.length) result[sheetName] = roa;
        // });
        // see the result, caution: it works after reader event is done.
        var res = XLSX.utils
          .sheet_to_json(workbook.Sheets[workbook.SheetNames[0]], { header: 1 })
          .filter(r => !!r[0]); // cuma yg ada datanya

        // remove header
        res.splice(0, 1);

        _this.logs.push(
          "Reading file completed. Found " + res.length + " rows <br>"
        );

        var dataToImport = res
          .map(r => {
            return {
              plant: r[0],
              sloc: r[1],
              mvt: r[3],
              posting_date: r[4],
              mat_doc: r[5],
              material: r[6],
              mat: r[7],
              material_description: r[8],
              batch: r[9],
              text: r[13],
              doc_date: r[14],
              entry_date: r[15],
              time: _this.excelTimeToStr(r[16]),
              bun: r[17],
              quantity: Number(r[18])
            };
          })
          .filter(d => _this.plants.indexOf(d.plant) >= 0); // remove non existence plant

        console.log("raw data: ", dataToImport.length);
        // MVT 311 dan 312 digunakan untuk reverse (menetralkan stock). Jadi Mvt 312 berfungsi membatalkan transaksi Mvt 311
        // looping buat cari 311 (yang dibatalkan)
        dataToImport
          .filter(d => d.mvt == 312)
          .forEach(d312 => {
            // cari data dengan mvt 311, material & quantity sama
            let mvt_311 = dataToImport.findIndex(
              d311 =>
                d311.mvt == 311 &&
                d311.material == d312.material &&
                d311.quantity == -d312.quantity
            );
            // console.log({id : mvt_311})
            if (mvt_311 > -1) {
              // keluarkan dari dataToImport
              dataToImport.splice(mvt_311, 1);
            }
          });

        console.log("hapus 311: ", dataToImport.length);

        // hapus mvt 312 (pembatal mvt 311)
        dataToImport = dataToImport.filter(
          d => d.mvt != 312 && (d.mat == "PM" || d.mat == "FG" || d.mat == "SF")
        );

        console.log("hapus 312: ", dataToImport.length);

        if (dataToImport.length == 0) {
          _this.logs.push(
            "No valid row found.Please make sure your data is correct."
          );
          return;
        }

        _this.logs.push("Found " + dataToImport.length + " valid rows <br>");
        _this.logs.push(
          "Importing data. This may take long moment. Don't close this window. Please wait ... <br>"
        );
        console.log("data to import: ", dataToImport.length);
        _this.save(dataToImport);
      };

      reader.readAsArrayBuffer(oFile);
    },
    filterChange(f) {
      let column = Object.keys(f)[0];
      this.filters[column] = Object.values(f[column]);
      this.page = 1;
      this.requestData();
    },
    sortChange(column) {
      if (this.sort !== column.prop || this.order !== column.order) {
        this.sort = column.prop;
        this.order = column.order;
        this.requestData();
      }
    },
    requestData() {
      let params = {
        page: this.page,
        keyword: this.keyword,
        pageSize: this.pageSize,
        sort: this.sort,
        order: this.order
      };
      this.loading = true;

      axios
        .get("/stockWp", { params: Object.assign(params, this.filters) })
        .then(r => {
          this.paginatedData = r.data;
        })
        .catch(e => {
          this.$message({
            message: e.response.data.message || e.response.message,
            type: "error"
          });
        })
        .finally(() => {
          this.loading = false;
        });
    }
  },
  mounted() {
    this.requestData();
    this.getPlant();
    this.$store.commit("getSlocList");
    this.$store.commit("getMvtList");
    this.$store.commit("getMatList");
    this.$store.commit("getLocationList");
    this.$store.commit("getKategoriBarangList");
    let _this = this;
    $("body").on("change", "#file-upload", function(ev) {
      _this.readFile(ev);
    });
  }
};
</script>

<style lang="css" scoped>
.logs-container {
  margin-top: 20px;
  padding: 10px;
  color: #fff;
  background: #333;
  max-height: 300px;
  overflow: auto;
}
</style>
