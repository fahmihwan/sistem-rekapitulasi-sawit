@extends('layouts.main')

@section('style')
    <style>
        /* Sembunyikan tanggal */
        .ui-datepicker-calendar {
            display: none;
        }

        /* Tampilkan tombol OK saja */
        .ui-datepicker-close {
            display: inline-block;
        }
    </style>
@endsection

@section('container')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div style="display: flex; justify-content: space-between;  align-items: center">
                    <div style="display: flex; align-items: center">
                        <h1 class="page-header">Laba periode ke-{{ $periode->periode }}
                        </h1>
                        <h3>
                            @if (!$periode->periode_berakhir)
                                <span style="color:red; margin-left: 20px">(Belum ditutup)</span>
                            @endif
                        </h3>

                    </div>
                    <h3>
                        <b>{{ \Carbon\Carbon::parse($periode->periode_mulai)->format('d M Y') }}</b>
                        @if ($periode->periode_berakhir)
                            sampai
                            <b>{{ \Carbon\Carbon::parse($periode->periode_berakhir)->format('d M Y') }}</b>
                        @endif
                    </h3>

                </div>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <!-- Tampilkan error validasi -->
            @if ($errors->any())
                <div style="color:red;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading" style="display: flex; justify-content: space-between">
                        <div>
                            Pembelian
                        </div>
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table  table-bordered ">
                                <thead>
                                    <tr style="text-align: center">
                                        <th style="text-align: center">Jenis TBS</th>
                                        <th style="text-align: center">Tonase</th>
                                        <th style="text-align: center">Harga</th>
                                        <th style="text-align: center">Uang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pembelian as $item)
                                        <tr>
                                            <td>{{ $item->type_tbs }}</td>
                                            <td>{{ number_format($item->netto, 0, ',', '.') }} Kg</td>
                                            <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($item->uang, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <th>
                                        Total
                                    </th>
                                    <th style="background-color: yellow">
                                        {{ number_format($pembelian->sum('netto'), 0, ',', '.') }} Kg
                                    </th>
                                    <th>

                                    </th>
                                    <th style="background-color: yellow">
                                        Rp {{ number_format($pembelian->sum('uang'), 0, ',', '.') }}
                                    </th>
                                </tfoot>
                            </table>
                        </div>
                        <!-- /.table-responsive -->
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
            </div>
            <!-- /.col-lg-12 -->
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading" style="display: flex; justify-content: space-between">
                        <div>
                            Penjualan
                        </div>
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered ">
                                <thead>
                                    <tr style="text-align: center">
                                        <th style="text-align: center">Tonase Pabrik</th>
                                        <th style="text-align: center">Harga</th>
                                        <th style="text-align: center">Uang</th>
                                        <th style="text-align: center">Jenis DO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($penjualan as $item)
                                        <tr>


                                            <td>{{ number_format($item->netto, 0, ',', '.') }} Kg</td>
                                            <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($item->uang, 0, ',', '.') }}</td>
                                            <td>{{ $item->delivery_order_type }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <th style="background-color: yellow">
                                        {{ number_format($penjualan->sum('netto'), 0, ',', '.') }} Kg
                                    </th>
                                    <th>

                                    </th>
                                    <th style="background-color: yellow">
                                        Rp {{ number_format($penjualan->sum('uang'), 0, ',', '.') }}
                                    </th>
                                    <th>

                                    </th>
                                </tfoot>
                            </table>
                        </div>
                        <!-- /.table-responsive -->
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
            </div>
            <!-- /.col-lg-12 -->
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading" style="display: flex; justify-content: space-between">
                        <div>
                            Perhitungan Laba
                        </div>
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered ">
                                <tbody>
                                    <tr>
                                        <th>Cair Do</th>
                                        <td>Rp {{ number_format($penjualan->sum('uang'), 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Modal</th>
                                        <td>Rp {{ number_format($pembelian->sum('uang'), 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <th>OPS</th>
                                        <td>Rp
                                            {{ number_format($penjualan->sum('netto') * $periode->ops->ops, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Laba Bersih</th>
                                        <td style="background-color: yellow">Rp
                                            {{ number_format($penjualan->sum('uang') - $pembelian->sum('uang') - $penjualan->sum('netto') * $periode->ops->ops, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    {{-- @foreach ($penjualan as $item)
                                        <tr>


                                            <td>{{ number_format($item->netto, 0, ',', '.') }} Kg</td>
                                            <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>

                                        </tr>
                                    @endforeach --}}
                                </tbody>
                            </table>
                        </div>
                        <!-- /.table-responsive -->
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
            </div>
            <!-- /.col-lg-12 -->
        </div>


    </div>
@endsection

@section('script')
@endsection
