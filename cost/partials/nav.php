<div class="horizontal-topnav shadow-sm">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg topnav-menu">
            <div id="topnav-menu-content" class="collapse navbar-collapse">
                <ul id="side-menu" class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="index.php" id="topnav-dashboard" role="button">
                            <i class="bx bxs-dashboard mr-1"></i> Dashboards
                            <i class="bx bx-chevron-down"></i>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link dropdown-toggle" href="#" onclick="loadContent('page-content','../app/views/analysis/prices.php')" id="topnav-dashboard" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bxs-customize mr-1"></i> Generar Precios
                            <i class="bx bx-chevron-down"></i>
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" onclick="loadContent('page-content','../app/views/analysis/materials.php')" id="topnav-ui" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-tone mr-1"></i> Análisis Materia Prima
                            <i class="bx bx-chevron-down"></i>
                        </a>
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
                    </li>

                </ul>
            </div>
        </nav>
    </div>
</div>