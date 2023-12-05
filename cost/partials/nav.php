<div class="horizontal-topnav shadow-sm">
    <div class="row container-fluid">
        <nav class="col-sm-10 navbar navbar-expand-lg topnav-menu">
            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="/cost">
                            <i class="bx bxs-dashboard mr-1"></i> Dashboards
                            <i class="bx bx-chevron-right"></i>
                        </a>
                    </li>

                    <?php if (
                        ($_SESSION['price'] == 1 && $_SESSION['plan_cost_price'] == 1) ||
                        ($_SESSION['price_usd'] == 1 && $_SESSION['plan_cost_price_usd'] == 1) ||
                        ($_SESSION['custom_price'] == 1 && $_SESSION['plan_custom_price'] == 1)
                    ) { ?>
                        <li class="nav-item dropdown" id="navPrices">
                            <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bi bi-cash mr-1"></i> Precios
                                <i class="bx bx-chevron-right"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown" style="margin-left: 3px; margin-top:-7px; margin-bottom:-7px">
                                <?php if ($_SESSION['price'] == 1 && $_SESSION['plan_cost_price'] == 1) { ?>
                                    <a class="dropdown-item aPricesCOP" href="/cost/prices">
                                        <span>Lista de Precios <i class="bi bi-currency-dollar mr-1"></i>(COP)</span>
                                    </a>
                                <?php } ?>
                                <?php if ($_SESSION['price_usd'] == 1 && $_SESSION['plan_cost_price_usd'] == 1) { ?>
                                    <a class="dropdown-item aPricesUSD" href="/cost/prices-usd">
                                        <span>Lista de Precios <i class="bi bi-currency-dollar mr-1"></i>(USD)</span>
                                    </a>
                                <?php } ?>
                                <?php if ($_SESSION['custom_price'] == 1 && $_SESSION['plan_custom_price'] == 1) { ?>
                                    <a class="dropdown-item aCustomPrices" href="/cost/custom-prices">
                                        <span><i class="bx bx-dollar-circle mr-1"></i>Lista de Precios Personalizado</span>
                                    </a>
                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>
                    <?php if (
                        $_SESSION['analysis_material'] == 1 && $_SESSION['plan_cost_analysis_material'] == 1 ||
                        $_SESSION['cost_economy_scale'] == 1 && $_SESSION['plan_cost_economy_sale'] == 1 ||
                        $_SESSION['cost_multiproduct'] == 1 && $_SESSION['plan_cost_multiproduct'] == 1
                    ) { ?>
                        <li class="nav-item dropdown" id="navTools">
                            <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bi bi-tools mr-1"></i> Herramientas
                                <i class="bx bx-chevron-right"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <?php if ($_SESSION['analysis_material'] == 1 && $_SESSION['plan_cost_analysis_material'] == 1) { ?>
                                    <li class="dropdown-submenu aAnalysisMaterials" style="margin-left: 3px; margin-top:-7px; margin-bottom:-7px">
                                        <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="bi bi-card-heading mr-1"></i>Análisis Materia Prima
                                            <i class="bx bx-chevron-right"></i>
                                        </a>
                                        <ul class="dropdown-menu1">
                                            <a class="dropdown-item" href="/cost/analysis-materials-product">
                                                <i class="bi bi-graph-up mr-1"></i> Producto
                                            </a>
                                            <a class="dropdown-item" href="/cost/analysis-materials-lot">
                                                <i class="bi bi-graph-down mr-1"></i> Lote
                                            </a>
                                        </ul>
                                    </li>
                                <?php } ?>

                                <?php if ($_SESSION['cost_economy_scale'] == 1 && $_SESSION['plan_cost_economy_sale'] == 1) { ?>
                                    <a class="dropdown-item aEconomyScale" href="/cost/economyScale">
                                        <i class="bx bx-dollar-circle mr-1"></i> Economias de Escala
                                    </a>
                                <?php } ?>
                                <?php if ($_SESSION['cost_multiproduct'] == 1 && $_SESSION['plan_cost_multiproduct'] == 1) { ?>
                                    <a class="dropdown-item aMultiproducts" href="/cost/multiproduct">
                                        <i class="bx bx-bible mr-1"></i> Punto de Equilibrio Multiproducto
                                    </a>
                                <?php } ?>
                                <?php if ($_SESSION['simulator'] == 1 && $_SESSION['plan_cost_simulator'] == 1) { ?>
                                    <a class="dropdown-item aSimulator" href="/cost/simulator">
                                        <i class="bi bi-gear-wide-connected mr-1"></i> Simulador
                                    </a>
                                <?php } ?>
                                <?php if ($_SESSION['historical'] == 1 && $_SESSION['plan_cost_historical'] == 1) { ?>
                                    <a class="dropdown-item aHistorical" href="/cost/historical">
                                        <i class="bi bi-clock-history mr-1"></i> Historico
                                    </a>
                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>
                    <?php if ($_SESSION['quotes'] == 1 && $_SESSION['plan_cost_quote'] == 1) { ?>
                        <li class="nav-item aQuotes">
                            <a class="nav-link" href="/cost/quotes">
                                <i class="bx bx-columns mr-1"></i> Cotizar
                                <i class="bx bx-chevron-right"></i>
                            </a>
                        </li>
                    <?php } ?>

                    <?php if ($_SESSION['support'] == 1 && $_SESSION['plan_cost_support'] == 1) { ?>
                        <li class="nav-item aSupport">
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
        <div class="justify-logo">
            <div class="col-sm-2 d-none d-lg-block form-inline justify-content-sm-end">
                <a href="/cost" class="mt-1 logo logo-dark">
                    <span><img src="/assets/images/logo/logo_tezlik1.png" alt="Logo tezlik" style="width: 80px;margin-top: 10px;"></span>
                </a>
            </div>
        </div>
    </div>
</div>