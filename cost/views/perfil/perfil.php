<?php
if (!isset($_SESSION)) {
    session_start();
    if (sizeof($_SESSION) == 0)
        header('location: /');
}
if (sizeof($_SESSION) == 0)
    header('location: /');
?>
<?php include_once dirname(dirname(__DIR__)) . '/modals/contract.php' ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tezlik - Cost | Perfil</title>
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
                <a href="javascript:;" class="close-btn" style="display: none;"><i class="bi bi-x-circle-fill"></i></a>
                <div class="loader"></div>
            </div>

            <!-- Content -->
            <div class="page-content">
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
                <div class="container py-5">
                    <form id="formSaveProfile">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <div class="picture-container">
                                            <div class="picture">
                                                <img id="avatar" src="" class="img-fluid" style="width: 100px;" />
                                                <input class="form-control" type="file" id="formFile">
                                            </div>
                                        </div>
                                        <h5 class="my-3" id="profileName"></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <div class="form-row" style="margin-bottom:-30px">
                                            <input type="" id="idUser" name="idUser" hidden>
                                            <div class="col-sm-4 floating-label enable-floating-label show-label">
                                                <input type="text" class="form-control text-center firstname general" placeholder="" aria-label="First name" id="firstname" name="nameUser">
                                                <label>Nombres *</label>
                                            </div>
                                            <div class="col-sm-4 floating-label enable-floating-label show-label">
                                                <input type="text" class="form-control text-center general" placeholder="" aria-label="Last name" id="lastname" name="lastnameUser">
                                                <label>Apellidos *</label>
                                            </div>
                                            <div class="col-sm-4 floating-label enable-floating-label show-label">
                                                <input type="text" class="form-control text-center" placeholder="" aria-label="Position" id="position" name="position">
                                                <label>Cargo *</label>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-row" style="margin-bottom:-30px">
                                            <div class="col-sm-4 floating-label enable-floating-label show-label">
                                                <input type="email" class="form-control text-center" id="email" name="emailUser">
                                                <label for="email" class="form-label">Email *</label>
                                            </div>
                                            <div class="col-sm-4 floating-label enable-floating-label show-label">
                                                <input type="password" class="form-control text-center" placeholder="" aria-label="Password" id="password" name="password">
                                                <label class="form-label">Nueva Contraseña</label>
                                            </div>
                                            <div class="col-sm-4 floating-label enable-floating-label show-label">
                                                <input type="password" class="form-control text-center" placeholder="" aria-label="Confirm Password" id="conPassword" name="conPassword">
                                                <label class="form-label">Confirmar Contraseña</label>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-row">
                                            <?php if ($_SESSION['contract'] == 1) { ?>
                                                <div class="col-sm-3">
                                                    <a href="javascript:;" id="btnShowModalContract">Contrato de Prestación <br><?php echo $_SESSION['date_contract'] ?></a>
                                                </div>
                                                <div class="col-sm-9 d-flex justify-content-end">
                                                <?php } else { ?>
                                                    <div class="col-sm-12 d-flex justify-content-end">
                                                    <?php } ?>
                                                    <button type="button" class="btn btn-primary" id="btnSaveProfile">Actualizar Usuario</button>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <div class="picture-container mb-4">
                                                <div class="pictureC">
                                                    <img id="logo" src="" class="img-fluid" style="width: 400px;" />
                                                    <input class="form-control" type="file" id="formFileC">
                                                </div>
                                            </div>
                                            <h5 class="my-3" id="profileName"></h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="card companyData">
                                        <div class="card-body">
                                            <input type="" id="state" name="companyState" hidden>
                                            <input type="" id="idCompany" name="idCompany" hidden>

                                            <div class="form-row">
                                                <div class="col-sm-4 floating-label enable-floating-label show-label">
                                                    <label class="form-label">Compañia</label>
                                                    <input class="form-control text-center general" type="text" id="company" name="company">
                                                </div>
                                                <div class="col-sm-4 floating-label enable-floating-label show-label">
                                                    <label class="form-label">NIT</label>
                                                    <input class="form-control text-center general" type="number" id="nit" name="companyNIT">
                                                </div>
                                                <div class="col-sm-4 floating-label enable-floating-label show-label">
                                                    <label class="form-label">Ciudad</label>
                                                    <input class="form-control text-center general" type="text" id="city" name="companyCity">
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-sm-6 floating-label enable-floating-label show-label">
                                                    <label class="form-label">Pais</label>
                                                    <input class="form-control text-center general" type="text" id="country" name="companyCountry">
                                                </div>
                                                <div class="col-sm-6 floating-label enable-floating-label show-label">
                                                    <label class="form-label">Telefono</label>
                                                    <input class="form-control text-center general" type="number" id="phone" name="companyTel">
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-sm-12 floating-label enable-floating-label show-label">
                                                    <label class="form-label">Dirección</label>
                                                    <textarea class="form-control text-center general" id="address" name="companyAddress"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
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

        // price_usd = 
        flag_currency_usd = "<?= $_SESSION['flag_currency_usd'] ?>";
        flag_expense_distribution = "<?= $_SESSION['flag_expense_distribution'] ?>";
    </script>
    <script src="/global/js/global/loadImg.js"></script>
    <script src="/cost/js/profile/profile.js"></script>
</body>

</html>