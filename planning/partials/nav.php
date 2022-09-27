<div class="horizontal-topnav shadow-sm">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg topnav-menu">
            <div id="topnav-menu-content" class="collapse navbar-collapse">
                <ul id="side-menu" class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="javascript:history.go(0);" id="topnav-dashboard" role="button">
                            <i class="bx bxs-dashboard mr-1"></i> Dashboards
                            <i class="bx bx-chevron-down"></i>
                        </a>
                    </li>
                    <?php if ($_SESSION['aInventory'] == 1) { ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" onclick="loadContent('page-content','../planning/views/inventory/inventory.php')" id="topnav-ui" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-tone mr-1"></i> Inventarios
                                <i class="bx bx-chevron-down"></i>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if ($_SESSION['aOrder'] == 1) { ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" onclick="loadContent('page-content','../planning/views/orders/orders.php')" id="topnav-ui" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-tone mr-1"></i> Pedidos
                                <i class="bx bx-chevron-down"></i>
                            </a>
                        </li>
                    <?php } ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="topnav-component" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-layer mr-1"></i> Programa
                            <i class="bx bx-chevron-down"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-components">
                            <div class="dropdown">
                                <a href="javascript:void(0);" onclick="loadContent('page-content','../planning/views/program/consolidated/consolidated.php')" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                    <i class="bx bxs-customize mr-1"></i>
                                    <span> Consolidado</span>
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                                <?php if ($_SESSION['aProgramming'] == 1) { ?>
                                    <a href="javascript:void(0);" onclick="loadContent('page-content','../planning/views/program/programming/programming.php')" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                        <i class="bx bxs-customize mr-1"></i>
                                        <span> Programación</span>
                                        <i class="bx bx-chevron-right"></i>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    </li>
                    <?php if ($_SESSION['aPlanLoad'] == 1) { ?>
                        <li class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#" onclick="loadContent('page-content','../app/views/analysis/prices.php')" id="topnav-dashboard" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bx bxs-customize mr-1"></i> Cargues
                                <i class="bx bx-chevron-down"></i>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if ($_SESSION['aExplosionOfMaterial'] == 1) { ?>
                        <li class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#" onclick="loadContent('page-content','../app/views/analysis/prices.php')" id="topnav-dashboard" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bx bxs-customize mr-1"></i> Explosión de Materiales
                                <i class="bx bx-chevron-down"></i>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if ($_SESSION['aOffice'] == 1) { ?>
                        <li class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#" onclick="loadContent('page-content','../app/views/analysis/prices.php')" id="topnav-dashboard" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bx bxs-customize mr-1"></i> Despachos
                                <i class="bx bx-chevron-down"></i>
                            </a>
                        </li>
                    <?php } ?>
                    <!--
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="topnav-component" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-cube-alt mr-1"></i> Reportes
                            <i class="bx bx-chevron-down"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-components">
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                    <span> Reporte 1</span>
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-Auth">
                                    <a class="dropdown-item" href="auth-login.html">Login </a>
                                    <a class="dropdown-item" href="auth-signup.html"> Register </a>
                                    <a class="dropdown-item" href="auth-recover.html"> Recover Password </a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                    <span> Reporte 2 </span>
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-forms">
                                    <a class="dropdown-item" href="auth-login-basic.html"> Login </a>
                                    <a class="dropdown-item" href="auth-signup-basic.html"> Register </a>
                                    <a class="dropdown-item" href="auth-recover-basic.html"> Recover Password</a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                    <span> Reporte 3 </span>
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-forms">
                                    <a class="dropdown-item" href="auth-login-full.html"> Login </a>
                                    <a class="dropdown-item" href="auth-signup-full.html"> Register </a>
                                    <a class="dropdown-item" href="auth-recover-full.html"> Recover Password</a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                    <span> Reporte 4 </span>
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-forms">
                                    <a class="dropdown-item" href="auth-lockscreen.html"> Lock Screen </a>
                                    <a class="dropdown-item" href="auth-confirmation.html"> Confirmation Screen </a>
                                    <a class="dropdown-item" href="auth-400.html"> 400 </a>
                                    <a class="dropdown-item" href="auth-404.html"> 404 </a>
                                    <a class="dropdown-item" href="auth-500.html"> 500 </a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="topnav-component" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-layer mr-1"></i> Herramientas
                            <i class="bx bx-chevron-down"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-components">
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                    <i class="bx bxs-layer-plus mr-1"></i>
                                    <span> Calculadora Horas Extras</span>
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-advanced UI">
                                    <a class="dropdown-item" href="advanced-confirmation-box.html"> Confirmation Box </a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                    <i class="bx bxs-eraser mr-1"></i>
                                    <span> Conversor de Unidades </span>
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-forms">
                                    <a class="dropdown-item" href="forms-elements.html"> General Elements </a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a href="javascript:void(0);" onclick="loadContent('page-content','../app/views/tutorials/tutorials.php')" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                    <i class="bx bx-table mr-1"></i>
                                    <span> Tutoriales </span>
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                            </div>
                            <div class="dropdown">
                                <a href="javascript:void(0);" onclick="loadContent('page-content','../app/views/support/emailSupport.php')" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                    <i class="bx bxs-bar-chart-alt-2 mr-1"></i>
                                    <span> Soporte</span>
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    </li> -->
                </ul>
            </div>
        </nav>
    </div>
</div>