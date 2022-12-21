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
    <meta name="description" content="LetStart Admin is a full featured, multipurpose, premium bootstrap admin template built with Bootstrap 4 Framework, HTML5, CSS and JQuery.">
    <meta name="keywords" content="admin, panels, dashboard, admin panel, multipurpose, bootstrap, bootstrap4, all type of dashboards">
    <meta name="author" content="MatrrDigital">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login | Autenticación</title>
    <link rel="shortcut icon" href="/assets/images/favicon.png" type="image/x-icon" />

    <!-- ================== BEGIN PAGE LEVEL CSS START ================== -->
    <link rel="stylesheet" href="/assets/css/icons.css" />
    <link rel="stylesheet" href="/assets/libs/wave-effect/css/waves.min.css" />
    <link rel="stylesheet" href="/assets/libs/owl-carousel/css/owl.carousel.min.css" />
    <!-- ================== BEGIN PAGE LEVEL END ================== -->
    <!-- ================== BEGIN APP CSS  ================== -->
    <link rel="stylesheet" href="/assets/css/bootstrap.css" />
    <link rel="stylesheet" href="/assets/css/styles.css" />
    <!-- ================== END APP CSS ================== -->

    <!-- ================== BEGIN POLYFILLS  ================== -->
    <!--[if lt IE 9]>
     <script src="assets/libs/html5shiv/js/html5shiv.js"></script>
     <script src="assets/libs/respondjs/js/respond.min.js"></script>
  <![endif]-->
    <!-- ================== END POLYFILLS  ================== -->
</head>

<body>
    <div class="backhome">
        <a href="/index.php" class="avatar avatar-sm bg-primary text-white"><i class="bx bx-home-alt fs-sm"></i></a>
    </div>
    <!-- Begin Page -->
    <div class="auth-pages">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body p-4 p-md-5">
                            <div class="clearfix" style="display:flex;justify-content:center">
                                <img src="/assets/images/logo/logo_tezlik1.png" height="42" alt="logo">
                            </div>
                            <h5 class="mt-4 font-weight-600">¡Obtén un código para acceder!</h5>
                            <p class="text-muted mb-4">Este paso adicional nos ayuda a confirmar y proteger su cuenta.</p>
                            <form id="loginForm" name="loginForm" novalidate>
                                <div class="form-group floating-label">
                                    <input type="text" class="form-control" name="validation-password" id="factor" />
                                    <label for="factor">Código</label>

                                    <p class="text-muted mb-4 mt-3">Se ha enviado un mensaje con un código de verificación a su email.</p>
                                    <div class="validation-error d-none font-size-13">
                                        <p>Campo requerido</p>
                                    </div>
                                </div>

                                <div class="form-group text-center" style="display:flex;justify-content:center">
                                    <button class="btn btn-primary btn-block" data-effect="wave" type="submit" style="width:100px" id="btnCheckCode">Enviar</button>
                                </div>

                                <div class="form-group">
                                    <a href="javascript:;" class="text-primary">Reenviar código</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->
    </div>
    <!-- end container -->
    </div>
    <script src="../../js/login/autentication.js"></script>
    <!-- Page End -->
    <!-- ================== BEGIN BASE JS ================== -->
    <script src="/assets/js/vendor.min.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <!-- ================== END BASE JS ================== -->

    <!-- ================== BEGIN PAGE LEVEL JS ================== -->
    <script src="/assets/js/utils/colors.js"></script>
    <script src="/assets/libs/jquery-validation/js/jquery.validate.min.js"></script>
    <script src="/assets/libs/jquery-validation/js/additional-methods.min.js"></script>
    <!-- ================== END PAGE LEVEL JS ================== -->
    <!-- ================== BEGIN PAGE JS ================== -->
    <script src="/assets/js/app.js"></script>
    <!-- ================== END PAGE JS ================== -->
    <script>
        //Initialize form
        $('#loginForm').validate({
            focusInvalid: false,
            rules: {
                'validation-email': {
                    required: true,
                    email: true
                },
                'validation-password': {
                    required: true,
                }
            },
            errorPlacement: function errorPlacement(error, element) {
                $(element).siblings(".validation-error").removeClass("d-none")
                return true
            },
            highlight: function(element) {
                var $el = $(element);
                var $parent = $el.parents('.form-group');
                $parent.addClass("invalid-field")
            },
            unhighlight: function(element) {
                var $el = $(element);
                var $parent = $el.parents('.form-group');
                $parent.removeClass("invalid-field");
                $(element).siblings(".validation-error").addClass("d-none")
            },
            submitHandler: function(form) {
                var formdata = $(form).serializeArray();
                var data = {};
                $(formdata).each(function(index, obj) {
                    data[obj.name] = obj.value;
                });
                alert("Data has been submitted. Please see console log");
                console.log("form data ===>", data);
                $(form).trigger('reset')
                $(".floating-label").removeClass("enable-floating-label");
            }
        });
    </script>
</body>

</html>