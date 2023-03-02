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

                    <?php if ($_SESSION['price'] == 1 || $_SESSION['price_usd'] == 1) { ?>
                        <li class="nav-item dropdown tools">
                            <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bi bi-cash mr-1"></i> Precios
                                <i class="bx bx-chevron-down"></i>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <?php if ($_SESSION['price'] == 1) { ?>
                                    <a class="dropdown-item" href="/cost/prices">
                                        <i class="bi bi-currency-dollar mr-1"></i>
                                        <span> Lista de Precios COP</span>
                                        <i class="bx bx-chevron-right"></i>
                                    </a>
                                <?php } ?>
                                <?php if ($_SESSION['price_usd'] == 1 && $_SESSION['plan_cost_price_usd'] == 1) { ?>
                                    <a class="dropdown-item" href="/cost/prices-usd">
                                        <i class="bi bi-currency-dollar mr-1"></i>
                                        <span> Lista de Precios USD</span>
                                        <i class="bx bx-chevron-right"></i>
                                    </a>
                                <?php } ?>
                            </div>
                        </li>
                    <?php } ?>

                    <?php if ($_SESSION['analysis_material'] == 1 || $_SESSION['cost_economy_scale'] == 1) { ?>
                        <li class="nav-item dropdown tools">
                            <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bi bi-tools mr-1"></i> Herramientas
                                <i class="bx bx-chevron-down"></i>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <?php if ($_SESSION['analysis_material'] == 1 && $_SESSION['plan_cost_analysis_material'] == 1) { ?>
                                    <a class="dropdown-item" href="/cost/analysis-materials">
                                        <i class="bx bx-tone mr-1"></i> Análisis Materia Prima
                                        <i class="bx bx-chevron-down"></i>
                                    </a>
                                <?php } ?>
                                <?php if ($_SESSION['cost_economy_scale'] == 1 && $_SESSION['plan_cost_economy_sale'] == 1) { ?>
                                    <a class="dropdown-item" href="/cost/economyScale">
                                        <i class="bx bx-dollar-circle mr-1"></i> Economias de Escala
                                        <i class="bx bx-chevron-down"></i>
                                    </a>
                                <?php } ?>
                                <?php if ($_SESSION['cost_multiproduct'] == 1 && $_SESSION['plan_cost_multiproduct'] == 1) { ?>
                                    <a class="dropdown-item" href="/cost/multiproduct">
                                        <i class="bx bx-bible mr-1"></i> Punto de Equilibrio Multiproducto
                                        <i class="bx bx-chevron-down"></i>
                                    </a>
                                <?php } ?>
                            </div>
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

                    <?php if ($_SESSION['support'] == 1 && $_SESSION['plan_cost_support'] == 1) { ?>
                        <li class="nav-item support">
                            <a class="nav-link" href="/cost/support">
                                <i class="bx bxs-bar-chart-alt-2 mr-1"></i>
                                <span> Soporte</span>
                                <i class="bx bx-chevron-right"></i>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </nav>
    </div>
</div>