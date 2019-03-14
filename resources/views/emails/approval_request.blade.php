@component('mail::message')
<strong>Dear {{$user->name}},</strong>
<br>

<p>
    Melalui email ini diberitahukan adanya pengajuan penjualan
    {{ $pengajuanPenjualan->jenis == 'BB' ? 'barang bekas' : 'waste product' }} dengan detail sebagai berikut:
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

@component('mail::table')
|Kategori | Jumlah | Unit |
| :--- | :---: | :---: |
@foreach ($pengajuanPenjualan->itemsBb as $item)
| {{$item->kategori->jenis}} : {{$item->kategori->kode}} - {{$item->kategori->nama}} | {{$item->jumlah_terima}} | {{$item->eun}} |
@endforeach
@endcomponent

@component('mail::button', ['url' => config('app.url') . '/app/approvalPengajuanPenjualan/'. $pengajuanPenjualan->id . '?level=' .$level])
KLIK DI SINI UNTUK APPROVAL
@endcomponent

Terimakasih,<br>
{{ config('app.name') }}
@endcomponent 