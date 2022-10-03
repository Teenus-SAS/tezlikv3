<?php
if (!isset($_SESSION))
    session_start();
?>
<!-- Begin Header -->
<header id="page-topbar" class="topbar-header">
    <div class="navbar-header">
        <div class="left-bar">
            <div class="navbar-brand-box">
                <a href="/admin" class="logo logo-dark">
                    <span class="logo-sm"><img src="/assets/images/favicon/favicon_tezlik.jpg" alt="icon tezlik"></span>
                    <span class="logo-lg"><img src="/assets/images/logo/logo_tezlik.png" alt="Logo tezlik"></span>
                </a>
                <a href="/admin" class="logo logo-light">
                    <span class="logo-sm"><img src="/assets/images/favicon/favicon_tezlik.jpg" alt="Lettstart Admin"></span>
                    <span class="logo-lg"><img src="/assets/images/logo/logo_tezlik.png" alt="Lettstart Admin"></span>
                </a>
            </div>
            <a class="navbar-toggle collapsed" href="javascript:void(0)" data-toggle="collapse" data-target="#topnav-menu-content" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </a>

        </div>
        <div class="right-bar">
            <!-- <form class="app-search mr-2 d-none d-lg-block">
                <div class="search-box position-relative">
                    <input type="text" placeholder="Search..." class="form-control">
                    <span class="bx bx-search"></span>
                </div>
            </form> -->
            <div class="dropdown-mega dropdown d-none d-lg-block ml-2">
                <a href="javascript:void(0)" data-toggle="dropdown" id="mega-dropdown" aria-haspopup="true" aria-expanded="false" class="btn header-item">
                    <i class="bx bx-cog bx-spin" style="font-size: 2em;"></i>
                </a>
                <div class="dropdown-megamenu dropdown-menu" aria-labelledby="mega-dropdown">
                    <div class="row">
                        <div class="col-sm-9">
                            <div class="row" id="nav">
                                <!-- <div class="col-md-3" id="navBasics">
                                    <h5 class="font-size-14 font-weight-600">Básico</h5>
                                    <ul class="list-unstyled megamenu-list">
                                        <li class="createProducts"><a href="">Creación Productos</a></li>
                                        <li class="createMaterials"><a href="">Creación Materia Prima</a></li>
                                        <li class="createMachines"><a href="">Creación Máquinas</a></li>
                                        <li class="createProcess"><a href="">Creación Procesos</a></li>
                                    </ul>
                                </div>
                                <div class="col-md-3" id="navSetting">
                                    <h5 class="font-size-14 font-weight-600">Configuración</h5>
                                    <ul class="list-unstyled megamenu-list">
                                        <li class="productsMaterials"><a href="">Ficha Técnica Materia Prima</a></li>
                                        <li class="productsProcess"><a href="">Ficha Técnica Procesos</a></li>
                                        <li class="factoryLoad"><a href="">Carga Fabril</a></li>
                                        <li class="servicesExternal"><a href="">Servicios Externos</a></li>
                                    </ul>
                                </div>
                                <div class="col-md-3" id="navGeneral">
                                    <h5 class="font-size-14 font-weight-600">General</h5>
                                    <ul class="list-unstyled megamenu-list">
                                        <li class="payroll"><a href="">Carga Nómina</a></li>
                                        <li class="generalExpenses"><a href="">Asignación Gastos Generales</a></li>
                                        <li class="distributionExpenses"><a href="">Distribución de Gastos</a></li>
                                    </ul>
                                </div> -->
                                <div class="col-md-3" id="navAdmin">
                                    <h5 class="font-size-14 font-weight-600">Administrador</h5>
                                    <ul class="list-unstyled megamenu-list">
                                        <li class="users"><a href="/admin/users">Usuarios</a></li>
                                        <li class="companies"><a href="/admin/companies">pruebas empresas</a></li>
                                        <li class="licenses"><a href="/admin/companies-licences">pruebas licencias</a></li>
                                        <!-- <li><a href="javascript:void(0);">Compact Sidebar</a></li>
                                        <li><a href="javascript:void(0);">Horizontal layout</a></li>
                                        <li><a href="javascript:void(0);">Maintenance</a></li>
                                        <li><a href="javascript:void(0);">Coming Soon</a></li>
                                        <li><a href="javascript:void(0);">Timeline</a></li>
                                        <li><a href="javascript:void(0);">FAQs</a></li> -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-sm-4">
                            <div class="row align-items-center">
                                <div class="col-sm-6">
                                    <h5 class="font-size-14 font-weight-600">UI Components</h5>
                                    <ul class="list-unstyled megamenu-list">
                                        <li><a href="javascript:void(0);">Lightbox</a></li>
                                        <li><a href="javascript:void(0);">Range Slider</a></li>
                                        <li><a href="javascript:void(0);">Sweet Alert</a></li>
                                        <li><a href="javascript:void(0);">Rating</a></li>
                                        <li><a href="javascript:void(0);">Forms</a></li>
                                        <li><a href="javascript:void(0);">Tables</a></li>
                                        <li><a href="javascript:void(0);">Charts</a></li>
                                    </ul>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mega-dd-slider">
                                        <div class="owl-carousel">
                                            <div class="item">
                                                <img src="assets/images/megamenu-img.svg" alt="Lettstart Admin" class="img-fluid mx-auto d-block">
                                            </div>
                                            <div class="item">
                                                <img src="assets/images/megamenu-img2.svg" alt="Lettstart Admin" class="img-fluid mx-auto d-block">
                                            </div>
                                            <div class="item">
                                                <img src="assets/images/megamenu-img3.svg" alt="Lettstart Admin" class="img-fluid mx-auto d-block">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
            <div class="d-inline-flex ml-0 ml-sm-2 d-lg-none dropdown">
                <button data-toggle="dropdown" aria-haspopup="true" type="button" id="page-header-search-dropdown" aria-expanded="false" class="btn header-item notify-icon">
                    <i class="bx bx-search"></i>
                </button>
                <div aria-labelledby="page-header-search-dropdown" class="dropdown-menu-lg dropdown-menu-right p-0 dropdown-menu">
                    <form class="p-3">
                        <div class="search-box">
                            <div class="position-relative">
                                <input type="text" placeholder="Search..." class="form-control form-control-sm">
                                <i class="bx bx-search icon"></i>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- <div class="d-inline-flex ml-0 ml-sm-2 dropdown">
                <button aria-haspopup="true" data-toggle="dropdown" type="button" id="page-header-country-dropdown" aria-expanded="false" class="btn header-item">
                    <img src="assets/images/flags/us.svg" class="mh-16" alt="USA">
                    <span class="ml-2 d-none d-sm-inline-block">EN</span>
                </button>
                <div aria-labelledby="page-header-country-dropdown" id="countries" class="dropdown-menu-right dropdown-menu">
                    <a href="javascript:void(0);" class="dropdown-item">
                        <img class="mr-1 mh-12" src="assets/images/flags/us.svg" alt="USA">
                        <span class="align-middle" data-lang="en">USA</span>
                    </a>
                    <a href="javascript:void(0);" class="dropdown-item">
                        <img class="mr-1 mh-12" src="assets/images/flags/ge.svg" alt="German">
                        <span class="align-middle" data-lang="ge">German</span>
                    </a>
                    <a href="javascript:void(0);" class="dropdown-item">
                        <img class="mr-1 mh-12" src="assets/images/flags/ru.svg" alt="Russia">
                        <span class="align-middle" data-lang="ru">Russia</span>
                    </a>
                    <a href="javascript:void(0);" class="dropdown-item">
                        <img class="mr-1 mh-12" src="assets/images/flags/in.svg" alt="India">
                        <span class="align-middle" data-lang="in">India</span>
                    </a>
                </div>
            </div> -->
            <!-- <div class="d-none d-lg-inline-flex ml-2 dropdown">
                <button data-toggle="dropdown" aria-haspopup="true" type="button" id="page-header-app-dropdown" aria-expanded="false" class="btn header-item notify-icon">
                    <i class="bx bx-customize"></i>
                </button>
                <div aria-labelledby="page-header-app-dropdown" class="dropdown-menu-lg dropdown-menu-right dropdown-menu">
                    <div class="px-lg-2">
                        <div class="row no-gutters">
                            <div class="col">
                                <a href="javascript: void(0);" class="dropdown-icon-item">
                                    <img src="assets/images/brands/github.png" alt="Github">
                                    <span>GitHub</span>
                                </a>
                            </div>
                            <div class="col">
                                <a href="javascript: void(0);" class="dropdown-icon-item">
                                    <img src="assets/images/brands/bitbucket.png" alt="bitbucket">
                                    <span>Bitbucket</span>
                                </a>
                            </div>
                            <div class="col">
                                <a href="javascript: void(0);" class="dropdown-icon-item">
                                    <img src="assets/images/brands/dribbble.png" alt="dribbble">
                                    <span>Dribbble</span>
                                </a>
                            </div>
                        </div>
                        <div class="row no-gutters">
                            <div class="col">
                                <a href="javascript: void(0);" class="dropdown-icon-item">
                                    <img src="assets/images/brands/dropbox.png" alt="dropbox">
                                    <span>Dropbox</span>
                                </a>
                            </div>
                            <div class="col">
                                <a href="javascript: void(0);" class="dropdown-icon-item">
                                    <img src="assets/images/brands/mail_chimp.png" alt="mail_chimp">
                                    <span>Mail Chimp</span>
                                </a>
                            </div>
                            <div class="col">
                                <a href="javascript: void(0);" class="dropdown-icon-item">
                                    <img src="assets/images/brands/slack.png" alt="slack">
                                    <span>Slack</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
            <div class="d-none d-lg-inline-flex ml-2">
                <button type="button" data-toggle="fullscreen" class="btn header-item notify-icon" id="full-screen">
                    <i class="bx bx-fullscreen"></i>
                </button>
            </div>
            <div class="d-inline-flex ml-0 ml-sm-2 dropdown">
                <button data-toggle="dropdown" aria-haspopup="true" type="button" id="page-header-notification-dropdown" aria-expanded="false" class="btn header-item notify-icon position-relative">
                    <i class="bx bx-bell bx-tada"></i>
                    <span class="badge badge-danger badge-pill notify-icon-badge">3</span>
                </button>
                <div aria-labelledby="page-header-notification-dropdown" class="dropdown-menu-lg dropdown-menu-right p-0 dropdown-menu">
                    <div class="notify-title p-3">
                        <h5 class="font-size-14 font-weight-600 mb-0">
                            <span>Notificationes</span>
                            <a class="text-primary" href="javascript: void(0);">
                                <small>Limpiar Todo</small>
                            </a>
                        </h5>
                    </div>
                    <div class="notify-scroll">
                        <div class="scroll-content" id="notify-scrollbar">
                            <div class="scroll-content">
                                <a href="javascript:void(0);" class="dropdown-item notification-item">
                                    <div class="media">
                                        <div class="avatar avatar-xs bg-primary">
                                            <i class="bx bx-user-plus"></i>
                                        </div>
                                        <p class="media-body">
                                            New user registered.
                                            <small class="text-muted">5 hours ago</small>
                                        </p>
                                    </div>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item notification-item">
                                    <div class="media">
                                        <div class="avatar avatar-xs">
                                            <img alt="Lettstart Admin" class="img-fluid rounded-circle" src="/assets/images/users/avatar-1.jpg">
                                        </div>
                                        <p class="media-body">
                                            John likes your photo
                                            <small class="text-muted">5 hours ago</small>
                                        </p>
                                    </div>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item notification-item">
                                    <div class="media">
                                        <div class="avatar avatar-xs">
                                            <img alt="Lettstart Admin" class="img-fluid rounded-circle" src="/assets/images/users/avatar-2.jpg">
                                        </div>
                                        <p class="media-body">
                                            Johnson
                                            <small class="text-muted">Wow! admin looks good</small>
                                        </p>
                                    </div>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item notification-item">
                                    <div class="media">
                                        <div class="avatar avatar-xs bg-danger">
                                            <i class="bx bx-server"></i>
                                        </div>
                                        <p class="media-body">
                                            Server getting down
                                            <small class="text-muted">1 min ago</small>
                                        </p>
                                    </div>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item notification-item">
                                    <div class="media">
                                        <div class="avatar avatar-xs bg-info">
                                            <i class="bx bx-tag"></i>
                                        </div>
                                        <p class="media-body">
                                            Someone tag you
                                            <small class="text-muted">2 hours ago</small>
                                        </p>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="notify-all">
                            <a href="javascript: void(0);" class="text-primary text-center p-3">
                                <small>Mostrar todo</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-inline-flex ml-0 ml-sm-2 dropdown">
                <button data-toggle="dropdown" aria-haspopup="true" type="button" id="page-header-profile-dropdown" aria-expanded="false" class="btn header-item">
                    <?php
                    if (empty($_SESSION['avatar']))
                        $avatar = "/assets/images/users/empty_user.png";
                    else
                        $avatar = $_SESSION['avatar'];
                    ?>

                    <img src="<?php echo $avatar; ?>" alt="Header Avatar" class="avatar avatar-xs mr-0">
                    <span class="d-none d-xl-inline-block ml-1 userName"><?php echo "{$_SESSION['name']} {$_SESSION['lastname']}"; ?></span>
                    <i class="bx bx-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div aria-labelledby="page-header-profile-dropdown" class="dropdown-menu-right dropdown-menu">
                    <a href="/admin/profile" class="dropdown-item">
                        <i class="bx bx-user mr-1"></i> Perfil
                    </a>
                    <a href="javascript: void(0);" onclick="loadContent('page-content','views/perfil/configuracion.php')" class="dropdown-item">
                        <i class="bx bx-wrench mr-1"></i> Configuración
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="javascript: void(0);" class="text-danger dropdown-item logout">
                        <i class="bx bx-log-in mr-1 text-danger"></i> Salir
                    </a>
                </div>
            </div>
            <!-- <div class="d-inline-flex">
                <button type="button" id="layout" class="btn header-item notify-icon">
                    <i class="bx bx-cog bx-spin"></i>
                </button>
            </div> -->
        </div>
    </div>
</header>
<!-- Header End -->

<?php include_once dirname(__DIR__) . '../../global/partials/scriptsJS.php'; ?>