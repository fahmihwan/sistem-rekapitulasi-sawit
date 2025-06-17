@extends('layouts.main')

@section('container')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Master Karyawan</h1>
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
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading ">
                        <div style=" display: flex; justify-content: space-between; align-items: center">
                            <div>
                                Master Karyawan
                            </div>

                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                data-target="#modalCreate">
                                <i class="fa fa-user-plus"></i> Tambah Data
                            </button>
                        </div>
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Bulan</th>
                                        <th>Tahun</th>
                                        <th>Total bruto masuk</th>
                                        <th>Total netto masuk</th>
                                        <th>Total bruto keluar</th>
                                        <th>Total netto keluar</th>
                                        <th>Saldo bruto Bulan ini</th>
                                        <th>Saldo netto bulan ini</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr class="">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->bulan }}</td>
                                            <td>{{ $item->tahun }}</td>
                                            <td>{{ $item->total_bruto_masuk }}</td>
                                            <td>{{ $item->total_netto_masuk }}</td>
                                            <td>{{ $item->total_bruto_keluar }}</td>
                                            <td>{{ $item->total_netto_keluar }}</td>
                                            <td>{{ $item->saldo_bruto_bulan_ini }}</td>
                                            <td>{{ $item->saldo_netto_bulan_ini }}</td>
                                        </tr>
                                    @endforeach

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






        <!-- /.row -->


        <!-- /.row -->
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {

            $('.btn-edit').on('click', function() {
                var userId = $(this).data('id');
                var nama = $(this).data('nama');
                var type = $(this).data('typekaryawan');

                $('#form-edit').attr('action', '/master/karyawan/' + userId);
                // Isi field
                $('#modal-nama').val(nama);
                $("input[name='type_karyawan'][value='" + type + "']").prop('checked', true);
            });
        });
    </script>
@endsection
