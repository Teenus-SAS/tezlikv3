<header id="page-topbar" class="topbar-header">
    <div class="navbar-header">
        <div class="left-bar">
            <div class="d-flex justify-content-center">
                <!-- <div class="d-lg-none">
                    <a href="/cost" class="logo logo-dark">
                        <span class="logo-sm"><img src="/assets/images/favicon/favicon_tezlik.jpg" alt="icon tezlik"></span>
                    </a>
                    <a href="/cost" class="logo logo-light">
                        <span class="logo-sm"><img src="/assets/images/favicon/favicon_tezlik.jpg" alt="Lettstart Admin"></span>
                    </a>
                </div> -->
                <?php
                $logoCompany = $_SESSION['logoCompany'];
                ?>
                <div class="d-none d-lg-block">
                    <a href="/cost" class="logo logo-light">
                        <span class="logo-lg"><img src="<?php echo $logoCompany; ?>" alt="Lettstart Admin"></span>
                    </a>
                </div>
            </div>
            <a class="navbar-toggle collapsed" href="javascript:void(0)" data-toggle="collapse" data-target="#topnav-menu-content" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </a>

        </div>
        <div class="right-bar">
            <div class="dropdown-mega dropdown d-inline-flex ml-0 ml-sm-2">
                <a href="javascript:void(0)" data-toggle="dropdown" id="mega-dropdown" aria-haspopup="true" aria-expanded="false" class="btn header-item">
                    <i class="bx bx-cog bx-spin" style="font-size: 2em;" data-toggle="tooltip" title="Configuración General"></i>
                </a>
                <div class="dropdown-megamenu dropdown-menu" aria-labelledby="mega-dropdown">
                    <div class="row">
                        <div class="col-sm-9">
                            <div class="row" id="nav">
                                <?php if (
                                    $_SESSION['cost_product'] != 0 || $_SESSION['cost_material'] != 0 ||
                                    $_SESSION['cost_machine'] != 0 || $_SESSION['cost_process'] != 0
                                ) { ?>
                                    <div class="col-md-3" id="navCostBasics">
                                        <h5 class="font-size-14 font-weight-600">Básico</h5>
                                        <ul class="list-unstyled megamenu-list">
                                            <?php if ($_SESSION['cost_product'] == 1) { ?>
                                                <li class="aProducts"><a href="/cost/products">Productos</a></li>
                                            <?php } ?>
                                            <?php if ($_SESSION['cost_material'] == 1) { ?>
                                                <li class="aMaterials"><a href="/cost/materials">Materia Prima</a></li>
                                            <?php } ?>
                                            <?php if ($_SESSION['cost_machine'] == 1) { ?>
                                                <li class="aMachines"><a href="/cost/machines">Máquinas</a></li>
                                            <?php } ?>
                                            <?php if ($_SESSION['cost_process'] == 1) { ?>
                                                <li class="aProcess"><a href="/cost/process">Procesos</a></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                <?php } ?>
                                <?php if (
                                    $_SESSION['cost_products_material'] != 0 || $_SESSION['cost_products_process'] != 0 ||
                                    $_SESSION['factory_load'] != 0 || $_SESSION['external_service'] != 0
                                ) { ?>
                                    <div class="col-md-3" id="navCostSetting">
                                        <h5 class="font-size-14 font-weight-600">Configuración</h5>
                                        <ul class="list-unstyled megamenu-list">
                                            <?php if ($_SESSION['cost_products_material'] == 1) { ?>
                                                <li class="aProductsMaterials"><a href="/cost/product-materials">Ficha Técnica Materia Prima</a></li>
                                            <?php } ?>
                                            <?php if ($_SESSION['cost_products_process'] == 1) { ?>
                                                <li class="aProductsProcess"><a href="/cost/product-process">Ficha Técnica Procesos</a></li>
                                            <?php } ?>
                                            <?php if ($_SESSION['factory_load'] == 1) { ?>
                                                <li class="aFactoryLoad"><a href="/cost/factory-load">Carga Fabril</a></li>
                                            <?php } ?>
                                            <?php if ($_SESSION['external_service'] == 1) { ?>
                                                <li class="aServices"><a href="/cost/external-services">Servicios Externos</a></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                <?php } ?>

                                <?php if (
                                    $_SESSION['payroll_load'] != 0 || $_SESSION['expense'] != 0 ||
                                    $_SESSION['expense_distribution'] != 0
                                ) { ?>
                                    <div class="col-md-3" id="navCostGeneral">
                                        <h5 class="font-size-14 font-weight-600">General</h5>
                                        <ul class="list-unstyled megamenu-list">
                                            <?php if ($_SESSION['payroll_load'] == 1) { ?>
                                                <li class="aPayroll"><a href="/cost/payroll">Nómina Producción</a></li>
                                            <?php } ?>
                                            <?php if (
                                                $_SESSION['expense'] == 1 || $_SESSION['cost_multiproduct'] == 1
                                                || $_SESSION['plan_cost_multiproduct'] == 1 || $_SESSION['flag_expense'] != 2
                                            ) { ?>
                                                <li class="aExpenses"><a href="/cost/general-expenses">Asignación Gastos Generales</a></li>
                                            <?php } ?>
                                            <?php if ($_SESSION['expense_distribution'] == 1) { ?>
                                                <?php if ($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) { ?>
                                                    <li class="aExpensesDistribution"><a href="/cost/expenses-distribution">Distribución de Gastos</a></li>
                                                <?php } ?>
                                                <?php if ($_SESSION['flag_expense'] == 2) { ?>
                                                    <li class="aExpensesDistribution"><a href="/cost/expenses-distribution">Recuperación de Gastos</a></li>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php if ($_SESSION['custom_price'] == 1 || $_SESSION['plan_custom_price'] == 1) { ?>
                                                <li class="aCustomPrices"><a href="/cost/price-list">Lista de Precios</a></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                <?php } ?>
                                <?php if ($_SESSION['cost_user'] != 0 || $_SESSION['cost_backup'] != 0) { ?>
                                    <div class="col-md-3" id="navCostAdmin">
                                        <h5 class="font-size-14 font-weight-600">Administrador</h5>
                                        <ul class="list-unstyled megamenu-list">
                                            <?php if ($_SESSION['cost_backup'] == 1) { ?>
                                                <li class="aBackup"><a href="javascript:;">Backup</a></li>
                                            <?php } ?>
                                            <?php if ($_SESSION['cost_user'] == 1) { ?>
                                                <li class="aUsers"><a href="/cost/users">Usuarios</a></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                <?php } ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($_SESSION['quote_payment_method'] != 0 || $_SESSION['quote_company'] != 0 || $_SESSION['quote_contact'] != 0) { ?>
                <div class="dropdown-mega dropdown d-inline-flex ml-0 ml-sm-2" id="navCostQuotesBasics">
                    <a href="javascript:void(0)" data-toggle="dropdown" id="mega-dropdown" aria-haspopup="true" aria-expanded="false" class="btn header-item">
                        <i class="bx bxs-cog bx-spin" style="font-size: 2em;" data-toggle="tooltip" title="Configuración Cotización"></i>
                    </a>
                    <div class="dropdown-megamenu dropdown-menu" aria-labelledby="mega-dropdown" x-placement="bottom-start" style="position: absolute; transform: translate3d(35px, 70px, 0px); top: 0px; left: 0px; will-change: transform;">
                        <div class="row">
                            <div class="col-sm-9">
                                <div class="row">
                                    <div class="col-md-3">
                                        <h5 class="font-size-14 font-weight-600">Cotización</h5>
                                        <ul class="list-unstyled megamenu-list">
                                            <?php if ($_SESSION['quote_payment_method'] == 1) { ?>
                                                <li class="aPaymentMethods"><a href="/cost/payment-methods">Metodos de Pago</a></li>
                                            <?php } ?>
                                            <?php if ($_SESSION['quote_company'] == 1) { ?>
                                                <li class="aCompanies"><a href="/cost/companies">Empresas</a></li>
                                            <?php } ?>
                                            <?php if ($_SESSION['quote_contact'] == 1) { ?>
                                                <li class="aContacts"><a href="/cost/contacts">Contactos</a></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <!-- <div class="d-none d-lg-block ml-0 ml-sm-2 dropdown ">
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
            </div> -->

            <div class="d-none d-lg-inline-flex ml-2">
                <button type="button" data-toggle="fullscreen" class="btn header-item notify-icon" id="full-screen">
                    <i class="bx bx-fullscreen"></i>
                </button>
            </div>

            <div class="d-inline-flex ml-0 ml-sm-2 dropdown">
                <button data-toggle="dropdown" aria-haspopup="true" type="button" id="page-header-notification-dropdown" aria-expanded="false" class="btn header-item notify-icon position-relative">
                    <i class="bx bx-bell bx-tada" data-toggle="tooltip" title="Área de Notificaciones"></i>
                    <span class="badge badge-danger badge-pill notify-icon-badge" id="count"></span>
                </button>
                <div aria-labelledby="page-header-notification-dropdown" class="dropdown-menu-lg dropdown-menu-right p-0 dropdown-menu notify-scrollbar" style="max-height:280px; overflow-y: auto;">

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
                    <img id="hAvatar" src="<?php echo $avatar; ?>" alt="Header Avatar" class="avatar avatar-xs mr-0">
                    <span class="d-none d-xl-inline-block ml-1 userName"><?php if (!empty($_SESSION))
                                                                                echo  "{$_SESSION['name']} {$_SESSION['lastname']}"; ?>
                    </span>
                    <i class="bx bx-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div aria-labelledby="page-header-profile-dropdown" class="dropdown-menu-right dropdown-menu">
                    <a href="/cost/profile" class="dropdown-item">
                        <i class="bx bx-user mr-1"></i> Perfil
                    </a>
                    <!-- <a href="/cost/configuration" class="dropdown-item">
                        <i class="bx bx-wrench mr-1"></i> Configuración
                    </a> -->
                    <div class="dropdown-divider"></div>
                    <a href="javascript: void(0);" class="text-danger dropdown-item logout">
                        <i class="bx bx-log-in mr-1 text-danger"></i> Salir
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- Header End -->