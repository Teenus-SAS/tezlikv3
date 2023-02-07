<div class="horizontal-topnav shadow-sm">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg topnav-menu">
            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="/cost">
                            <i class="bx bxs-dashboard mr-1"></i> Dashboards
                            <i class="bx bx-chevron-down"></i>
                        </a>
                    </li>
                    <?php if ($_SESSION['price'] == 1) { ?>
                        <li class="nav-item prices">
                            <a class="nav-link" href="/cost/prices">
                                <i class="bx bxs-customize mr-1"></i> Lista de Precios
                                <i class="bx bx-chevron-down"></i>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if ($_SESSION['analysis_material'] == 1 && $_SESSION['plan_cost_analysis_material'] == 1) { ?>
                        <li class="nav-item analysisMaterials">
                            <a class="nav-link" href="/cost/analysis-materials">
                                <i class="bx bx-tone mr-1"></i> Análisis Materia Prima
                                <i class="bx bx-chevron-down"></i>
                            </a>
                        </li>
                    <?php } ?>

                    <?php if ($_SESSION['cost_economy_scale'] == 1 && $_SESSION['plan_cost_economy_sale'] == 1) { ?>
                        <li class="nav-item economyScale">
                            <a class="nav-link" href="/cost/economyScale">
                                <i class="bx bx-dollar-circle mr-1"></i> Economias de Escala
                                <i class="bx bx-chevron-down"></i>
                            </a>
                        </li>
                    <?php } ?>

                    <?php if ($_SESSION['quotes'] == 1 && $_SESSION['plan_cost_quote'] == 1) { ?>
                        <li class="nav-item quotes">
                            <a class="nav-link" href="/cost/quotes">
                                <i class="bx bx-columns mr-1"></i> Cotizar
                                <i class="bx bx-chevron-down"></i>
                            </a>
                        </li>
                    <?php } ?>

                    <?php if ($_SESSION['tool'] == 1 && $_SESSION['plan_cost_tool'] == 1) { ?>
                        <li class="nav-item dropdown tools">
                            <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-layer mr-1"></i> Herramientas
                                <i class="bx bx-chevron-down"></i>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="/cost/support">
                                    <i class="bx bxs-bar-chart-alt-2 mr-1"></i>
                                    <span> Soporte</span>
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                            </div>
                        </li>
                    <?php } ?>

                </ul>
            </div>
        </nav>
    </div>
</div>