<?php
if (!isset($_SESSION)) {
    session_start();
    if (sizeof($_SESSION) == 0)
        header('location: /');
}
if (sizeof($_SESSION) == 0)
    header('location: /');
?>
<?php include_once dirname(dirname(dirname(__DIR__))) . '/modals/notProducts.php' ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tezlik - Cost | Custom Prices</title>
    <link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />

    <?php include_once dirname(dirname(dirname(dirname(__DIR__)))) . '/global/partials/scriptsCSS.php'; ?>
</head>

<body class="horizontal-navbar">
    <!-- Begin Page -->
    <div class="page-wrapper">
        <!-- Begin Header -->
        <?php include_once dirname(dirname(dirname(__DIR__))) . '/partials/header.php'; ?>

        <!-- Begin Left Navigation -->
        <?php include_once dirname(dirname(dirname(__DIR__))) . '/partials/nav.php'; ?>

        <!-- Begin main content -->
        <div class="main-content">
            <!-- Loader -->
            <div class="loading">
                <a href="javascript:;" class="close-btn" style="display: none;"><i class="bi bi-x-circle-fill"></i></a>
                <div class="loader"></div>
            </div>

            <!-- Content -->
            <div class="page-content">
                <?php if ($_SESSION['license_days'] <= 30) { ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="alert alert-danger alert-dismissible fade show text-center" style="margin-bottom: 0px;" role="alert">
                                <strong>¡Pronto se acabara tu licencia (<?php echo $_SESSION['license_days']; ?> días)! .</strong> Comunícate con tu administrador para mas información.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($_SESSION['license_days'] > 30 && $_SESSION['license_days'] < 40) { ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="alert alert-warning alert-dismissible fade show text-center" style="margin-bottom: 0px;" role="alert">
                                <strong>¡Pronto se acabara tu licencia (<?php echo $_SESSION['license_days']; ?> días)! .</strong> Comunícate con tu administrador para mas información.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <!-- Page header -->
                <div class="page-title-box">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-sm-5 col-xl-6">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark"><i class="bi bi-list"></i>Precios Personalizados</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Asignación de Precios Personalizados</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                <?php if ($_SESSION['type_custom_price'] == '-1') { ?>
                                    <div class="col-xs-2 mr-2">
                                        <button class="btn btn-warning" id="btnNewCustomPercentage">Configurar Lista De Precios</button>
                                    </div>
                                <?php } ?>
                                <div class="col-xs-2 mr-2">
                                    <button class="btn btn-info" id="btnNewImportCustom" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Importar Lista de Precios Personalizados"><i class="fas fa-download" style="font-size: 20px;"></i></button>
                                </div>
                                <div class="col-xs-2 mr-2">
                                    <button class="btn btn-success" id="btnExportCustomPrice" data-toggle="tooltip" title="Exportar" style="height: 39px"><i class="fas fa-file-excel fa-lg"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardCreateCustomPrices">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formCreateCustomPrices">
                                            <div class="form-row">
                                                <div class="col-sm-4 floating-label enable-floating-label show-label">
                                                    <label>Producto</label>
                                                    <select class="form-control selectNameProduct" name="idProduct" id="idProduct"></select>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label">
                                                    <label>Lista de Precios</label>
                                                    <select class="form-control pricesList" name="idPriceList" id="pricesList"></select>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label">
                                                    <label>Precio - Lista de Precios</label>
                                                    <input type="text" class="form-control text-center" id="customPricesValue2" readonly>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label">
                                                    <label>Valor</label>
                                                    <input type="number" class="form-control text-center" id="customPricesValue" name="customPricesValue">
                                                </div>
                                                <div class="col-sm-1 mt-1">
                                                    <button class="btn btn-primary" id="btnCreateCustomPrice">Actualizar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardCreateCustomPercentages">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formCreateCustomPercentage">
                                            <div class="form-row">
                                                <div class="col-sm-3 floating-label enable-floating-label show-label">
                                                    <label>Lista de Precios</label>
                                                    <select class="form-control pricesList" name="idPriceList" id="pricesList2"></select>
                                                </div>
                                                <!-- <div class="col-sm-3 floating-label enable-floating-label show-label">
                                                    <label>Precio</label>
                                                    <select class="form-control" name="typePrice" id="typePrice">
                                                        <option disabled selected>Seleccionar</option>
                                                        <option value="0">ACTUAL</option>
                                                        <option value="1">SUGERIDO</option>
                                                    </select>
                                                </div> -->
                                                <div class="col-sm-2 floating-label enable-floating-label show-label inputPercentage">
                                                    <label>Porcentaje</label>
                                                    <input type="number" class="form-control text-center" id="percentage" name="percentage">
                                                </div>
                                                <div class="col-sm-2 mt-1">
                                                    <button class="btn btn-primary" id="btnCreateCustomPercentage">Asignar Porcentaje</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportCustom">
                    <div class="container-fluid">
                        <div class="row">
                            <form class="col-12" id="formImportCustom" enctype="multipart/form-data">
                                <div class="card">
                                    <div class="card-body pt-3">
                                        <div class="form-row" id="formCustom">
                                            <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                <input class=" form-control" type="file" id="fileCustom" accept=".xls,.xlsx">
                                                <label for="formFile" class="form-label"> Importar Personalizados</label>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-success" id="btnImportCustom">Importar</button>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-info" id="btnDownloadImportsCustomPrices">Descarga Formato (Precios)</button>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-info" id="btnDownloadImportsCustomPercentage">Descarga Formato (Porcentaje)</button>
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
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table text-center table-striped" style="font-size: small;" id="tblCustomPrices"> </table>
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
        <?php include_once  dirname(dirname(dirname(dirname(__DIR__)))) . '/global/partials/footer.php'; ?>
    </div>
    <!-- Page End -->

    <?php include_once dirname(dirname(dirname(dirname(__DIR__)))) . '/global/partials/scriptsJS.php'; ?>

    <script>
        flag_expense = "<?= $_SESSION['flag_expense'] ?>";

        // price_usd = 
        flag_currency_usd = "<?= $_SESSION['flag_currency_usd'] ?>";
        flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";;
        flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";
        type_custom_price = "<?= $_SESSION['type_custom_price'] ?>";
        flag_type_price = "<?= $_SESSION['flag_type_price'] ?>";
        flag_composite_product = "<?= $_SESSION['flag_composite_product'] ?>";
    </script>
    <script src="/global/js/global/orderData.js"></script>
    <script src="/cost/js/basic/products/configProducts.js"></script>
    <script src="/cost/js/general/priceList/configPriceList.js"></script>
    <script>
        $(document).ready(function() {
            loadPriceList(1);
            getDataProducts('/api/products');
        });
    </script>
    <script src="/cost/js/prices/customPrices/tblNotProducts.js"></script>
    <script src="/cost/js/prices/customPrices/tblCustomPrices.js"></script>
    <script src="/cost/js/prices/customPrices/customPrices.js"></script>
    <script src="/cost/js/prices/customPrices/exportCustomPrices.js"></script>
    <script src="/global/js/import/import.js"></script>
    <script src="/cost/js/prices/customPrices/importCustom.js"></script>
    <script src="/global/js/import/file.js"></script>
    <script src="/cost/js/prices/customPrices/customPercentages.js"></script>
    <script src="/cost/js/prices/customPrices/addProducts.js"></script>
</body>

</html>