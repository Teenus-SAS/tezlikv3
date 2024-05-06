<div class="horizontal-topnav shadow-sm">
    <div class="row container-fluid">
        <nav class="col-sm-10 navbar navbar-expand-lg topnav-menu">
            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="/cost">
                            <i class="bi bi-speedometer mr-1"></i> Dashboards
                            <i class="bx bx-chevron-right"></i>
                        </a>
                    </li>
                    <!-- $_SESSION['price_usd'] -->
                    <?php if (
                        ($_SESSION['price'] == 1 && $_SESSION['plan_cost_price'] == 1) ||
                        ($_SESSION['plan_cost_price_usd'] == 1) ||
                        ($_SESSION['custom_price'] == 1 && $_SESSION['plan_custom_price'] == 1)
                    ) { ?>
                        <li class="nav-item dropdown" id="navPrices">
                        <?php } else { ?>
                        <li class="nav-item dropdown" id="navPrices" style="display: none;">
                        <?php } ?>
                        <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bi bi-cash mr-1"></i> Precios
                            <i class="bx bx-chevron-right"></i>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown" style="margin-left: 3px; margin-top:-7px; margin-bottom:-7px">
                            <?php if ($_SESSION['price'] == 1 && $_SESSION['plan_cost_price'] == 1) { ?>
                                <a class="dropdown-item aPricesCOP" href="/cost/prices">
                                <?php } else { ?>
                                    <a class="dropdown-item aPricesCOP" href="/cost/prices" style="display: none;">
                                    <?php } ?>
                                    <span><i class="bi bi-currency-dollar mr-1"></i>Lista de Precios </span>
                                    </a>
                                    <?php //if ($_SESSION['price_usd'] == 1 && $_SESSION['plan_cost_price_usd'] == 1) { 
                                    ?>
                                    <!-- <a class="dropdown-item aPricesUSD" href="/cost/prices-usd"> -->
                                    <?php //} else { 
                                    ?>
                                    <!-- <a class="dropdown-item aPricesUSD" href="/cost/prices-usd" style="display: none;"> -->
                                    <?php //} 
                                    ?>
                                    <!-- <span><i class="bi bi-currency-dollar mr-1"></i>Lista de Precios (USD)</span>
                                            </a> -->
                                    <?php if ($_SESSION['custom_price'] == 1 && $_SESSION['plan_custom_price'] == 1) { ?>
                                        <a class="dropdown-item aCustomPrices" href="/cost/custom-prices">
                                        <?php } else { ?>
                                            <a class="dropdown-item aCustomPrices" href="/cost/custom-prices" style="display: none;">
                                            <?php } ?>
                                            <span><i class="bx bx-dollar-circle mr-1"></i>Lista de Precios Personalizado</span>
                                            </a>
                        </ul>
                        </li>

                        <?php if (
                            $_SESSION['analysis_material'] == 1 && $_SESSION['plan_cost_analysis_material'] == 1 ||
                            $_SESSION['plan_cost_economy_sale'] == 1 &&
                            ($_SESSION['cost_economy_scale'] == 1 && $_SESSION['flag_economy_scale'] == 1) ||
                            ($_SESSION['sale_objectives'] == 1 && $_SESSION['flag_sales_objective'] == 1) ||
                            $_SESSION['cost_multiproduct'] == 1 && $_SESSION['plan_cost_multiproduct'] == 1 ||
                            $_SESSION['historical'] == 1 && $_SESSION['plan_cost_historical'] == 1
                        ) { ?>
                            <li class="nav-item dropdown" id="navTools">
                            <?php } else { ?>
                            <li class="nav-item dropdown" id="navTools" style="display: none;">
                            <?php } ?>
                            <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bi bi-tools mr-1"></i> Herramientas
                                <i class="bx bx-chevron-right"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <?php if ($_SESSION['analysis_material'] == 1 && $_SESSION['plan_cost_analysis_material'] == 1) { ?>
                                    <li class="dropdown-submenu aAnalysisMaterials" style="margin-left: 3px; margin-top:-7px; margin-bottom:-7px">
                                    <?php } else { ?>
                                    <li class="dropdown-submenu aAnalysisMaterials" style="margin-left: 3px; margin-top:-7px; margin-bottom:-7px; display: none;">
                                    <?php } ?>
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
                                    <li class="dropdown-submenu" style="margin-left: 3px; margin-top:-7px; margin-bottom:-7px">
                                        <?php if (
                                            $_SESSION['plan_cost_economy_sale'] == 1 &&
                                            ($_SESSION['cost_economy_scale'] == 1 && $_SESSION['flag_economy_scale'] == 1 ||
                                                $_SESSION['sale_objectives'] == 1 && $_SESSION['flag_sales_objective'] == 1)
                                        ) { ?>
                                            <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarEconomy" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <?php } else { ?>
                                                <a class="nav-link dropdown-toggle" style="display: none;" href="javascript:void(0)" id="navbarEconomy" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <?php } ?>
                                                <i class="bi bi-card-heading mr-1"></i>Economias de Escala
                                                <i class="bx bx-chevron-right"></i>
                                                </a>
                                                <ul class="dropdown-menu1">
                                                    <?php if ($_SESSION['cost_economy_scale'] == 1 && $_SESSION['plan_cost_economy_sale'] == 1 && $_SESSION['flag_economy_scale'] == 1) { ?>
                                                        <a class="dropdown-item aEconomyScale" href="/cost/economyScale">
                                                        <?php } else { ?>
                                                            <a class="dropdown-item aEconomyScale" href="/cost/economyScale" style="display: none;">
                                                            <?php } ?>
                                                            <i class="bi bi-graph-up mr-1"></i> Negociaciones Eficientes
                                                            </a>
                                                            <?php if ($_SESSION['sale_objectives'] == 1 && $_SESSION['flag_sales_objective'] == 1 && $_SESSION['plan_cost_economy_sale'] == 1) { ?>
                                                                <a class="dropdown-item aSaleObjectives" href="/cost/saleObjectives">
                                                                <?php } else { ?>
                                                                    <a class="dropdown-item aSaleObjectives" href="/cost/saleObjectives" style="display: none;">
                                                                    <?php } ?>
                                                                    <i class="bi bi-graph-down mr-1"></i> Objetivos De Ventas
                                                                    </a>
                                                </ul>
                                    </li>
                                    <?php if ($_SESSION['cost_multiproduct'] == 1 && $_SESSION['plan_cost_multiproduct'] == 1) { ?>
                                        <a class="dropdown-item aMultiproducts" href="/cost/multiproduct">
                                        <?php } else { ?>
                                            <a class="dropdown-item aMultiproducts" href="/cost/multiproduct" style="display: none;">
                                            <?php } ?>
                                            <i class="bx bx-bible mr-1"></i> Punto de Equilibrio Multiproducto
                                            </a>
                                            <?php if ($_SESSION['simulator'] == 1 && $_SESSION['plan_cost_simulator'] == 1) { ?>
                                                <a class="dropdown-item aSimulator" href="/cost/simulator">
                                                <?php } else { ?>
                                                    <a class="dropdown-item aSimulator" href="/cost/simulator" style="display: none;">
                                                    <?php } ?>
                                                    <i class="bi bi-gear-wide-connected mr-1"></i> Simulador
                                                    </a>
                                                    <?php if ($_SESSION['historical'] == 1 && $_SESSION['plan_cost_historical'] == 1) { ?>
                                                        <a class="dropdown-item aHistorical" href="/cost/historical">
                                                        <?php } else { ?>
                                                            <a class="dropdown-item aHistorical" href="/cost/historical" style="display: none;">
                                                            <?php } ?>
                                                            <i class="bi bi-clock-history mr-1"></i> Historico
                                                            </a>
                            </ul>
                            </li>

                            <?php if ($_SESSION['quotes'] == 1 && $_SESSION['plan_cost_quote'] == 1) { ?>
                                <li class="nav-item aQuotes">
                                <?php } else { ?>
                                <li class="nav-item aQuotes" style="display: none;">
                                <?php } ?>
                                <a class="nav-link" href="/cost/quotes">
                                    <i class="bi bi-cash-stack mr-1"></i> Cotizar
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                                </li>

                                <?php if ($_SESSION['support'] == 1 && $_SESSION['plan_cost_support'] == 1) { ?>
                                    <li class="nav-item aSupport">
                                    <?php } else { ?>
                                    <li class="nav-item aSupport" style="display: none;">
                                    <?php } ?>
                                    <a class="nav-link" href="/cost/support">
                                        <i class="bi bi-headset mr-1"></i>
                                        <span> Soporte</span>
                                        <i class="bx bx-chevron-right"></i>
                                    </a>
                                    </li>
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