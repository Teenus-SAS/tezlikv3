<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="description" content="LetStart Admin is a full featured, multipurpose, premium bootstrap admin template built with Bootstrap 4 Framework, HTML5, CSS and JQuery.">
	<meta name="keywords" content="admin, panels, dashboard, admin panel, multipurpose, bootstrap, bootstrap4, all type of dashboards">
	<meta name="author" content="MatrrDigital">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Tezlik - Cost | Quotes</title>
	<link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />

	<?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsCSS.php'; ?>
</head>

<body class="horizontal-navbar">
	<?php include_once dirname(dirname(__DIR__)) . '/modals/createQuote.php' ?>
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
									<h3 class="mb-1 font-weight-bold text-dark">Cotizaciones</h3>
									<!-- <ol class="breadcrumb mb-3 mb-md-0">
										<li class="breadcrumb-item active">Creación de Cotizaciones</li>
									</ol> -->
								</div>
							</div>
							<div class="col-sm-4 col-xl-6">
								<div class="form-inline justify-content-sm-end">
									<button class="btn btn-warning" id="btnNewQuotes">Nueva Cotización</button>
									<!-- <button class="btn btn-info ml-3" id="btnImportNewQuotes">Importar Cotizaciones</button> -->
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
								<div class="card">
									<div class="card-header">
										<h5 class="card-title">Cotizaciones</h5>
									</div>
									<div class="card-body">
										<div class="table-responsive">
											<table class="table table-striped" id="tblQuotes">

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
	<script src="/global/js/global/number.js"></script>
	<script src="/global/js/global/searchData.js"></script>

	<script src="/cost/js/basic/products/configProducts.js"></script>
	<script src="/cost/js/quotes/contacts/configContact.js"></script>
	<script src="/cost/js/quotes/companies/configCompanies.js"></script>
	<script src="/cost/js/quotes/paymentMethods/configPaymentMethods.js"></script>
	<script src="/cost/js/quotes/quotes.js"></script>
	<script src="/cost/js/quotes/addProduct.js"></script>
	<script src="/cost/js/quotes/tblQuotes.js"></script>
	<!-- <script src="../global/js/import/import.js"></script>
    <script src="/cost/js/basic/Quotes/importQuotes.js"></script>
    <script src="../global/js/import/file.js"></script>
    <script src="../global/js/global/validateImgExt.js"></script> -->
</body>

</html>