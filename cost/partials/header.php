<header id="page-topbar" class="topbar-header">
    <div class="navbar-header">
        <div class="left-bar">
            <div class="d-flex justify-content-center">
                <?php
                $logoCompany = $_SESSION['logoCompany'];
                ?>
                <div class="image_width">
                    <img src="<?php echo $logoCompany; ?>" alt="">
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
                                    $_SESSION['cost_product'] == 0 && $_SESSION['cost_material'] == 0 &&
                                    $_SESSION['cost_machine'] == 0 && $_SESSION['cost_process'] == 0
                                ) { ?>
                                    <div class="col-md-3" id="navCostBasics" style="display: none;">
                                    <?php } else { ?>
                                        <div class="col-md-3" id="navCostBasics">
                                        <?php } ?>
                                        <h5 class="font-size-14 font-weight-600">Maestros</h5>
                                        <ul class="list-unstyled megamenu-list">
                                            <?php if ($_SESSION['cost_product'] == 1) { ?>
                                                <li class="aProducts">
                                                <?php } else { ?>
                                                <li class="aProducts" style="display: none;">
                                                <?php } ?>
                                                <i class='bi bi-box-fill mr-1'></i>
                                                <a href="/cost/products">Productos</a>
                                                </li>
                                                <?php if ($_SESSION['cost_material'] == 1) { ?>
                                                    <li class="aMaterials">
                                                    <?php } else { ?>
                                                    <li class="aMaterials" style="display: none;">
                                                    <?php } ?>
                                                    <i class='bi bi-gear mr-1'></i>
                                                    <a href="/cost/materials">Materia Prima</a>
                                                    </li>
                                                    <?php if ($_SESSION['cost_machine'] == 1) { ?>
                                                        <li class="aMachines">
                                                        <?php } else { ?>
                                                        <li class="aMachines" style="display: none;">
                                                        <?php } ?>
                                                        <i class="bi bi-gear-wide mr-1"></i>
                                                        <a href="/cost/machines">Máquinas</a>
                                                        </li>
                                                        <?php if ($_SESSION['cost_process'] == 1) { ?>
                                                            <li class="aProcess">
                                                            <?php } else { ?>
                                                            <li class="aProcess" style="display: none;">
                                                            <?php } ?>
                                                            <i class="bi bi-diagram-2 mr-1"></i>
                                                            <a href="/cost/process">Procesos</a>
                                                            </li>
                                        </ul>
                                        </div>
                                        <?php if (
                                            $_SESSION['cost_products_material'] == 0 && $_SESSION['factory_load'] == 0
                                        ) { ?>
                                            <div class="col-md-3" id="navCostSetting" style="display: none;">
                                            <?php } else { ?>
                                                <div class="col-md-3" id="navCostSetting">
                                                <?php } ?>
                                                <h5 class="font-size-14 font-weight-600">Configuración</h5>
                                                <ul class="list-unstyled megamenu-list">
                                                    <?php if ($_SESSION['cost_products_material'] == 1) { ?>
                                                        <li class="aProductsMaterials">
                                                        <?php } else { ?>
                                                        <li class="aProductsMaterials" style="display: none;">
                                                        <?php } ?>
                                                        <i class="bi bi-file-text mr-1"></i>
                                                        <a href="/cost/product-materials">Ficha Técnica Productos</a>
                                                        </li>
                                                        <?php if ($_SESSION['external_service'] == 1) { ?>
                                                            <li class="aGServices">
                                                            <?php } else { ?>
                                                            <li class="aGServices" style="display: none;">
                                                            <?php } ?>
                                                            <i class="bi bi-list"></i>
                                                            <a href="/cost/external-services">Servicios</a>
                                                            </li>
                                                            <?php if ($_SESSION['factory_load'] == 1) { ?>
                                                                <li class="aFactoryLoad">
                                                                <?php } else { ?>
                                                                <li class="aFactoryLoad" style="display: none;">
                                                                <?php } ?>
                                                                <i class="bi bi-gear-wide-connected mr-1"></i>
                                                                <a href="/cost/factory-load">Carga Fabril</a>
                                                                </li>
                                                                <?php if ($_SESSION['custom_price'] == 1 || $_SESSION['plan_custom_price'] == 1) { ?>
                                                                    <li class="aCustomPrices">
                                                                    <?php } else { ?>
                                                                    <li class="aCustomPrices" style="display: none;">
                                                                    <?php } ?>
                                                                    <i class="bi bi-list"></i>
                                                                    <a href="/cost/price-list">Lista de Precios</a>
                                                                    </li>
                                                </ul>
                                                </div>

                                                <?php if (
                                                    $_SESSION['payroll_load'] == 0 && $_SESSION['expense'] == 0 && $_SESSION['expense_distribution'] == 0
                                                ) { ?>
                                                    <div class="col-md-3" id="navCostGeneral" style="display: none;">
                                                    <?php } else { ?>
                                                        <div class="col-md-3" id="navCostGeneral">
                                                        <?php } ?>
                                                        <h5 class="font-size-14 font-weight-600">General</h5>
                                                        <ul class="list-unstyled megamenu-list">
                                                            <?php if ($_SESSION['payroll_load'] == 1) { ?>
                                                                <li class="aPayroll">
                                                                <?php } else { ?>
                                                                <li class="aPayroll" style="display: none;">
                                                                <?php } ?>
                                                                <i class="bi bi-people-fill mr-1"></i>
                                                                <a href="/cost/payroll">Nómina Producción</a>
                                                                </li>
                                                                <?php if (
                                                                    $_SESSION['expense'] == 1
                                                                    || $_SESSION['expense_distribution'] == 1
                                                                    || $_SESSION['cost_multiproduct'] == 1
                                                                    || $_SESSION['plan_cost_multiproduct'] == 1
                                                                    || ($_SESSION['production_center'] == 1
                                                                        && $_SESSION['flag_production_center'] == 1)
                                                                ) { ?>
                                                                    <li class="aExpenses">
                                                                    <?php } else { ?>
                                                                    <li class="aExpenses" style="display: none;">
                                                                    <?php } ?>
                                                                    <i class="bi bi-currency-dollar mr-1"></i>
                                                                    <a href="/cost/general-expenses">Gastos Generales</a>
                                                                    </li>
                                                        </ul>
                                                        </div>

                                                        <?php if ($_SESSION['cost_user'] == 0 && $_SESSION['cost_backup'] == 0) { ?>
                                                            <div class="col-md-3" id="navCostAdmin" style="display: none;">
                                                            <?php } else { ?>
                                                                <div class="col-md-3" id="navCostAdmin">
                                                                <?php } ?>
                                                                <h5 class="font-size-14 font-weight-600">Administrador</h5>
                                                                <ul class="list-unstyled megamenu-list">
                                                                    <?php if ($_SESSION['cost_backup'] == 1) { ?>
                                                                        <li class="aBackup">
                                                                        <?php } else { ?>
                                                                        <li class="aBackup" style="display: none;">
                                                                        <?php } ?>
                                                                        <i class="bi bi-shield-lock mr-1"></i>
                                                                        <a href="javascript:;">Backup</a>
                                                                        </li>
                                                                        <?php if ($_SESSION['cost_user'] == 1) { ?>
                                                                            <li class="aUsers">
                                                                            <?php } else { ?>
                                                                            <li class="aUsers" style="display: none;">
                                                                            <?php } ?>
                                                                            <i class="bi bi-lock-fill mr-1"></i>
                                                                            <a href="/cost/users">Usuarios y Accesos</a>
                                                                            </li>
                                                                </ul>
                                                                </div>
                                                            </div>
                                                    </div>
                                            </div>
                                    </div>
                            </div>
                            <?php if ($_SESSION['quote_payment_method'] == 0 && $_SESSION['quote_company'] == 0 && $_SESSION['quote_contact'] == 0) { ?>
                                <div class="dropdown-mega dropdown d-inline-flex ml-0 ml-sm-2" id="navCostQuotesBasics" style="display: none;">
                                <?php } else { ?>
                                    <div class="dropdown-mega dropdown d-inline-flex ml-0 ml-sm-2" id="navCostQuotesBasics">
                                    <?php } ?>
                                    <a href="javascript:void(0)" data-toggle="dropdown" id="mega-dropdown" aria-haspopup="true" aria-expanded="false" class="btn header-item">
                                        <i class="bx bx-reset bx-flashing" style="font-size: 2em;" data-toggle="tooltip" title="Configuración Cotización"></i>
                                    </a>
                                    <div class="dropdown-megamenu dropdown-menu" aria-labelledby="mega-dropdown" x-placement="bottom-start" style="position: absolute; transform: translate3d(35px, 70px, 0px); top: 0px; left: 0px; will-change: transform;">
                                        <div class="row">
                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <h5 class="font-size-14 font-weight-600">Cotización</h5>
                                                        <ul class="list-unstyled megamenu-list">
                                                            <?php if ($_SESSION['quote_payment_method'] == 1) { ?>
                                                                <li class="aPaymentMethods">
                                                                <?php } else { ?>
                                                                <li class="aPaymentMethods" style="display: none;">
                                                                <?php } ?>
                                                                <a href="/cost/payment-methods">Metodos de Pago</a>
                                                                </li>
                                                                <?php if ($_SESSION['quote_company'] == 1) { ?>
                                                                    <li class="aCompanies">
                                                                    <?php } else { ?>
                                                                    <li class="aCompanies" style="display: none;">
                                                                    <?php } ?>
                                                                    <a href="/cost/companies">Empresas</a>
                                                                    </li>
                                                                    <?php if ($_SESSION['quote_contact'] == 1) { ?>
                                                                        <li class="aContacts">
                                                                        <?php } else { ?>
                                                                        <li class="aContacts" style="display: none;">
                                                                        <?php } ?>
                                                                        <a href="/cost/contacts">Contactos</a>
                                                                        </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>


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
                                            <div class="user-info" style="display: inline-block;">
                                                <span class="d-none d-xl-inline-block ml-1 userName" style="display: block;"><?php if (!empty($_SESSION))
                                                                                                                                    echo  "{$_SESSION['name']} {$_SESSION['lastname']}"; ?></span>
                                                <br>
                                                <span class="role" style="font-size: 0.8rem;"><?php if (!empty($_SESSION))
                                                                                                    echo  $_SESSION['position']; ?></span>
                                            </div>
                                            <i class="bx bx-chevron-down d-none d-xl-inline-block"></i>
                                        </button>
                                        <div aria-labelledby="page-header-profile-dropdown" class="dropdown-menu-right dropdown-menu">
                                            <a href="/cost/profile" class="dropdown-item">
                                                <i class="bx bx-user mr-1"></i> Perfil
                                            </a>
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