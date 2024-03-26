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
    <title>Tezlik - Cost | Product Materials</title>
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

            <!-- content -->
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
                <!-- page header -->
                <div class="page-title-box">
                    <div class="container-fluid">
                        <div class="tab-pane cardProductsMaterials">
                            <div class="row align-items-center">
                                <div class="col-sm-5 col-xl-6">
                                    <div class="page-title">
                                        <h3 class="mb-1 font-weight-bold text-dark"><i class="bi bi-file-text mr-1"></i>Ficha Técnica Productos</h3>
                                        <ol class="breadcrumb mb-3 mb-md-0">
                                            <li class="breadcrumb-item active">Asignación de materias primas al producto</li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                    <div class="col-xs-2 mr-2">
                                        <button class="btn btn-warning" id="btnCreateProduct">Adicionar Nueva Materia Prima</button>
                                    </div>
                                    <?php if ($_SESSION['flag_composite_product'] == 1) { ?>
                                        <div class="col-xs-2 mr-2">
                                            <button class="btn btn-secondary" id="btnAddNewProduct" style="display: none;">Adicionar Nuevo Producto</button>
                                        </div>
                                    <?php } ?>
                                    <div class="col-xs-2 py-2 mr-2">
                                        <button class="btn btn-info" id="btnImportNewProductsMaterials">Importar Materia Prima</button>
                                    </div>
                                    <div class="col-xs-2 py-2 mr-2">
                                        <button class="btn btn-secondary btnDownloadXlsx"><i class="bi bi-cloud-arrow-up-fill"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane cardProductsProcess" style="display: none;">
                            <div class="row align-items-center">
                                <div class="col-sm-5 col-xl-6">
                                    <div class="page-title">
                                        <h3 class="mb-1 font-weight-bold text-dark">Productos</h3>
                                        <ol class="breadcrumb mb-3 mb-md-0">
                                            <li class="breadcrumb-item active">Ingrese los tiempos para la fabricacion de sus productos de acuerdo con sus procesos, máquinas o proceso manual necesario</li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                    <div class="col-xs-2 mr-2">
                                        <button class="btn btn-warning" id="btnCreateProcess">Nuevo Proceso</button>
                                    </div>
                                    <div class="col-xs-2 py-2 mr-2">
                                        <button class="btn btn-info" id="btnImportNewProductProcess">Importar Procesos</button>
                                    </div>
                                    <div class="col-xs-2 py-2 mr-2">
                                        <button class="btn btn-secondary btnDownloadXlsx"><i class="bi bi-cloud-arrow-up-fill"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane cardServices" style="display: none;">
                            <div class="row align-items-center">
                                <div class="col-sm-5 col-xl-6">
                                    <div class="page-title">
                                        <h3 class="mb-1 font-weight-bold text-dark">Servicios Externos</h3>
                                        <ol class="breadcrumb mb-3 mb-md-0">
                                            <li class="breadcrumb-item active">Ingrese los productos o servicios complementarios para un producto en especifico y que solo se haya utilizado en este. Ejm: transporte, envio, etc</li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                    <div class="col-xs-2 mr-2">
                                        <button class="btn btn-warning" id="btnNewService">Nuevo Servicio</button>
                                    </div>
                                    <div class="col-xs-2 py-2 mr-2">
                                        <button class="btn btn-info" id="btnImportNewExternalServices">Importar Servicios Externos</button>
                                    </div>
                                    <div class="col-xs-2 py-2 mr-2">
                                        <button class="btn btn-secondary btnDownloadXlsx"><i class="bi bi-cloud-arrow-up-fill"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardProducts">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card" style="height: 80px;">
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
                </div>

                <!-- Materiales -->
                <div class="page-content-wrapper mt--45 mb-5 cardAddMaterials">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body pb-0">
                                        <form id="formAddMaterials">
                                            <div class="form-row">
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label>Categoria</label>
                                                    <select class="form-control material" name="categories" id="categories"></select>
                                                </div>
                                                <div class="col-sm-4 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label>Referencia</label>
                                                    <select class="form-control material" name="refMaterial" id="refMaterial"></select>
                                                </div>
                                                <div class="col-sm-6 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label>Materia Prima</label>
                                                    <select class="form-control material" name="material" id="nameMaterial"></select>
                                                </div>
                                                <div class="w-100"></div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                    <select class="form-control" id="units" name="unit"></select>
                                                    <label>Unidad</label>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label>Cantidad</label>
                                                    <input class="form-control text-center" type="number" name="quantity" id="quantity">
                                                </div>
                                                <div class="col-xs-1 mt-1">
                                                    <button class="btn btn-success" id="btnAddMaterials">Adicionar Materia Prima</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportProductsMaterials">
                    <div class="container-fluid">
                        <div class="row">
                            <form class="col-12" id="formImportProductMaterial" enctype="multipart/form-data">
                                <div class="card">
                                    <div class="card-body pt-3">
                                        <div class="form-row" id="formProductMaterials">
                                            <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                <input class="form-control" type="file" id="fileProductsMaterials" accept=".xls,.xlsx">
                                                <label for="formFile" class="form-label"> Importar Productos*Materia Prima</label>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-success" id="btnImportProductsMaterials">Importar</button>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-info" id="btnDownloadImportsProductsMaterials">Descarga Formato</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardAddNewProduct">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body pb-0">
                                        <form id="formAddNewProduct">
                                            <div class="form-row">
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label>Referencia</label>
                                                    <select class="form-control compositeProduct" name="refCompositeProduct" id="refCompositeProduct"></select>
                                                </div>
                                                <div class="col-sm-5 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label>Producto</label>
                                                    <select class="form-control compositeProduct" name="compositeProduct" id="compositeProduct"></select>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                    <select class="form-control" id="unit2" name="unit">
                                                    </select>
                                                    <label>Unidad</label>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label>Cantidad</label>
                                                    <input class="form-control text-center" type="number" name="quantity" id="quantity2">
                                                </div>
                                                <div class="col-xs-1 mt-1">
                                                    <button class="btn btn-success" id="btnAddProduct">Adicionar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Procesos -->
                <div class="page-content-wrapper mt--45 mb-5 cardAddProcess">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formAddProcess">
                                            <div class="form-row">
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label>Proceso</label>
                                                    <select class="form-control" name="idProcess" id="idProcess"></select>
                                                </div>
                                                <div class="col-sm-3 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label>Maquina</label>
                                                    <select class="form-control" name="idMachine" id="idMachine"></select>
                                                </div>
                                                <div class="col-xs-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <?php if ($_SESSION['inyection'] == 1) { ?>
                                                        <label class="text-center">Tiempo/Und</label>
                                                    <?php } else { ?>
                                                        <label class="text-center">t.alistamiento (min)</label>
                                                    <?php } ?>
                                                    <input class="form-control text-center time" type="number" name="enlistmentTime" id="enlistmentTime" data-toggle="tooltip" title="Ingrese solo el tiempo necesario para fabricar una unidad">
                                                </div>
                                                <div class="col-xs-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <?php if ($_SESSION['inyection'] == 1) { ?>
                                                        <label class="text-center">% Eficiencia</label>
                                                    <?php } else { ?>
                                                        <label class="text-center">t.operacion (min)</label>
                                                    <?php } ?>
                                                    <input class="form-control text-center time" type="number" name="operationTime" id="operationTime" data-toggle="tooltip" title="Ingrese solo el tiempo necesario para fabricar una unidad">
                                                </div>
                                                <div class="col-xs-2 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                    <?php if ($_SESSION['inyection'] == 1) { ?>
                                                        <label class="text-center">Total</label>
                                                    <?php } else { ?>
                                                        <label class="text-center">t.total (min)</label>
                                                    <?php } ?>
                                                    <input class="form-control text-center" type="number" name="totalTime" id="totalTime" disabled>
                                                </div>
                                                <div class="col-xs-2 mt-1">
                                                    <button class="btn btn-success" id="btnAddProcess">Adicionar</button>
                                                </div>
                                                <div class="col-xs-2 ml-3 checkbox checkbox-success mb-2 checkMachine">
                                                    <input id="checkMachine" name="checkMachine" type="checkbox"><label for="checkMachine">Máquina Autonoma </label>
                                                </div>
                                            </div>
                                        </form>
                                        <!-- <div class="alert alert-warning" role="alert">
                                            Active los procesos creando la nomina antes de asignar los procesos y máquinas para un producto.
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportProductsProcess">
                    <div class="container-fluid">
                        <div class="row">
                            <form class="col-12" id="formImportProductProcess" enctype="multipart/form-data">
                                <div class="card">
                                    <div class="card-body pt-3">
                                        <div class="form-row" id="formProductProcess">
                                            <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                <input class="form-control" type="file" id="fileProductsProcess" accept=".xls,.xlsx">
                                                <label for="formFile" class="form-label"> Importar Productos*Procesos</label>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-success" id="btnImportProductsProcess">Importar</button>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-info" id="btnDownloadImportsProductsProcess">Descarga Formato</button>
                                            </div>
                                        </div>
                                        <!-- <div class="alert alert-warning mt-2" role="alert">
                                            Active los procesos creando la nomina antes de asignar los procesos y máquinas para un producto.
                                        </div> -->
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Servicios Externos -->
                <div class="page-content-wrapper mt--45 mb-5 cardAddService">
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

                <div class="page-content-wrapper mt--45 mb-5 cardImportExternalServices">
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
                                                    <button type="text" class="btn btn-info" id="btnDownloadImportsExternalServices">Descarga Formato</button>
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
                        <div class="row">
                            <div class="col-12">
                                <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active selectNavigation" id="materials" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-activity" aria-selected="true">
                                            <i class="fas fa-flask mr-1"></i>Materias Primas
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link selectNavigation" id="process" data-toggle="pill" href="#pills-projects" role="tab" aria-controls="pills-projects" aria-selected="false">
                                            <i class="bi bi-arrow-repeat mr-1"></i>Tiempos y Procesos
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link selectNavigation" id="services" data-toggle="pill" href="#pills-projects" role="tab" aria-controls="pills-projects" aria-selected="false">
                                            <i class="bi bi-arrow-repeat mr-1"></i>Servicios Externos
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="tab-pane cardProductsMaterials">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="fixed-table-loading table table-hover text-center" id="tblConfigMaterials" name="tblConfigMaterials">
                                                    <tfoot>
                                                        <tr>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
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
                                    <div class="tab-pane cardProductsProcess" style="display: none;">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped text-center" id="tblConfigProcess" name="tblConfigProcess">
                                                    <tbody id="tblConfigProcessBody"></tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th></th>
                                                            <th></th>
                                                            <th>Total:</th>
                                                            <th id="totalAlistment"></th>
                                                            <th id="totalOperation"></th>
                                                            <th id="totalWorkforce"></th>
                                                            <th id="totalIndirect"></th>
                                                            <?php if ($_SESSION['flag_employee'] == 1) { ?>
                                                                <th></th>
                                                            <?php } ?>
                                                            <th></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane cardServices" style="display: none;">
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
        flag_composite_product = "<?= $_SESSION['flag_composite_product'] ?>";
        flag_employee = "<?= $_SESSION['flag_employee'] ?>";
        flag_indirect = "<?= $_SESSION['flag_indirect'] ?>";
        inyection = "<?= $_SESSION['inyection'] ?>";
        $(document).ready(function() {

            $('#refMaterial').change(async function(e) {
                e.preventDefault();
                let id = this.value;

                $('#nameMaterial option').prop('selected', function() {
                    return $(this).val() == id;
                });
            });

            $('#nameMaterial').change(async function(e) {
                e.preventDefault();
                let id = this.value;

                $('#refMaterial option').prop('selected', function() {
                    return $(this).val() == id;
                });
            });

            $('#refCompositeProduct').change(async function(e) {
                e.preventDefault();
                let id = this.value;

                $('#compositeProduct option').prop('selected', function() {
                    return $(this).val() == id;
                });
            });

            $('#compositeProduct').change(async function(e) {
                e.preventDefault();
                let id = this.value;

                $('#refCompositeProduct option').prop('selected', function() {
                    return $(this).val() == id;
                });
            });

            // const loadData = async (endpoint, key) => {
            //     try {
            //         const data = await searchData(endpoint);
            //         sessionStorage.setItem(key, JSON.stringify(data));
            //         return data;
            //     } catch (error) {
            //         console.error(`Error loading ${key} data:`, error);
            //         throw error;
            //     }
            // };

            // const populateSelectWithOptions = ($select, options, defaultValue = null) => {
            //     debugger;

            //     $select.empty();
            //     if (defaultValue) {
            //         $select.append(`<option value="0" disabled selected>${defaultValue}</option>`);
            //     }
            //     options.forEach(option => {
            //         $select.append(`<option value="${option.id}">${option.label}</option>`);
            //     });
            // };

            // const loadAllDataProducts = async () => {
            //     try {
            //         const [
            //             dataUnits,
            //             dataProducts,
            //             dataProcess,
            //             dataMachines,
            //             dataMaterials
            //         ] = await Promise.all([
            //             loadData('/api/units', 'dataUnits'),
            //             loadData('/api/products', 'dataProducts'),
            //             loadData('/api/process', 'dataProcess'),
            //             loadData('/api/machines', 'dataMachines'),
            //             loadData('/api/materials', 'dataMaterials')
            //         ]);

            //         const sortedDataProducts = sortFunction(dataProducts, 'reference');
            //         const sortedDataMaterials = sortFunction(dataMaterials, 'reference');
            //         const sortedDataMaterialsByName = sortFunction(dataMaterials, 'material');

            //         populateSelectWithOptions($('.refProduct'), sortedDataProducts, 'Seleccionar');
            //         populateSelectWithOptions($('.selectNameProduct'), sortedDataProducts, 'Seleccionar');
            //         populateSelectWithOptions($('#refCompositeProduct'), sortedDataProducts.filter(product => product.composite == 1), 'Seleccionar');
            //         populateSelectWithOptions($('#compositeProduct'), sortedDataProducts.filter(product => product.composite == 1), 'Seleccionar');
            //         populateSelectWithOptions($('#idProcess'), dataProcess, 'Seleccionar');
            //         populateSelectWithOptions($('#idMachine'), dataMachines, null);
            //         populateSelectWithOptions($('#refMaterial'), sortedDataMaterials, 'Seleccionar');
            //         populateSelectWithOptions($('#nameMaterial'), sortedDataMaterialsByName, 'Seleccionar');
            //     } catch (error) {
            //         console.error('Error loading data:', error);
            //     }
            // };

            loadAllDataProducts = async () => {
                try {
                    const [dataUnits, dataProducts, dataProcess, dataMachines, dataMaterials, dataCategories] = await Promise.all([
                        searchData('/api/units'),
                        searchData('/api/products'),
                        searchData('/api/process'),
                        searchData('/api/machines'),
                        searchData('/api/materials'),
                        searchData('/api/categories')
                    ]);

                    /* Unidades */
                    sessionStorage.setItem('dataUnits', JSON.stringify(dataUnits));

                    /* Productos */
                    sessionStorage.setItem('dataProducts', JSON.stringify(dataProducts));

                    let $select = $(`.refProduct`);
                    $select.empty();

                    // let ref = dataProducts.sort(sortReference); 
                    let ref = sortFunction(dataProducts, 'reference');

                    $select.append(
                        `<option value='0' disabled selected>Seleccionar</option>`
                    );
                    $.each(ref, function(i, value) {
                        $select.append(
                            `<option value ='${value.id_product}' class='${value.composite}'> ${value.reference} </option>`
                        );
                    });

                    let $select1 = $(`.selectNameProduct`);
                    $select1.empty();

                    let prod = sortFunction(dataProducts, 'product');

                    $select1.append(`<option value='0' disabled selected>Seleccionar</option>`);
                    $.each(prod, function(i, value) {
                        $select1.append(
                            `<option value ='${value.id_product}' class='${value.composite}'> ${value.product} </option>`
                        );
                    });

                    let compositeProduct = prod.filter(item => item.composite == 1);
                    let $select2 = $(`#refCompositeProduct`);
                    $select2.empty();

                    $select2.append(
                        `<option value='0' disabled selected>Seleccionar</option>`
                    );
                    $.each(compositeProduct, function(i, value) {
                        $select2.append(
                            `<option value ="${value.id_product}"> ${value.reference} </option>`
                        );
                    });

                    let $select3 = $(`#compositeProduct`);
                    $select3.empty();

                    $select3.append(
                        `<option value='0' disabled selected>Seleccionar</option>`
                    );
                    $.each(compositeProduct, function(i, value) {
                        $select3.append(
                            `<option value ="${value.id_product}"> ${value.product} </option>`
                        );
                    });

                    /* Procesos */
                    $select = $(`#idProcess`);
                    $select.empty();

                    $select.append(`<option disabled selected>Seleccionar</option>`);
                    $.each(dataProcess, function(i, value) {
                        $select.append(
                            `<option value = ${value.id_process} class='${value.status}'> ${value.process} </option>`
                        );
                    });

                    /* Maquinas */
                    sessionStorage.setItem('dataMachines', JSON.stringify(dataMachines));

                    $select = $(`#idMachine`);
                    $select.empty();
                    $select.append(`<option disabled>Seleccionar</option>`);
                    $select.append(`<option value="0" selected>PROCESO MANUAL</option>`);
                    $.each(dataMachines, function(i, value) {
                        $select.append(
                            `<option value = '${value.id_machine}'> ${value.machine} </option>`
                        );
                    });

                    /* Materiales */
                    sessionStorage.setItem('dataMaterials', JSON.stringify(dataMaterials));
                    ref = sortFunction(dataMaterials, 'reference');

                    $select = $(`#refMaterial`);
                    $select.empty();
                    $select.append(`<option disabled selected value='0'>Seleccionar</option>`);
                    $.each(ref, function(i, value) {
                        $select.append(
                            `<option value = ${value.id_material}> ${value.reference} </option>`
                        );
                    });

                    let name = sortFunction(dataMaterials, 'material');

                    $select1 = $(`#nameMaterial`);
                    $select1.empty();
                    $select1.append(`<option disabled selected value='0'>Seleccionar</option>`);
                    $.each(name, function(i, value) {
                        $select1.append(
                            `<option value = ${value.id_material}> ${value.material} </option>`
                        );
                    });

                    /* Categorias */
                    $select = $(`#categories`);
                    $select.empty();

                    $select.append(`<option disabled selected>Seleccionar</option>`);
                    $select.append(`<option value='0'>Todos</option>`);
                    $.each(dataCategories, function(i, value) {
                        $select.append(
                            `<option value ='${value.id_category}'> ${value.category} </option>`
                        );
                    });
                } catch (error) {
                    console.error('Error loading data:', error);
                }
            }

            loadAllDataProducts();

            loadUnitsByMagnitude = async (data, op) => {
                Object.prototype.toString.call(data) === '[object Object]' ?
                    (id_magnitude = data.id_magnitude) :
                    (id_magnitude = data);

                let dataUnits = JSON.parse(sessionStorage.getItem('dataUnits'));

                let dataPMaterials = dataUnits.filter(item => item.id_magnitude == id_magnitude);

                let $select = $(`#units`);
                $select.empty();

                $select.append(`<option disabled selected>Seleccionar</option>`);
                $.each(dataPMaterials, function(i, value) {
                    if (id_magnitude == '4' && op == 2) {
                        if (value.id_unit == data.id_unit) {
                            $select.empty();
                            $select.append(
                                `<option value ='${value.id_unit}' selected> ${value.unit} </option>`
                            );
                            return false;
                        }
                    } else $select.append(`<option value = ${value.id_unit}> ${value.unit} </option>`);
                });
            };
        });
    </script>
    <script src="/cost/js/config/productMaterials/tblConfigMaterials.js"></script>
    <script src="/cost/js/config/productProcess/tblConfigProcess.js"></script>
    <script src="/cost/js/config/services/tblExternalServices.js"></script>
    <script src="/global/js/global/orderData.js"></script>
    <script src="/cost/js/config/productMaterials/productMaterials.js"></script>
    <script src="/cost/js/config/productMaterials/compositeProducts.js"></script>
    <script src="/cost/js/config/productProcess/productProcess.js"></script>
    <script src="/cost/js/config/services/externalServices.js"></script>
    <script src="../global/js/import/import.js"></script>
    <script src="/cost/js/config/productMaterials/importProductMaterials.js"></script>
    <script src="/cost/js/config/productProcess/importProductProcess.js"></script>
    <script src="/cost/js/config/services/importExternalServices.js"></script>
    <script src="../global/js/import/file.js"></script>
</body>

</html>