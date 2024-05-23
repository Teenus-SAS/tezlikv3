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
                <a href="javascript:;" class="close-btn" style="display: none;"><i class="bi bi-x-circle-fill"></i></a>
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
                                        <button class="btn btn-info" id="btnImportNewProductsMaterials" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Importar Materia Prima"><i class="fas fa-download" style="font-size: 20px;"></i></button>
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
                                        <button class="btn btn-info" id="btnImportNewProductProcess" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Importar Procesos"><i class="fas fa-download" style="font-size: 20px;"></i></button>
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
                                        <button class="btn btn-info" id="btnImportNewExternalServices" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Importar Servicios Externos"><i class="fas fa-download" style="font-size: 20px;"></i></button>
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
                                                <div class="col-sm-2 floating-label enable-floating-label show-label categories" style="margin-bottom:20px">
                                                    <label>Categoria</label>
                                                    <select class="form-control material" name="categories" id="categories"></select>
                                                </div>
                                                <div class="col-sm-4 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label>Referencia</label>
                                                    <select class="form-control material inputs" name="refMaterial" id="refMaterial"></select>
                                                </div>
                                                <div class="col-sm-6 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label>Materia Prima</label>
                                                    <select class="form-control material inputs" name="material" id="nameMaterial"></select>
                                                </div>
                                                <div class="w-100"></div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                    <select class="form-control inputs" id="units" name="unit"></select>
                                                    <label>Unidad</label>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label>Cantidad</label>
                                                    <input class="form-control text-center quantity inputs" type="number" name="quantity" id="quantity">
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label>Desperdicio (%)</label>
                                                    <input class="form-control text-center quantity" type="number" name="waste" id="waste">
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label>Cantidad Total</label>
                                                    <input class="form-control text-center" type="number" name="quantityYotal" id="quantityYotal" readonly>
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
                                                    <select class="form-control compositeProduct inputs" name="refCompositeProduct" id="refCompositeProduct"></select>
                                                </div>
                                                <div class="col-sm-5 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label>Producto</label>
                                                    <select class="form-control compositeProduct inputs" name="compositeProduct" id="compositeProduct"></select>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                    <select class="form-control inputs" id="unit2" name="unit">
                                                    </select>
                                                    <label>Unidad</label>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label>Cantidad</label>
                                                    <input class="form-control text-center inputs" type="number" name="quantity" id="quantity2">
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
                                                <div class="col-sm-4 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label>Proceso</label>
                                                    <select class="form-control inputs" name="idProcess" id="idProcess"></select>
                                                </div>
                                                <div class="col-sm-8 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label>Maquina</label>
                                                    <select class="form-control inputs" name="idMachine" id="idMachine"></select>
                                                </div>
                                                <div class="col-xs-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <?php if ($_SESSION['inyection'] == 1) { ?>
                                                        <label class="text-center">Tiempo/Und</label>
                                                    <?php } else { ?>
                                                        <label class="text-center">t.alistamiento (min)</label>
                                                    <?php } ?>
                                                    <input class="form-control text-center inputs time" type="number" name="enlistmentTime" id="enlistmentTime" data-toggle="tooltip" title="Ingrese solo el tiempo necesario para fabricar una unidad">
                                                </div>
                                                <div class="col-xs-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <?php if ($_SESSION['inyection'] == 1) { ?>
                                                        <label class="text-center">% Eficiencia</label>
                                                    <?php } else { ?>
                                                        <label class="text-center">t.operacion (min)</label>
                                                    <?php } ?>
                                                    <input class="form-control text-center inputs time" type="number" name="operationTime" id="operationTime" data-toggle="tooltip" title="Ingrese solo el tiempo necesario para fabricar una unidad">
                                                </div>
                                                <div class="col-xs-2 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                    <?php if ($_SESSION['inyection'] == 1) { ?>
                                                        <label class="text-center">t.total</label>
                                                    <?php } else { ?>
                                                        <label class="text-center">t.total (min)</label>
                                                    <?php } ?>
                                                    <input class="form-control text-center inputs" type="number" name="subTotalTime" id="subTotalTime" readonly>
                                                </div>
                                                <div class="col-xs-2 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                    <label class="text-center">Eficiencia(%)</label>
                                                    <input class="form-control text-center time" type="number" name="efficiency" id="efficiency">
                                                </div>
                                                <div class="col-xs-2 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                    <label class="text-center">t.total</label>
                                                    <input class="form-control text-center" type="number" name="totalTime" id="totalTime" readonly>
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
                                                <?php if ($_SESSION['external_service'] == 1) { ?>
                                                    <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <?php } else { ?>
                                                        <div class="col-sm-2 floating-label enable-floating-label show-label" style="display:none">
                                                        <?php } ?>
                                                        <label>Servicio Almacenado</label>
                                                        <select class="form-control inputs" name="generalServices" id="generalServices"></select>
                                                        </div>
                                                        <div class="col-sm-6 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                            <label>Servicio Externo</label>
                                                            <input class="form-control inputs" type="text" name="service" id="service">
                                                        </div>
                                                        <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                            <label>Costo</label>
                                                            <input class="form-control text-center inputs" type="number" name="costService" id="costService">
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
                                                            <th></th>
                                                            <th>Total:</th>
                                                            <th></th>
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
                                                            <th id="totalEfficiency"></th>
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

        // price_usd = 
        flag_currency_usd = "<?= $_SESSION['flag_currency_usd'] ?>";
        flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";
        flag_composite_product = "<?= $_SESSION['flag_composite_product'] ?>";
        flag_employee = "<?= $_SESSION['flag_employee'] ?>";
        flag_indirect = "<?= $_SESSION['flag_indirect'] ?>";
        inyection = "<?= $_SESSION['inyection'] ?>";
        // $('.loading').show(800);
        // document.body.style.overflow = 'hidden';

        $(document).ready(function() {

            // Evita la duplicación de código al manipular los selectores
            const $refProduct = $('.refProduct');
            const $selectNameProduct = $('.selectNameProduct');
            const $refCompositeProduct = $('#refCompositeProduct');
            const $compositeProduct = $('#compositeProduct');
            const $process = $('#idProcess');
            const $machines = $('#idMachine');
            const $refMaterial = $('#refMaterial');
            const $nameMaterial = $('#nameMaterial');
            const $categories = $('#categories');
            const $generalServices = $('#generalServices');

            // Evita la duplicación de código al manejar la carga de datos
            async function loadData(url, key) {
                const data = await searchData(url);
                sessionStorage.setItem(key, JSON.stringify(data));
                return data;
            }

            async function loadAllDataProducts() {
                try {
                    // console.time('Load all data products');
                    const [
                        dataUnits,
                        dataProducts,
                        dataProcess,
                        dataMachines,
                        dataMaterials,
                        dataCategories,
                        dataProductMaterials,
                        dataCompositeProduct,
                        dataProductProcess,
                        dataServices,
                        dataGServices
                    ] = await Promise.all([
                        loadData('/api/units', 'dataUnits'),
                        loadData('/api/products', 'dataProducts'),
                        loadData('/api/process', 'dataProcess'),
                        loadData('/api/machines', 'dataMachines'),
                        loadData('/api/materials', 'dataMaterials'),
                        loadData('/api/categories', 'dataCategories'),
                        loadData('/api/allProductsMaterials', 'dataProductMaterials'),
                        loadData('/api/allCompositeProducts', 'dataCompositeProduct'),
                        loadData('/api/allProductsProcess', 'dataProductProcess'),
                        loadData('/api/allExternalservices', 'dataServices'),
                        loadData('/api/generalExternalservices', 'dataGServices'),
                    ]);
                    // console.timeEnd('Load all data products');

                    // console.time('Update DOM');

                    // Función para crear opciones y añadirlas al fragmento
                    const createOptions = (data, key, textKey) => {
                        const fragment = document.createDocumentFragment();
                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item[key];
                            option.textContent = item[textKey];
                            fragment.appendChild(option);
                        });
                        return fragment;
                    };

                    // Lógica para cargar datos en los selectores
                    // Productos
                    $refProduct.empty().append(createOptions([{
                        id_product: 0,
                        reference: 'Seleccionar'
                    }], 'id_product', 'reference'));
                    $refProduct.append(createOptions(sortFunction(dataProducts, 'reference'), 'id_product', 'reference'));

                    $selectNameProduct.empty().append(createOptions([{
                        id_product: 0,
                        product: 'Seleccionar'
                    }], 'id_product', 'product'));
                    $selectNameProduct.append(createOptions(sortFunction(dataProducts, 'product'), 'id_product', 'product'));

                    // Productos Compuestos
                    const compositeProduct = dataProducts.filter(item => item.composite == 1);

                    $refCompositeProduct.empty().append(createOptions([{
                        id_product: 0,
                        reference: 'Seleccionar'
                    }], 'id_product', 'reference'));
                    $refCompositeProduct.append(createOptions(sortFunction(compositeProduct, 'reference'), 'id_product', 'reference'));

                    $compositeProduct.empty().append(createOptions([{
                        id_product: 0,
                        product: 'Seleccionar'
                    }], 'id_product', 'product'));
                    $compositeProduct.append(createOptions(sortFunction(compositeProduct, 'product'), 'id_product', 'product'));

                    // Procesos
                    $process.empty().append(createOptions([{
                        id_process: 0,
                        process: 'Seleccionar'
                    }], 'id_process', 'process'));
                    $process.append(createOptions(dataProcess, 'id_process', 'process'));

                    // Maquinas
                    $machines.empty().append(createOptions([{
                        id_machine: 0,
                        machine: 'PROCESO MANUAL'
                    }], 'id_machine', 'machine'));
                    $machines.append(createOptions(dataMachines, 'id_machine', 'machine'));

                    // Materiales
                    $refMaterial.empty().append(createOptions([{
                        id_material: 0,
                        reference: 'Seleccionar'
                    }], 'id_material', 'reference'));
                    $refMaterial.append(createOptions(sortFunction(dataMaterials, 'reference'), 'id_material', 'reference'));

                    $nameMaterial.empty().append(createOptions([{
                        id_material: 0,
                        material: 'Seleccionar'
                    }], 'id_material', 'material'));
                    $nameMaterial.append(createOptions(sortFunction(dataMaterials, 'material'), 'id_material', 'material'));

                    // Categorias
                    $categories.empty().append(createOptions([{
                        id_category: 0,
                        category: 'Seleccionar'
                    }, {
                        id_category: 0,
                        category: 'Todos'
                    }], 'id_category', 'category'));
                    $categories.append(createOptions(dataCategories, 'id_category', 'category'));

                    // Servicios Generales 
                    $generalServices.empty().append(createOptions([{
                        id_general_service: 0,
                        name_service: 'Seleccionar'
                    }], 'id_general_service', 'name_service'));
                    $generalServices.append(createOptions(dataGServices, 'id_general_service', 'name_service'));

                    // $('.loading').hide(800);
                    // document.body.style.overflow = '';
                } catch (error) {
                    console.error('Error loading data:', error);
                }
            }

            loadUnitsByMagnitude = async (data, op) => {
                const id_magnitude = typeof data === 'object' ? data.id_magnitude : data;

                let dataUnits = JSON.parse(sessionStorage.getItem('dataUnits'));
                let dataPMaterials = dataUnits.filter(item => item.id_magnitude == id_magnitude);

                let $select = $('#units');
                $select.empty().append(`<option disabled selected>Seleccionar</option>`);

                dataPMaterials.forEach(value => {
                    if (id_magnitude == '4' && op == 2 && value.id_unit == data.id_unit) {
                        $select.empty().append(`<option value ='${value.id_unit}' selected> ${value.unit} </option>`);
                        return false;
                    } else {
                        $select.append(`<option value = ${value.id_unit}> ${value.unit} </option>`);
                    }
                });
            };

            // Manejadores de eventos
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

            $('#idMachine').change(function(e) {
                e.preventDefault();
                // Lógica para manejar el cambio de máquina

                let data = JSON.parse(sessionStorage.getItem('dataMachines'));

                data = data.filter(item => item.id_machine == this.value);

                !data[0] ? unity_time = 0 : unity_time = data[0].unity_time;

                $('#enlistmentTime').val(unity_time);

                if (this.value === '0') {
                    $('.checkMachine').hide(800);
                    $('#checkMachine').prop('checked', false);
                } else
                    $('.checkMachine').show(800);
            });

            // Inicia la carga de datos
            loadAllDataProducts();
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