<?php

/* use tezlikv3\dao\UserInactiveTimeDao;

require_once(dirname(__DIR__) . "../api/src/dao/app/global/login/UserInactiveTimeDao.php");
$userinactivetimeDao = new UserInactiveTimeDao();
$userinactivetimeDao->findSession(); */
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
    <title>Tezlik - Selector </title>
    <link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />

    <?php include_once (__DIR__) . '../../global/partials/scriptsCSS.php'; ?>
</head>

<body class="horizontal-navbar">
    <!-- Begin Page -->
    <div class="page-wrapper">
        <!-- Begin Header -->
        <?php include_once (__DIR__) . '/partials/header.php'; ?>

        <!-- Begin Left Navigation -->
        <?php //include_once dirname(__DIR__) . '/partials/nav.php';
        ?>

        <!-- Begin main content -->
        <div class="main-content">
            <!-- content -->
            <div class="page-content">
                <!-- page header
				<div class="page-title-box">
					<div class="container-fluid">
						<div class="row align-items-center">
							<div class="col-sm-5 col-xl-6">
								<div class="page-title">
									<h3 class="mb-1 font-weight-bold text-dark">Dashboard</h3>
									<ol class="breadcrumb mb-3 mb-md-0">
										<li class="breadcrumb-item active">Bienvenido</li>
									</ol>
								</div>
							</div>
						</div>
					</div>
				</div> -->
                <!-- page content -->
                <div class="px-4 pt-5 my-5 text-center border-bottom">
                    <div class="col-lg-6 mx-auto">
                        <!-- <h1 class="display-5 fw-bold">Escoja un sitio al cual desee navegar</h1> -->

                        <div style="display: flex;">
                            <div class="card mr-3" style="width: 18rem;">
                                <img src="/selector/assets/img/cost.png" class="card-img-top btnLocation" alt="cost" style="width:80%; margin:auto;" id="1">
                                <div class="card-body">
                                    <h5 class="card-title">Costos</h5>
                                    <p class="card-text"></p>
                                    <!-- <a href="#" class="btn btn-primary btnLocation px-4 me-sm-3">Ir</a> -->
                                </div>
                            </div>

                            <div class="card" style="width: 18rem;">
                                <img src="/selector/assets/img/time-planning.png" class="card-img-top btnLocation" alt="planning" style="width:80%; margin:auto;" id="2">
                                <div class="card-body">
                                    <h5 class="card-title">Planeación</h5>
                                    <p class="card-text"></p>
                                    <!-- <a href="javascript:;" class="btn btn-primary btnLocation px-4 me-sm-3">Ir</a> -->
                                </div>
                            </div>
                        </div>
                        <!-- <div class="d-grid gap-2 d-sm-flex justify-content-sm-center mb-4">
							<button type="button" class="btnLocation btn btn-primary btn-lg px-4 me-sm-3" style="margin-left:20px;" id="1">Costos</button>
							<button type="button" class="btnLocation btn btn-primary btn-lg px-4 me-sm-3" style="margin-left:20px" id="2">Planeación</button>
						</div> -->
                    </div>
                </div>
                <!-- <script src="/app/js/dashboard/indicatorsGeneral.js"></script>
				<script src="/app/js/dashboard/graphicsGeneral.js"></script> -->
            </div>
        </div>
        <!-- main content End -->

        <!-- footer -->
        <?php include_once (__DIR__) . '../../global/partials/footer.php'; ?>

        <!-- <div class="setting-sidebar">
			<div class="card mb-0">
				<div class="card-header">
					<h5 class="card-title dflex-between-center">
						Layouts
						<a href="javascript:void(0)"><i class="mdi mdi-close fs-sm"></i></a>
					</h5>
				</div>
				<div class="card-body">
					<div class="layout">
						<a href="index-horizontal.html">
							<img src="assets/images/horizontal.png" alt="Lettstart Admin" class="img-fluid" />
							<h6 class="font-size-16">Horizontal Layout</h6>
						</a>
					</div>
					<div class="layout">
						<a href="index.html">
							<img src="assets/images/vertical.png" alt="Lettstart Admin" class="img-fluid" />
							<h6 class="font-size-16">Vertical Layout</h6>
						</a>
					</div>
					<div class="layout">
						<a href="layout-dark-sidebar.html">
							<img src="assets/images/dark.png" alt="Lettstart Admin" class="img-fluid" />
							<h6 class="font-size-16">Dark Sidebar</h6>
						</a>
					</div>
				</div>
			</div>
		</div> -->
    </div>
    <!-- Page End -->

    <?php include_once (__DIR__) . '../../global/partials/scriptsJS.php'; ?>
    <!-- <script src="../global/js/global/loadContent.js"></script> -->
    <script src="../global/js/global/logout.js"></script>
    <!-- <script src="../global/js/login/access.js"></script> -->
    <script src="../../selector/js/location/location.js"></script>
</body>

</html>