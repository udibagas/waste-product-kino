@component('mail::message')
<strong>Dear {{$pengajuanPenjualan->user->name}},</strong>
<br>

<p>
    Pengajuan penjualan Anda dengan detail sebagai berikut tidak disetujui:
</p>

@component('mail::panel')
<table style="width:100%">
    <tbody>
        <tr>
            <td><b>No. Pengajuan</b></td>
            <td>: {{$pengajuanPenjualan->no_aju}}</td>
        </tr>
        <tr>
            <td><b>Tanggal</b></td>
            <td>: {{$pengajuanPenjualan->tanggal}}</td>
        </tr>
        <tr>
            <td><b>Plant</b></td>
            <td>: {{$pengajuanPenjualan->location->plant}} - {{$pengajuanPenjualan->location->name}}</td>
        </tr>
        <tr>
            <td><b>Periode</b></td>
            <td>: {{$pengajuanPenjualan->period_from}} - {{$pengajuanPenjualan->period_to}}</td>
        </tr>
        <tr>
            <td><b>Jenis</b></td>
            <td>: {{$pengajuanPenjualan->jenis}}</td>
        </tr>
        @if ($pengajuanPenjualan->jenis == 'WP')
        <tr>
            <td><b>MVT Type</b></td>
            <td>: {{$pengajuanPenjualan->mvt_type}}</td>
        </tr>
        <tr>
            <td><b>Sloc</b></td>
            <td>: {{$pengajuanPenjualan->sloc}}</td>
        </tr>
        @endif
    </tbody>
</table>
@endcomponent

@if ($pengajuanPenjualan->jenis == 'BB')
@component('mail::table')
|Kategori | Berat/Qty | Satuan |
| :--- | ---: | ---: |
@foreach ($pengajuanPenjualan->itemsBb as $item)
| {{$item->kategori->jenis}} : {{$item->kategori->kode}} - {{$item->kategori->nama}} | {{number_format($item->timbangan_manual)}} | {{$item->eun}} |
@endforeach
@endcomponent

@component('mail::button', ['url' => config('app.url') . '/app/pengajuanPenjualanBb/', 'color' => 'success'])
KLIK DI SINI UNTUK MELAKUKAN PENGAJUAN KEMBALI
@endcomponent
@endif

@if ($pengajuanPenjualan->jenis == 'WP')
@component('mail::table')
|Material ID | Material Description | Berat | Price per Unit | Value
| :--- | :--- | ---: | ---: | ---: |
@foreach ($pengajuanPenjualan->itemsWp as $item)
| {{$item->material_id}} | {{$item->material_description}} | {{number_format($item->berat)}} KG | Rp {{number_format($item->price_per_unit)}} | Rp {{number_format($item->value, 0)}} |
@endforeach
@endcomponent

@component('mail::button', ['url' => config('app.url') . '/app/pengajuanPenjualanWp/', 'color' => 'success'])
KLIK DI SINI UNTUK MELAKUKAN PENGAJUAN KEMBALI
@endcomponent
@endif

Terimakasih,<br>
{{ config('app.name') }}
@endcomponent
