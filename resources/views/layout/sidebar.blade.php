<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" key="t-menu">Menu</li>

                @can('view dashboard')
                <li>
                    <a href="{{ url('/dashboard') }}" class="">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-dashboards">Dashboards</span>
                    </a>
                </li>
                @endcan

                @can('tulisan data master')
                <li class="menu-title" key="t-apps">Data Master</li>
                @endcan

                @can('view siswa')
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-detail"></i>
                        <span key="t-crypto">Siswa</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @can('view data-siswa')                           
                        <li><a href="{{ url('/siswa') }}" key="t-wallet">Data Siswa</a></li>
                        @endcan
                        @can('view kelas-siswa')
                        <li><a href="{{ route('showKelasSiswa') }}" key="t-buy">Kelas - Siswa</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan


                @can('view periode')     
                <li>
                    <a href="{{ url('/periode') }}" class="">
                        <i class="bx bx-detail"></i>
                        <span key="t-dashboards">periode</span>
                    </a>
                </li>
                @endcan

                @can('view kelas')
                <li>
                    <a href="{{ url('/kelas') }}" class="">
                        <i class="bx bx-detail"></i>
                        <span key="t-dashboards">kelas</span>
                    </a>
                </li>
                @endcan

                @can('view riwayat')                   
                <li>
                    <a href="{{ url('riwayat') }}" class="">
                        <i class="bx bx-detail"></i>
                        <span key="t-dashboards">Riwayat Kelas</span>
                    </a>
                </li>
                @endcan

                @can('view jurusan')
                <li>
                    <a href="{{ url('/jurusan') }}" class="">
                        <i class="bx bx-detail"></i>
                        <span key="t-dashboards">jurusan</span>
                    </a>
                </li>
                @endcan

                

                @can('view bk')
                 <li>
                    <a href="{{ url('/bk') }}" class="waves-effect">
                        <i class="bx bx-detail"></i>
                        <span key="t-ecommerce">Bimbingan Konseling</span>
                    </a>
                </li>
                @endcan

                @can('view wali-kelas')
                <li>
                    <a href="{{ url('/wali-kelas') }}" class="waves-effect">
                        <i class="bx bx-detail"></i>
                        <span key="t-ecommerce">Kelas pengampuh</span>
                    </a>
                </li>
                @endcan

                @can('motoring wali-kelas')
                <li>
                    <a href="{{ url('/wali-kelas/detail') }}" class="waves-effect">
                        <i class="bx bx-detail"></i>
                        <span key="t-ecommerce">Monitoring</span>
                    </a>
                </li>
                @endcan
                

                @can('tulisan pelanggaran')
                <li class="menu-title" key="t-apps">Pelanggaran</li>
                @endcan

                @can('view skor-pelanggaran')
                <li>
                    <a href="{{ url('/skor-pelanggaran') }}" class="waves-effect">
                        <i class="bx bx-file"></i>
                        <span key="t-file-manager">Skor Pelanggaran</span>
                    </a>
                </li>
                @endcan

                @can('view kategori-tindakan')
                <li>
                    <a href="{{ url ('/kategori-tindakan') }}" class="waves-effect">
                        <i class="bx bx-store"></i>
                        <span key="t-store">Kategori Tindakan</span>
                    </a>
                </li>
                @endcan

                @can('view pelanggaran')
                <li>
                    <a href="{{ url('/pelanggaran') }}" class="waves-effect">
                        <i class="bx bx-chat"></i>
                        <span key="t-chat">Catatan pelanggaran</span>
                    </a>
                </li>
                @endcan

                @can('view tindakan-siswa')                   
                <li>
                    <a href="{{ url('/tindakan-siswa') }}" class="waves-effect">
                        <i class="bx bx-chat"></i>
                        <span key="t-chat">Tindakan Siswa</span>
                    </a>
                </li>
                @endcan

                @can('view monitoring-pelanggaran')
                <li>
                    <a href="{{ url('/monitoring-pelanggaran') }}" class="waves-effect">
                        <i class="bx bx-receipt"></i>
                        <span key="t-ecommerce">Monitoring-pelanggaran</span>
                    </a>
                </li>
                @endcan

                @can('tulisan absensi')
                <li class="menu-title" key="t-apps">Absensi</li>
                @endcan

                @can('view catatan-absensi')
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-briefcase-alt"></i>
                        <span key="t-ecommerce">Catatan Absensi</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @can('view input-absensi')                            
                        <li><a href="{{ route('createHariIni') }}" key="t-products">Input Absensi Hari ini</a></li>
                        @endcan
                        @can('view riwayat-absensi')                          
                        <li><a href="{{ route('riwayatHariIni') }}" key="t-product-detail">Riwayat Absen Hari ini</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan

                @can('view validasi surat')
                <li>
                    <a href="{{ route('validasi.index') }}">
                        <i class="bx bx-check-square"></i>
                        @php
                            $jumlahValidasi = \App\Models\Absensi::where('status_surat', 'tertunda')->count();
                        @endphp
                        @if($jumlahValidasi > 0)
                            <span class="badge rounded-pill bg-danger float-end">{{ $jumlahValidasi }}</span>
                        @endif
                        <span>Validasi Surat</span>
                    </a>
                </li>
                @endcan

                @can('view monitoring-absensi')                    
                <li>
                    <a href="{{ url('/monitoring-absensi') }}" class="waves-effect">
                        <i class="bx bx-store"></i>
                        <span key="t-ecommerce">Monitoring-Absensi</span>
                    </a>
                </li>
                @endcan

                @can('tulisan user management')                
                <li class="menu-title" key="t-apps">User Management</li>
                @endcan

                @can('view user')
                 <li>
                    <a href="{{ url('/users') }}" class="waves-effect">
                        <i class="bx bx-user-circle"></i>
                        <span key="t-ecommerce">Users</span>
                    </a>
                </li>
                @endcan

                @can('view role')
                    <li>
                        <a href="/role" class=" waves-effect">
                            <i class="bx bx-briefcase-alt-2"></i>
                            <span key="t-projects">Role</span>
                        </a>
                    </li>
                @endcan

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->