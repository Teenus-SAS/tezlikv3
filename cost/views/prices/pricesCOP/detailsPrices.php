<?php
require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/api/src/Auth/authMiddleware.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Cotice en minutos, y no vuelva a perder mas oportunidades de negocio">
    <meta name="keywords" content="cotizar, costos, precio, competitividad, ventajas, beneficios, diferenciacion">
    <meta name="author" content="Teenus">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TezlikSoftware Cost | Details Prices</title>
    <link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />

    <?php include_once dirname(dirname(dirname(dirname(__DIR__)))) . '/public/partials/scriptsCSS.php'; ?>
    <style>
        /* body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        } */

        .dashboard-card {
            transition: all 0.3s ease;
            border-radius: 16px;
            overflow: hidden;
            border: none !important;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .hover-lift {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .hover-lift:hover {
            transform: translateY(-3px);
        }

        .icon-shape {
            transition: all 0.3s ease;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dashboard-card:hover .icon-shape {
            transform: scale(1.1);
        }

        .progress {
            background-color: #f0f0f0;
            border-radius: 10px;
        }

        .progress-bar {
            border-radius: 10px;
            transition: width 1s ease-in-out;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
            border-radius: 8px;
        }

        .text-primary {
            color: #4e73df !important;
        }

        .text-success {
            color: #1cc88a !important;
        }

        .text-warning {
            color: #f6c23e !important;
        }

        .text-info {
            color: #36b9cc !important;
        }

        .bg-primary {
            background-color: #4e73df !important;
        }

        .bg-success {
            background-color: #1cc88a !important;
        }

        .bg-warning {
            background-color: #f6c23e !important;
        }

        .bg-info {
            background-color: #36b9cc !important;
        }

        .container-custom {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .section-header {
            margin-bottom: 30px;
            text-align: center;
        }

        .section-title {
            color: #2d3748;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .section-subtitle {
            color: #718096;
            font-size: 16px;
        }

        .card-value {
            margin: 0;
        }

        .card-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-body {
            padding: 1rem !important;
        }

        .icon-shape {
            width: 40px !important;
            height: 40px !important;
        }

        .icon-shape i {
            font-size: 18px !important;
        }

        @media (max-width: 768px) {
            .container-custom {
                padding: 20px 15px;
            }

            .section-title {
                font-size: 24px;
            }

            .card-value {
                font-size: 1.75rem;
            }
        }
    </style>

    <style>
        /*  body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 30px;
        } */

        .price-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            height: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .price-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-color: #cbd5e0;
        }

        .price-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .card-real::before {
            background: #ef4444;
        }

        .card-list::before {
            background: #22c55e;
        }

        .card-suggested::before {
            background: #3b82f6;
        }

        .card-header-custom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .card-title {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin: 0;
        }

        .card-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .icon-real {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .icon-list {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
        }

        .icon-suggested {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        .price-main {
            font-size: 1.75rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 1.5rem;
        }

        .price-real {
            color: #ef4444;
        }

        .price-list {
            color: #22c55e;
        }

        .price-suggested {
            color: #3b82f6;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .info-item {
            text-align: center;
            padding: 0.75rem;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #f1f5f9;
        }

        .info-label {
            font-size: 0.7rem;
            color: #64748b;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-size: 0.9rem;
            font-weight: 700;
            color: #1e293b;
        }

        .comparison-section {
            border-top: 1px solid #f1f5f9;
            padding-top: 1rem;
        }

        .comparison-title {
            font-size: 0.7rem;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 0.75rem;
        }

        .comparison-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .comparison-item:last-child {
            margin-bottom: 0;
        }

        .comparison-label {
            font-size: 0.75rem;
            color: #64748b;
        }

        .comparison-value {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
        }

        .diff-positive {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
        }

        .diff-negative {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .diff-neutral {
            background: rgba(100, 116, 139, 0.1);
            color: #64748b;
        }

        .container-custom {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-header {
            margin-bottom: 30px;
            text-align: center;
        }

        .section-title {
            color: #1e293b;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .section-subtitle {
            color: #64748b;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            body {
                padding: 20px;
            }

            .price-card {
                padding: 1.25rem;
            }

            .price-main {
                font-size: 1.5rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }
        }
    </style>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 30px;
        }

        .analysis-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            height: 100%;
            transition: all 0.3s ease;
        }

        .analysis-card:hover {
            border-color: #cbd5e0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .card-header-modern {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
            border-radius: 8px 8px 0 0;
        }

        .card-title-modern {
            color: #1e293b;
            font-size: 0.95rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-body-modern {
            padding: 0;
            display: flex;
            flex-direction: column;
            height: calc(100% - 70px);
        }

        /* Costeo Card Styles */
        .costeo-item {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 1rem;
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            align-items: center;
        }

        .costeo-item:last-child {
            border-bottom: none;
        }

        .costeo-item:hover {
            background-color: #f8fafc;
        }

        .costeo-label {
            font-size: 0.875rem;
            color: #64748b;
            margin: 0;
            font-weight: 500;
        }

        .costeo-value {
            font-size: 0.875rem;
            font-weight: 600;
            margin: 0;
            text-align: right;
        }

        .highlight-sales {
            background: #f0fdf4;
            border-left: 3px solid #22c55e;
        }

        .highlight-sales .costeo-label,
        .highlight-sales .costeo-value {
            color: #15803d;
            font-weight: 700;
        }

        .highlight-cost {
            background: #fef2f2;
            border-left: 3px solid #ef4444;
        }

        .highlight-cost .costeo-label,
        .highlight-cost .costeo-value {
            color: #dc2626;
            font-weight: 700;
        }

        .highlight-profit {
            background: #eff6ff;
            border-left: 3px solid #3b82f6;
        }

        .highlight-profit .costeo-label,
        .highlight-profit .costeo-value {
            color: #1d4ed8;
            font-weight: 700;
        }

        /* Chart Card Styles */
        .chart-container {
            padding: 1rem;
            height: 300px;
            width: 100%;
            position: relative;
        }

        .chart-container canvas {
            max-height: 250px !important;
            max-width: 100% !important;
        }

        /* Sales Card Styles */
        .sales-item {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sales-item:last-child {
            border-bottom: none;
        }

        .sales-item:hover {
            background-color: #f8fafc;
        }

        .sales-info {
            flex-grow: 1;
        }

        .sales-label {
            color: #64748b;
            font-size: 0.8rem;
            margin-bottom: 0.25rem;
            font-weight: 500;
        }

        .sales-value {
            color: #1e293b;
            font-size: 1.125rem;
            font-weight: 700;
            margin: 0;
        }

        .sales-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            margin-left: 1rem;
        }

        .icon-units {
            background: #06b6d4;
        }

        .icon-revenue {
            background: #3b82f6;
        }

        .icon-price {
            background: #10b981;
        }

        /* Save Button */
        .save-section {
            margin-top: auto;
            padding: 1rem 1.25rem;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .btn-save {
            width: 100%;
            background: #3b82f6;
            border: none;
            border-radius: 6px;
            padding: 0.65rem;
            color: white;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .btn-save:hover {
            background: #2563eb;
            color: white;
        }

        .btn-save:disabled {
            background: #e2e8f0;
            color: #94a3b8;
        }

        .container-demo {
            max-width: 1400px;
            margin: 0 auto;
        }

        .demo-title {
            text-align: center;
            margin-bottom: 30px;
            color: #1e293b;
            font-size: 24px;
            font-weight: 600;
        }

        @media (max-width: 768px) {

            .costeo-item,
            .sales-item {
                padding: 0.65rem 1rem;
            }

            .sales-value {
                font-size: 1rem;
            }

            .sales-icon {
                width: 35px;
                height: 35px;
                font-size: 16px;
            }
        }
    </style>
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
            <div class="social-bar btnPrintPDF" style="display: none;">
                <a href="javascript:;" class="bi bi-file-image" id="imageProduct"></a>
            </div>
            <!-- Loader -->
            <div class="loading">
                <a href="javascript:;" class="close-btn" style="display: none;"><i class="bi bi-x-circle-fill"></i></a>
                <div class="loader"></div>
            </div>

            <!-- Content -->
            <div class="page-content" id="invoice">
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
                            <div class="col-12">
                                <div class="page-title">
                                    <div class="row">
                                        <div class="col-sm-5 col-xl-5 p-4">
                                            <h3 class="mb-1 font-weight-bold text-dark" id="nameProduct"></h3>
                                            <ol class="col-sm-5 col-xl-6 breadcrumb mb-3 mb-md-0 cardHeader">
                                                <li class="breadcrumb-item active">Análisis de Costos</li>
                                            </ol>
                                        </div>
                                        <div class="col-sm-7 col-xl-7 d-flex justify-content-end mt-4">
                                            <div class="col-xs-5 mr-2">
                                                <select id="product" class="form-control btnPrintPDF">
                                                </select>
                                            </div>
                                            <!-- $_SESSION['price_usd'] -->
                                            <?php if ($_SESSION['flag_currency_usd'] == 1 || $_SESSION['flag_currency_eur'] == 1) { ?>
                                                <div class="col-xs-2 mr-2 floating-label enable-floating-label show-label">
                                                    <label class="text-dark">Moneda</label>
                                                    <select class="form-control selectCurrency" id="selectCurrency">
                                                        <option disabled>Seleccionar</option>
                                                        <option value="1" selected>COP</option>
                                                        <?php if ($_SESSION['flag_currency_usd'] == 1) { ?>
                                                            <option value="2">USD</option>
                                                        <?php } ?>
                                                        <?php if ($_SESSION['flag_currency_eur'] == 1) { ?>
                                                            <option value="3">EUR</option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                                <div class="col-xs-2 mr-2 form-group floating-label enable-floating-label cardUSD coverageUSDInput" style="display: none;">
                                                    <label class="font-weight-bold text-dark">Valor Dolar</label>
                                                    <input type="text" style="background-color: aliceblue;" class="form-control text-center" name="valueCoverageUSD" id="valueCoverageUSD" value="<?php
                                                                                                                                                                                                    $coverage_usd = sprintf("%.2f", $_SESSION['coverage_usd']);
                                                                                                                                                                                                    echo  $coverage_usd ?>" readonly>
                                                </div>
                                                <div class="col-xs-2 mr-2 form-group floating-label enable-floating-label cardEUR coverageEURInput" style="display: none;">
                                                    <label class="font-weight-bold text-dark">Valor Euro</label>
                                                    <input type="text" style="background-color: aliceblue;" class="form-control text-center" name="valueCoverageEUR" id="valueCoverageEUR" value="<?php
                                                                                                                                                                                                    $coverage_eur = sprintf('$ %s', number_format($_SESSION['coverage_eur'], 2, ',', '.'));
                                                                                                                                                                                                    echo  $coverage_eur ?>" readonly>
                                                </div>
                                                <div class="col-xs-2 btnPrintPDF" id="btnPdf">
                                                    <a href="javascript:;" <i class="bi bi-filetype-pdf" data-toggle='tooltip' onclick="printPDF(2)" style="font-size: 30px; color:red;"></i></a>
                                                </div>
                                            <?php } ?>
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
                        <!-- Widget  -->
                        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4" id="cardsIndicatorsProducts">
                            <!-- Materia Prima -->
                            <div class="col">
                                <div class="card dashboard-card border-0 shadow-sm hover-lift">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="flex-grow-1">
                                                <span class="text-muted text-uppercase small fw-semibold d-block mb-1 card-label">Materia Prima</span>
                                                <h3 class="mb-0 text-primary fw-bold card-value" id="rawMaterial">$0.00</h3>
                                            </div>
                                            <div class="d-flex align-items-center mt-2">
                                                <span class="badge bg-primary bg-opacity-10 text-white small" id="percentRawMaterial">
                                                    <i class="fas fa-chart-line me-1"></i>
                                                    <span>0.0%</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent border-0 pt-0">
                                        <div class="progress" style="height: 4px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 0%" id="progressRawMaterial"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mano de Obra -->
                            <div class="col">
                                <div class="card dashboard-card border-0 shadow-sm hover-lift">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="flex-grow-1">
                                                <span class="text-muted text-uppercase small fw-semibold d-block mb-1 card-label">Mano de Obra</span>
                                                <h3 class="mb-0 text-success fw-bold card-value" id="workforce">$0.00</h3>
                                            </div>
                                            <div class="d-flex align-items-center mt-2">
                                                <span class="badge bg-success bg-opacity-10 text-white small" id="percentWorkforce">
                                                    <i class="fas fa-chart-line me-1"></i>
                                                    <span>0.0%</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent border-0 pt-0">
                                        <div class="progress" style="height: 4px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 0%" id="progressWorkforce"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Costos Indirectos -->
                            <div class="col">
                                <div class="card dashboard-card border-0 shadow-sm hover-lift">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="flex-grow-1">
                                                <span class="text-muted text-uppercase small fw-semibold d-block mb-1 card-label">Costos Indirectos</span>
                                                <h3 class="mb-0 text-warning fw-bold card-value" id="indirectCost">$0.00</h3>

                                            </div>
                                            <div class="d-flex align-items-center mt-2">
                                                <span class="badge bg-warning bg-opacity-10 text-white small" id="percentIndirectCost">
                                                    <i class="fas fa-chart-line me-1"></i>
                                                    <span>0.0%</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent border-0 pt-0">
                                        <div class="progress" style="height: 4px;">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" id="progressIndirectCost"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Gastos Generales -->
                            <div class="col">
                                <div class="card dashboard-card border-0 shadow-sm hover-lift">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="flex-grow-1">
                                                <span class="text-muted text-uppercase small fw-semibold d-block mb-1 card-label">Gastos Generales</span>
                                                <h3 class="mb-0 text-info fw-bold card-value" id="assignableExpenses">$0.00</h3>

                                            </div>
                                            <div class="d-flex align-items-center mt-2">
                                                <span class="badge bg-info bg-opacity-10 text-info small" id="percentAssignableExpenses">
                                                    <i class="fas fa-chart-line me-1"></i>
                                                    <span>0.0%</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent border-0 pt-0">
                                        <div class="progress" style="height: 4px;">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: 0%" id="progressAssignableExpenses"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($_SESSION['flag_expense'] != 2) {
                        ?>

                            <div class="row actualSalePrice mb-3">
                                <!-- Card Precio Real -->
                                <div class="col-md-4">
                                    <div class="price-card card-real">
                                        <div class="card-header-custom">
                                            <h6 class="card-title">Precio Real</h6>
                                            <div class="card-icon icon-real">
                                                <i class="fas fa-calculator"></i>
                                            </div>
                                        </div>

                                        <div class="price-section">
                                            <div class="price-main price-real" id="recomendedPrice">$38,687</div>
                                            <div class="price-subtitle">
                                                <span class="subtitle-label">Margen:</span>
                                                <span class="subtitle-value" id="realMargin">3.0%</span>
                                            </div>
                                        </div>

                                        <div class="comparison-section">
                                            <div class="comparison-item">
                                                <span class="comparison-label">Base Costo</span>
                                                <span class="comparison-value diff-neutral" id="baseCost">$37,527</span>
                                            </div>
                                            <div class="comparison-item">
                                                <span class="comparison-label">Referencia</span>
                                                <span class="comparison-value diff-neutral">100%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Precio Lista -->
                                <div class="col-md-4">
                                    <div class="price-card card-list">
                                        <div class="card-header-custom">
                                            <h6 class="card-title">Precio Lista</h6>
                                            <div class="card-icon icon-list">
                                                <i class="fas fa-tag"></i>
                                            </div>
                                        </div>

                                        <div class="price-section">
                                            <div class="price-main price-list" id="actualSalePrice">$41,320</div>
                                            <div class="price-subtitle">
                                                <span class="subtitle-label">Margen:</span>
                                                <span class="subtitle-value" id="listMargin">9.2%</span>
                                            </div>
                                        </div>

                                        <div class="comparison-section">
                                            <div class="comparison-item">
                                                <span class="comparison-label">Diferencia</span>
                                                <span class="comparison-value diff-positive" id="listDifference">+$2,633</span>
                                            </div>
                                            <div class="comparison-item">
                                                <span class="comparison-label">vs Real</span>
                                                <span class="comparison-value diff-positive" id="listVsReal">+6.8%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Precio Sugerido -->
                                <div class="col-md-4">
                                    <div class="price-card card-suggested">
                                        <div class="card-header-custom">
                                            <h6 class="card-title">Precio Sugerido</h6>
                                            <div class="card-icon icon-suggested">
                                                <i class="fas fa-lightbulb"></i>
                                            </div>
                                        </div>

                                        <div class="price-section">
                                            <div class="price-main price-suggested suggestedPrice" id="suggestedPrice">$42,500</div>
                                            <div class="price-subtitle">
                                                <span class="subtitle-label">Margen:</span>
                                                <span class="subtitle-value" id="suggestedMargin">11.7%</span>
                                            </div>
                                        </div>

                                        <div class="comparison-section">
                                            <div class="comparison-item">
                                                <span class="comparison-label">Diferencia</span>
                                                <span class="comparison-value diff-positive" id="suggestedDifference">+$3,813</span>
                                            </div>
                                            <div class="comparison-item">
                                                <span class="comparison-label">vs Real</span>
                                                <span class="comparison-value diff-positive" id="suggestedVsReal">+9.9%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Rentabilidad Deseada -->
                            </div>


                            <!-- <div class="row row-cols-1 row-cols-md-2 row-cols-xl-2 actualSalePrice">
                                <div class="col-xl-6">
                                    <div class="card radius-10 border-start border-0 border-3 border-success">
                                        <div class="card-body">
                                            <div class="media align-items-center">
                                                <div class="card-body row row-cols-1 row-cols-md-2 row-cols-xl-2" style="padding-bottom: 0px;padding-top: 0px">
                                                    <div class="media-body align-items-center cardDistribution">
                                                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Precio (Real)</span>
                                                        <h2 class="mb-0 mt-1 text-danger recomendedPrice" id="recomendedPrice" style="font-size: x-large"></h2>
                                                    </div>
                                                    <div class="media-body align-items-center cardSalePrice">
                                                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Precio (Lista)</span>
                                                        <h2 class="mb-0 mt-1 text-success" id="actualSalePrice" style="font-size: x-large"></h2>
                                                    </div>
                                                    <div class="media-body align-items-center">
                                                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Precio (Sugerido)</span>
                                                        <h3 class="mb-0 mt-1 text-info suggestedPrice" style="font-size: x-large"></h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-2 cardRecomendedPrice cardDistribution">
                                </div>
                                <div class="col-xl-2 cardTrafficLight cardSalePrice">
                                </div>
                                <div class="col-xl-2">
                                    <div class="card radius-10 border-start border-0 border-3 border-info">
                                        <div class="card-body">
                                            <div class="media align-items-center">
                                                <div class="media-body">
                                                    <?php if ($_SESSION['id_company'] == '10') { ?>
                                                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Margen Deseado</span>
                                                    <?php } else { ?>
                                                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Rentab Deseada</span>
                                                    <?php } ?>
                                                    <h2 class="mb-0 mt-1 text-info" id="minProfit"></h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        <?php  }
                        ?>

                        <!-- Row 2-->


                        <div class="row flex-nowrap overflow-hidden no-scroll">
                            <!-- Card 1: Costeo Total -->
                            <div class="col-auto d-flex">
                                <div class="analysis-card" style="width: 280px;">
                                    <div class="card-header-modern">
                                        <h5 class="card-title-modern">
                                            <i class="fas fa-calculator text-primary"></i>
                                            Costeo Total
                                        </h5>
                                    </div>

                                    <div class="card-body-modern">
                                        <div class="flex-grow-1">
                                            <!-- Precio de Venta -->
                                            <div class="costeo-item highlight-sales">
                                                <p class="costeo-label">Precio de Venta</p>
                                                <h6 class="costeo-value" id="salesPrice">$41,320</h6>
                                            </div>

                                            <!-- Total Costos -->
                                            <div class="costeo-item highlight-cost">
                                                <p class="costeo-label">Total Costos</p>
                                                <h6 class="costeo-value" id="costTotal">$37,527</h6>
                                            </div>

                                            <!-- Materia Prima -->
                                            <div class="costeo-item">
                                                <p class="costeo-label">Materia Prima</p>
                                                <h6 class="costeo-value" id="payRawMaterial">$36,662</h6>
                                            </div>

                                            <!-- Mano de Obra -->
                                            <div class="costeo-item">
                                                <p class="costeo-label">Mano de Obra</p>
                                                <h6 class="costeo-value" id="payWorkforce">$149.68</h6>
                                            </div>

                                            <!-- Costos Indirectos -->
                                            <div class="costeo-item">
                                                <p class="costeo-label">Costos Indirectos</p>
                                                <h6 class="costeo-value" id="payIndirectCost">$0.00</h6>
                                            </div>

                                            <!-- Servicios Externos -->
                                            <div class="costeo-item">
                                                <p class="costeo-label">Servicios Externos</p>
                                                <h6 class="costeo-value" id="services">$0.00</h6>
                                            </div>

                                            <!-- Gastos -->
                                            <div class="costeo-item">
                                                <p class="costeo-label">Gastos Generales</p>
                                                <h6 class="costeo-value" id="payAssignableExpenses">$715.66</h6>
                                            </div>

                                            <!-- Rentabilidad -->
                                            <div class="costeo-item highlight-profit">
                                                <p class="costeo-label">Rentabilidad</p>
                                                <h6 class="costeo-value" id="profitability">3.0%</h6>
                                            </div>

                                            <!-- Comisión -->
                                            <div class="costeo-item">
                                                <p class="costeo-label">Comisión Venta</p>
                                                <h6 class="costeo-value" id="commisionSale">$0.00</h6>
                                            </div>
                                        </div>

                                        <!-- Botón Guardar -->
                                        <div class="save-section d-none" id="saveContainer">
                                            <button id="saveChanges" class="btn btn-save" type="button">
                                                <span class="spinner-border spinner-border-sm me-2 d-none" id="spinnerSave" role="status"></span>
                                                <i class="fas fa-save me-2" id="saveIcon"></i>
                                                <span id="saveText">Guardar cambios</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 2: Costos y Gastos (Chart) -->
                            <div class="col d-flex">
                                <div class="analysis-card">
                                    <div class="card-header-modern">
                                        <h5 class="card-title-modern">
                                            <i class="fas fa-chart-pie text-success"></i>
                                            Costos y Gastos
                                        </h5>
                                    </div>
                                    <div class="chart-container">
                                        <canvas id="chartProductCosts" width="400" height="250"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 3: Ventas -->
                            <div class="col-auto d-flex">
                                <div class="analysis-card" style="width: 250px;">
                                    <div class="card-header-modern">
                                        <h5 class="card-title-modern">
                                            <i class="fas fa-chart-line text-info"></i>
                                            Ventas
                                        </h5>
                                    </div>

                                    <div class="card-body-modern">
                                        <!-- Unidades Vendidas -->
                                        <div class="sales-item">
                                            <div class="sales-info">
                                                <p class="sales-label">Número de Unidades</p>
                                                <h4 class="sales-value" id="unitsSold">4</h4>
                                            </div>
                                            <div class="sales-icon icon-units">
                                                <i class="fas fa-cubes"></i>
                                            </div>
                                        </div>

                                        <!-- Ingresos -->
                                        <div class="sales-item">
                                            <div class="sales-info">
                                                <p class="sales-label">Ingresos</p>
                                                <h4 class="sales-value" id="turnover">$165,280</h4>
                                            </div>
                                            <div class="sales-icon icon-revenue">
                                                <i class="fas fa-chart-bar"></i>
                                            </div>
                                        </div>

                                        <!-- Precio Real -->
                                        <div class="sales-item">
                                            <div class="sales-info">
                                                <p class="sales-label">Precio (Real)</p>
                                                <h4 class="sales-value" id="recomendedPrice">$38,687</h4>
                                            </div>
                                            <div class="sales-icon icon-price">
                                                <i class="fas fa-dollar-sign"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Row 4-->
                        <div class="row d-flex align-items-center mt-3">
                            <!-- Begin total sales chart -->
                            <div class="col-lg-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Costo Mano de Obra</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <canvas id="chartWorkForce" style="width: 80%;"></canvas>
                                            <div class="center-text">
                                                <p class="text-muted mb-1 font-weight-600">Total Costo</p>
                                                <p class="mb-0 font-weight-bold" id="totalCostWorkforceEsp"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Total Tiempo Proceso</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <canvas id="chartTimeProcess" style="width: 80%;"></canvas>
                                            <div class="center-text">
                                                <p class="text-muted mb-1 font-weight-600">Tiempo Total</p>
                                                <p class="mb-0 font-weight-bold" id="totalTimeProcess"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Total Tiempos</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <canvas id="chartManufactTime" style="width: 80%;"></canvas>
                                            <div class="center-text">
                                                <p class="text-muted mb-1 font-weight-600">Tiempo Total</p>
                                                <p class="mb-0 font-weight-bold" id="manufactPromTime"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 pageBreak">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Composición Precio </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <canvas id="chartPrice" style="width: 80%;"></canvas>
                                            <div class="center-text">
                                                <p class="text-muted mb-1 font-weight-600">Precio Total</p>
                                                <p class="mb-0 font-weight-bold" id="totalPricesComp"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End total sales chart -->
                            <!-- Begin earning chart -->
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header dflex-between-center">
                                        <h5 class="card-title">Costos Materia Prima</h5>
                                    </div>
                                    <div class="card-body pt-2">
                                        <canvas id="chartMaterialsCosts" style="width: 80%;"></canvas>
                                    </div>
                                </div>
                            </div>
                            <!-- End earning chart -->
                            <!-- Begin earning chart -->
                            <!-- <div class="col-lg-6">
                                            <div class="card">
                                                <div class="card-header dflex-between-center">
                                                    <h5 class="card-title">Costos Servicios</h5>
                                                </div>
                                                <div class="card-body pt-2">
                                                    <canvas id="chartServicesCosts" style="width: 80%;"></canvas>
                                                </div>
                                            </div>
                                        </div> -->
                            <!-- End earning chart -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Main content end -->

        <!-- Footer -->
        <?php include_once  dirname(dirname(dirname(dirname(__DIR__)))) . '/public/partials/footer.php'; ?>
    </div>
    <!-- Page End -->
    </div>
    </div>
    <?php include_once dirname(dirname(dirname(dirname(__DIR__)))) . '/public/partials/scriptsJS.php'; ?>

    <script src="/public/js/components/orderData.js"></script>
    <script src="/cost/js/prices/pricesCOP/configPrices.js"></script>
    <script>
        flag_expense = "<?= $_SESSION['flag_expense'] ?>";

        // price_usd = 
        flag_currency_usd = "<?= $_SESSION['flag_currency_usd'] ?>";
        flag_currency_eur = "<?= $_SESSION['flag_currency_eur'] ?>";
        flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";
        coverage_usd = "<?= $_SESSION['coverage_usd'] ?>";
        coverage_eur = "<?= $_SESSION['coverage_eur'] ?>";
        id_company = "<?= $_SESSION['id_company'] ?>";
        viewPrices = 2;
    </script>
    <script src="/cost/js/dashboard/dashboardEvents.js"></script>
    <script src="/cost/js/dashboard/indicatorsProduct.js"></script>
    <script src="/cost/js/dashboard/calcDataCost.js"></script>
    <script src="/cost/js/dashboard/graphicsProduct.js"></script>
    <script src="/public/js/components/printPdf.js"></script>
</body>

</html>