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
    <title>Tezlik - Cost | Contacts</title>
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
                        <div class="row align-items-center">
                            <div class="col-sm-5 col-xl-6">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Contactos</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Creación de Contactos</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-4 col-xl-6">
                                <div class="form-inline justify-content-sm-end">
                                    <button class="btn btn-warning" id="btnNewContact">Nuevo Contacto</button>
                                    <!-- <button class="btn btn-info ml-3" id="btnImportNewContacts">Importar Contactos</button> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardCreateContact">
                    <div class="container-fluid">
                        <div class="row">
                            <form id="formCreateContact">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="form-row">
                                                <div class="col-sm-3 form-group floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <input type="text" class="form-control" name="firstname" id="firstname">
                                                    <label for="firstname">Nombre<span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                                <div class="col-sm-3 form-group floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <input type="text" class="form-control" name="lastname" id="lastname">
                                                    <label for="lastname">Apellido<span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                                <div class="col-sm-3 form-group floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <input type="text" class="form-control" name="phone" id="phone">
                                                    <label for="phone">Telefono<span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                                <div class="col-sm-3 form-group floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <input type="text" class="form-control" name="phone1" id="phone1">
                                                    <label for="phone1">Telefono (2)</label>
                                                </div>
                                                <div class="col-sm-3 form-group floating-label enable-floating-label show-label">
                                                    <input type="email" class="form-control" name="email" id="email">
                                                    <label for="email">Email<span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                                <div class="col-sm-3 form-group floating-label enable-floating-label show-label">
                                                    <input type="text" class="form-control" name="position" id="position">
                                                    <label for="position">Cargo<span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                                <div class="col-sm-3 form-group floating-label enable-floating-label show-label">
                                                    <label>Compañia<span class="text-danger">*</span></label>
                                                    <select class="form-control" name="idCompany" id="company"></select>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                                <div class="col-xs-2" style="margin-top:3px">
                                                    <button type="text" class="btn btn-success" id="btnCreateContact">Crear Contacto</button>
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
                                <div class="card disable-select">
                                    <div class="card-header">
                                        <h5 class="card-title">Contactos</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="tblContacts">

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
    <script>
        flag_expense = "<?= $_SESSION['flag_expense'] ?>";
        flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";
    </script>
    <script src="/cost/js/quotes/companies/configCompanies.js"></script>
    <script src="/cost/js/quotes/contacts/tblContacts.js"></script>
    <script src="/cost/js/quotes/contacts/contacts.js"></script>
</body>

</html>