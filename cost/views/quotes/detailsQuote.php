<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="description" content="LetStart Admin is a full featured, multipurpose, premium bootstrap admin template built with Bootstrap 4 Framework, HTML5, CSS and JQuery.">
	<meta name="keywords" content="admin, panels, dashboard, admin panel, multipurpose, bootstrap, bootstrap4, all type of dashboards">
	<meta name="author" content="MatrrDigital">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Tezlik - Cost | Details Quote</title>
	<link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />
	<link href="/assets/css/app.css" rel="stylesheet">
	<link href="/assets/css/icons.css" rel="stylesheet">


	<?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsCSS.php'; ?>
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
			<!-- Content -->
			<div class="page-content">
				<div class="card">
					<div class="card-body">
						<div class="toolbar hidden-print">
							<div class="d-flex justify-content-end">
								<button class="btn btn-dark mr-2" id="btnImprimirQuote"><i class="fa fa-print"></i> Print</button>
								<!-- <button class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Export as PDF</button> -->
							</div>
							<hr>
						</div>
						<div id="invoice">
							<div class="invoice overflow-auto">
								<div style="min-width: 600px">
									<header>
										<div class="row">
											<div class="col">
												<a href="javascript:;">
													<img id="companyImg" src="" width="80" alt="">
												</a>
											</div>
											<div class="col company-details">
												<h2 class="name" id="companyName">
												</h2>
												<div id="companyAddress"></div>
												<div id="companyPhone"></div>
												<div id="companyCity"></div>
											</div>
										</div>
									</header>
									<main>
										<div class="row contacts">
											<div class="col invoice-to">
												<div class="text-gray-light">Cotizado Por:</div>
												<h2 id="contactName"></h2>
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
													<th class="text-center">Precio</th>
													<th class="text-center">Descuento</th>
													<th class="text-center">Total</th>
												</tr>
											</thead>
											<tbody id="tblQuotesProductsBody">
											</tbody>
											<tfoot>
												<tr>
													<td colspan="2"></td>
													<td colspan="4">SUBTOTAL</td>
													<td id="subtotal"></td>
												</tr>
												<tr>
													<td colspan="2"></td>
													<td colspan="4">IVA 19%</td>
													<td id="iva"></td>
												</tr>
												<tr>
													<td colspan="2"></td>
													<td colspan="4" style="color: #8DAC18;"><b>TOTAL</b></td>
													<td id="total" style="color: #8DAC18;"><b></b></td>
												</tr>
											</tfoot>
										</table>
										<div class="notices">
											<h3>Condiciones Comerciales:</h3>
											<div id="paymentMethod"></div>
										</div>
										<br>
										<div class="notices">
											<h3>Observaciones Generales:</h3>
											<div id="observation"></div>
										</div>
									</main>
									<footer> Autorizo a Tezlik. para recaudar, almacenar, utilizar y actualizar mis datos personales con fines exclusivamente comerciales y garantizándome que esta información no será revelada a terceros salvo orden de autoridad competente. Ley 1581 de 2012, Decreto 1377 de 2013.</footer>
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
	<script src="/global/js/global/searchData.js"></script>

	<script src="/cost/js/quotes/detailsQuote.js"></script>
</body>

</html>