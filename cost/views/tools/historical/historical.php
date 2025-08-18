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
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <!-- Botones principales -->
                                    <div class="col-sm-3 col-xl-3">
                                        <div class="mb-1 d-flex align-items-center">
                                            <button class="btn btn-warning mr-1 shadow-lg" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver Lista de Históricos" id="btnList">
                                                <i class="fas fa-list-ul"></i>
                                            </button>

                                            <button class="btn btn-success mr-1 shadow-lg" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver Gráficos y Dashboard" id="btnGraphic">
                                                <i class="fas fa-chart-line"></i>
                                            </button>

                                            <div class="mx-2 text-muted" style="font-size: 24px;">|</div>

                                            <button class="btn btn-primary ml-1 shadow-lg" data-bs-toggle="tooltip" data-bs-placement="top" title="Guardar Histórico de Costos" id="btnNewHistorical">
                                                <i class="fas fa-save"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Controles del Dashboard (ocultos por defecto) -->
                                    <div class="col-sm-9 col-xl-9" id="analysisControls" style="display: none;">
                                        <div class="row align-items-end">
                                            <!-- Selector de Semana -->
                                            <div class="col-md-3">
                                                <div class="form-group mb-2">
                                                    <label for="weekSelector" class="form-label-modern">
                                                        <i class="fas fa-calendar-week mr-1"></i>
                                                        Período de Análisis
                                                    </label>
                                                    <select class="form-control modern-select" id="weekSelector">
                                                        <option value="all">📊 Todas las Semanas</option>
                                                        <!-- Las opciones se llenan dinámicamente -->
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Selector de Producto (oculto inicialmente) -->
                                            <div class="col-md-3" id="productSelectorGroup" style="display: none;">
                                                <div class="form-group mb-2">
                                                    <label for="productSelector" class="form-label-modern">
                                                        <i class="fas fa-box mr-1"></i>Producto Específico
                                                    </label>
                                                    <select class="form-control modern-select" id="productSelector">
                                                        <option value="all">📦 Todos los Productos</option>
                                                        <!-- Las opciones se llenan dinámicamente -->
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Tipo de Gráfico -->
                                            <div class="col-md-3">
                                                <div class="form-group mb-2">
                                                    <label class="form-label-modern">
                                                        <i class="fas fa-chart-bar mr-1"></i>
                                                        Tipo de Vista
                                                    </label>
                                                    <div class="btn-group btn-group-sm d-flex" role="group">
                                                        <button type="button" class="btn btn-chart-type active" data-chart="line" title="Gráfico de Líneas">
                                                            <i class="fas fa-chart-line"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-chart-type" data-chart="bar" title="Gráfico de Barras">
                                                            <i class="fas fa-chart-bar"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-chart-type" data-chart="area" title="Gráfico de Área">
                                                            <i class="fas fa-chart-area"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Acciones -->
                                            <div class="col-md-3">
                                                <div class="form-group mb-2">
                                                    <label class="form-label-modern">
                                                        <i class="fas fa-tools mr-1"></i>
                                                        Acciones
                                                    </label>
                                                    <div class="btn-group btn-group-sm d-flex" role="group">
                                                        <button type="button" class="btn btn-outline-success" id="btnRefreshData" title="Actualizar Datos">
                                                            <i class="fas fa-sync"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-info" id="btnExportData" title="Exportar a CSV">
                                                            <i class="fas fa-download"></i>
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