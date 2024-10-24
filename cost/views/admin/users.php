<?php
if (!isset($_SESSION)) {
    session_start();
    if (sizeof($_SESSION) == 0)
        header('location: /');
}
if (sizeof($_SESSION) == 0)
    header('location: /');
?>
<?php require_once dirname(dirname(dirname(__DIR__))) . '/cost/modals/createUserAccess.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tezlik - Cost | Users</title>
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
                                    <h3 class="mb-1 font-weight-bold text-dark"><i class="bi bi-people-fill mr-1"></i>Usuarios y<i class="bi bi-lock-fill mr-1"></i>Accesos</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Creación de Usuario y Configuración de accesos</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-warning" id="btnNewUser">Nuevo Usuario y Accesos</button>
                                </div>
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
                                            <table class="table table-striped" id="tblUsers">

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

        // price_usd =  
        flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";
        expense = "<?= $_SESSION['expense'] ?>";
        plan_cost_price = "<?= $_SESSION['plan_cost_price'] ?>";
        // price_usd = "<?= $_SESSION['flag_currency_usd'] ?>";
        plan_custom_price = "<?= $_SESSION['plan_custom_price'] ?>";
        plan_cost_analysis_material = "<?= $_SESSION['plan_cost_analysis_material'] ?>";
        plan_cost_economy_sale = "<?= $_SESSION['plan_cost_economy_sale'] ?>";
        plan_sale_objectives = "<?= $_SESSION['plan_sale_objectives'] ?>";
        flag_economy_scale = "<?= $_SESSION['flag_economy_scale'] ?>";
        flag_sales_objective = "<?= $_SESSION['flag_sales_objective'] ?>";
        plan_cost_multiproduct = "<?= $_SESSION['plan_cost_multiproduct'] ?>";
        type_custom_price = "<?= $_SESSION['type_custom_price'] ?>";
        plan_cost_simulator = "<?= $_SESSION['plan_cost_simulator'] ?>";
        plan_cost_historical = "<?= $_SESSION['plan_cost_historical'] ?>";
        plan_cost_quote = "<?= $_SESSION['plan_cost_quote'] ?>";
        plan_cost_support = "<?= $_SESSION['plan_cost_support'] ?>";
        flag_production_center = "<?= $_SESSION['flag_production_center'] ?>";
        flag_expense_anual = "<?= $_SESSION['flag_expense_anual'] ?>";
        flag_export_import = "<?= $_SESSION['flag_export_import'] ?>";
        // idUser = "< ?= $_SESSION['idUser'] ?>";
    </script>
    <script src="/cost/js/general/priceList/configPriceList.js"></script>
    <script>
        $(document).ready(function() {
            loadPriceList(2);
        });
    </script>
    <script src="/cost/js/admin/users/tblUsers.js"></script>
    <script src="/cost/js/admin/users/users.js"></script>
    <script src="/cost/js/admin/users/userAccess.js"></script>
</body>

</html>