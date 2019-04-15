@component('mail::message')
Dear, {{ $penerimaan->pengeluaran->user->name }},

Pengeluaran barang bekas dengan nomor {{ $penerimaan->no_sj_keluar }} telah diterima dengan detail sebagai berikut:


@component('mail::panel')
<table style="width:100%">
    <tbody>
        <tr>
            <td style="width:200px"><b>No. Surat Jalan Keluar</b></td>
            <td>: {{$penerimaan->no_sj_keluar}}</td>
        </tr>
        <tr>
            <td><b>Tanggal</b></td>
            <td>: {{$penerimaan->tanggal}}</td>
        </tr>
        <tr>
            <td><b>Lokasi Asal</b></td>
            <td>: {{$penerimaan->lokasi_asal}}</td>
        </tr>
        <tr>
            <td><b>Lokasi Terima</b></td>
            <td>: {{$penerimaan->lokasi_terima}}</td>
        </tr>
        <tr>
            <td><b>Nama Penerima</b></td>
            <td>: {{$penerimaan->penerima}}</td>
        </tr>
        <tr>
            <td><b>Keterangan</b></td>
            <td>: {{$penerimaan->keterangan}}</td>
        </tr>
    </tbody>
</table>
@endcomponent

@component('mail::table')
|Kategori | Jumlah Kirim | Jumlah Terima | Selisih Jumlah | Timbangan Kirim | Timbangan Terima | Selisih Timbangan | Keterangan |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
@foreach ($penerimaan->items as $i)
| {{$i->barang->jenis}} : {{$i->barang->kode}} - {{$i->barang->nama}} | {{$i->qty_kirim}} | {{$i->qty_terima}} | {{$i->qty_terima - $i->qty_kirim}} | {{$i->timbangan_manual_kirim}} kg | {{$i->timbangan_manual_terima}} kg | {{$i->timbangan_manual_terima - $i->timbangan_manual_kirim}} kg | {{$i->keterangan}} |
@endforeach
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent