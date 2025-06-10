@extends('layouts.main')

@section('container')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Pembelian {{ $title }}</h1>
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
                                Master TBS
                            </div>

                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                data-target="#modalCreate">
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
                                    <a href="{{ '/pembelian/tbs/' . $menu . '/view' }}" class="btn btn-info btn-sm">
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
                                        <th>Nama</th>
                                        <th>Netto</th>
                                        <th>Harga</th>
                                        <th>Uang</th>
                                        <th>Timbagan 1</th>
                                        <th>Timbagan 2</th>
                                        <th>Bruto</th>
                                        <th>Sortasi</th>
                                        <th>Created at</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr class="">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->nama_customer }}</td>
                                            <td>{{ $item->netto_formatted }}</td>
                                            <td>{{ $item->harga_formatted }}</td>
                                            <td>{{ $item->uang_formatted }}</td>
                                            <td>{{ $item->timbangan_first_formatted }}</td>
                                            <td>{{ $item->timbangan_second_formatted }}</td>
                                            <td>{{ $item->bruto_formatted }}</td>
                                            <td>{{ $item->sortasi_formatted }}</td>
                                            <td class="center">{{ $item->formatted_created_at }}</td>

                                            <td class="center" style="display: flex">
                                                <form method="POST" action="/master/pabrik/{{ $item->id }}">
                                                    @method('delete')
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-circle"
                                                        style="margin-right: 5px">
                                                        <i class="fa fa-trash"></i></button>
                                                </form>


                                                <button type="button" class="btn btn-warning btn-circle btn-edit"
                                                    data-toggle="modal" data-target="#modalEdit"
                                                    data-id="{{ $item->id }}"> {{-- data-namapabrik="{{ $item->nama_pabrik }}"> --}} <i
                                                        class="fa fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                {{-- <p>Showing 1 to 10 of 34 entries</p> --}}
                                <p> Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} of
                                    {{ $items->total() }} entries</p>

                                {{ $items->links() }}

                                {{-- <ul class="pagination" style="padding: 0; margin: 0">
                                        <li class="disabled"><a href="#" aria-label="Previous"><span
                                                    aria-hidden="true">&laquo;</span></a></li>
                                        <li class="active"><a href="#">1 <span class="sr-only">(current)</span></a>
                                        </li>
                                    </ul> --}}

                            </div>

                        </div>

                        <!-- /.table-responsive -->
                    </div>
                    <!-- /.panel-body -->
                </div>
            </div>
            <!-- /.col-lg-12 -->
        </div>



        <!-- Modal CREATE-->
        <div class="modal fade" id="modalCreate" tabindex="-1" role="dialog" aria-labelledby="myModalCreate"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form role="form" method="POST" action="/master/pabrik">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModalCreate">Tambah Pabrik</h4>
                        </div>
                        <div class="modal-body">


                            @method('POST')
                            @csrf
                            <div class="form-group "> {{-- has-success, has-warning, has-error --}}
                                <label>Nama Customer</label>
                                <input class="form-control" name="nama_customer" value="{{ old('nama_customer') }}">
                            </div>

                            <div class="form-group ">
                                <label>Netto</label>
                                <input class="form-control" name="netto" value="{{ old('netto') }}">
                            </div>

                            <div class="form-group ">
                                <label>Harga</label>
                                <input class="form-control" name="harga" value="{{ old('harga') }}">
                            </div>
                            <div class="form-group ">
                                <label>Uang</label>
                                <input class="form-control" name="uang" value="{{ old('uang') }}">
                            </div>
                            <div class="form-group ">
                                <label>Timbangan 1</label>
                                <input class="form-control" name="timbangan_first" value="{{ old('timbangan_first') }}">
                            </div>
                            <div class="form-group ">
                                <label>Timbangan 2</label>
                                <input class="form-control" name="timbangan_second"
                                    value="{{ old('timbangan_second') }}">
                            </div>
                            <div class="form-group ">
                                <label>Bruto</label>
                                <input class="form-control" name="bruto" value="{{ old('bruto') }}">
                            </div>
                            <div class="form-group ">
                                <label>Sortasi</label>
                                <input class="form-control" name="sortasi" value="{{ old('sortasi') }}">
                            </div>


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>


        <!-- /.row -->
        <!-- Modal EDIT-->
        <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="myModalEdit"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form role="form" method="POST" id="form-edit">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"
                                aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModalEdit">Ubah Pabrik</h4>
                        </div>
                        <div class="modal-body">


                            @method('PUT')
                            @csrf
                            <div class="form-group "> {{-- has-success, has-warning, has-error --}}
                                <label>Nama</label>
                                <input class="form-control" name="nama_pabrik" id="modal-namapabrik">
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
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
