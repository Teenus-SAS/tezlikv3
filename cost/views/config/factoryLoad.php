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
    <meta name="description" content="LetStart Admin is a full featured, multipurpose, premium bootstrap admin template built with Bootstrap 4 Framework, HTML5, CSS and JQuery.">
    <meta name="keywords" content="admin, panels, dashboard, admin panel, multipurpose, bootstrap, bootstrap4, all type of dashboards">
    <meta name="author" content="MatrrDigital">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tezlik - Cost | Factory Load</title>
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
                                    <h3 class="mb-1 font-weight-bold text-dark">Carga Fabril</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Asignación de costos directos relacionados a una máquina</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mr-2">
                                    <button class="btn btn-warning" id="btnNewFactoryLoad">Nueva Carga Fabril Máquina</button>
                                </div>
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-info" id="btnImportNewFactoryLoad">Importar Carga Fabril Máquinas</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardFactoryLoad">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formNewFactoryLoad">
                                            <div class="form-row">
                                                <div class="col-sm-4 floating-label enable-floating-label show-label">
                                                    <label for="">Máquina</label>
                                                    <select class="form-control" name="idMachine" id="machine"></select>
                                                </div>
                                                <div class="col-sm-5 floating-label enable-floating-label show-label">
                                                    <label for="">Descripción Carga fabril</label>
                                                    <input class="form-control" name="descriptionFactoryLoad" id="descriptionFactoryLoad" />
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label">
                                                    <label for="">Costo</label>
                                                    <input class="form-control text-center number" type="text" name="costFactory" id="costFactory" />
                                                </div>
                                                <div class="col-xs-2 mt-1">
                                                    <button class="btn btn-primary" id="btnCreateFactoryLoad">Asignar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportFactoryLoad">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <form id="formImportFactoryLoad" enctype="multipart/form-data">
                                    <div class="card">
                                        <div class="card-body pt-3">
                                            <div class="form-row">
                                                <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:0px!important">
                                                    <input class="form-control" type="file" id="fileFactoryLoad" accept=".xls,.xlsx">
                                                    <label for="formFile" class="form-label"> Importar Carga Fabril Máquinas</label>
                                                </div>
                                                <div class="col-xs-2" style="margin-bottom:0px;margin-top:7px">
                                                    <button type="text" class="btn btn-success" id="btnImportFactoryLoad">Importar</button>
                                                </div>
                                                <div class="col-xs-2" style="margin-bottom:0px;margin-top:7px">
                                                    <button type="text" class="btn btn-info" id="btnDownloadImportsFactoryLoad">Descarga Formato</button>
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
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="tblFactoryLoad">

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

    <script src="/cost/js/basic/machines/configMachines.js"></script>
    <script src="/cost/js/config/factoryLoad/tblFactoryLoad.js"></script>
    <script src="/cost/js/config/factoryLoad/factoryLoad.js"></script>
    <script src="../global/js/import/import.js"></script>
    <script src="/cost/js/config/factoryLoad/importFactoryLoad.js"></script>
    <script src="../global/js/import/file.js"></script>
</body>

</html>