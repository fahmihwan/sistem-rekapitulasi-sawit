<aside class="sidebar navbar-default" role="navigation">
    <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu">
            <li class="sidebar-search">
                <div class="input-group custom-search-form">
                    <input type="text" class="form-control" placeholder="Search...">
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="button">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
                <!-- /input-group -->
            </li>
            <li>
                {{-- class="flex {{ request()->is('admin/dashboard') ? 'bg-white text-black' : 'text-gray-200' }} items-center py-2 px-3 text-base font-normal  rounded-lg  hover:bg-gray-100 hover:text-black "> --}}
                <a href="/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
            </li>
            <li>
                <a href="/master/karyawan">
                    {{-- <i class="fa fa-bar-chart-o fa-fw"></i>  --}}
                    <i class="fa fa-fw" aria-hidden="true" title="Copy to use tag">&#xf02b</i>

                    Master Data<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="/master/karyawan">Master Karyawan</a>
                    </li>
                    <li>
                        <a href="/master/pabrik">Master Pabrik</a>
                    </li>
                    <li>
                        <a href="/master/tarif">Master Tarif</a>
                    </li>
                    <li>
                        <a href="/master/ops">Master Ops</a>
                    </li>
                </ul>
                <!-- /.nav-second-level -->
            </li>
            <li>
                <a href="/periode">
                    {{-- <i class="fa fa-fw" aria-hidden="true" title="Copy to use calendar-plus-o">&#xf271</i> --}}
                    <i class="fa fa-fw" aria-hidden="true" title="Copy to use hourglass-half">&#xf252</i>
                    Periode</a>
            </li>
            <li>
                <a href="#">
                    {{-- <i class="fa fa-fw" aria-hidden="true" title="Copy to use truck">&#xf0d1</i> --}}
                    <i class="fa fa-fw" aria-hidden="true" title="Copy to use cart-arrow-down">&#xf218</i>
                    Pembelian TBS<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    {{-- /pembelian/tbs/{menu}/view --}}
                    <li>
                        <a href="/pembelian/tbs/LAHAN/view">TBS LAHAN</a>
                    </li>
                    <li>
                        <a href="/pembelian/tbs/RUMAH/view">TBS RUMAH</a>
                    </li>
                    <li>
                        <a href="/pembelian/tbs/RAM/view">TBS RAM</a>
                    </li>
                </ul>
                <!-- /.nav-second-level -->
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-fw" aria-hidden="true" title="Copy to use truck">&#xf0d1</i>
                    Penjualan TBS<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="/penjualan/tbs/PLASMA/view">Plasma</a>
                    </li>
                    <li>
                        <a href="/penjualan/tbs/LU/view">LU (Lahan Usaha)</a>
                    </li>
                </ul>
            </li>
            {{-- <li>
                <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i>Slip Gaji<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="flot.html">Karyawan</a>
                    </li>
                    <li>
                        <a href="flot.html">TKBM</a>
                    </li>
                </ul>
            </li> --}}
            {{-- <li>
                <a href="/slipgaji/karyawan"><i class="fa fa-table fa-fw"></i> Slip Gaji</a>
            </li> --}}
            <li>
                <a href="/penggajian">
                    {{-- <i class="fa fa-table fa-fw"></i> --}}
                    <i class="fa fa-fw" aria-hidden="true" title="Copy to use file-text-o">&#xf0f6</i>
                    Penggajian</a>
                {{-- <a href="/penggajianv2">
                    <i class="fa fa-table fa-fw"></i>
                    <i class="fa fa-fw" aria-hidden="true" title="Copy to use file-text-o">&#xf0f6</i>
                    Penggajian V2</a> --}}
            </li>
            <li>
                <a href="/pinjaman">
                    {{-- <i class="fa fa-fw" aria-hidden="true" title="Copy to use exchange">&#xf0ec</i> --}}
                    <i class="fa fa-fw" aria-hidden="true" title="Copy to use money">&#xf0d6</i>
                    Pinjaman (Kasbon)</a>
            </li>


            <li>
                <a href="/laba">
                    <i class="fa fa-fw" aria-hidden="true" title="Copy to use line-chart">&#xf201</i>
                    Perhitungan Laba</a>
            </li>
            <li>
                <a href="/laporan/laporan-stock">
                    {{-- <i class="fa fa-table fa-fw"></i> --}}
                    <i class="fa fa-fw" aria-hidden="true" title="Copy to use cubes">&#xf1b3</i>
                    Laporan Stok</a>
            </li>
            <li>
                <a href=""><i class="fa fa-files-o fa-fw"></i> Sample Pages<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="blank.html">Blank Page</a>
                    </li>
                    <li>
                        <a href="login.html">Login Page</a>
                    </li>
                </ul>
                <!-- /.nav-second-level -->
            </li>


        </ul>
    </div>
</aside>
