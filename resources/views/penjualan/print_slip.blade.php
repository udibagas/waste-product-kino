@extends('layouts.print')

@section('content')

<div class="container">
    <table style="width:100%">
        <tbody>
            <tr>
                <td>
                    <img src="{{asset('/images/logo.png')}}" alt="" style="width: 150px"><br>
                    <h3>{{ config('app.name', 'Laravel') }}</h3>
                </td>
                <td class="text-right" style="vertical-align:center">
                    <h1>SLIP JUAL {{$penjualan->jenis}}</h1>
                </td>
            </tr>
        </tbody>
    </table>
    <hr>
    <table class="table table-bordered table-sm table-striped">
        <tbody>
            <tr>
                <td style="width: 200px"><strong>Plant</strong></td>
                <td> {{$penjualan->location->plant}} - {{$penjualan->location->name}}</td>
            </tr>
            <tr>
                <td><strong>Pembeli</strong></td>
                <td>{{$penjualan->pembeli->nama}} ({{$penjualan->pembeli->kontak}})</td>
            </tr>
            <tr>
                <td><strong>Kategori</strong></td>
                <td>{{$penjualan->jenis}}</td>
            </tr>
            <tr>
                <td><strong>No. Surat Jalan</strong></td>
                <td>{{$penjualan->no_sj}}</td>
            </tr>
            <tr>
                <td><strong>Jembatan Timbang</strong></td>
                <td>{{number_format($penjualan->jembatan_timbang, 4)}} kg</td>
            </tr>
            <tr>
                <td><strong>Tanggal Transaksi</strong></td>
                <td>{{date('d-M-Y', strtotime($penjualan->tanggal))}}</td>
            </tr>
        </tbody>
    </table>

    <br><br>

    <table class="table table-striped table-bordered table-sm">
        <thead>
            <tr>
                <th>Nama Kategori</th>
                <th class="text-right">Berat Timbangan Manual</th>
                <th class="text-right">Value Penjualan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penjualan->itemsBb as $i)
            <tr>
                <td>{{$i->kategori->nama}}</td>
                <td class="text-right">{{$i->timbangan_manual}} kg</td>
                <td class="text-right">Rp {{number_format($i->value, 0)}}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>TOTAL</th>
                <th class="text-right">
                    {{ number_format(array_reduce($penjualan->itemsBb->toArray(), function($total, $current) {
                        return $total + $current['timbangan_manual'];
                    }, 0), 4) }} kg
                </th>
                <th class="text-right">
                    Rp {{ number_format(array_reduce($penjualan->itemsBb->toArray(), function($total, $current) {
                        return $total + $current['value'];
                    }, 0), 0) }}
                </th>
            </tr>
        </tfoot>
    </table>

    <table class="table table-bordered table-sm" style="margin-top:100px">
        <thead>
            <tr>
                <th class="text-center" style="width:25%">Received By</th>
                <th class="text-center" style="width:25%">Checked By</th>
                <th class="text-center" style="width:25%">Approval 1</th>
                <th class="text-center" style="width:25%">Approval 2</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="height:100px">&nbsp;</td>
                <td style="height:100px">&nbsp;</td>
                <td style="height:100px">&nbsp;</td>
                <td style="height:100px">&nbsp;</td>
            </tr>
            <tr>
                <th class="text-center">{{$penjualan->pembeli->nama}}</th>
                <!-- <th class="text-center">{{$penjualan->user->name}}</th> -->
                <th class="text-center">Logistik/DA</th>
                <th class="text-center">Finance Plant</th>
                <th class="text-center">Manager Plant</th>
            </tr>
            <tr>
                <td class="text-center">{{date('d-M-Y')}}</td>
                <td class="text-center">{{ date('d-M-Y', strtotime($penjualan->tanggal)) }}</td>
                <td class="text-center">{{ date('d-M-Y', strtotime($penjualan->pengajuan->approval1_time)) }}</td>
                <td class="text-center">{{ date('d-M-Y', strtotime($penjualan->pengajuan->approval2_time)) }}</td>
            </tr>
        </tbody>
    </table>
</div>

@endsection