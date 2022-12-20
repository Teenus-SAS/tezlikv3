<?php
if (!isset($_SESSION)) {
    session_start();
    if (sizeof($_SESSION) == 0)
        header('location: /');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="LetStart Admin is a full featured, multipurpose, premium bootstrap admin template built with Bootstrap 4 Framework, HTML5, CSS and JQuery.">
    <meta name="keywords" content="admin, panels, dashboard, admin panel, multipurpose, bootstrap, bootstrap4, all type of dashboards">
    <meta name="author" content="MatrrDigital">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tezlik - Cost | EconomySale</title>
    <link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsCSS.php'; ?>
</head>

<body class="horizontal-navbar">
    <!-- Begin Page -->
    <div class="page-wrapper">
        <!-- Begin Header -->
        <?php include_once dirname(dirname(__DIR__)) . '/partials/header.php'; ?>

        <!-- Begin Left Navigation -->
        <?php include_once dirname(dirname(__DIR__)) . '/partials/nav.php'; ?>

        <!-- Begin main content -->
        <div class="main-content">
            <!-- Content -->
            <div class="page-content">
                <!-- Page header -->
                <div class="page-title-box">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-sm-5 col-xl-6">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Economia de Escalas</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Calculo de economia de escalas</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-4 col-xl-6">
                                <div class="form-inline justify-content-sm-end">
                                    <button class="btn btn-warning" id="btnNewEconomySale">Nuevo Calculo</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- page content -->
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <div class="vertical-app-tabs" id="rootwizard">
                            <div class="col-md-12 col-lg-12 InputGroup">
                                <form id="formNewEconomySale">
                                    <div class="row mt-5">
                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Producto</b></label>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <select class="form-control refProduct" id="refProduct" name="idProduct"></select>
                                                <label for="refProduct" class="form-label">Referencia <span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-8">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <select class="form-control selectNameProduct" id="selectNameProduct" name="idProduct"></select>
                                                <label for="selectNameProduct" class="form-label">Producto <span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <!-- <div class="row px-3">

                                            <div class="col-12 col-lg-3">
                                                <div class="form-group floating-label enable-floating-label show-label">
                                                    <input class="form-control number text-center calcPrice" type="text" name="quantity" id="quantity">
                                                    <label for="quantity" class="form-label">Cantidad <span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-3">
                                                <div class="form-group floating-label enable-floating-label show-label">
                                                    <input class="form-control number text-center calcPrice" type="text" name="price" id="price">
                                                    <label for="prices" class="form-label">Precio Unitario <span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-3">
                                                <div class="form-group floating-label enable-floating-label show-label calcPrice">
                                                    <select name="discount" id="discount" class="form-control">
                                                        <option value="0">0%</option>
                                                        <option value="1">1%</option>
                                                        <option value="2">2%</option>
                                                        <option value="3">3%</option>
                                                        <option value="4">4%</option>
                                                        <option value="5">5%</option>
                                                        <option value="6">6%</option>
                                                        <option value="7">7%</option>
                                                        <option value="8">8%</option>
                                                        <option value="9">9%</option>
                                                        <option value="10">10%</option>
                                                    </select>
                                                    <label for="prices" class="form-label">Descuento <span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-3">
                                                <div class="form-group floating-label enable-floating-label show-label">
                                                    <input class="form-control text-center" type="text" name="totalPrice" id="totalPrice" readonly>
                                                    <label for="prices" class="form-label">Precio Total <span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-6 mb-4">
                                                <img src="" id="imgProduct" style="width:80px">
                                            </div>
                                            <div class="col-12 col-lg-12">
                                                <button class="btn btn-warning mb-4" id="btnAddProduct">Adicionar producto</button>
                                            </div>
                                        </div> -->
                                        <hr>
                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Descripción</b></label>
                                        </div>
                                        <div class="col-12 col-lg-12">
                                            <div class="card mt-4">
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center">Referencia</th>
                                                                    <th class="text-center">Producto</th>
                                                                    <th class="text-center">Cantidad</th>
                                                                    <th class="text-center">Valor Unitario</th>
                                                                    <th class="text-center">Descuento</th>
                                                                    <th class="text-center">Valor Total</th>
                                                                    <th class="text-center">Acciones</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tableEconomySaleBody">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Main content end -->

        <!-- Footer -->
        <?php include_once  dirname(dirname(dirname(__DIR__))) . '/global/partials/footer.php'; ?>
    </div>
    <!-- Page End -->

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsJS.php'; ?>
    <script src="/global/js/global/number.js"></script>
    <script src="/global/js/global/searchData.js"></script>

    <script src="/cost/js/basic/products/configProducts.js"></script>
    <script src="/cost/js/economySale/economySale.js"></script>
    <script src="/cost/js/economySale/tblEconomySale.js"></script>
</body>

</html>