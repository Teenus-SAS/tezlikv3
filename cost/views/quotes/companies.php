<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="LetStart Admin is a full featured, multipurpose, premium bootstrap admin template built with Bootstrap 4 Framework, HTML5, CSS and JQuery.">
    <meta name="keywords" content="admin, panels, dashboard, admin panel, multipurpose, bootstrap, bootstrap4, all type of dashboards">
    <meta name="author" content="MatrrDigital">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tezlik - Cost | Companies</title>
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
                                    <h3 class="mb-1 font-weight-bold text-dark">Compañias</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Creación de Compañias</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-4 col-xl-6">
                                <div class="form-inline justify-content-sm-end">
                                    <button class="btn btn-warning" id="btnNewCompany">Nueva Compañia</button>
                                    <!-- <button class="btn btn-info ml-3" id="btnImportNewCompanies">Importar Compañias</button> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardCreateCompany">
                    <div class="container-fluid">
                        <div class="row">
                            <form id="formCreateCompany">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col form-group floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                                    <input type="text" class="form-control" name="nit" id="nit">
                                                    <label for="nit">NIT<span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                                <div class="col form-group floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                                    <input type="text" class="form-control" name="companyName" id="companyName">
                                                    <label for="companyName">Nombre<span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                                <div class="col form-group floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                                    <input type="text" class="form-control" name="address" id="address">
                                                    <label for="address">Dirección<span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                                <div class="w-100 mt-4"></div>
                                                <div class="col form-group floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                                    <input type="text" class="form-control" name="phone" id="phone">
                                                    <label for="phone">Telefono <span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                                <div class="col form-group floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                                    <input type="text" class="form-control" name="city" id="city">
                                                    <label for="city">Ciudad<span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                                <div class="col form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                                    <button type="text" class="btn btn-success" id="btnCreateCompany">Crear Compañia</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- <div class="page-content-wrapper mt--45 mb-5 cardImportCompanies">
                    <div class="container-fluid">
                        <div class="row">
                            <form id="formImportCompany" enctype="multipart/form-data">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body pt-3 pb-0">
                                            <div class="gridx4ip">
                                                <div class="form-group floating-label enable-floating-label show-label mt-3 drag-area" style="margin-top:0px!important">
                                                    <input class="form-control" type="file" id="fileCompanies" accept=".xls,.xlsx">
                                                    <label for="formFile" class="form-label"> Importar Compañias</label>
                                                </div>
                                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                                    <button type="text" class="btn btn-success" id="btnImportCompanies">Importar</button>
                                                </div>
                                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                                    <button type="text" class="btn btn-info" id="btnDownloadImportsCompanies">Descarga Formato</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div> -->

                <!-- page content -->
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <!-- Row 5 -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <!-- <div class="card-header">
                                        <h5 class="card-title">Compañias</h5>
                                    </div> -->
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="tblCompanies">

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
    <script src="/cost/js/users/usersAccess.js"></script>

    <script src="/cost/js/quotes/companies/tblCompanies.js"></script>
    <script src="/cost/js/quotes/companies/companies.js"></script>
    <!--<script src="../global/js/import/import.js"></script>
    <script src="/cost/js/basic/Companies/importCompanies.js"></script>
    <script src="../global/js/import/file.js"></script>
    <script src="../global/js/global/validateImgExt.js"></script> -->
</body>

</html>