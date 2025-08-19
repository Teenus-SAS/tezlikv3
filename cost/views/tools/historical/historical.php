<?php require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/api/src/Auth/authMiddleware.php'; ?>
<?php require_once dirname(dirname(dirname(__DIR__))) . '/modals/manualHistorical.php'; ?>
<?php require_once dirname(dirname(dirname(__DIR__))) . '/modals/weekHistorical.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="Teenus SAS">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TezlikSoftware | Historical</title>
    <link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />

    <?php include_once dirname(dirname(dirname(dirname(__DIR__)))) . '/public/partials/scriptsCSS.php'; ?>

    <!-- Chart.js para gráficos -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

    <!-- Estilos modernos para el dashboard -->
    <style>
        /* Estilos para los selectores modernos */
        .modern-select {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: white;
            transition: all 0.3s ease;
            font-size: 0.875rem;
            padding: 8px 12px;
        }

        .modern-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .form-label-modern {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            font-size: 0.75rem;
            color: #6b7280;
        }

        /* Contenedor de controles de análisis */
        #analysisControls {
            border-left: 3px solid #3b82f6;
            padding-left: 20px;
            background: linear-gradient(90deg, rgba(59, 130, 246, 0.05) 0%, rgba(255, 255, 255, 0) 100%);
            border-radius: 0 8px 8px 0;
        }

        /* Botones de tipo de gráfico */
        .btn-chart-type {
            border-radius: 6px;
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
            background: white;
            color: #6b7280;
        }

        .btn-chart-type:hover {
            border-color: #3b82f6;
            color: #3b82f6;
            transform: translateY(-1px);
        }

        .btn-chart-type.active {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
        }

        /* Animación suave para mostrar/ocultar controles */
        .slide-in {
            animation: slideIn 0.4s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Icono de carga en el select */
        .select-loading::after {
            content: "⏳";
            margin-left: 8px;
        }
    </style>

    <!-- CSS Moderno para Header Profesional -->
    <style>
        /* Variables CSS para consistencia */
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --warning-gradient: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%);
            --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --shadow-modern: 0 6px 15px rgba(0, 0, 0, 0.08);
            --shadow-hover: 0 8px 20px rgba(0, 0, 0, 0.12);
            --border-radius: 8px;
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Card principal compacto */
        .modern-dashboard-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-modern);
            backdrop-filter: blur(8px);
            position: relative;
            overflow: hidden;
            margin-bottom: 15px;
        }

        .modern-dashboard-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-gradient);
        }

        /* Card body compacto */
        .modern-dashboard-header .card-body {
            padding: 12px 20px !important;
        }

        /* Sección de botones principales compacta */
        .navigation-section {
            background: white;
            border-radius: var(--border-radius);
            padding: 12px 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            position: relative;
        }

        .navigation-section::after {
            content: '';
            position: absolute;
            right: 0;
            top: 20%;
            bottom: 20%;
            width: 1px;
            background: linear-gradient(to bottom, transparent, #e2e8f0, transparent);
        }

        /* Botones principales más pequeños */
        .nav-btn {
            position: relative;
            width: 45px;
            height: 45px;
            border: none;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: white;
            transition: var(--transition);
            overflow: hidden;
            margin: 0 6px;
        }

        .nav-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.2);
            opacity: 0;
            transition: var(--transition);
        }

        .nav-btn:hover::before {
            opacity: 1;
        }

        .nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .nav-btn:active {
            transform: translateY(-1px);
        }

        /* Estilos específicos para cada botón */
        .nav-btn.btn-list {
            background: var(--warning-gradient);
            box-shadow: 0 4px 12px rgba(252, 74, 26, 0.25);
        }

        .nav-btn.btn-chart {
            background: var(--success-gradient);
            box-shadow: 0 4px 12px rgba(17, 153, 142, 0.25);
        }

        .nav-btn.btn-save {
            background: var(--primary-gradient);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25);
        }

        /* Separador más pequeño */
        .nav-separator {
            width: 1px;
            height: 30px;
            background: linear-gradient(to bottom, transparent, #cbd5e0, transparent);
            margin: 0 12px;
            border-radius: 1px;
        }

        /* Etiquetas más compactas */
        .nav-label {
            font-size: 0.65rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-top: 4px;
            transition: var(--transition);
        }

        .nav-btn:hover+.nav-label {
            color: #334155;
            transform: translateY(-1px);
        }

        /* Sección de controles más compacta */
        .analysis-controls {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
            border-radius: var(--border-radius);
            padding: 12px 16px;
            margin-left: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
        }

        .analysis-controls::before {
            content: '';
            position: absolute;
            left: -2px;
            top: 25%;
            bottom: 25%;
            width: 3px;
            background: var(--info-gradient);
            border-radius: 2px;
        }

        /* Labels más pequeños */
        .form-label-modern {
            font-weight: 600;
            font-size: 0.7rem;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
        }

        .form-label-modern i {
            color: #6366f1;
            margin-right: 4px;
            font-size: 0.75rem;
        }

        /* Selectores más compactos */
        .modern-select {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            background: white;
            font-size: 0.8rem;
            color: #334155;
            transition: var(--transition);
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
            height: 36px;
        }

        .modern-select:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.1);
            outline: none;
            transform: translateY(-1px);
        }

        .modern-select:hover {
            border-color: #c7d2fe;
            transform: translateY(-1px);
        }

        /* Grupos de botones más compactos */
        .btn-group-modern {
            background: #f8fafc;
            border-radius: 6px;
            padding: 2px;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .btn-chart-type {
            border: none;
            background: transparent;
            color: #64748b;
            padding: 6px 8px;
            border-radius: 4px;
            transition: var(--transition);
            font-size: 0.85rem;
            margin: 0 1px;
            height: 32px;
            width: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-chart-type:hover {
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
            transform: translateY(-1px);
        }

        .btn-chart-type.active {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
            transform: translateY(-1px);
        }

        /* Botones de acción más pequeños */
        .action-btn {
            border: 1px solid transparent;
            padding: 6px 8px;
            border-radius: 4px;
            background: white;
            color: #64748b;
            transition: var(--transition);
            font-size: 0.85rem;
            margin: 0 1px;
            height: 32px;
            width: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .action-btn.btn-refresh {
            border-color: #10b981;
            color: #10b981;
        }

        .action-btn.btn-refresh:hover {
            background: #10b981;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }

        .action-btn.btn-export {
            border-color: #3b82f6;
            color: #3b82f6;
        }

        .action-btn.btn-export:hover {
            background: #3b82f6;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }

        /* Animación slide-in más rápida */
        .slide-in {
            animation: slideInRight 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Responsive design compacto */
        @media (max-width: 768px) {
            .navigation-section {
                text-align: center;
                margin-bottom: 10px;
                padding: 8px 12px;
            }

            .nav-btn {
                width: 40px;
                height: 40px;
                margin: 0 4px;
                font-size: 1rem;
            }

            .analysis-controls {
                margin-left: 0;
                margin-top: 10px;
                padding: 10px 12px;
            }

            .nav-separator {
                display: none;
            }

            .nav-label {
                font-size: 0.6rem;
                margin-top: 2px;
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
            <!-- Loader -->
            <div class="loading">
                <a href="javascript:;" class="close-btn" style="display: none;"><i class="bi bi-x-circle-fill"></i></a>
                <div class="loader"></div>
            </div>

            <!-- Content -->
            <div class="page-content">
                <!-- Page header -->
                <div class="page-title-box" style="padding-bottom: 45px;">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-sm-5 col-xl-5">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Histórico de Costos y Precios</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card con botones principales y controles del dashboard -->
                <div class="page-content-wrapper mt--45" style="margin-bottom: 60px;">
                    <div class="container-fluid">
                        <div class="card modern-dashboard-header">
                            <div class="card-body p-4">
                                <div class="row align-items-center">

                                    <!-- Sección de Navegación Principal -->
                                    <div class="col-lg-4 col-md-12">
                                        <div class="navigation-section">
                                            <div class="d-flex align-items-center justify-content-center justify-content-lg-start">

                                                <!-- Botón Lista -->
                                                <div class="text-center">
                                                    <button class="nav-btn btn-list"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Ver Lista de Históricos"
                                                        id="btnList">
                                                        <i class="fas fa-list-ul"></i>
                                                    </button>
                                                    <div class="nav-label">Lista</div>
                                                </div>

                                                <!-- Botón Dashboard -->
                                                <div class="text-center">
                                                    <button class="nav-btn btn-chart"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Ver Dashboard Analítico"
                                                        id="btnGraphic">
                                                        <i class="fas fa-chart-line"></i>
                                                    </button>
                                                    <div class="nav-label">Dashboard</div>
                                                </div>

                                                <!-- Separador -->
                                                <div class="nav-separator"></div>

                                                <!-- Botón Guardar -->
                                                <div class="text-center">
                                                    <button class="nav-btn btn-save"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Crear Nuevo Histórico"
                                                        id="btnNewHistorical">
                                                        <i class="fas fa-save"></i>
                                                    </button>
                                                    <div class="nav-label">Nuevo</div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- Controles de Análisis (ocultos por defecto) -->
                                    <div class="col-lg-8 col-md-12" id="analysisControls" style="display: none;">
                                        <div class="analysis-controls">
                                            <div class="row g-3">

                                                <!-- Selector de Período -->
                                                <div class="col-lg-3 col-md-6">
                                                    <div class="form-group">
                                                        <label for="weekSelector" class="form-label-modern">
                                                            <i class="fas fa-calendar-alt"></i>
                                                            Período
                                                        </label>
                                                        <select class="form-control modern-select" id="weekSelector">
                                                            <option value="all">📊 Todas las Semanas</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Selector de Producto -->
                                                <div class="col-lg-3 col-md-6" id="productSelectorGroup" style="display: none;">
                                                    <div class="form-group">
                                                        <label for="productSelector" class="form-label-modern">
                                                            <i class="fas fa-cube"></i>
                                                            Producto
                                                        </label>
                                                        <select class="form-control modern-select" id="productSelector">
                                                            <option value="all">📦 Todos los Productos</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Tipo de Visualización -->
                                                <div class="col-lg-3 col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label-modern">
                                                            <i class="fas fa-chart-bar"></i>
                                                            Visualización
                                                        </label>
                                                        <div class="btn-group-modern d-flex">
                                                            <button type="button"
                                                                class="btn-chart-type active"
                                                                data-chart="line"
                                                                title="Gráfico de Líneas">
                                                                <i class="fas fa-chart-line"></i>
                                                            </button>
                                                            <button type="button"
                                                                class="btn-chart-type"
                                                                data-chart="bar"
                                                                title="Gráfico de Barras">
                                                                <i class="fas fa-chart-bar"></i>
                                                            </button>
                                                            <button type="button"
                                                                class="btn-chart-type"
                                                                data-chart="area"
                                                                title="Gráfico de Área">
                                                                <i class="fas fa-chart-area"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Acciones Rápidas -->
                                                <div class="col-lg-3 col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label-modern">
                                                            <i class="fas fa-tools"></i>
                                                            Acciones Rápidas
                                                        </label>
                                                        <div class="d-flex">
                                                            <button type="button"
                                                                class="action-btn btn-refresh flex-fill"
                                                                id="btnRefreshData"
                                                                title="Actualizar Datos">
                                                                <i class="fas fa-sync-alt"></i>
                                                            </button>
                                                            <button type="button"
                                                                class="action-btn btn-export flex-fill"
                                                                id="btnExportData"
                                                                title="Exportar Datos">
                                                                <i class="fas fa-upload"></i>
                                                            </button>
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
                </div>

                <!-- Lista Histórica (vista por defecto) -->
                <div class="page-content-wrapper mt--45 cardHistoricalResume">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card disable-select">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="tblHistoricalResume">
                                                <!-- Tabla se llena dinámicamente -->
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Productos Históricos -->
                <div class="page-content-wrapper mt--45 cardHistoricalProducts" style="display: none;">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card disable-select">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="tblHistoricalProducts">
                                                <!-- Tabla se llena dinámicamente -->
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dashboard de Ganancias (oculto por defecto) -->
                <div class="page-content-wrapper mt--45 cardDashboard" style="display: none;">
                    <div class="container-fluid">
                        <!-- Aquí irá el contenido del dashboard -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body text-center py-5">
                                        <h4 class="text-muted">
                                            <i class="fas fa-chart-line fa-3x mb-3 d-block"></i>
                                            Dashboard de Ganancias
                                        </h4>
                                        <canvas id="weeklyChart" height="400"></canvas>
                                        <canvas id="productChart" height="300"></canvas>
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

    <!-- 2. Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="/public/js/components/orderData.js"></script>

    <!-- Variables PHP para JavaScript -->
    <script>
        flag_expense = "<?= $_SESSION['flag_expense'] ?>";
        flag_currency_usd = "<?= $_SESSION['flag_currency_usd'] ?>";
        flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";
        type = 'manual';
        sessionStorage.removeItem('typePrice');
    </script>



    <!-- Scripts originales del sistema -->
    <script src="/cost/js/tools/historical/historicalConfig.js"></script>
    <script src="/cost/js/tools/historical/saveHistorical.js"></script>
    <script src="/cost/js/tools/historical/historicalUtils.js"></script>
    <script src="/cost/js/tools/historical/historical.js"></script>
    <script src="/cost/js/tools/historical/historicalUI.js"></script>
    <script src="/cost/js/tools/historical/historicalEvents.js"></script>
    <script src="/cost/js/tools/historical/tblHistoricalResume.js"></script>
    <script src="/cost/js/tools/historical/tblHistorical.js"></script>
    <script src="/cost/js/tools/historical/dashboardCharts.js"></script>
    <script src="/cost/js/tools/historical/historicalIndicators.js"></script>

    <!-- Script básico para mostrar/ocultar controles -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Botón para mostrar gráficos y controles
            document.getElementById('btnGraphic').addEventListener('click', function() {
                console.log('Botón Gráficos clickeado');

                // Ocultar otras secciones
                document.querySelector('.cardHistoricalResume').style.display = 'none';
                document.querySelector('.cardHistoricalProducts').style.display = 'none';

                // Mostrar dashboard
                document.querySelector('.cardDashboard').style.display = 'block';

                // Mostrar controles con animación
                const controls = document.getElementById('analysisControls');
                controls.style.display = 'block';
                controls.classList.add('slide-in');

                // Aquí se inicializará el dashboard
                console.log('Dashboard y controles mostrados');
            });

            // Botón para mostrar lista
            document.getElementById('btnList').addEventListener('click', function() {
                console.log('Botón Lista clickeado');

                // Ocultar dashboard y controles
                document.querySelector('.cardDashboard').style.display = 'none';
                document.querySelector('.cardHistoricalProducts').style.display = 'none';
                document.getElementById('analysisControls').style.display = 'none';

                // Mostrar lista
                document.querySelector('.cardHistoricalResume').style.display = 'block';

                console.log('Lista mostrada, controles ocultos');
            });

            // Event listeners para los selectores (preparados para funcionalidad futura)
            document.getElementById('weekSelector').addEventListener('change', function() {
                console.log('Semana seleccionada:', this.value);

                // Mostrar/ocultar selector de productos según la selección
                const productGroup = document.getElementById('productSelectorGroup');
                if (this.value !== 'all') {
                    productGroup.style.display = 'block';
                } else {
                    productGroup.style.display = 'none';
                    document.getElementById('productSelector').value = 'all';
                }
            });

            document.getElementById('productSelector').addEventListener('change', function() {
                console.log('Producto seleccionado:', this.value);
            });

            // Event listeners para botones de tipo de gráfico
            document.querySelectorAll('.btn-chart-type').forEach(btn => {
                btn.addEventListener('click', function() {
                    // Remover active de todos
                    document.querySelectorAll('.btn-chart-type').forEach(b => b.classList.remove('active'));
                    // Agregar active al clickeado
                    this.classList.add('active');

                    console.log('Tipo de gráfico seleccionado:', this.dataset.chart);
                });
            });

            // Event listeners para botones de acción
            document.getElementById('btnRefreshData').addEventListener('click', function() {
                console.log('Actualizando datos...');
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                // Simular carga
                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-sync"></i>';
                }, 1000);
            });

            document.getElementById('btnExportData').addEventListener('click', function() {
                console.log('Exportando datos...');
            });
        });
    </script>
</body>

</html>