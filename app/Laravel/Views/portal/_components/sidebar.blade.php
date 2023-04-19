<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="index.html" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{asset('assets/images/logo-sm.png')}}" alt="" height="22" />
            </span>
            <span class="logo-lg">
                <img src="{{asset('assets/images/logo-dark.png')}}" alt="" height="17" />
            </span>
        </a>
        <!-- Light Logo-->
        <a href="index.html" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{asset('assets/images/logo-sm.png')}}" alt="" height="22" />
            </span>
            <span class="logo-lg">
                <img src="{{asset('assets/images/logo-light.png')}}" alt="" height="17" />
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{route('portal.index')}}"> <i class="ri-honour-line"></i> <span data-key="t-widgets">Dashboard</span> </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{route('portal.index')}}"> <i class="ri-honour-line"></i> <span data-key="t-widgets">Revenue Collection Officer Module</span> </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{route('portal.index')}}"> <i class="ri-honour-line"></i> <span data-key="t-widgets">eReceipt Issued &amp; Deposit Inquiry</span> </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarTables" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarTables">
                        <i class="ri-layout-grid-line"></i> <span data-key="t-tables">Reports</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarTables">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-basic-tables">Batch Control Sheet A</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-grid-js">Batch Control Sheet B</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-list-js">Deposited Collections</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-list-js">Undeposited Collections</a>
                            </li>

                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-list-js">Statement of Collections (ROR)</a>
                            </li>

                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-list-js">Statement of Collections (OR)</a>
                            </li>
                        </ul>
                    </div>
                </li>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->