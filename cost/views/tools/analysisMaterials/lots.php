<?php
if (!isset($_SESSION)) {
    session_start();
    if (sizeof($_SESSION) == 0)
        header('location: /');
}
if (sizeof($_SESSION) == 0)
    header('location: /');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tezlik - Cost | Analysis Materials - Products</title>
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
                                    <h3 class="mb-1 font-weight-bold text-dark"><i class="bi bi-view-list"></i>Lotes</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Análisis de Materias Primas</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-info ml-3" id="btnProductsLoaded">Productos Cargados</button>
                                </div>
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-warning" id="btnRawMaterialsAnalysis">Analizar Materias Primas</button>
                                </div>
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-dark" id="btnConsolidatedMP">Consolidado MP</button>
                                </div>
                                <div class="col-xs-2 py-2">
                                    <button class="btn btn-secondary" id="btnClearAnalysis"><i class="bi bi-cloud-arrow-up-fill"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardAddLot" style="display: none;">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formAddLot">
                                            <div class="form-row">
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label for="">Referencia</label>
                                                    <select class="form-control refProduct" name="refProduct" id="refProduct"></select>
                                                </div>
                                                <div class="col-sm-6 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                    <label for="">Producto</label>
                                                    <select class="form-control selectNameProduct" name="selectNameProduct" id="selectNameProduct"></select>
                                                </div>
                                                <div class="col-xs-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <input class="form-control text-center" type="number" id="unitsmanufacturated" style="width: 200px;" />
                                                    <label>Unidades a Fabricar</label>
                                                </div>
                                                <div class="col-xs-2 mt-1">
                                                    <button class="btn btn-success" id="btnAddLot">Adicionar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- page content -->
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <!-- Row 5 -->
                        <div class="row disable-select">
                            <div class="col-12 cardTableProducts">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Productos Lote</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="tblProducts">
                                                <tfoot>
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th>Total:</th>
                                                        <th></th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 cardRawMaterialsAnalysis" style="display:none;">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Análisis Materia Prima</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-row">
                                            <div class="col-xs-3 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                <input class="form-control text-center" id="totalUnits" style="width: 200px;" readonly />
                                                <label>Unidades a Fabricar</label>
                                            </div>
                                            <div class="col-xs-3 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                <input class="form-control text-center" id="monthlySavings" style="width: 200px;" readonly />
                                                <label>Ahorro Mensual</label>
                                            </div>
                                            <div class="col-xs-3 floating-label enable-floating-label show-label" style="margin: bottom 5px;">
                                                <input class="form-control text-center" id="annualSavings" style="width: 200px;" readonly />
                                                <label>Ahorro Anual</label>
                                            </div>
                                        </div>
                                        <div class="alert alert-success mt-3" role="alert">
                                            Cantidad de Materias primas que consumen el 80% del valor del costo
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-striped text-center" id="tblAnalysisMaterials">
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 cardTableRawMaterials" style="display:none;">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Consolidado Materias Primas</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="tblMaterials">
                                                <tbody></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <!-- <th></th> -->
                                                        <th>Total:</th>
                                                        <th></th>
                                                        <th></th>
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
        <?php include_once  dirname(dirname(dirname(dirname(__DIR__)))) . '/global/partials/footer.php'; ?>
    </div>
    <!-- Page End -->

    <?php include_once dirname(dirname(dirname(dirname(__DIR__)))) . '/global/partials/scriptsJS.php'; ?>
    <script>
        flag_expense = "<?= $_SESSION['flag_expense'] ?>";
        flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";
    </script>
    <script src="/cost/js/basic/products/configProducts.js"></script>
    <script src="/global/js/global/orderData.js"></script>
    <!-- <script src="/cost/js/basic/rawMaterials/configRawMaterials.js"></script> -->
    <script src="/cost/js/tools/analysisMaterials/lots/lots.js"></script>
    <script src="/cost/js/tools/analysisMaterials/lots/tblProducts.js"></script>
    <script src="/cost/js/tools/analysisMaterials/lots/tblAnalysisMaterials.js"></script>
    <script src="/cost/js/tools/analysisMaterials/lots/calcAnalysisMaterials.js"></script>
</body>

</html>