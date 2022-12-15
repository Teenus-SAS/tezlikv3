<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="LetStart Admin is a full featured, multipurpose, premium bootstrap admin template built with Bootstrap 4 Framework, HTML5, CSS and JQuery.">
    <meta name="keywords" content="admin, panels, dashboard, admin panel, multipurpose, bootstrap, bootstrap4, all type of dashboards">
    <meta name="author" content="MatrrDigital">
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
                                        <li class="breadcrumb-item active">Asignación de materias primas al producto</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6">
                                <div class="form-inline justify-content-sm-end">
                                    <button class="btn btn-warning" id="btnCreateProduct">Adicionar Nueva Materia Prima</button>
                                    <button class="btn btn-info ml-3" id="btnImportNewProductsMaterials">Importar Materia Prima</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardCreateRawMaterials">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="gridx2">
                                            <label for="">Referencia</label>
                                            <label for="">Producto</label>
                                            <select class="form-control refProduct" name="refProduct" id="refProduct"></select>
                                            <select class="form-control selectNameProduct" name="selectNameProduct" id="selectNameProduct"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardAddMaterials">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formAddMaterials">
                                            <div class="gridx4pm">
                                                <label for="">Materia Prima</label>
                                                <label for="">Cantidad</label>
                                                <label for="">Unidad</label>
                                                <label for=""></label>
                                                <select class="form-control" name="material" id="material"></select>
                                                <input class="form-control text-center number" type="text" name="quantity" id="quantity">
                                                <input class="form-control text-center number" type="text" name="unity" id="unity" disabled>
                                                <button class="btn btn-success" id="btnAddMaterials">Adicionar Materia Prima</button>
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
                            <form id="formImportProductMaterial" enctype="multipart/form-data">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body pt-3 pb-0">
                                            <div class="gridx4ip">
                                                <div class="form-group floating-label enable-floating-label show-label mt-3 drag-area" style="margin-top:0px!important">
                                                    <input class="form-control" type="file" id="fileProductsMaterials" accept=".xls,.xlsx">
                                                    <label for="formFile" class="form-label"> Importar Productos*Materia Prima</label>
                                                </div>
                                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                                    <button type="text" class="btn btn-success" id="btnImportProductsMaterials">Importar</button>
                                                </div>
                                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                                    <button type="text" class="btn btn-info" id="btnDownloadImportsProductsMaterials">Descarga Formato</button>
                                                </div>
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
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped text-center" id="tblConfigMaterials" name="tblConfigMaterials">

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

    <script src="/global//js/global/number.js"></script>
    <script src="/cost/js/config/productMaterials/tblConfigMaterials.js"></script>
    <script src="/cost/js/basic/products/configProducts.js"></script>
    <script src="/cost/js/basic/rawMaterials/configRawMaterials.js"></script>
    <script src="/cost/js/config/productMaterials/productMaterials.js"></script>
    <script src="../global/js/import/import.js"></script>
    <script src="/cost/js/config/productMaterials/importProductMaterials.js"></script>
    <script src="../global/js/import/file.js"></script>
</body>

</html>