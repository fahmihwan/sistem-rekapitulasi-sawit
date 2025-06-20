@extends('layouts.main')

@section('container')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Stok periode</h1>
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
                                Stok periode
                            </div>

                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                data-target="#modalCreateEdit">
                                <i class="fa fa-plus"></i> Tambah Data
                            </button>
                        </div>
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Periode</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Berakhir</th>
                                        <th>Stok</th>
                                        <th>Created At</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr class="">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->label_periode }}</td>
                                            <td>{{ $item->formatted_mulai }}</td>
                                            <td>{{ $item->formatted_berakhir }}</td>
                                            <td>{{ $item->stok }}</td>

                                            <td class="center">{{ $item->formatted_created_at }}</td>
                                            <td style="display: flex; border-bottom: 1px">
                                                <form method="POST" action="/periode/{{ $item->id }}">
                                                    @method('delete')
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-circle"
                                                        style="margin-right: 5px">
                                                        <i class="fa fa-trash"></i></button>
                                                </form>

                                                <button data-id="{{ $item->id }}"
                                                    data-nama="{{ $item->sopir->id ?? '' }}"
                                                    data-pabrik="{{ $item->pabrik_id ?? '' }}"
                                                    data-tkbms='@json($item->tkbms)'
                                                    data-timbangan1="{{ $item->timbangan_first }}"
                                                    data-timbangan2="{{ $item->timbangan_second }}"
                                                    data-bruto="{{ $item->bruto }}" data-sortasi="{{ $item->sortasi }}"
                                                    data-netto="{{ $item->netto }}" data-harga="{{ $item->harga }}"
                                                    data-uang="{{ $item->uang }}" data-bs-toggle="modal" type="button"
                                                    class="btn btn-warning btn-circle btn-edit" data-toggle="modal"
                                                    data-target="#modalCreateEdit" data-id="{{ $item->id }}"><i
                                                        class="fa fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <p> Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} of
                                    {{ $items->total() }} entries</p>

                                {{ $items->links() }}
                            </div>
                        </div>
                        <!-- /.table-responsive -->

                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
            </div>
            <!-- /.col-lg-12 -->
        </div>



        <div class="modal fade" id="modalCreateEdit" tabindex="-1" role="dialog" aria-labelledby="mymodalCreateEdit"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id='mainForm' role="form" method=POST action={{ '/periode' }}>
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="mymodalCreateEdit">Periode</h4>
                        </div>


                        <div class="modal-body">
                            <input type="hidden" id="formMethod" name="_method" value="POST">
                            @csrf

                            <div class="form-group "> {{-- has-success, has-warning, has-error --}}
                                <label>Periode ke</label>
                                <input class="form-control" name="periode" value="{{ $get_first_periode->periode + 1 }}"
                                    readonly>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tanggal mulai</label>
                                        <input class="form-control" name="periode_mulai" type="date"
                                            value="{{ now()->toDateString() }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tanggal Selesai</label>
                                        <input class="form-control" name="periode_berakhir" type="date" value=""
                                            readonly>
                                    </div>
                                </div>
                            </div>



                        </div>
                        <div class="modal-footer">

                            <div class="">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>


                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.row -->


        <!-- /.row -->
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {

            $('#btn-create').on('click', function() {
                $('#mainForm')[0].reset();
                $('#mymodalCreateEdit').text('Buat periode baru ');
                $('#mainForm').attr('action', '/periode');
                $('#formMethod')
                    .val('POST')
            });


            $('.btn-edit').on('click', function() {
                // let id = $(this).data('id');
                // $('#mainForm')[0].reset();
                // $('#mymodalCreateEdit').text('Edit TBS ' + menu);
                // $('#mainForm').attr('action', '/penjualan/tbs/' + menu + '/view/' + id);
                // $('#formMethod').val('PUT')


                // const tkbms = $(this).data('tkbms');
                // const karyawanIds = tkbms.map(t => t.karyawan_id);


                // $('#pabrik_id').val($(this).data('pabrik')).trigger('change');;
                // $('#sopir_id').val($(this).data('nama')).trigger('change');;
                // $('#tkbm_id').val(karyawanIds).trigger('change');;


                // $('#netto').val($(this).data('netto'));
                // $('#harga').val($(this).data('harga'));
                // $('#uang').val($(this).data('uang'));

                // $('#timbangan_first').val($(this).data('timbangan1'));
                // $('#timbangan_second').val($(this).data('timbangan2'));
                // $('#bruto').val($(this).data('bruto'));
                // $('#sortasi').val($(this).data('sortasi'));
            });


        });
    </script>
@endsection
