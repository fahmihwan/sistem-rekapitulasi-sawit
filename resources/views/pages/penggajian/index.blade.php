@extends('layouts.main')

@section('container')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Penggajian</h1>
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
                    {{-- <div class="panel-heading ">
                        <div style=" display: flex; justify-content: space-between; align-items: center">
                            <div>
                                Stok periode
                            </div>

                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                data-target="#modalCreateEdit">
                                <i class="fa fa-plus"></i> Tambah Data
                            </button>
                        </div>
                    </div> --}}

                    <div class="panel panel-default">
                        {{-- <div class="panel-heading ">
                            <div style=" display: flex; justify-content: space-between; align-items: center">
                                <div>
                                    Master TBS
                                </div>

                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" id="btn-create"
                                    data-target="#modalCreateEdit">
                                    <i class="fa fa-plus"></i> Tambah Data
                                </button>
                            </div>
                        </div> --}}

                        <div class="panel-heading ">
                            <div style=" display: flex; justify-content: space-between; align-items: center">
                                <div>
                                    Stok periode
                                </div>

                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" id="btn-create"
                                    data-target="#modalCreateEdit">
                                    <i class="fa fa-plus"></i> Tambah Data
                                </button>
                            </div>
                        </div>


                        <div>
                            <form method="GET" id="perPageForm" class="container-filter-datatables">
                                <div class="container-left-datatables">
                                    <span style="margin-left: 5px; margin-right: 5px">Show</span>
                                    <select class="form-control" name="per_page" style="width: 100px"
                                        onchange="document.getElementById('perPageForm').submit()">
                                        @foreach ([10, 25, 50, 100] as $size)
                                            <option value="{{ $size }}"
                                                {{ request('per_page') == $size ? 'selected' : '' }}>
                                                {{ $size }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span style="margin-left: 5px; margin-right: 5px">entries</span>
                                </div>




                                <div class="container-right-datatables">
                                    <div class="form-filter-datatables">
                                        <span style="display: block; margin-right: 5px; width: 140px">Filter Tanggal</span>
                                        <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                                            class="form-control" style="width: 100%">
                                    </div>

                                    <div class="form-filter-datatables">
                                        <span style="display: block; margin-right: 5px ">Search </span>
                                        <input class="form-control" name="search" value="{{ request('search') }}">
                                    </div>

                                    <div class="form-filter-datatables">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-search"></i> Search
                                        </button>
                                    </div>

                                    <div class="form-filter-datatables">
                                        <a href="{{ '/periode' }}" class="btn btn-info btn-sm">
                                            <i class="fa fa-refresh"></i> clear
                                        </a>
                                    </div>
                                </div>

                            </form>
                        </div>

                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Periode Mulai</th>
                                            <th>Periode Berakhir</th>
                                            <th>Karyawan</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($items as $item)
                                            <tr class="">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item['periode_awal'] }}</td>
                                                <td>{{ $item['periode_akhir'] }}</td>
                                                <td>
                                                    @foreach ($item['karyawans'] as $d)
                                                        <a
                                                            href="/penggajian/{{ $item['id'] }}/{{ $d['id'] }}/detail-gaji">{{ $d['nama'] }}</a><br>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    <form method="POST" action="/penggajian/{{ $item['id'] }}">
                                                        @method('delete')
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-circle "
                                                            style="margin-right: 5px">
                                                            <i class="fa fa-trash"></i></button>
                                                    </form>
                                                </td>
                                                {{-- <td>{{ $loop->iteration }}</td>

                                                <td>{{ $item->label_periode }}</td>
                                                <td>{{ $item->formatted_mulai }}</td>
                                                <td>
                                                    @if ($item->formatted_berakhir == null)
                                                        <span class="label label-success" style="font-size: 12px">Periode
                                                            masih berjalan</span>
                                                    @else
                                                        {{ $item->formatted_berakhir }}
                                                    @endif
                                                </td>

                                                <td class="center">{{ $item->formatted_created_at }}</td>
                                                <td style="display: flex; border-bottom: 1px">

                                                    <button type="button" class="btn btn-sm  btn-warning btn-edit"
                                                        style="margin-right: 10px" data-id="{{ $item->id }}"
                                                        data-mulai="{{ $item->periode_awal }}"
                                                        data-berakhir="{{ $item->periode_akhir }}"
                                                        data-periode="{{ $item->periode }}"
                                                        data-idops="{{ $item->ops->id }}" data-ops="{{ $item->ops->ops }}"
                                                        data-bs-toggle="modal" data-toggle="modal"
                                                        data-target="#modalCreateEdit" type="button">TUTUP PERIODE
                                                    </button>


                                                    @if ($item->periode_akhir != null)
                                                        <button data-bs-toggle="modal" type="button"
                                                            class="btn  btn-circle btn-edit"
                                                            style="background-color: gray; color:white"
                                                            onclick="alert('Periode sudah ditutup');">
                                                            <i class="fa fa-lock"></i>
                                                        </button>
                                                    @else
                                                        <form method="POST" action="/periode/{{ $item->id }}">
                                                            @method('delete')
                                                            @csrf
                                                            <button type="submit" class="btn btn-danger btn-circle "
                                                                style="margin-right: 5px">
                                                                <i class="fa fa-trash"></i></button>
                                                        </form>
                                                    @endif

                                                </td> --}}
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
                        <form id='mainForm' role="form" method=POST action={{ '/penggajian' }}>
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"
                                    aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="mymodalCreateEdit">Penggajian</h4>
                            </div>


                            <div class="modal-body">
                                <input type="hidden" id="formMethod" name="_method" value="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Periode Awal</label>
                                            <input class="form-control" name="periode_awal" type="date" value=""
                                                {{-- value="{{ now()->toDateString() }}" --}} id="periode_awal">

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Periode Akhir</label>
                                            <input class="form-control" name="periode_akhir" type="date" value=""
                                                id="periode_akhir">
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
                    $('#mainForm').attr('action', '/penggajian');
                    $('#formMethod').val('POST')


                    // $('#periode_awal').prop('readonly', false);
                    // $('#periode_akhir').prop('readonly', true);

                    // $('#ops_id_select').val('').trigger('change');
                });


                $('.btn-edit').on('click', function() {
                    let id = $(this).data('id');
                    $('#mainForm')[0].reset();
                    $('#mymodalCreateEdit').text('Tutup Periode');
                    $('#mainForm').attr('action', '/penggajian/' + id);
                    $('#formMethod').val('PUT')


                    // $('#periodeke').prop('readonly', true)
                    // $('#periode_akhir').prop('readonly', false);

                    // const ops = $(this).data('ops')
                    // const idops = $(this).data('idops')

                    // if ($('#ops_id_select option[value="' + idops + '"]').length === 0) {
                    //     $('#ops_id_select').append(
                    //         $('<option>', {
                    //             value: idops,
                    //             text: ops
                    //         })
                    //     );
                    // }


                    // $('#ops_id_select').val(idops).trigger('change');

                    // $('#periodeke').val($(this).data('periode'))
                    // $('#periode_awal').val($(this).data('mulai'))
                    // $('#periode_akhir').val($(this).data('berakhir'))

                });


            });
        </script>
    @endsection
