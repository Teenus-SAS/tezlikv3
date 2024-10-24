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
    <title>Tezlik - Admin | Users</title>
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
                                    <h3 class="mb-1 font-weight-bold text-dark">Usuarios</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Creación de Usuario</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6">
                                <div class="form-inline justify-content-sm-end">
                                    <button class="btn btn-warning" id="btnNewUser">Nuevo Usuario</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardCreateUser">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formCreateUser">
                                            <div class="form-row">
                                                <div class="col">
                                                    <label>Empresa</label>
                                                    <select name="company" class="form-control company" id="company" name="company"></select>
                                                </div>
                                                <div class="col">
                                                    <label>Nombres</label>
                                                    <input type="text" class="form-control" name="nameUser" id="firstname">
                                                </div>
                                                <div class="col">
                                                    <label>Apellidos</label>
                                                    <input type="text" class="form-control" name="lastnameUser" id="lastname">
                                                </div>
                                                <div class="col">
                                                    <label>Correo</label>
                                                    <input type="email" class="form-control" name="emailUser" id="email">
                                                </div>
                                                <div class="col-xs-2 text-center">
                                                    <label>Usuario Principal</label>
                                                    <select name="principalUser" id="principalUser" class="form-control">
                                                        <option selected disabled value="0">Seleccionar</option>
                                                        <option value="1">Si</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                    <!-- <input type="checkBox" class="form-control-updated" name="" id="principalUser"> -->
                                                </div>
                                                <div style="margin-top:32px;">
                                                    <button class="btn btn-success" id="btnCreateUser">Crear Usuario</button>
                                                </div>
                                            </div>
                                        </form>
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
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="tblUsers">

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

    <script src="/admin/js/global/changeCompany.js"></script>
    <script src="/admin/js/companies/configCompanies.js"></script>
    <script src="/admin/js/companies/configCompanies.js"></script>
    <script src="/admin/js/users/tblUsers.js"></script>
    <script src="/admin/js/users/users.js"></script>
</body>

</html>