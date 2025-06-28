@extends('layouts.main')


@section('style')
    <!-- Select2 CSS -->
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" /> --}}
    <!-- Select2 CSS (versi lama, cocok untuk jQuery 2.1.3) -->
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" /> --}}

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-results>.select2-results__options {
            max-height: 200px;
            overflow-y: auto;
        }

        .select2-container .select2-selection--single {
            height: 38px !important;
            /* padding: 10px 12px; */
            font-size: 14px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
        }

        .select2-container--default .select2-selection--multiple {
            min-height: 100px;
            /* Bisa kamu ubah sesuai kebutuhan */
            max-height: 200px;
            /* Tambahkan jika mau batasi */
            overflow-y: auto;
            padding: 5px;
        }
    </style>
@endsection


@section('container')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Pinjaman</h1>
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
                                    Pinjaman
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
                                        <a href="{{ '/pinjaman' }}" class="btn btn-info btn-sm">
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
                                            <th>Tanggal</th>
                                            <th>Karyawan</th>
                                            <th>Pekerjaan utama</th>
                                            <th>Nominal Peminjaman</th>
                                            <th>Nominal Pengembalian</th>
                                            <th>Keterangan</th>
                                            <th>Created At</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($items as $item)
                                            <tr class="">
                                                <td>{{ $loop->iteration }}</td>
                                                {{-- <span class="label label-info">Info</span> --}}
                                                <td>{{ $item->tanggal_formatted }}</td>
                                                <td>{{ $item->karyawan->nama }}</td>
                                                <td>{{ $item->karyawan->main_type_karyawan->type_karyawan }}</td>
                                                {{-- <td>{{ $item->nominal_peminjaman }}</td>
                                                <td>{{ $item->nominal_pengembalian }}</td> --}}
                                                <td>{{ $item->nominal_peminjaman_formatted }}</td>
                                                <td>{{ $item->nominal_pengembalian_formatted }}</td>
                                                <td>{{ $item->keterangan }}</td>
                                                <td class="center">{{ $item->formatted_created_at }}</td>
                                                <td style="display: flex; border-bottom: 1px">

                                                    <button type="button" class="btn btn-warning btn-circle btn-edit"
                                                        style="margin-right: 10px" data-toggle="modal"
                                                        data-target="#modalCreateEdit" data-id="{{ $item->id }}"
                                                        data-tanggal="{{ $item->tanggal }}"
                                                        data-nama="{{ $item->karyawan->nama }}"
                                                        data-karyawanid="{{ $item->karyawan_id }}"
                                                        data-nominalpinjam="{{ $item->nominal_peminjaman }}"
                                                        data-nominalkembali="{{ $item->nominal_pengembalian }}"
                                                        data-keterangan="{{ $item->keterangan }}">
                                                        <i class="fa fa-edit"></i>
                                                    </button>


                                                    @if ($item->periode_berakhir != null)
                                                        <button data-bs-toggle="modal" type="button"
                                                            class="btn  btn-circle btn-edit"
                                                            style="background-color: gray; color:white"
                                                            onclick="alert('Periode sudah ditutup');">
                                                            <i class="fa fa-lock"></i>
                                                        </button>
                                                    @else
                                                        <form method="POST" action="/pinjaman/{{ $item->id }}">
                                                            @method('delete')
                                                            @csrf
                                                            <button type="submit" class="btn btn-danger btn-circle "
                                                                style="margin-right: 5px">
                                                                <i class="fa fa-trash"></i></button>
                                                        </form>
                                                    @endif

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


            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Sisa Pinjaman
                    </div>

                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Pekerjaan utama</th>
                                        <th>sisa pinjaman</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($list_sisa_pinjaman as $item)
                                        <tr>
                                            <td>{{ $item->nama }}</td>
                                            <td>{{ $item->type_karyawan }}</td>
                                            <td>{{ $item->sisa_pinjaman_formatted }}</td>

                                        </tr>
                                    @endforeach


                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>


            <div class="modal fade" id="modalCreateEdit" tabindex="-1" role="dialog" aria-labelledby="mymodalCreateEdit"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form id='mainForm' role="form" method=POST action={{ '/pinjaman' }}>
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"
                                    aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="mymodalCreateEdit">Periode</h4>
                            </div>


                            <div class="modal-body">
                                <input type="hidden" id="formMethod" name="_method" value="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Tanggal</label>
                                            <input class="form-control" name="tanggal" type="date"
                                                value="{{ now()->toDateString() }}" id="tanggal">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="karyawan_id">Pilih Karyawan </label><br>
                                            <select name="karyawan_id" id="karyawan_id" class="form-control"
                                                style="width: 100%; !important;">
                                                <option value="">-- Pilih --</option>
                                                @foreach ($karyawans as $k)
                                                    <option value="{{ $k->id }}"
                                                        {{ old('karyawan_id') == $k->id ? 'selected' : '' }}>
                                                        {{ $k->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            <label>Nominal Peminjaman</label>
                                            <input class="form-control" name="nominal_peminjaman" type="number"
                                                value="{{ old('nominal_peminjaman', 0) }}" id="nominal_peminjaman">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            <label>Nominal Pengembalian</label>
                                            <input class="form-control" name="nominal_pengembalian" type="number"
                                                value="{{ old('nominal_pengembalian', 0) }}" id="nominal_pengembalian">
                                        </div>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label>Keterangan</label>
                                    <textarea class="form-control" name="keterangan" id="keterangan" rows="3">{{ old('keterangan') }}</textarea>
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
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {

                $('#karyawan_id').select2({
                    placeholder: "Pilih User",
                    allowClear: true,
                    dropdownParent: $('#modalCreateEdit')
                });

                $('#btn-create').on('click', function() {
                    $('#mainForm')[0].reset();
                    $('#mymodalCreateEdit').text('Buat peminjaman');
                    $('#mainForm').attr('action', '/pinjaman');
                    $('#formMethod').val('POST')


                    $('#periode_mulai').prop('readonly', false);
                    $('#periode_berakhir').prop('readonly', true);
                });


                $('.btn-edit').on('click', function() {
                    let id = $(this).data('id');
                    $('#mainForm')[0].reset();
                    $('#mymodalCreateEdit').text('Ubah peminjaman');
                    $('#mainForm').attr('action', '/pinjaman/' + id);
                    $('#formMethod').val('PUT')



                    $('#tanggal').val($(this).data('tanggal'))

                    $('#nominal_peminjaman').val($(this).data('nominalpinjam'))
                    $('#nominal_pengembalian').val($(this).data('nominalkembali'))
                    $('#keterangan').val($(this).data('keterangan'))

                    const karyawan_id = $(this).data('karyawanid')
                    const karyawan_nama = $(this).data('nama')

                    if ($('#karyawan_id option[value="' + karyawan_id + '"]').length === 0) {
                        $('#karyawan_id').append(
                            $('<option>', {
                                value: karyawan_id,
                                text: karyawan_nama
                            })
                        );
                    }

                    $('#karyawan_id').val(karyawan_id).trigger('change');
                });


            });
        </script>
    @endsection
