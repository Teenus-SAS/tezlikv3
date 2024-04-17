<?php
if (!isset($_SESSION)) {
    session_start();
    if (sizeof($_SESSION) == 0)
        header('location: /');
}
if (sizeof($_SESSION) == 0)
    header('location: /');
?>
<?php require_once dirname(dirname(__DIR__)) . '/modals/productsByMaterial.php'; ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <title>Tezlik - Cost | Materials</title>
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
                        <div class="tab-pane cardMaterials">
                            <div class="row align-items-center">
                                <div class="col-sm-5 col-xl-6">
                                    <div class="page-title">
                                        <h3 class="mb-1 font-weight-bold text-dark"><i class="bi bi-gear mr-1"></i>Materias Primas</h3>
                                        <ol class="breadcrumb mb-3 mb-md-0">
                                            <li class="breadcrumb-item active">Ingrese los datos de las materias primas de acuerdo con las magnitudes y unidades (Internacionales) que compra</li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                    <div class="col-xs-2 mr-2">
                                        <button class="btn btn-warning" id="btnNewMaterial" name="btnNewMaterial">Nueva Materia Prima</button>
                                    </div>
                                    <div class="col-xs-2 py-2 mr-2">
                                        <button class="btn btn-info" id="btnImportNewMaterials" name="btnNewImportMaterials">Importar Materias Primas</button>
                                    </div>
                                    <?php if ($_SESSION['flag_materials_usd'] == '1') { ?>
                                        <div class="col-xs-2 py-2 mr-2">
                                            <button class="btn btn-sm btn-outline-primary" id="btnPriceUSD">Moneda (USD)</button>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane cardCategories" style="display: none;">
                            <div class="row align-items-center">
                                <div class="col-sm-5 col-xl-6">
                                    <div class="page-title">
                                        <h3 class="mb-1 font-weight-bold text-dark"><i class="bi bi-gear mr-1"></i>Categorias</h3>
                                        <ol class="breadcrumb mb-3 mb-md-0">
                                            <li class="breadcrumb-item active">Ingrese cada uno de las categorias que sus materias necesitan</li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                    <div class="col-xs-2 mr-2">
                                        <button class="btn btn-warning" id="btnNewCategory" name="btnNewCategory">Nueva Categoria</button>
                                    </div>
                                    <div class="col-xs-2 py-2 mr-2">
                                        <button class="btn btn-info" id="btnImportNewCategory" name="btnImportNewCategory">Importar Categoria</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Materiales -->
                <div class="page-content-wrapper mt--45 mb-5 cardRawMaterials">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form class="col-12" id="formCreateMaterial">
                                            <div class="form-row">
                                                <div class="col-sm-4 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <input type="text" class="form-control text-center" id="refRawMaterial" name="refRawMaterial">
                                                    <label>Referencia</label>
                                                </div>
                                                <div class="col-sm-8 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <input type="text" class="form-control" id="nameRawMaterial" name="nameRawMaterial">
                                                    <label>Nombre Materia Prima</label>
                                                </div>
                                                <div class="col-sm-3 floating-label enable-floating-label show-label mb-0 categories">
                                                    <select class="form-control" id="idCategory" name="idCategory"></select>
                                                    <label>Categoria</label>
                                                </div>
                                                <div class="col-sm-3 floating-label enable-floating-label show-label mb-0">
                                                    <select class="form-control" id="magnitudes" name="magnitude"></select>
                                                    <label>Magnitud</label>
                                                </div>
                                                <div class="col-sm-3 floating-label enable-floating-label show-label mb-0">
                                                    <select class="form-control" id="units" name="unit">
                                                        <option disabled selected>Seleccionar</option>
                                                    </select>
                                                    <label>Unidad</label>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label mb-0">
                                                    <input type="number" class="form-control text-center" step="any" id="costRawMaterial" name="costRawMaterial" data-toggle="tooltip" title="Ingrese el valor de compra en COP">
                                                    <label>Costo</label>
                                                </div>
                                                <div class="col-xs-2" style="margin-bottom:0px;margin-top:5px">
                                                    <button class="btn btn-info" id="btnCreateMaterial" name="btnCreateMaterial">Crear</button>
                                                </div>
                                            </div>
                                            <?php if ($_SESSION['flag_materials_usd'] == '1') { ?>
                                                <div class="alert alert-warning mt-3 cardAlertPrice" role="alert">
                                                    Ingrese el valor de compra en COP
                                                </div>
                                            <?php } ?>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportMaterials">
                    <div class="container-fluid">
                        <div class="row">
                            <form class="col-12" id="formImportMaterials" enctype="multipart/form-data">
                                <div class="card">
                                    <div class="card-body pt-3">
                                        <div class="form-row" id="formMaterials">
                                            <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                <input class="form-control" type="file" id="fileMaterials" accept=".xls,.xlsx">
                                                <label for="formFile" class="form-label">Importar Materia Prima</label>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-success" id="btnImportMaterials">Importar</button>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-info" id="btnDownloadImportsMaterials">Descarga Formato</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Categorias -->
                <div class="page-content-wrapper mt--45 mb-5 cardAddCategories">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <form id="formCreateCategory">
                                        <div class="card-body">
                                            <div class="form-row">
                                                <div class="col-sm-10 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                    <input type="text" class="form-control" id="category" name="category">
                                                    <label>Categoria</label>
                                                </div>
                                                <div class="col-xs-2" style="margin-bottom:0px;margin-top:4px">
                                                    <button class="btn btn-info" id="btnCreateCategory" name="btnCreateCategory">Crear</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportCategories">
                    <div class="container-fluid">
                        <div class="row">
                            <form class="col-12" id="formImportCategory" enctype="multipart/form-data">
                                <div class="card">
                                    <div class="card-body pt-3">
                                        <div class="form-row" id="formCategory">
                                            <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                <input class="form-control" type="file" id="fileCategories" accept=".xls,.xlsx">
                                                <label for="formFile" class="form-label">Importar Categoria</label>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-success" id="btnImportCategory">Importar</button>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-info" id="btnDownloadImportsCategories">Descarga Formato</button>
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
                        <div class="row">
                            <div class="col-12">
                                <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active selectNavigation" id="materials" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-activity" aria-selected="true">
                                            <i class="fas fa-flask mr-1"></i>Materias Primas
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link selectNavigation" id="categories" data-toggle="pill" href="#pills-projects" role="tab" aria-controls="pills-projects" aria-selected="false">
                                            <i class="bi bi-arrow-repeat mr-1"></i>Categorias
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <!-- <div class="cardMaterials">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="tblRawMaterials">

                                        </table>
                                    </div>
                                </div>
                            </div> -->
                                    <div class="tab-pane cardMaterials">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped" id="tblRawMaterials">

                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane cardCategories" style="display: none;">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped" id="tblCategories">

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
        </div>
        <!-- Main content end -->

        <!-- Footer -->
        <?php include_once  dirname(dirname(dirname(__DIR__))) . '/global/partials/footer.php'; ?>
    </div>
    <!-- Page End -->

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsJS.php'; ?>
    <script>
        flag_expense = "<?= $_SESSION['flag_expense'] ?>";
        flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";
        flag_indirect = "<?= $_SESSION['flag_indirect'] ?>";
        flag_materials_usd = "<?= $_SESSION['flag_materials_usd'] ?>";
    </script>
    <script src="/global/js/global/configMagnitudes.js"></script>
    <script src="/global/js/global/configUnits.js"></script>
    <script src="/cost/js/basic/rawMaterials/tblRawMaterials.js"></script>
    <script src="/cost/js/basic/rawMaterials/tblCategories.js"></script>
    <script src="/cost/js/basic/rawMaterials/rawMaterials.js"></script>
    <script src="/cost/js/basic/rawMaterials/categories.js"></script>
    <script src="../global/js/import/import.js"></script>
    <script src="/cost/js/basic/rawMaterials/importRawMaterials.js"></script>
    <script src="/cost/js/basic/rawMaterials/importCategories.js"></script>
    <script src="../global/js/import/file.js"></script>
    <script src="../global/js/global/validateExt.js"></script>
</body>

</html>