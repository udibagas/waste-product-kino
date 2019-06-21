<template>
    <table class="table table-sm table-bordered" style="width: 800px">
        <thead>
            <tr>
                <th>#</th>
                <th class="text-center">Material</th>
                <th class="text-center">Material Description</th>
                <th class="text-center">Berat (kg)</th>
                <th class="text-center">Price Per Unit (Rp)</th>
                <th class="text-center">Value (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(item, index) in data.items_wp" :key="index">
                <td>{{index+1}}.</td>
                <td>{{item.material_id}}</td>
                <td>{{item.material_description}}</td>
                <td class="text-right">{{item.berat | formatNumber}} kg</td>
                <td class="text-right">Rp {{item.price_per_unit | formatNumber}}</td>
                <td class="text-right">Rp {{item.value | formatNumber}}</td>
            </tr>
        </tbody>
    </table>
</template>

<script>
export default {
    props: ['data']
}
</script>

<style scoped>
</style>
