<?php
if (!isset($_SESSION)) {
	session_start();
	if (sizeof($_SESSION) == 0)
		header('location: /');
}
if (sizeof($_SESSION) == 0)
	header('location: /');
?>
<?php include_once dirname(dirname(__DIR__)) . '/modals/sendEmailQuote.php' ?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="author" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Tezlik - Cost | Details Quote</title>
	<link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />
	<link href="/assets/css/app.css" rel="stylesheet">
	<link href="/assets/css/icons.css" rel="stylesheet">


	<?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsCSS.php'; ?>
	<style>
		@media print {
			.invoice table th {
				white-space: nowrap;
				font-weight: 400;
				font-size: 10px;
			}

			.dtitle {
				font-size: 10px;
			}

			.invoice table tfoot tr:last-child td {
				color: #0d6efd;
				font-size: 1em;
			}

			.invoice table tfoot td {
				background: 0 0;
				border-bottom: none;
				white-space: nowrap;
				text-align: right;
				padding: 10px 20px;
				font-size: 1em;
				border-top: 1px solid #aaa;
			}
		}
	</style>
</head>

<body class="horizontal-navbar">
	<!-- Begin Page -->
	<div class="page-wrapper mb-0">
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
			<div class="page-content" style="margin-bottom:0px">
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
				<div class="card">
					<div class="card-body" style="padding-right: 35px; padding-left: 35px">
						<div class="toolbar hidden-print noImprimir">
							<div class="d-flex justify-content-end">
								<button class="btn btn-dark mr-2" id="btnImprimirQuote"><i class="fa fa-print"></i> Imprimir</button>
								<button class="btn btn-danger mr-2" id="btnNewSend"><i class="fa fa-mail-bulk"></i> Enviar</button>
								<button type="button" class="btn btn-secondary" onclick="copyQuote()"><i class="fa fa-file"></i> Copiar</button>
							</div>
							<hr>
						</div>

						<div id="invoice">
							<div class="invoice overflow-auto">
								<main>
									<div style="min-width: 600px;">
										<div class="row">
											<div class="col">
												<a href="javascript:;" id="logo">

												</a>
											</div>
											<div class="col company-details">
												<!-- <h2 class="name" id="company"></h2> -->
												<div id="address"></div>
												<div id="phone"></div>
												<div id="city"></div>
											</div>
										</div>
										<hr>
										<div class="row contacts">
											<div class="col invoice-to">
												<div class="text-gray-light">Cotizado a:</div>
												<h2 id="qCompany"></h2>
												<div id="contactName"></div>
												<div id="contactPhone"></div>
												<div id="contactEmail">
												</div>
											</div>
											<div class="col invoice-details">
												<h1 id="idQuote"></h1>
												<div id="dateQuote"></div>
												<!-- <div class="date">Due Date: 30/10/2018</div> -->
											</div>
										</div>
										<table>
											<thead>
												<tr>
													<th>#</th>
													<th class="text-left">Referencia</th>
													<th class="text-left">Descripción</th>
													<th class="text-center">Cantidad</th>
													<?php if ($_SESSION['flag_indirect'] == 0) { ?>
														<th class="text-center">Precio</th>
														<th class="text-center">Descuento</th>
														<th class="text-center">Total</th>
													<?php } ?>
												</tr>
											</thead>
											<tbody id="tblQuotesProductsBody">
											</tbody>
											<tfoot id="tblQuotesProductsFoot">
												<tr>
													<?php if ($_SESSION['flag_indirect'] == 0) { ?>
														<td colspan="4"></td>
													<?php } else { ?>
														<td colspan="1"></td>
													<?php } ?>
													<td colspan="2">SUBTOTAL</td>
													<td id="subtotal"></td>
												</tr>
												<tr>
													<?php if ($_SESSION['flag_indirect'] == 0) { ?>
														<td colspan="4"></td>
													<?php } else { ?>
														<td colspan="1"></td>
													<?php } ?>
													<td colspan="2">IVA 19%</td>
													<td id="iva"></td>
												</tr>
												<tr>
													<?php if ($_SESSION['flag_indirect'] == 0) { ?>
														<td colspan="4"></td>
													<?php } else { ?>
														<td colspan="1"></td>
													<?php } ?>
													<td colspan="2" style="color: #8DAC18;"><b>TOTAL</b></td>
													<td id="total" style="color: #8DAC18;"><b></b></td>
												</tr>
											</tfoot>
										</table>
										<div class="row">
											<div class="col-md-4 notices" id="qDescription">
											</div>
											<div class="col-md-8 notices py-5">
												<h3>Observaciones Generales:</h3>
												<div id="observation"></div>
											</div>
										</div>
										<div style="width: 100%;text-align: center;color: #777;border-top: 1px solid #aaa;padding: 8px 0;" id="qFooter"></div>
									</div>
								</main>
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
		flag_indirect = "<?= $_SESSION['flag_indirect'] ?>";
	</script>
	<script src="/global/js/global/companyData.js"></script>
	<script src="/cost/js/quotes/copyQuote.js"></script>
	<script src="/cost/js/quotes/detailsQuote/dataQuote.js"></script>
	<script src="/cost/js/quotes/detailsQuote/generalQuote.js"></script>
</body>

</html>