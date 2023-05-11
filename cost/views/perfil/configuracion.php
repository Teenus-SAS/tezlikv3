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
    <title>Tezlik - Cost | Config</title>
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
            <div class="page-title-box">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-sm-5 col-xl-6">
                            <div class="page-title">
                                <h3 class="mb-1 font-weight-bold text-dark">Configuración</h3>
                                <!-- <ol class="breadcrumb mb-3 mb-md-0">
                        <li class="breadcrumb-item active">Creación de Máquinas</li>
                    </ol> -->
                            </div>
                        </div>
                        <!-- <div class="col-sm-7 col-xl-6">
                <div class="form-inline justify-content-sm-end">
                    <button class="btn btn-primary" id="btnCreateMachine">Crear Máquina</button>
                </div>
            </div> -->
                    </div>
                </div>
            </div>

            <div class="page-content-wrapper mt--45 mb-5 cardConfiguration">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <!-- <div class="card-header">
                        <h5 class="card-title">Crear Máquina</h5>
                    </div> -->
                                <div class="card-body">
                                    <div class="gridx5M">
                                        <label for="">Empresa</label>
                                        <label for="">NIT</label>
                                        <label for=""></label>
                                        <label for="">Versión</label>
                                        <label for="">Plan Actual</label>

                                        <label for=""><b>Teenus</b></label>
                                        <label for="">900.725.888-1</label>
                                        <label for=""><img src="../app/assets/images/logo/logo-teenus.png" alt="Logo teenus" width="90%"></label>
                                        <label for="">2.0.15</label>
                                        <h4 style="color: whitesmoke;"><span class="badge bg-primary">TEZLIK PREMIUM</span></h4>
                                    </div>
                                </div>
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
                            <div class="page-title-box1 mb-5">
                                <div class="container-fluid">
                                    <!-- <div class="page-title dflex-between-center">
                            <h3 class="mb-1 font-weight-bold">Pricing</h3>
                            <ol class="breadcrumb mb-0 mt-1">
                                <li class="breadcrumb-item">
                                    <a href="../index.html">
                                        <i class="bx bx-home fs-xs"></i>
                                    </a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="utility-animation.html"> Utility </a>
                                </li>
                                <li class="breadcrumb-item active">Pricing</li>
                            </ol>
                        </div> -->
                                </div>
                            </div>
                            <!-- page content -->
                            <div class="page-content-wrapper mt--45">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-xl-4 col-md-6">
                                            <div class="pricing-card">
                                                <div class="pricing-header p-3">
                                                    <h4 class="plan-title">Tezlik Basic</h4>
                                                    <div class="plan-price">
                                                        <div class="price">
                                                            <h3><sup>$</sup> 99.900</h3>
                                                            <span>Mes</span>
                                                        </div>
                                                        <!-- <div class="price-text">
                                                <p>Curabitur mollis bibendum luctus duis.</p>
                                            </div> -->
                                                    </div>
                                                </div>
                                                <div class="pricing-body px-3 pb-4">
                                                    <ul class="list-unstyled">
                                                        <li>
                                                            <i class="bx bx-check text-primary fs-sm align-middle"></i>
                                                            <span class="align-middle">Acceso Completo</span>
                                                        </li>
                                                        <li>
                                                            <i class="bx bx-check text-primary fs-sm align-middle"></i>
                                                            <span class="align-middle">1 Usuarios</span>
                                                        </li>
                                                        <li>
                                                            <i class="bx bx-x text-danger fs-sm align-middle"></i>
                                                            <span class="align-middle">Analisis de Materia Prima</span>
                                                        </li>
                                                        <li>
                                                            <i class="bx bx-x text-danger fs-sm align-middle"></i>
                                                            <span class="align-middle">Soporte Telefonico</span>
                                                        </li>
                                                        <li>
                                                            <i class="bx bx-x text-danger fs-sm align-middle"></i>
                                                            <span class="align-middle">Actualizaciones ilimitadas</span>
                                                        </li>
                                                    </ul>
                                                    <div class="text-center">
                                                        <button class="btn btn-primary" data-effect="wave">
                                                            Signup Now
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-md-6">
                                            <div class="pricing-card">
                                                <div class="pricing-header p-3">
                                                    <h4 class="plan-title">Tezlik Premium</h4>
                                                    <div class="plan-price">
                                                        <div class="price">
                                                            <h3><sup>$</sup> 119.900</h3>
                                                            <span>Mes</span>
                                                        </div>
                                                        <!-- <div class="price-text">
                                                <p>Curabitur mollis bibendum luctus duis.</p>
                                            </div> -->
                                                    </div>
                                                </div>
                                                <div class="pricing-body px-3 pb-4">
                                                    <ul class="list-unstyled">
                                                        <li>
                                                            <i class="bx bx-check text-primary fs-sm align-middle"></i>
                                                            <span class="align-middle">Acceso Completo</span>
                                                        </li>
                                                        <li>
                                                            <i class="bx bx-check text-primary fs-sm align-middle"></i>
                                                            <span class="align-middle">2 Usuarios</span>
                                                        </li>
                                                        <li>
                                                            <i class="bx bx-x text-danger fs-sm align-middle"></i>
                                                            <span class="align-middle">Analisis de Materia Prima</span>
                                                        </li>
                                                        <li>
                                                            <i class="bx bx-x text-danger fs-sm align-middle"></i>
                                                            <span class="align-middle">Soporte Telefonico</span>
                                                        </li>
                                                        <li>
                                                            <i class="bx bx-x text-danger fs-sm align-middle"></i>
                                                            <span class="align-middle">Actualizaciones ilimitadas</span>
                                                        </li>
                                                    </ul>
                                                    <div class="text-center">
                                                        <button class="btn btn-primary" data-effect="wave">
                                                            Signup Now
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-md-6">
                                            <div class="pricing-card recommand-box text-white" style="background-color: #51cbce;">
                                                <div class="pricing-header p-3">
                                                    <h4 class="plan-title text-white">Tezlik Elite</h4>
                                                    <div class="plan-price">
                                                        <div class="price">
                                                            <h3 class="text-white"><sup>$</sup> 199.900</h3>
                                                            <span>Mes</span>
                                                        </div>
                                                        <!--  <div class="price-text">
                                                <p>Curabitur mollis bibendum luctus duis.</p>
                                            </div> -->
                                                    </div>
                                                </div>
                                                <div class="pricing-body px-3 pb-4">
                                                    <ul class="list-unstyled">
                                                        <li>
                                                            <i class="bx bx-check text-white fs-sm align-middle"></i>
                                                            <span class="align-middle">Accesso Completo</span>
                                                        </li>
                                                        <li>
                                                            <i class="bx bx-check text-white fs-sm align-middle"></i>
                                                            <span class="align-middle">3 Usuarios</span>
                                                        </li>
                                                        <li>
                                                            <i class="bx bx-check text-white fs-sm align-middle"></i>
                                                            <span class="align-middle">Análisis de Materia Prima</span>
                                                        </li>

                                                        <li>
                                                            <i class="bx bx-check text-white fs-sm align-middle"></i>
                                                            <span class="align-middle">Soporte Telefonico</span>
                                                        </li>
                                                        <li>
                                                            <i class="bx bx-check text-white fs-sm align-middle"></i>
                                                            <span class="align-middle">Actualizaciones Ilimitadas</span>
                                                        </li>
                                                    </ul>
                                                    <div class="text-center">
                                                        <button class="btn btn-light" data-effect="wave">
                                                            Signup Now
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="col-xl-3 col-md-6">
                                    <div class="pricing-card">
                                        <div class="pricing-header p-3">
                                            <h4 class="plan-title">Tezlik Premium</h4>
                                            <div class="plan-price">
                                                <div class="price">
                                                    <h3><sup>$</sup> 39</h3>
                                                    <span>per month</span>
                                                </div>
                                                <div class="price-text">
                                                    <p>Curabitur mollis bibendum luctus duis.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pricing-body px-3 pb-4">
                                            <ul class="list-unstyled">
                                                <li>
                                                    <i class="bx bx-check text-primary fs-sm align-middle"></i>
                                                    <span class="align-middle">Full Access</span>
                                                </li>
                                                <li>
                                                    <i class="bx bx-check text-primary fs-sm align-middle"></i>
                                                    <span class="align-middle">Free Live Support</span>
                                                </li>
                                                <li>
                                                    <i class="bx bx-check text-primary fs-sm align-middle"></i>
                                                    <span class="align-middle">Powerful Admin Panel</span>
                                                </li>
                                                <li>
                                                    <i class="bx bx-check text-primary fs-sm align-middle"></i>
                                                    <span class="align-middle">Email Accounts</span>
                                                </li>
                                                <li>
                                                    <i class="bx bx-x text-danger fs-sm align-middle"></i>
                                                    <span class="align-middle">Unlimited Updates</span>
                                                </li>
                                            </ul>
                                            <div class="text-center">
                                                <button class="btn btn-primary" data-effect="wave">
                                                    Signup Now
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
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

    <script src="../../app/js/machines/tblMachines.js"></script>
    <script src="../../app/js/machines/machines.js"></script>
</body>

</html>