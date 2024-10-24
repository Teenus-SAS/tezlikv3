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
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tezlik - Cost | Contract</title>
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
            <!-- Content -->
            <div class="page-content">
                <!-- Page header -->
                <div class="page-title-box">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-sm-5 col-xl-6">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Contrato De Prestación De Servicios</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <!-- <li class="breadcrumb-item active">Escribanos, que estaremos muy atentos de atender sus requerimientos</li> -->
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <div class="row align-items-stretch">
                            <div class="col-md-8 col-lg-12">
                                <div class="inbox-rightbar card">
                                    <div class="card-body">
                                        <form id="formSaveContract">
                                            <input type="text" class="form-control" id="idContract" name="idContract" style="display: none;" />

                                            <div class="form-group" style="overflow-y: scroll;max-height: 300px;">
                                                <div id="compose-editor" name="content">Hey</div>
                                            </div>

                                            <div class="form-group pt-2">
                                                <div class="text-center">
                                                    <button class="btn btn-primary chat-send-btn" data-effect="wave" id="btnSave">
                                                        <span class="d-none d-sm-inline-block mr-2 align-middle">Guardar</span>
                                                        <i class="bx bxs-send fs-sm align-middle"></i>
                                                    </button>
                                                    <button class="btn btn-warning chat-send-btn" data-effect="wave" id="btnSend">
                                                        <span class="d-none d-sm-inline-block mr-2 align-middle">Enviar</span>
                                                        <i class="bx bxs-send fs-sm align-middle"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
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
    <script src="/admin/js/contract/contract.js"></script>
</body>

</html>