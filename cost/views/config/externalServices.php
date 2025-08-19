<?php
require_once dirname(dirname(dirname(__DIR__))) . '/api/src/Auth/authMiddleware.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="Teenus SAS">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TezlikSoftware Cost | External Services</title>
    <link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/public/partials/scriptsCSS.php'; ?>
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
                <a href="javascript:;" class="close-btn" style="display: none;"><i class="bi bi-x-circle-fill"></i></a>
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
                                    <h3 class="mb-1 font-weight-bold text-dark">Servicios Externos</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Cree una lista de servicios externos predefinidos: En tu sistema, crea una lista de servicios externos comunes que se utilizan con frecuencia en las fichas técnicas de productos. Estos pueden incluir servicios como envío, embalaje, instalación, mantenimiento, etc.</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mr-2">
                                    <button class="btn btn-warning" id="btnNewService">Nuevo Servicio</button>
                                </div>
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-info" id="btnImportNewExternalServices" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Importar Servicios Externos"><i class="fas fa-upload" style="font-size: 20px;"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div class="page-content-wrapper mt--45 mb-5">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form-row">
                                            <div class="col-sm-4 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                <label>Referencia</label>
                                                <select class="form-control refProduct" name="refProduct" id="refProduct"></select>
                                            </div>
                                            <div class="col-sm-8 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                <label>Producto</label>
                                                <select class="form-control selectNameProduct" name="selectNameProduct" id="selectNameProduct"></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

                <div class="page-content-wrapper mt--45 mb-5 cardAddService" style="display: none;">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formAddService">
                                            <div class="form-row">
                                                <div class="col-sm-7 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label>Servicio</label>
                                                    <input class="form-control" type="text" name="service" id="service">
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                    <label>Costo</label>
                                                    <input class="form-control text-center" type="number" name="costService" id="costService">
                                                </div>
                                                <div class="col-xs-2 mt-1">
                                                    <button class="btn btn-primary" id="btnAddService">Adicionar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportExternalServices" style="display: none;">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <form id="formImportExternalServices" enctype="multipart/form-data">
                                    <div class="card">
                                        <div class="card-body pt-3">
                                            <div class="form-row" id="formExternalServices">
                                                <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                    <input class="form-control" type="file" id="fileExternalServices" accept=".xls,.xlsx">
                                                    <label for="formFile" class="form-label">Importar Servicios Externos</label>
                                                </div>
                                                <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                    <button type="text" class="btn btn-success" id="btnImportExternalServices">Importar</button>
                                                </div>
                                                <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                    <button type="text" class="btn btn-info" id="btnDownloadImportsExternalServices"><i class="fas fa-download"></i> Descargar Formato</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
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
                                            <table class="table table-striped" id="tblExternalServices">
                                                <tfoot>
                                                    <tr>
                                                        <th></th>
                                                        <th>Total</th>
                                                        <th id="totalCost"></th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
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
        <?php include_once  dirname(dirname(dirname(__DIR__))) . '/public/partials/footer.php'; ?>
    </div>
    <!-- Page End -->

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/public/partials/scriptsJS.php'; ?>
    <script>
        flag_expense = "<?= $_SESSION['flag_expense'] ?>";

        // price_usd = 
        flag_currency_usd = "<?= $_SESSION['flag_currency_usd'] ?>";
        flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";
    </script>
    <script src="/public/js/components/orderData.js"></script>
    <script src="/cost/js/config/generalServices/tblExternalServices.js"></script>
    <script src="/cost/js/config/generalServices/externalServices.js"></script>
    <script src="/cost/js/config/generalServices/configGExternalServices.js"></script>
    <script>
        $(document).ready(function() {
            loadAllDataGServices(1);
        });
    </script>
    <script src="../public/js/import/import.js"></script>
    <script src="/cost/js/config/generalServices/importExternalServices.js"></script>
    <script src="../public/js/import/file.js"></script>
</body>

</html>