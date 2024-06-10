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
    <title>Tezlik - Cost | Expenses</title>
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
                        <?php if (
                            $_SESSION['expense'] == 1 || ($_SESSION['cost_multiproduct'] == 1 &&
                                $_SESSION['plan_cost_multiproduct'] == 1)
                        ) { ?>
                            <div class="tab-pane cardsGeneral cardExpenses">
                                <div class="row align-items-center">
                                    <div class="col-sm-5 col-xl-6">
                                        <div class="page-title">
                                            <h3 class="mb-1 font-weight-bold text-dark"><i class="bi bi-currency-dollar mr-1"></i>Gastos Generales</h3>
                                            <ol class="breadcrumb mb-3 mb-md-0">
                                                <li class="breadcrumb-item active">Ingrese todos los gastos de su empresa de acuerdo con su contabilidad</li>
                                            </ol>
                                        </div>
                                    </div>
                                    <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                        <div class="col-xs-2 mr-2">
                                            <button class="btn btn-warning" id="btnNewExpense">Nuevo Gasto</button>
                                        </div>
                                        <div class="col-xs-2 py-2 mr-2">
                                            <button class="btn btn-info" id="btnImportNewAssExpenses" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Importar Gastos"><i class="fas fa-download" style="font-size: 20px;"></i></button>
                                        </div>
                                        <?php if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) { ?>
                                            <div class="col-xs-2 py-2 mr-2 mb-0 first-two-selectors floating-label enable-floating-label show-label">
                                                <label>Und Produccion</label>
                                                <select class="form-control selectProductionCenter" id="selectProductionCenterExpenses1"></select>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($_SESSION['anual_expense'] == 1) { ?>
                            <div class="tab-pane cardsGeneral cardExpensesAnual" style="display: none;">
                                <div class="row align-items-center">
                                    <div class="col-sm-5 col-xl-6">
                                        <div class="page-title">
                                            <h3 class="mb-1 font-weight-bold text-dark"><i class="bi bi-currency-dollar mr-1"></i>Gastos Generales (Anuales)</h3>
                                            <ol class="breadcrumb mb-3 mb-md-0">
                                                <li class="breadcrumb-item active">Ingrese todos los gastos de su empresa de acuerdo con su contabilidad</li>
                                            </ol>
                                        </div>
                                    </div>
                                    <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                        <div class="col-xs-2 mr-2">
                                            <button class="btn btn-warning" id="btnNewExpenseAnual">Nuevo Gasto</button>
                                        </div>
                                        <div class="col-xs-2 py-2 mr-2">
                                            <button class="btn btn-info" id="btnImportNewAssExpensesAnual" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Importar Gastos"><i class="fas fa-download" style="font-size: 20px;"></i></button>
                                        </div>
                                        <!-- if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) { ?>
                                        <div class="col-xs-2 py-2 mr-2 mb-0 first-two-selectors floating-label enable-floating-label show-label">
                                            <label>Und Produccion</label>
                                            <select class="form-control selectProductionCenter" id="selectProductionCenterExpenses1"></select>
                                        </div>
                                    } -->
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($_SESSION['expense_distribution'] == 1) { ?>
                            <?php if (
                                $_SESSION['expense_distribution'] == 1 && $_SESSION['expense'] == 1
                                || ($_SESSION['cost_multiproduct'] == 1 &&
                                    $_SESSION['plan_cost_multiproduct'] == 1)
                            ) { ?>
                                <div class="tab-pane cardsGeneral cardExpenseDistribution" style="display: none;">
                                <?php } else { ?>
                                    <div class="tab-pane cardExpenseDistribution">
                                    <?php } ?>
                                    <div class="row align-items-center">
                                        <div class="col-sm-4 col-xl-4">
                                            <div class="page-title">
                                                <h3 class="mb-1 font-weight-bold text-dark">Gastos Generales</h3>
                                                <ol class="breadcrumb mb-3 mb-md-0">
                                                    <li class="breadcrumb-item active mt-2" id="descrExpense"></li>
                                                </ol>
                                            </div>
                                        </div>
                                        <div class="col-sm-8 col-xl-8 form-inline justify-content-sm-end">
                                            <div class="col-xs-2 mr-2 cardBtnUpdateExpenses" style="display:none">
                                                <button class="btn btn-secondary" id="btnUpdateExpenses">Modificar Gastos</button>
                                            </div>
                                            <div class="col-xs-2 mr-2 cardBtnExpensesDistribution" style="display:none">
                                                <button class="btn btn-warning" id="btnExpensesDistribution">Distribuir Gastos</button>
                                            </div>
                                            <div class="col-xs-2 mr-2 cardNewProducts" style="display:none">
                                                <button class="btn btn-secondary" id="btnNewProducts">Nuevos Productos</button>
                                            </div>
                                            <div class="col-xs-2 mr-2 cardBtnExpenseRecover" style="display:none">
                                                <button class="btn btn-warning" id="btnNewExpenseRecover">Recuperar Gastos</button>
                                            </div>
                                            <div class="col-xs-2 py-2 mr-2 cardBtnImportExpenses" style="display:none">
                                                <button class="btn btn-info" id="btnImportNewExpenses" data-bs-toggle="tooltip" data-bs-placement="bottom" title=""><i class="fas fa-download" style="font-size: 20px;"></i></button>
                                            </div>
                                            <?php if ($_SESSION['type_expense'] == 1) { ?>
                                                <div class="col-xs-2 mr-2 btn-group cardBtnExpensesDistribution" style="display: none;">

                                                </div>
                                            <?php } ?>
                                            <?php if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) { ?>
                                                <div class="col-xs-2 py-2 mr-2 mb-0 first-two-selectors floating-label enable-floating-label show-label">
                                                    <label>Und Produccion</label>
                                                    <select class="form-control selectProductionCenter" id="selectProductionCenterED1"></select>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    </div>
                                <?php } ?>
                                <?php if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) { ?>
                                    <div class="tab-pane cardsGeneral cardProductionCenter" style="display: none;">
                                        <div class="row align-items-center">
                                            <div class="col-sm-5 col-xl-6">
                                                <div class="page-title">
                                                    <h3 class="mb-1 font-weight-bold text-dark"><i class="bi bi-currency-dollar mr-1"></i>Centros de Produccion</h3>
                                                    <ol class="breadcrumb mb-3 mb-md-0">
                                                        <li class="breadcrumb-item active">Ingrese todos los gastos de su empresa de acuerdo con su contabilidad</li>
                                                    </ol>
                                                </div>
                                            </div>
                                            <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                                <div class="col-xs-2 mr-2">
                                                    <button class="btn btn-warning" id="btnNewProductionCenter">Nuevo Und Produccion</button>
                                                </div>
                                                <div class="col-xs-2 py-2 mr-2">
                                                    <button class="btn btn-info" id="btnImportNewAssExpenses" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Importar Gastos"><i class="fas fa-download" style="font-size: 20px;"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ($_SESSION['anual_expense'] == 1 && $_SESSION['flag_expense_anual'] == 1) { ?>
                                    <div class="tab-pane cardsGeneral cardExpenseDistributionAnual" style="display: none;">
                                        <div class="row align-items-center">
                                            <div class="col-sm-4 col-xl-4">
                                                <div class="page-title">
                                                    <h3 class="mb-1 font-weight-bold text-dark">Gastos Generales (Anuales)</h3>
                                                    <ol class="breadcrumb mb-3 mb-md-0">
                                                        <li class="breadcrumb-item active mt-2">Distribución de gastos anuales</li>
                                                    </ol>
                                                </div>
                                            </div>
                                            <div class="col-sm-8 col-xl-8 form-inline justify-content-sm-end">
                                                <div class="col-xs-2 mr-2 cardBtnExpensesDistributionAnual">
                                                    <button class="btn btn-warning" id="btnExpensesDistributionAnual">Distribuir Gastos</button>
                                                </div>
                                                <div class="col-xs-2 py-2 mr-2 cardBtnImportExpensesAnual">
                                                    <button class="btn btn-info" id="btnImportNewExpensesAnual" data-bs-toggle="tooltip" data-bs-placement="bottom" title=""><i class="fas fa-download" style="font-size: 20px;"></i></button>
                                                </div>
                                                <!-- if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) { ?>
                                                <div class="col-xs-2 py-2 mr-2 mb-0 first-two-selectors floating-label enable-floating-label show-label">
                                                    <label>Und Produccion</label>
                                                    <select class="form-control selectProductionCenter" id="selectProductionCenterED1"></select>
                                                </div>
                                            } -->
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                </div>

                    </div>

                    <!-- Asignacion -->
                    <?php if (
                        $_SESSION['expense'] == 1 ||
                        ($_SESSION['cost_multiproduct'] == 1 &&
                            $_SESSION['plan_cost_multiproduct'] == 1)
                    ) { ?>
                        <div class="page-content-wrapper mt--45 mb-5 cardsGeneral cardCreateExpenses">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <form id="formCreateExpenses">
                                                    <div class="form-row">
                                                        <?php if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) { ?>
                                                            <div class="col-sm-4 floating-label enable-floating-label show-label">
                                                                <label>Und Produccion</label>
                                                                <select class="form-control selectProductionCenter" id="selectProductionCenterExpenses"></select>
                                                            </div>
                                                        <?php } ?>
                                                        <?php if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) { ?>
                                                            <div class="col-sm-4 floating-label enable-floating-label show-label">
                                                            <?php } else { ?>
                                                                <div class="col-sm-6 floating-label enable-floating-label show-label">
                                                                <?php } ?>
                                                                <label>Cuenta</label>
                                                                <select class="form-control idPuc" name="idPuc" id="idPuc"></select>
                                                                </div>
                                                                <div class="col-xs-2 floating-label enable-floating-label show-label">
                                                                    <label>Valor</label>
                                                                    <input type="number" class="form-control text-center" id="expenseValue" name="expenseValue">
                                                                </div>
                                                                <div class="col-xs-2 mt-1">
                                                                    <button class="btn btn-primary" id="btnCreateExpense">Crear Gasto</button>
                                                                </div>
                                                            </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="page-content-wrapper mt--45 mb-5 cardsGeneral cardImportExpensesAssignation">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12">
                                        <form id="formImportExpesesAssignation" enctype="multipart/form-data">
                                            <div class="card">
                                                <div class="card-body pt-3">
                                                    <div class="form-row" id="formExpenses">
                                                        <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                            <input class="form-control" type="file" id="fileExpensesAssignation" accept=".xls,.xlsx">
                                                            <label for="formFile" class="form-label">Importar Asignacion de Gastos</label>
                                                        </div>
                                                        <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                            <button type="text" class="btn btn-success" id="btnImportExpensesAssignation">Importar</button>
                                                        </div>
                                                        <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                            <button type="text" class="btn btn-info" id="btnDownloadImportsExpensesAssignation">Descarga Formato</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Asignacion anual -->
                    <?php if ($_SESSION['anual_expense'] == 1 && $_SESSION['flag_expense_anual'] == 1) { ?>
                        <div class="page-content-wrapper mt--45 mb-5 cardsGeneral cardCreateExpensesAnual" style="display: none;">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <form id="formCreateExpensesAnual">
                                                    <div class="form-row">
                                                        <!-- if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) { ?>
                                                        <div class="col-sm-4 floating-label enable-floating-label show-label">
                                                            <label>Und Produccion</label>
                                                            <select class="form-control selectProductionCenter" id="selectProductionCenterExpenses"></select>
                                                        </div>
                                                     } ?>
                                                     if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) { ?>
                                                        <div class="col-sm-4 floating-label enable-floating-label show-label">
                                                     } else { ?> -->
                                                        <div class="col-sm-6 floating-label enable-floating-label show-label">
                                                            <!-- } ?> -->
                                                            <label>Cuenta</label>
                                                            <select class="form-control idPuc" name="idPuc" id="idPucAnual"></select>
                                                        </div>
                                                        <div class="col-xs-2 floating-label enable-floating-label show-label">
                                                            <label>Valor</label>
                                                            <input type="number" class="form-control text-center" id="expenseValueAnual" name="expenseValue">
                                                        </div>
                                                        <div class="col-xs-2 mt-1">
                                                            <button class="btn btn-primary" id="btnCreateExpenseAnual">Crear Gasto</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="page-content-wrapper mt--45 mb-5 cardsGeneral cardImportExpensesAssignationAnual" style="display: none;">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12">
                                        <form id="formImportExpesesAssignationAnual" enctype="multipart/form-data">
                                            <div class="card">
                                                <div class="card-body pt-3">
                                                    <div class="form-row" id="formExpensesAnual">
                                                        <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                            <input class="form-control" type="file" id="fileExpensesAssignationAnual" accept=".xls,.xlsx">
                                                            <label for="formFile" class="form-label">Importar Asignacion de Gastos</label>
                                                        </div>
                                                        <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                            <button type="text" class="btn btn-success" id="btnImportExpensesAssignationAnual">Importar</button>
                                                        </div>
                                                        <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                            <button type="text" class="btn btn-info" id="btnDownloadImportsExpensesAssignationAnual">Descarga Formato</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($_SESSION['expense_distribution'] == 1) { ?>
                        <!-- Distribucion -->
                        <div class="page-content-wrapper mt--45 mb-5 cardsGeneral cardAddNewFamily" style="display: none;">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <form id="formFamily">
                                                    <div class="form-row">
                                                        <div class="col-sm-8 floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                            <input type="text" class="form-control" id="family" name="family">
                                                            <label for="family">Nombre Familia</label>
                                                        </div>
                                                        <div class="col-xs-2" style="margin-top:12px">
                                                            <button class="btn btn-primary" id="btnSaveFamily">Guardar</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="page-content-wrapper mt--45 mb-5 cardsGeneral cardAddProductFamily" style="display: none;">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <form id="formProductFamily">
                                                    <div class="form-row">
                                                        <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                            <select class="form-control refProduct" name="refProduct" id="familyRefProduct"></select>
                                                            <label for="familyRefProduct">Referencia</label>
                                                        </div>
                                                        <div class="col-sm-5 floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                            <select class="form-control selectNameProduct" name="selectNameProduct" id="familyNameProduct"></select>
                                                            <label for="familyNameProduct">Nombre Producto</label>
                                                        </div>
                                                        <div class="col-sm-3 floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                            <select class="form-control families" name="idFamily" id="families"></select>
                                                            <label for="families">Familia</label>
                                                        </div>
                                                        <div class="col-xs-2" style="margin-top:12px">
                                                            <button class="btn btn-primary" id="btnAddProductFamily">Asignar</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="page-content-wrapper mt--45 mb-5 cardsGeneral cardExpensesDistribution">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="form-row">
                                                    <div class="col-sm-3">
                                                        <label>Gastos a distribuir</label>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control text-center" id="expensesToDistribution" name="assignableExpense" style="width: 200px;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Distribuir Gastos -->
                        <div class="page-content-wrapper mt--45 mb-5 cardsGeneral cardExpensesDistribution">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <form id="formExpensesDistribution">
                                                    <div class="form-row">
                                                        <?php if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) { ?>
                                                            <div class="col-sm-3 distribution floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                                <label>Und Produccion</label>
                                                                <select class="form-control selectProductionCenter" id="selectProductionCenterED"></select>
                                                            </div>
                                                        <?php } ?>
                                                        <div class="col-sm-2 distribution floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                            <select class="form-control refProduct" name="refProduct" id="EDRefProduct"></select>
                                                            <label for="EDRefProduct">Referencia</label>
                                                        </div>
                                                        <div class="col-sm-5 distribution input-2 floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                            <select class="form-control selectNameProduct" name="selectNameProduct" id="EDNameProduct"></select>
                                                            <label for="EDNameProduct">Nombre Producto</label>
                                                        </div>
                                                        <div class="col-sm-5 distributionFamilies floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                            <select class="form-control families" name="idFamily" id="familiesDistribute"></select>
                                                            <label for="families">Familia</label>
                                                        </div>
                                                        <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                            <input type="number" class="form-control text-center" id="undVendidas" name="unitsSold">
                                                            <label for="undVendidas">Und Vendidas (Mes)</label>
                                                        </div>
                                                        <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:5px;margin-top:7px">
                                                            <input type="number" class="form-control text-center" id="volVendidas" name="turnover">
                                                            <label for="volVendidas">Total Ventas (Mes)</label>
                                                        </div>
                                                        <div class="col-xs-2" style="margin-top:12px">
                                                            <button class="btn btn-primary" id="btnAssignExpenses">Asignar</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Nuevos Productos -->
                        <div class="page-content-wrapper mt--45 mb-5 cardsGeneral cardNewProduct">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <form id="formNewProduct">
                                                    <div class="form-row">
                                                        <div class="col-sm-2 distribution floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                            <select class="form-control" name="idProduct" id="newRefProduct"></select>
                                                            <label for="newRefProduct">Referencia (Nuevo producto)</label>
                                                        </div>
                                                        <div class="col-sm-5 distribution input-2 floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                            <select class="form-control" name="newNameProduct" id="newNameProduct"></select>
                                                            <label for="newNameProduct">Nombre Producto (Nuevo producto)</label>
                                                        </div>
                                                        <!-- <div class="col-sm-2 distribution floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                            <select class="form-control refProduct" name="refOldProduct" id="refOldProduct"></select>
                                                            <label for="refOldProduct">Referencia (Creado)</label>
                                                        </div> -->
                                                        <div class="col-sm-5 distribution input-2 floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                            <select class="form-control" name="oldNameProduct" id="oldNameProduct"></select>
                                                            <label for="oldNameProduct">Nombre Producto (Creado)</label>
                                                        </div>
                                                        <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:5px;margin-top:7px">
                                                            <input type="text" class="form-control text-center" id="pAssignableExpense" name="pAssignableExpense" readonly>
                                                            <label for="pAssignableExpense">Gasto Asignable</label>
                                                        </div>
                                                        <div class="col-xs-2" style="margin-top:12px">
                                                            <button class="btn btn-primary" id="btnAddNewProduct">Asignar</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recuperar Gastos -->
                        <div class="page-content-wrapper mt--45 mb-5 cardsGeneral cardExpenseRecover">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <form id="formExpenseRecover">
                                                    <div class="form-row">
                                                        <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                            <select class="form-control refProduct" name="idProduct" id="ERRefProduct"></select>
                                                            <label for="ERRefProduct">Referencia</label>
                                                        </div>
                                                        <div class="col-sm-6 floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                            <select class="form-control selectNameProduct" name="nameProduct" id="ERNameProduct"></select>
                                                            <label for="ERNameProduct">Nombre Producto</label>
                                                        </div>
                                                        <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:5px;margin-top:7px">
                                                            <input type="number" class="form-control text-center" id="percentage" name="percentage">
                                                            <label for="percentage">Porcentaje</label>
                                                        </div>
                                                        <div class="col-xs-2" style="margin-top:12px">
                                                            <button class="btn btn-primary" id="btnExpenseRecover">Guardar Gasto</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="page-content-wrapper mt--45 mb-5 cardsGeneral cardImportExpenses">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12">
                                        <form id="formImportExpenses" enctype="multipart/form-data">
                                            <div class="card">
                                                <div class="card-body pt-3">
                                                    <div class="form-row" id="formExpensesD">
                                                        <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                            <input class="form-control" type="file" id="fileExpenses" accept=".xls,.xlsx">
                                                            <label for="formFile" id="lblImportExpense" class="form-label"></label>
                                                        </div>
                                                        <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                            <button type="text" class="btn btn-success" id="btnImportExpenses">Importar</button>
                                                        </div>
                                                        <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                            <button type="text" class="btn btn-info" id="btnDownloadImportsExpenses">Descarga Formato</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Distribucion de Gastos Anual -->
                    <?php if ($_SESSION['anual_expense'] == 1 && $_SESSION['flag_expense_anual'] == 1) { ?>
                        <div class="page-content-wrapper mt--45 mb-5 cardsGeneral cardExpensesDistributionAnual" style="display: none;">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="form-row">
                                                    <div class="col-sm-3">
                                                        <label>Gastos a distribuir</label>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control text-center" id="expensesToDistributionAnual" name="assignableExpense" style="width: 200px;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Distribuir Gastos -->
                        <div class="page-content-wrapper mt--45 mb-5 cardsGeneral cardExpensesDistributionAnual" style="display: none;">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <form id="formExpensesDistributionAnual">
                                                    <div class="form-row">
                                                        <!-- if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) { ?>
                                                        <div class="col-sm-3 distribution floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                            <label>Und Produccion</label>
                                                            <select class="form-control selectProductionCenter" id="selectProductionCenterED"></select>
                                                        </div>
                                                     } ?> -->
                                                        <div class="col-sm-2 distribution floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                            <select class="form-control refProduct" name="refProduct" id="EDARefProduct"></select>
                                                            <label for="EDARefProduct">Referencia</label>
                                                        </div>
                                                        <div class="col-sm-5 distribution input-2 floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                            <select class="form-control selectNameProduct" name="selectNameProduct" id="EDANameProduct"></select>
                                                            <label for="EDANameProduct">Nombre Producto</label>
                                                        </div>
                                                        <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                            <input type="number" class="form-control text-center" id="undAVendidas" name="unitsSold">
                                                            <label for="undAVendidas">Und Vendidas (Anual)</label>
                                                        </div>
                                                        <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:5px;margin-top:7px">
                                                            <input type="number" class="form-control text-center" id="volAVendidas" name="turnover">
                                                            <label for="volAVendidas">Total Ventas (Anual)</label>
                                                        </div>
                                                        <div class="col-xs-2" style="margin-top:12px">
                                                            <button class="btn btn-primary" id="btnAssignExpensesAnual">Asignar</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Importar -->
                        <div class="page-content-wrapper mt--45 mb-5 cardsGeneral cardImportExpensesAnual" style="display: none;">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12">
                                        <form id="formImportExpensesAnual" enctype="multipart/form-data">
                                            <div class="card">
                                                <div class="card-body pt-3">
                                                    <div class="form-row" id="formExpensesDA">
                                                        <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                            <input class="form-control" type="file" id="fileExpensesA" accept=".xls,.xlsx">
                                                            <label for="formFile" id="lblImportExpenseA" class="form-label">Importar Distribución de Gasto</label>
                                                        </div>
                                                        <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                            <button type="text" class="btn btn-success" id="btnImportExpensesA">Importar</button>
                                                        </div>
                                                        <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                            <button type="text" class="btn btn-info" id="btnDownloadImportsExpensesA">Descarga Formato</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Unidad de Produccion -->
                    <?php if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) { ?>
                        <div class="page-content-wrapper mt--45 mb-5 cardsGeneral cardAddNewProductionCenter" style="display: none;">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <form id="formAddPCenter">
                                                    <div class="form-row">
                                                        <div class="col-sm-8 floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
                                                            <input type="text" class="form-control" id="production" name="production">
                                                            <label for="family">Unidad de Produccion</label>
                                                        </div>
                                                        <div class="col-xs-2" style="margin-top:12px">
                                                            <button class="btn btn-primary" id="btnSavePCenter">Guardar</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- page content -->
                    <div class="page-content-wrapper mt--45">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                                        <?php if (
                                            $_SESSION['expense'] == 1 ||
                                            ($_SESSION['cost_multiproduct'] == 1 &&
                                                $_SESSION['plan_cost_multiproduct'] == 1)
                                        ) { ?>
                                            <li class="nav-item">
                                                <a class="nav-link active selectNavigation" id="sExpenses" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-activity" aria-selected="true">
                                                    <i class="fas fa-flask mr-1"></i>Asignación
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php if ($_SESSION['anual_expense'] == 1 && $_SESSION['flag_expense_anual'] == 1) { ?>
                                            <li class="nav-item">
                                                <a class="nav-link selectNavigation" id="sExpensesA" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-activity" aria-selected="true">
                                                    <i class="fas fa-flask mr-1"></i>Asignación (Anual)
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php if ($_SESSION['expense_distribution'] == 1) { ?>
                                            <li class="nav-item">
                                                <?php if (
                                                    $_SESSION['expense_distribution'] == 1 && $_SESSION['expense'] == 1
                                                    || ($_SESSION['cost_multiproduct'] == 1 &&
                                                        $_SESSION['plan_cost_multiproduct'] == 1)
                                                ) { ?>
                                                    <a class="nav-link selectNavigation" id="distribution" data-toggle="pill" href="#pills-projects" role="tab" aria-controls="pills-projects" aria-selected="false">
                                                    <?php } else { ?>
                                                        <a class="nav-link selectNavigation active" id="distribution" data-toggle="pill" href="#pills-projects" role="tab" aria-controls="pills-projects" aria-selected="true">
                                                        <?php } ?>
                                                        <?php if ($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) { ?>
                                                            <i class="bi bi-arrow-repeat mr-1"></i>Distribución
                                                        <?php } ?>
                                                        <?php if ($_SESSION['flag_expense'] == 2) { ?>
                                                            <i class="bi bi-arrow-repeat mr-1"></i>Recuperación
                                                        <?php } ?>
                                                        </a>
                                            </li>
                                        <?php } ?>
                                        <?php if ($_SESSION['anual_expense'] == 1 && $_SESSION['flag_expense_anual'] == 1) { ?>
                                            <li class="nav-item">
                                                <a class="nav-link selectNavigation" id="sDistributionA" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-activity" aria-selected="true">
                                                    <i class="fas fa-flask mr-1"></i>Distribución (Anual)
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) { ?>
                                            <li class="nav-item">
                                                <a class="nav-link selectNavigation" id="sProductionCenter" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-activity" aria-selected="true">
                                                    <i class="fas fa-flask mr-1"></i>Unidades Produccion
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <?php if (
                                            $_SESSION['expense'] == 1 ||
                                            ($_SESSION['cost_multiproduct'] == 1 &&
                                                $_SESSION['plan_cost_multiproduct'] == 1)
                                        ) { ?>
                                            <div class="tab-pane cardsGeneral cardExpenses">
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped" id="tblAssExpenses">
                                                            <tfoot>
                                                                <tr>
                                                                    <th></th>
                                                                    <th></th>
                                                                    <th></th>
                                                                    <th class="classRight">Total:</th>
                                                                    <th></th>
                                                                    <th></th>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php if ($_SESSION['anual_expense'] == 1 && $_SESSION['flag_expense_anual'] == 1) { ?>
                                            <div class="tab-pane cardsGeneral cardExpensesAnual" style="display: none;">
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped" id="tblAssExpensesAnual">
                                                            <tfoot>
                                                                <tr>
                                                                    <th></th>
                                                                    <th></th>
                                                                    <th></th>
                                                                    <th class="classRight">Total:</th>
                                                                    <th></th>
                                                                    <th></th>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php if ($_SESSION['expense_distribution'] == 1) { ?>
                                            <?php if (
                                                $_SESSION['expense_distribution'] == 1 && $_SESSION['expense'] == 1
                                                || ($_SESSION['cost_multiproduct'] == 1 &&
                                                    $_SESSION['plan_cost_multiproduct'] == 1)
                                            ) { ?>
                                                <div class="row cardsGeneral cardExpenseDistribution" style="display: none;">
                                                <?php } else { ?>
                                                    <div class="row cardsGeneral cardExpenseDistribution">
                                                    <?php } ?>
                                                    <div class="col-12 cardTblMultiproducts" style="display: none;">
                                                        <div class="card">
                                                            <div class="card-header row">
                                                                <h5 class="col-sm-10 card-title">Multiproductos</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="table-responsive">
                                                                    <table class="table table-striped text-center" id="tblMultiproducts">
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="width: 350px">Producto</th>
                                                                                <th>No Unidades Vendidas</th>
                                                                                <th style="width: 150px;" id="lblPrice">Precio</th>
                                                                                <!-- <th style="width: 150px;">Costo Variable</th> -->
                                                                                <th style="width: 150px;">Participacion</th>
                                                                                <th>Margen De Contribucion</th>
                                                                                <!-- <th>Promedio Ponderado</th> -->
                                                                                <th>Unidades A Vender</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="tblMultiproductsBody"></tbody>
                                                                        <tfoot>
                                                                            <tr>
                                                                                <td>Total:</td>
                                                                                <td id="totalSoldsUnits"></td>
                                                                                <!-- <td></td> -->
                                                                                <td></td>
                                                                                <td id="totalParticipation"></td>
                                                                                <td></td>
                                                                                <!-- <td id="totalAverages"></td> -->
                                                                                <td id="totalSumUnits"></td>
                                                                            </tr>
                                                                        </tfoot>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 cardTblExpensesDistribution">
                                                        <div class="card-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-striped" id="tblExpenses">
                                                                    <?php if ($_SESSION['flag_expense'] == 1) { ?>
                                                                        <tfoot>
                                                                            <tr>
                                                                                <th></th>
                                                                                <th></th>
                                                                                <th>Total:</th>
                                                                                <th></th>
                                                                                <th></th>
                                                                                <th></th>
                                                                                <th></th>
                                                                                <th></th>
                                                                            </tr>
                                                                        </tfoot>
                                                                    <?php } ?>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 cardTblFamilies" style="display: none;">
                                                        <div class="card-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-striped" id="tblFamilies">

                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($_SESSION['anual_expense'] == 1 && $_SESSION['flag_expense_anual'] == 1) { ?>
                                                    <div class="tab-pane cardsGeneral cardExpenseDistributionAnual" style="display: none;">
                                                        <div class="card-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-striped" id="tblExpensesDistributionAnual">
                                                                    <tfoot>
                                                                        <tr>
                                                                            <th></th>
                                                                            <th></th>
                                                                            <th>Total:</th>
                                                                            <th></th>
                                                                            <th></th>
                                                                            <th></th>
                                                                            <th></th>
                                                                            <!-- <th></th> -->
                                                                        </tr>
                                                                    </tfoot>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) { ?>
                                                    <div class="tab-pane cardsGeneral cardProductionCenter" style="display: none;">
                                                        <div class="card-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-striped" id="tblPCenter">
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
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
                type_expense = "<?= $_SESSION['type_expense'] ?>";
                flag_composite_product = "<?= $_SESSION['flag_composite_product'] ?>";
                production_center = "<?= $_SESSION['production_center'] ?>";
                flag_production_center = "<?= $_SESSION['flag_production_center'] ?>";
                DatatableTblMultiproducts = 0;
            </script>

            <script src="../global/js/import/import.js"></script>
            <script src="../global/js/import/file.js"></script>
            <script src="/global/js/global/orderData.js"></script>

            <?php if (
                $_SESSION['expense'] == 1 ||
                ($_SESSION['cost_multiproduct'] == 1 &&
                    $_SESSION['plan_cost_multiproduct'] == 1)
            ) { ?>
                <script src="/cost/js/general/expenses/expense.js"></script>
                <script src="/cost/js/general/expenses/tblExpenses.js"></script>
                <script src="/cost/js/general/puc/configPuc.js"></script>
                <script src="/cost/js/general/expenses/importExpense.js"></script>
            <?php } ?>
            <?php if ($_SESSION['anual_expense'] == 1 && $_SESSION['flag_expense_anual'] == 1) { ?>
                <script src="/cost/js/general/expensesAnual/expense.js"></script>
                <script src="/cost/js/general/expensesAnual/tblExpenses.js"></script>
                <script src="/cost/js/general/expensesAnual/importExpense.js"></script>
            <?php } ?>
            <?php if ($_SESSION['expense_distribution'] == 1) { ?>
                <script src="/cost/js/general/expensesDistribution/tblExpensesDistribution.js"></script>
                <script src="/cost/js/general/expenseRecover/tblExpenseRecover.js"></script>
                <script src="/cost/js/general/expensesDistribution/configExpensesDistribution.js"></script>
                <script src="/cost/js/general/expenseRecover/expenseRecover.js"></script>
                <script src="/cost/js/general/expenseRecover/updateExpenses.js"></script>
                <script src="/cost/js/general/expensesDistribution/expensesDistribution.js"></script>
                <script src="/cost/js/general/expensesDistribution/newProducts.js"></script>
                <script src="/cost/js/general/expensesDistribution/importExpensesDistribution.js"></script>

                <?php if ($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) { ?>
                    <script src="/cost/js/general/expensesDistribution/configProducts.js"></script>
                    <script src="/cost/js/general/expensesDistribution/families/configFamilies.js"></script>
                    <script src="/cost/js/general/expensesDistribution/families/family.js"></script>
                    <script src="/cost/js/general/expensesDistribution/families/tblFamilies.js"></script>
                    <script src="/cost/js/general/expensesDistribution/families/tblExpensesDistributionFamilies.js"></script>
                    <script src="/cost/js/general/expensesDistribution/families/configProducts.js"></script>
                    <script src="/cost/js/tools/multiproduct/tblMultiproducts.js"></script>
                    <script src="/cost/js/tools/multiproduct/calcMultiproducts.js"></script>
                    <script src="/cost/js/tools/multiproduct/saveMultiproducts.js"></script>
                <?php } ?>

                <?php if ($_SESSION['flag_expense'] == 2) { ?>
                    <script src="/cost/js/general/expenseRecover/configProducts.js"></script>
                <?php } ?>
            <?php } ?>
            <?php if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) { ?>
                <script src="/cost/js/general/productionCenter/productionCenter.js"></script>
                <script src="/cost/js/general/productionCenter/tblProductionCenter.js"></script>
                <script src="/cost/js/general/productionCenter/configProductionCenter.js"></script>
            <?php } ?>
            <?php if ($_SESSION['anual_expense'] == 1 && $_SESSION['flag_expense_anual'] == 1) { ?>
                <script src="/cost/js/general/expensesDistributionAnual/tblExpensesDistribution.js"></script>
                <script src="/cost/js/general/expensesDistributionAnual/configExpensesDistribution.js"></script>
                <script src="/cost/js/general/expensesDistributionAnual/expensesDistribution.js"></script>
                <script src="/cost/js/general/expensesDistributionAnual/importExpensesDistribution.js"></script>
                <script src="/cost/js/general/expensesDistributionAnual/configProducts.js"></script>
            <?php } ?>
</body>

</html>