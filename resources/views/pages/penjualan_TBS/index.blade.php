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
                <h1 class="page-header">Penjualan {{ $title }}</h1>
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
                                    <a href="{{ '/penjualan/tbs/' . $menu . '/view' }}" class="btn btn-info btn-sm">
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
                                        <th>Pabrik</th>
                                        <th>Sopir</th>
                                        <th>TKBM</th>
                                        <th>Timbangan 1</th>
                                        <th>Timbangan 2</th>
                                        <th>Bruto</th>
                                        <th>Sortasi</th>
                                        <th>Netto</th>
                                        <th>Harga</th>
                                        <th>Uang</th>
                                        <th>Created at</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr class="">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->pabrik->nama_pabrik ?? '-' }}</td>
                                            <td>{{ $item->sopir->nama ?? '-' }}</td>
                                            <td>
                                                @foreach ($item->tkbms as $d)
                                                    <p style="margin: 0; padding: 0;">- {{ $d->karyawan->nama ?? '-' }}</p>
                                                @endforeach
                                            </td>
                                            <td>{{ $item->timbangan_first_formatted }}</td>
                                            <td>{{ $item->timbangan_second_formatted }}</td>
                                            <td>{{ $item->bruto_formatted }}</td>
                                            <td>{{ $item->sortasi_formatted }}</td>
                                            <td>{{ $item->netto_formatted }}</td>
                                            <td>{{ $item->harga_formatted }}</td>
                                            <td>{{ $item->uang_formatted }}</td>
                                            <td class="center">{{ $item->formatted_created_at }}</td>
                                            <td style="display: flex; border-bottom: 1px">
                                                <form method="POST"
                                                    action="/penjualan/tbs/{{ $menu }}/delete/{{ $item->id }}">
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
            </div>
            <!-- /.col-lg-12 -->
        </div>


        <input type="hidden" value="{{ $menu }}" id="menu">


        <!-- Modal CREATE-->
        <div class="modal fade" id="modalCreateEdit" tabindex="-1" role="dialog" aria-labelledby="mymodalCreateEdit"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id='mainForm' role="form" method=POST action={{ '/penjualan/tbs/' . $menu . '/view' }}>
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="mymodalCreateEdit">Pembelian {{ $title }}</h4>
                        </div>


                        <div class="modal-body">
                            <input type="hidden" id="formMethod" name="_method" value="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pabrik_id">Pilih Pabrik</label><br>
                                        <select name="pabrik_id" id="pabrik_id" class="form-control"
                                            style="width: 100%; height: 200px !important;">
                                            <option value="">-- Pilih Sopir --</option>
                                            @foreach ($data_pabrik as $pabrik)
                                                <option value="{{ $pabrik->id }}">{{ $pabrik->nama_pabrik }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">

                                    <div class="form-group">
                                        <label for="sopir_id">Pilih Sopir</label><br>
                                        <select name="sopir_id" id="sopir_id" class="form-control"
                                            style="width: 100%; height: 200px !important;">
                                            <option value="">-- Pilih Sopir --</option>
                                            @foreach ($karyawans as $karyawan)
                                                @if ($karyawan->type_karyawan == 'SOPIR')
                                                    <option value="{{ $karyawan->id }}">{{ $karyawan->nama }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </div>



                            <div class="form-group">
                                <label for="tkbm_id">Pilih TKBM</label><br>
                                <select name="tkbm_id[]" multiple="multiple" id="tkbm_id" class="form-control"
                                    style="width: 100%; height: 200px !important;">
                                    <option value="">-- Pilih TKBM --</option>
                                    @foreach ($karyawans as $karyawan)
                                        @if ($karyawan->type_karyawan == 'TKBM')
                                            <option value="{{ $karyawan->id }}">{{ $karyawan->nama }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>



                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Timbangan 1</label>
                                        <div class="form-group input-group">
                                            <input type="number" class="form-control" name="timbangan_first"
                                                value="{{ old('timbangan_first') }}" id="timbangan_first">
                                            <span class="input-group-addon">Kg</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Timbangan 2</label>
                                        <div class="form-group input-group">
                                            <input type="number" class="form-control" name="timbangan_second"
                                                value="{{ old('timbangan_second') }}" id="timbangan_second">
                                            <span class="input-group-addon">Kg</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Bruto</label>
                                        <div class="form-group input-group">
                                            <input class="form-control" type="number" name="bruto"
                                                value="{{ old('bruto') }}" readonly id="bruto">
                                            <span class="input-group-addon">Kg</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Sortasi</label>
                                        <div class="form-group input-group">
                                            <input class="form-control" name="sortasi" value="{{ old('sortasi') }}"
                                                id="sortasi">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="form-group ">
                                <label>Netto</label>
                                <div class="form-group input-group">
                                    <input type="number" class="form-control" name="netto" readonly
                                        value="{{ old('netto') }}" id="netto">
                                    <span class="input-group-addon">Kg</span>
                                </div>
                            </div>

                            <div class="form-group ">
                                <label>Harga</label>
                                <div class="form-group input-group">
                                    <span class="input-group-addon">Rp</span>
                                    <input type="number" class="form-control" name="harga"
                                        value="{{ old('harga') }}" id="harga">
                                </div>
                            </div>

                            {{-- <div class="row">
                                <div class="col-md-6">
                                    <label>Tarif sopir saat ini</label>
                                    <div class="form-group input-group">
                                        <span class="input-group-addon">Rp</span>
                                        <input type="number" class="form-control" disabled
                                            value={{ $data_tarif['tarif_sopir_perkg'] }}>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label>Tarif TKBM saat ini</label>
                                    <div class="form-group input-group">
                                        <span class="input-group-addon">Rp</span>
                                        <input type="number" class="form-control" disabled
                                            value={{ $data_tarif['tarif_tkbm_perkg'] }}>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="form-group ">
                                <label>Uang</label>
                                <div class="form-group input-group">
                                    <span class="input-group-addon">Rp</span>
                                    <input type="text" class="form-control" readonly name="uang"
                                        value="{{ old('uang') }}" id="uang">
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">

                            <div style="display: flex; justify-content: space-between; color: red">

                                <div>
                                    <p style="margin: 0;  text-align: left; ">* Tarif <b>Sopir</b> saat ini
                                        Rp.{{ $data_tarif['tarif_sopir_perkg'] }}/kg
                                    </p>
                                    <p style="margin: 0; text-align: left">* Tarif <b>TKBM</b> saat ini
                                        Rp.{{ $data_tarif['tarif_tkbm_perkg'] }}/kg
                                    </p>
                                </div>

                                <div class="">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

    </div>
@endsection

@section('script')
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {


            const dataTarif = @json($data_tarif)



            if (dataTarif.tarif_sopir_id == null || dataTarif?.tarif_tkbm_id == null) {
                alert('tarif belum di set')
                window.location.href = '/master/tarif'
            }


            let menu = $('#menu').val()

            $(document).ready(function() {
                $('#pabrik_id').select2({
                    placeholder: "Pilih User",
                    allowClear: true,
                    dropdownParent: $('#modalCreateEdit')
                });


                $('#sopir_id').select2({
                    placeholder: "Pilih User",
                    allowClear: true,
                    dropdownParent: $('#modalCreateEdit')
                });

                $('#tkbm_id').select2({
                    placeholder: "Pilih User",
                    allowClear: true,
                    dropdownParent: $('#modalCreateEdit')
                });
            });





            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID').format(angka);
            }

            function hitungRUMAH_LAHAN() {
                var netto = parseInt($('#netto').val()) || 0;
                var harga = parseInt($('#harga').val()) || 0;
                var total = netto * harga;
                $('#uang').val(formatRupiah(total));
            }



            function hitungRam() {
                // let nama = $('#nama').val(menu);

                let timbang1 = parseInt($('#timbangan_first').val()) || 0;
                let timbang2 = parseInt($('#timbangan_second').val()) || 0;

                // $('#timbangan_first').val(1500)
                // $('#timbangan_second').val(1000)
                // let timbang1 = 1500;
                // let timbang2 = 1000;


                let bruto = timbang1 - timbang2;

                let sortasiPersen = parseFloat($('#sortasi').val()) || 0;
                let sortasiNilai = bruto * (sortasiPersen / 100);
                let netto = bruto - sortasiNilai;

                let harga = parseInt($('#harga').val()) || 0;
                let uang = netto * harga;

                $('#bruto').val(Math.ceil(bruto));
                $('#netto').val(Math.ceil(netto));
                $('#uang').val(formatRupiah(uang));

            }

            // if (menu == 'RAM') {
            $(' #timbangan_first, #timbangan_second, #sortasi, #harga').on('input', hitungRam);
            // } else {
            $('#netto, #harga').on('input', hitungRUMAH_LAHAN);
            // }

            $('#btn-create').on('click', function() {
                $('#mainForm')[0].reset(); // Kosongkan form
                $('#mymodalCreateEdit').text('Tambah TBS ' + menu);
                $('#mainForm').attr('action', '/penjualan/tbs/' + menu + '/view');
                $('#formMethod').val('POST')
            });


            $('.btn-edit').on('click', function() {
                let id = $(this).data('id');
                $('#mainForm')[0].reset(); // Kosongkan form
                $('#mymodalCreateEdit').text('Edit TBS ' + menu);
                $('#mainForm').attr('action', '/penjualan/tbs/' + menu + '/view/' + id);
                $('#formMethod').val('PUT')


                const tkbms = $(this).data('tkbms');
                const karyawanIds = tkbms.map(t => t.karyawan_id);



                // console.log($(this).data('nama'));
                $('#pabrik_id').val($(this).data('pabrik')).trigger('change');;
                $('#sopir_id').val($(this).data('nama')).trigger('change');;
                $('#tkbm_id').val(karyawanIds).trigger('change');;


                $('#netto').val($(this).data('netto'));
                $('#harga').val($(this).data('harga'));
                $('#uang').val($(this).data('uang'));

                $('#timbangan_first').val($(this).data('timbangan1'));
                $('#timbangan_second').val($(this).data('timbangan2'));
                $('#bruto').val($(this).data('bruto'));
                $('#sortasi').val($(this).data('sortasi'));


            });


        });
    </script>
@endsection
