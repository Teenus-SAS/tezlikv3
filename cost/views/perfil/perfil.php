<?php

use tezlikv3\dao\UserInactiveTimeDao;

require_once(dirname(dirname(dirname(__DIR__))) . "/api/src/dao/app/global/login/UserInactiveTimeDao.php");
$userinactivetimeDao = new UserInactiveTimeDao();
$userinactivetimeDao->findSession();
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
    <title>Tezlik | Profile</title>
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
                <div class="container">
                    <div class="row">
                        <div class="card">
                            <div class="card-body">
                                <div class="col-12">
                                    <!-- Page title -->
                                    <div class="my-4">
                                        <h3>Mi Perfil</h3>
                                        <hr>
                                    </div>
                                    <div class="row mb-5 gx-5">
                                        <form id="formSaveProfile">
                                            <div class="col-xxl-12 mb-5 mb-xxl-0">
                                                <div class="bg-secondary-soft px-4 py-2 rounded">
                                                    <div class="row g-3">
                                                        <input type="" id="idUser" name="idUser" hidden>
                                                        <div class="col-md-4">
                                                            <label class="form-label">Nombres *</label>
                                                            <input type="text" class="form-control" placeholder="" aria-label="First name" id="firstname" name="nameUser">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label">Apellidos *</label>
                                                            <input type="text" class="form-control" placeholder="" aria-label="Last name" id="lastname" name="lastnameUser">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label">Cargo *</label>
                                                            <input type="text" class="form-control" placeholder="" aria-label="Position" id="position" name="position" disabled>
                                                        </div>
                                                        <div class="col-md-4 mt-4">
                                                            <label for="email" class="form-label">Email *</label>
                                                            <input type="email" class="form-control" id="email" name="emailUser">
                                                        </div>
                                                        <div class="col-md-4 mt-4">
                                                            <label class="form-label">Nueva Contraseña</label>
                                                            <input type="password" class="form-control" placeholder="" aria-label="Password" id="password" name="password">
                                                        </div>
                                                        <div class="col-md-4 mt-4">
                                                            <label class="form-label">Confirmar Contraseña</label>
                                                            <input type="password" class="form-control" placeholder="" aria-label="Confirm Password" id="conPassword" name="conPassword">
                                                        </div>
                                                    </div> <!-- Row END -->
                                                </div>
                                            </div>
                                            <hr>
                                            <!-- Upload profile -->
                                            <div class="col-xxl-4">
                                                <div class="bg-secondary-soft px-4 py-2 rounded">
                                                    <div class="row">
                                                        <div class="col-md-5">
                                                            <label for="Image" class="form-label">Ingrese su foto</label>
                                                            <input class="form-control" type="file" id="formFile">
                                                        </div>
                                                        <div class="col-2">
                                                            <button class="btn btn-light" style="margin-top:33px" id="clearImg">Limpiar</button>
                                                        </div>
                                                        <div class="col-4">
                                                            <img id="avatar" src="" class="img-fluid" style="width: 100px;" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- button -->
                                    <div class="gap-3 d-md-flex justify-content-md-end text-center">
                                        <button type="button" class="btn btn-primary btn-lg" id="btnSaveProfile">Actualizar Usuario</button>
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

    <script src="/global/js/profile/profile.js"></script>
</body>

</html>