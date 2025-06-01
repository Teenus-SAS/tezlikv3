<?php
require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/api/src/Auth/authMiddleware.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="Teenus SAS">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TezlikSoftware Cost | Portfolio</title>
    <link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />

    <?php include_once dirname(dirname(dirname(dirname(__DIR__)))) . '/public/partials/scriptsCSS.php'; ?>
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
                        <div class="tab-pane cardPortfolio">
                            <div class="row align-items-center">
                                <div class="col-sm-5 col-xl-6">
                                    <div class="page-title">
                                        <h3 class="mb-1 font-weight-bold text-dark"><i class="bi bi-graph-up mr-1"></i>Portafolio de Rentabilidades</h3>
                                        <ol class="breadcrumb mb-3 mb-md-0">
                                            <li class="breadcrumb-item active">Lista de Productos</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="tab-pane cardPortfolio8020" style="display: none;">
                            <div class="row align-items-center">
                                <div class="col-sm-5 col-xl-6">
                                    <div class="page-title">
                                        <h3 class="mb-1 font-weight-bold text-dark"><i class="bi bi-graph-up mr-1"></i>Portafolio 80/20</h3>
                                        <ol class="breadcrumb mb-3 mb-md-0">
                                            <li class="breadcrumb-item active">Lista de Productos</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>

                <!-- page content -->
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <!-- <div class="row">
                            <div class="col-12">
                                <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active selectNavigation" id="link-portfolio" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-activity" aria-selected="true">
                                            <i class="fas fa-flask mr-1"></i>Portafolio
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link selectNavigation" id="link-portfolio8020" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-projects" aria-selected="false">
                                            <i class="bi bi-arrow-repeat mr-1"></i> 80/20
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div> -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <!-- <div class="tab-pane cardPortfolio">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped" id="tblPortfolio">
                                                     
                                                </table>
                                            </div>
                                        </div>
                                    </div> -->
                                    <div class="tab-pane cardPortfolio8020">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped" id="tblPortfolio8020">
                                                    <tfoot>
                                                        <tr>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td id="totalProfit8020"></td>
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
        <?php include_once dirname(dirname(dirname(dirname(__DIR__)))) . '/public/partials/footer.php'; ?>
    </div>
    <!-- Page End -->

    <?php include_once dirname(dirname(dirname(dirname(__DIR__)))) . '/public/partials/scriptsJS.php'; ?>
    <!-- <script src="/cost/js/tools/portfolio/portfolio.js"></script> -->
    <script src="/cost/js/tools/portfolio/tblPortfolio.js"></script>
</body>

</html>