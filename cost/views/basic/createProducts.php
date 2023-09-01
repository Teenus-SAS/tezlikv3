<?php
if (!isset($_SESSION)) {
    session_start();
    if (sizeof($_SESSION) == 0)
        header('location: /');
}
if (sizeof($_SESSION) == 0)
    header('location: /');
?>
<?php require_once dirname(dirname(__DIR__)) . '/modals/inactiveProducts.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="LetStart Admin is a full featured, multipurpose, premium bootstrap admin template built with Bootstrap 4 Framework, HTML5, CSS and JQuery.">
    <meta name="keywords" content="admin, panels, dashboard, admin panel, multipurpose, bootstrap, bootstrap4, all type of dashboards">
    <meta name="author" content="MatrrDigital">
    <title>Tezlik - Cost | Products</title>
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
            <!-- Loader -->
            <div class="loading">
                <div class="loader"></div>
            </div>

            <!-- Content -->
            <div class="page-content">
                <!-- Page header -->
                <div class="page-title-box">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-sm-5 col-xl-6">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Productos</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Creación de Productos</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-xl-6 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mr-2">
                                    <button class="btn btn-warning" id="btnNewProduct">Nuevo Producto</button>
                                </div>
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-info" id="btnImportNewProducts">Importar Productos</button>
                                </div>
                                <div class="col-xs-2">
                                    <button class="btn btn btn-primary" id="btnActiveProducts">Activar Productos</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardCreateProduct">
                    <div class="container-fluid">
                        <div class="row">
                            <form class="col-12" id="formCreateProduct">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form-row">
                                            <div class="col-sm-3 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                <input type="text" class="form-control" name="referenceProduct" id="referenceProduct">
                                                <label for="">Referencia</label>
                                            </div>
                                            <div class="col-sm-7 floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                                <input type="text" class="form-control" name="product" id="product">
                                                <label for="">Nombre Producto</label>
                                            </div>
                                            <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                <input type="text" class="form-control text-center number" name="salePrice" id="salePrice" data-toggle="tooltip" title="Ingrese el precio de venta actual, si existe">
                                                <label for="">Precio de Venta</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body pt-3">
                                        <div class="form-row">
                                            <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                <input type="number" class="form-control text-center" name="profitability" id="profitability" data-toggle="tooltip" title="Ingrese la rentabilidad que desea ganar para la venta del producto">
                                                <label for="">Rentabilidad Deseada(%)</label>
                                            </div>
                                            <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                <input type="number" class="form-control text-center" name="commissionSale" id="commisionSale" data-toggle="tooltip" title="Ingrese la comisión de ventas para sus vendedores, si existe">
                                                <label for="">Comisión de Ventas(%)</label>
                                            </div>

                                            <div class="col-sm-6 floating-label enable-floating-label show-label drag-area">
                                                <input class="form-control" type="file" id="formFile">
                                                <label for="formFile" class="form-label"> Cargar imagen producto</label>
                                            </div>
                                            <div class="col-xs-2" style="margin-bottom:0px;margin-top:7px">
                                                <button type="text" class="btn btn-success" id="btnCreateProduct">Crear Producto</button>
                                            </div>
                                        </div>
                                        <div id="preview"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportProducts">
                    <div class="container-fluid">
                        <div class="row">
                            <form class="col-12" id="formImportProduct" enctype="multipart/form-data">
                                <div class="card">
                                    <div class="card-body pt-3">
                                        <div class="form-row">
                                            <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                <input class="form-control" type="file" id="fileProducts" accept=".xls,.xlsx">
                                                <label for="formFile" class="form-label"> Importar Productos</label>
                                            </div>
                                            <div class="col-xs-2" style="margin-top:7px">
                                                <button type="text" class="btn btn-success" id="btnImportProducts">Importar</button>
                                            </div>
                                            <div class="col-xs-2" style="margin-top:7px">
                                                <button type="text" class="btn btn-info" id="btnDownloadImportsProducts">Descarga Formato</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- page content -->
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <!-- Row 5 -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card disable-select">
                                    <div class="card-header">
                                        <h5 class="card-title">Productos</h5>
                                        <div class="alert alert-danger limitPlan" role="alert" style="display:none;"> ¡Llegaste al limite de tu plan. Comunícate con tu administrador y sube de categoría para obtener más espacio! </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="tblProducts">

                                            </table>
                                        </div>
                                    </div>
                                </div>
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
    <script src="/cost/js/basic/products/tblProducts.js"></script>
    <script src="/cost/js/basic/products/inactiveProducts.js"></script>
    <script src="/cost/js/basic/products/products.js"></script>
    <script src="../global/js/import/import.js"></script>
    <script src="/cost/js/basic/products/importProducts.js"></script>
    <script src="../global/js/import/file.js"></script>
    <script src="../global/js/global/validateImgExt.js"></script>
</body>

</html>