<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="{{ asset('js/bootstrap.js') }}"></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
        </div>
    </nav>
    <div id="app" class="container" style="margin-top:40px">
        <main class="py-4">
            <div class="card">
                <div class="card-header">DETAIL PENGAJUAN PENJUALAN</div>
                <div class="card-body shadow">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm table-bordered">
                            <tbody>
                                <tr>
                                    <th style="width: 120px">No. Pengajuan</th>
                                    <td>{{$data->no_aju}}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                    <td>{{$data->tanggal}}</td>
                                </tr>
                                <tr>
                                    <th>Plant</th>
                                    <td>{{$data->location->plant}} - {{$data->location->name}}</td>
                                </tr>
                                <tr>
                                    <th>Periode</th>
                                    <td>{{$data->period_from}} - {{$data->period_to}}</td>
                                </tr>
                                <tr>
                                    <th>Jenis</th>
                                    <td>{{$data->jenis}}</td>
                                </tr>
                                <tr>
                                    <th>Approval 1</th>
                                    <td>:
                                        @if ($data->approval1_status == 0)
                                        <span>Pending</span>
                                        @elseif ($data->approval1_status == 1)
                                        <span>Approved</span>
                                        @elseif ($data->approval1_status == 2)
                                        <span>Rejected</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Approval 2</th>
                                    <td>:
                                        @if ($data->approval2_status == 0)
                                        <span>Pending</span>
                                        @elseif ($data->approval2_status == 1)
                                        <span>Approved</span>
                                        @elseif ($data->approval2_status == 2)
                                        <span>Rejected</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if ($data->jenis == "BB")
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="text-center">#</th>
                                    <th rowspan="2">Kategori</th>
                                    <th colspan="2" class="text-center">Stock</th>
                                    <th rowspan="2" class="text-center">Jumlah Diajukan</th>
                                    <th rowspan="2" class="text-center">Selisih</th>
                                    <th rowspan="2" class="text-center">Eun</th>
                                    <th rowspan="2" class="text-center">Timbangan Manual (kg)</th>
                                </tr>
                                <tr>
                                    <th style="width:100px" class="text-center">Qty</th>
                                    <th style="width:100px" class="text-center">Berat (kg)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->itemsBb as $index => $item)
                                <tr>
                                    <td class="text-center">{{$index + 1}}</td>
                                    <td>{{$item->kategori->kode}} - {{$item->kategori->nama}}</td>
                                    <td class="text-center">{{number_format($item->stock_qty)}}</td>
                                    <td class="text-center">{{number_format($item->stock_berat)}}</td>
                                    <td class="text-center">{{number_format($item->jumlah)}}</td>
                                    <td class="text-center">{{number_format($item->stock_qty - $item->jumlah)}}</td>
                                    <td class="text-center">{{$item->eun}}</td>
                                    <td class="text-center">{{number_format($item->timbangan_manual)}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    @if ($data->jenis == "WP")
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class="text-center">Material</th>
                                <th class="text-center">Material Description</th>
                                <th class="text-center">Berat</th>
                                <th class="text-center">Price Per Unit</th>
                                <th class="text-center">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data->itemsWp as $index => $item)
                            <tr>
                                <td>{{$index+1}}.</td>
                                <td>{{$item->material_id}}</td>
                                <td>{{$item->material_description}}</td>
                                <td class="text-right">{{number_format($item->berat, 4)}} KG</td>
                                <td class="text-right">Rp {{number_format($item->price_per_unit, 0)}}</td>
                                <td class="text-right">Rp {{number_format($item->value, 0)}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif

                    <div v-show="!approval_success">
                        <textarea rows="3" v-model="note" class="form-control" placeholder="Note"></textarea>
                        <br>
                        <button @click.prevent="approve(1)" class="btn btn-success btn-lg">APPROVE</button>
                        <button @click.prevent="approve(2)" class="btn btn-danger btn-lg">REJECT</button>
                    </div>

                    <div v-if="approval_success">
                        <h3 class="text-center">Approval berhasil.</h3>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        const app = new Vue({
            el: '#app',
            data: {
                note: '',
                approval_success: false
            },
            methods: {
                approve(status) {
                    if (status == 2 && !this.note) {
                        alert('Mohon isi note.')
                        return
                    }

                    if (!confirm('Anda yakin?')) return;

                    let data = {
                        level: {{ request('level') }},
                        status: status,
                        note: this.note
                    }
                    axios.put('{{ url("/pengajuanPenjualan/".$data->id."/approve") }}', data).then(r => {
                        this.approval_success = true
                        alert('Approval berhasil');
                    }).catch(e => {
                        alert('Approval gagal. ' + e.response.data.message);
                    })
                }
            }
        })
    </script>
</body>

</html>
