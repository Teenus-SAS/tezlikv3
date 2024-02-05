<?php
if (!isset($_SESSION)) {
	session_start();
	if (sizeof($_SESSION) == 0)
		header('location: /');
}
if (sizeof($_SESSION) == 0)
	header('location: /');
?>
<?php include_once dirname(dirname(__DIR__)) . '/modals/createQuote.php' ?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="author" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Tezlik - Cost | Quotes</title>
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
							<div class="alert alert-danger" role="alert" style="margin-bottom: 0px;"> ¡Pronto se acabara tu licencia (<?php echo $_SESSION['license_days']; ?> días). Comunícate con tu administrador para mas información! </div>
						</div>
					</div>
				<?php } ?>
				<?php if ($_SESSION['license_days'] > 30 && $_SESSION['license_days'] < 40) { ?>
					<div class="row">
						<div class="col-sm-12">
							<div class="alert alert-warning" role="alert" style="margin-bottom: 0px;"> ¡Pronto se acabara tu licencia. Comunícate con tu administrador para mas información! </div>
						</div>
					</div>
				<?php } ?>
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
								<div class="card disable-select">
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

	<script src="/cost/js/basic/products/configProducts.js"></script>
	<?php if ($_SESSION['custom_price'] == 1 && $_SESSION['plan_custom_price'] == 1) { ?>
		<script src="/cost/js/general/priceList/configPriceList.js"></script>
	<?php } ?>
	<script>
		custom_price = "<?= $_SESSION['custom_price'] ?>";
		type_custom_price = "<?= $_SESSION['type_custom_price'] ?>";
		flag_expense = "<?= $_SESSION['flag_expense'] ?>";
		flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";
		flag_indirect = "<?= $_SESSION['flag_indirect'] ?>";
		indirect = 1;
	</script>
	<script src="/global/js/global/orderData.js"></script>
	<script src="/cost/js/basic/rawMaterials/configRawMaterials.js"></script>
	<script src="/cost/js/quotes/contacts/configContact.js"></script>
	<script src="/cost/js/quotes/companies/configCompanies.js"></script>
	<script src="/cost/js/quotes/paymentMethods/configPaymentMethods.js"></script>
	<script src="/cost/js/quotes/copyQuote.js"></script>
	<script src="/cost/js/quotes/quotes.js"></script>
	<script src="/cost/js/quotes/addProduct.js"></script>
	<script src="/cost/js/quotes/addMaterials.js"></script>
	<script src="/cost/js/quotes/tblQuotes.js"></script>
</body>

</html>