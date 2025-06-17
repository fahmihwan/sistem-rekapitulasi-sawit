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
                <a href="/master/karyawan"><i class="fa fa-bar-chart-o fa-fw"></i> Master Data<span
                        class="fa arrow"></span></a>
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
                    {{-- <li>
                        <a href="flot.html">Master Data Harga</a>
                    </li>
                    <li>
                        <a href="flot.html">TBS RAM</a>
                    </li> --}}
                </ul>
                <!-- /.nav-second-level -->
            </li>
            <li>
                <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> Pembelian TBS<span
                        class="fa arrow"></span></a>
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
                <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> Penjualan TBS<span
                        class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="/penjualan/tbs/PLASMA/view">Plasma</a>
                    </li>
                    <li>
                        <a href="/penjualan/tbs/LU/view">LU (Lahan Usaha)</a>
                    </li>
                </ul>
                <!-- /.nav-second-level -->
            </li>
            <li>
                <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i>Slip Gaji<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="flot.html">Karyawan</a>
                    </li>
                    <li>
                        <a href="flot.html">TKBM</a>
                    </li>
                </ul>
                <!-- /.nav-second-level -->
            </li>
            <li>
                <a href="tables.html"><i class="fa fa-table fa-fw"></i> Perhitungan Laba</a>
            </li>
            <li>
                <a href="/laporan/laporan-stock"><i class="fa fa-table fa-fw"></i> Laporan Stok</a>
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

            {{-- <li>
                <a href="tables.html"><i class="fa fa-table fa-fw"></i> Tables</a>
            </li>
            <li>
                <a href="forms.html"><i class="fa fa-edit fa-fw"></i> Forms</a>
            </li>
            <li>
                <a href="#"><i class="fa fa-wrench fa-fw"></i> UI Elements<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="panels-wells.html">Panels and Wells</a>
                    </li>
                    <li>
                        <a href="buttons.html">Buttons</a>
                    </li>
                    <li>
                        <a href="notifications.html">Notifications</a>
                    </li>
                    <li>
                        <a href="typography.html">Typography</a>
                    </li>
                    <li>
                        <a href="icons.html"> Icons</a>
                    </li>
                    <li>
                        <a href="grid.html">Grid</a>
                    </li>
                </ul>
                <!-- /.nav-second-level -->
            </li>
            <li>
                <a href="#"><i class="fa fa-sitemap fa-fw"></i> Multi-Level Dropdown<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="#">Second Level Item</a>
                    </li>
                    <li>
                        <a href="#">Second Level Item</a>
                    </li>
                    <li>
                        <a href="#">Third Level <span class="fa arrow"></span></a>
                        <ul class="nav nav-third-level">
                            <li>
                                <a href="#">Third Level Item</a>
                            </li>
                            <li>
                                <a href="#">Third Level Item</a>
                            </li>
                            <li>
                                <a href="#">Third Level Item</a>
                            </li>
                            <li>
                                <a href="#">Third Level Item</a>
                            </li>
                        </ul>
                        <!-- /.nav-third-level -->
                    </li>
                </ul>
                <!-- /.nav-second-level -->
            </li>
            <li>
                <a href="#"><i class="fa fa-files-o fa-fw"></i> Sample Pages<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="blank.html">Blank Page</a>
                    </li>
                    <li>
                        <a href="login.html">Login Page</a>
                    </li>
                </ul>
            </li> --}}
        </ul>
    </div>
</aside>
