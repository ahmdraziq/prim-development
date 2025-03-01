<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">Main</li>

                <li>
                    <a href="/home" class="waves-effect">
                        <i class="ti-home"></i>
                        {{-- <span class="badge badge-pill badge-primary float-right">2</span> --}}
                        <span>Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('organization.index') }}" class=" waves-effect">
                        <i class="mdi mdi-account-group"></i>
                        <span>Organisasi</span>
                    </a>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fas fa-hand-holding-heart"></i>
                        <span>Derma</span>
                    </a>
                    <ul class="sub-menu mm-collapse" aria-expanded="false">
                        <li>
                            <a href="{{ route('donation.index') }}" class=" waves-effect">
                                <i class="fas fa-user-cog"></i>
                                <span>Urus Derma</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('donate.index') }}" class=" waves-effect">
                                <i class="fas fa-hand-holding-heart"></i>
                                <span>Derma</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('donate.donor_history') }}" class=" waves-effect">
                                <i class="far fa-clock"></i>
                                <span>Sejarah</span>
                            </a>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="far fa-calendar-check"></i>
                                <span>Peringatan</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="true">
                                <li><a href="{{ route('reminder.index') }}">Pengurusan Peringatan</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="{{ route('activity.index') }}" class="waves-effect">
                                <i class="mdi mdi-format-list-checks"></i>
                                <span>Aktiviti</span>
                            </a>
                        </li>
                    </ul>
                </li>



                @role('Jaim')
                <li>
                    <a href="{{ route('jaim.index') }}" class=" waves-effect">
                        <i class="ti-clipboard"></i>
                        <span>JAIM</span>
                    </a>
                </li>
                @endrole


                {{-- 
                @role('Superadmin')
                <li>
                    <a href="{{ route('organization.getAll') }}" class=" waves-effect">
                <i class="mdi mdi-account-group"></i>
                <span>Organisasi (All)</span>
                </a>
                </li>
                @endrole --}}

                @role('Superadmin|Pentadbir|Guru')

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fas fa-school"></i>
                        <span>Sekolah</span>
                    </a>

                    <ul class="sub-menu mm-collapse" aria-expanded="false">
                        @role('Superadmin|Pentadbir')
                        <li>
                            <a href="{{ route('teacher.index') }}" class=" waves-effect">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <span>Guru</span>
                            </a>
                        </li>
                        @endrole


                        @role('Superadmin|Pentadbir|Guru')
                        <li>
                            <a href="{{ route('class.index') }}" class=" waves-effect">
                                <i class="mdi mdi-google-classroom"></i>
                                <span>Kelas</span>
                            </a>
                        </li>
                        @endrole


                        @role('Superadmin|Pentadbir|Guru')
                        <li>
                            <a href="{{ route('student.index') }}" class=" waves-effect">
                                <i class="fas fa-user-graduate"></i>
                                <span>Pelajar</span>
                            </a>
                        </li>

                        @endrole

                        @role('Superadmin|Pentadbir|Guru')
                        <li>
                            <a href="{{ route('parent.index') }}" class=" waves-effect">
                                <i class="fas fa-user-friends"></i>
                                <span>Ibu Bapa</span>
                            </a>
                        </li>

                        @endrole
                    </ul>
                </li>
                @endrole




                @role('Superadmin|Pentadbir|Guru|Penjaga')

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Yuran</span>
                    </a>
                    <ul class="sub-menu mm-collapse" aria-expanded="false">
                        @role('Superadmin|Pentadbir|Guru')

                        <li>
                            <a href="{{ route('fees.report') }}" class=" waves-effect" aria-expanded="true">
                                <i class="fas fa-list-ul"></i>
                                <span>Laporan</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('fees.searchreport') }}" class=" waves-effect" aria-expanded="true">
                                <i class="fas fa-search"></i>
                                <span>Carian Laporan</span>
                            </a>
                        </li>
                        
                        <li>
                            <a href="{{ route('fees.A') }}" class=" waves-effect" aria-expanded="true">
                                <i class="fas fa-user-cog"></i>
                                <span>Kategori A</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('fees.B') }}" class=" waves-effect" aria-expanded="true">
                                <i class="fas fa-user-cog"></i>
                                <span>Kategori B</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('fees.C') }}" class=" waves-effect" aria-expanded="true">
                                <i class="fas fa-user-cog"></i>
                                <span>Kategori C</span>
                            </a>
                        </li>


                        @endrole


                        {{-- <li>
                            <a href="{{ route('fees.report') }}" class=" waves-effect" aria-expanded="true">
                        <i class="fas fa-list-ul"></i>
                        <span>Laporan</span>
                        </a>
                </li> --}}
                {{-- @role('Superadmin|Pentadbir|Guru')
                        
                        <li>
                            <a href="{{ route('category.index') }}" class=" waves-effect" aria-expanded="true">
                <i class="fas fa-list-ul"></i>
                <span>Kategori</span>
                </a>
                </li>
                <li>
                    <a href="{{ route('fees.index') }}" class=" waves-effect" aria-expanded="true">
                        <i class="fas fa-user-cog"></i>
                        <span>Urus</span>
                    </a>
                </li>

                @endrole --}}

                {{-- @role('Superadmin|Ibu|Bapa|Penjaga')
                        <li>
                            <a href="{{ route('parentpay') }}" class=" waves-effect">
                <i class="far fa-credit-card"></i>
                <span>Bayar</span>
                </a>
                </li>
                @endrole --}}

                @role('Superadmin|Penjaga')
                <li>
                    <a href="{{ route('parent.dependent') }}" class=" waves-effect">
                    <i class="fas fa-child"></i>
                    <span>Carian Tanggungan</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('dependent_fees') }}" class=" waves-effect">
                        <i class="far fa-credit-card"></i>
                        <span>Bayar</span>
                    </a>
                </li>
                @endrole
            </ul>
            </li>
            @endrole

            @role('Superadmin|Penjaga')
                
            @endrole

            {{-- @role('Superadmin|Pentadbir|Guru')
                <li>
                    <a href="{{ route('chat-user') }}" class=" waves-effect">
            <i class="mdi mdi-chat-outline"></i>
            <span>Chat</span>
            </a>
            </li>
            @endrole --}}

            {{-- @role('Superadmin|Ibu|Bapa|Penjaga')
                <li>
                    <a href="{{ route('billIndex') }}" class=" waves-effect">
            <i class="mdi"></i>
            <span>Bill Design</span>
            </a>
            </li>
            @endrole --}}


            <!-- <li>
                    <a href="" class=" waves-effect">
                        <i class="ti-clipboard"></i>
                        <span>Derma</span>
                    </a>
                </li> -->

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->