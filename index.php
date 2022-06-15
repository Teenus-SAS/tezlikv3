<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="description" content="LetStart Admin is a full featured, multipurpose, premium bootstrap admin template built with Bootstrap 4 Framework, HTML5, CSS and JQuery.">
  <meta name="keywords" content="admin, panels, dashboard, admin panel, multipurpose, bootstrap, bootstrap4, all type of dashboards">
  <meta name="author" content="MatrrDigital">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login | Tezlik</title>
  <link rel="shortcut icon" href="assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />

  <!-- ================== BEGIN PAGE LEVEL CSS START ================== -->
  <link rel="stylesheet" href="assets/css/icons.css" />
  <link rel="stylesheet" href="assets/libs/wave-effect/css/waves.min.css" />
  <link rel="stylesheet" href="assets/libs/owl-carousel/css/owl.carousel.min.css" />

  <!-- ================== Plugins CSS  ================== -->
  <link rel="stylesheet" href="assets/libs/owl-carousel/css/owl.carousel.min.css" />
  <link rel="stylesheet" href="assets/plugins/toast/toastr.min.css">

  <!-- ================== BEGIN APP CSS  ================== -->
  <link rel="stylesheet" href="assets/css/bootstrap.css" />
  <link rel="stylesheet" href="assets/css/styles.css" />

  <!-- ================== BEGIN POLYFILLS  ================== -->
  <!--[if lt IE 9]>
     <script src="assets/libs/html5shiv/js/html5shiv.js"></script>
     <script src="assets/libs/respondjs/js/respond.min.js"></script>
  <![endif]-->

  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet">
</head>

<body>
  <!-- Begin Page -->
  <div class="auth-pages full-auth-screen">
    <div class="container-fluid">
      <div class="backhome">
        <a href="https://teenus.com.co" target="_blank" class="avatar avatar-sm bg-primary text-white"><i class="bx bx-home-alt fs-sm"></i></a>
      </div>
      <div class="row">
        <div class="col-md-6 col-lg-7 col-xl-8 px-md-0">
          <div class="auth-page-sidebar">
            <div class="overlay"></div>
            <div class="auth-user-testimonial">
              <div class="owl-carousel">
                <div class="item">
                  <h3 class="text-white mb-1">Fije los precios de sus productos de forma muy sencilla!</h3>
                  <h5 class="text-white mb-3">"con sus costos y rentabilidad!"</h5>
                  <p>Tezlik Software</p>
                </div>
                <div class="item">
                  <h3 class="text-white mb-1">Modifique el costos de una materia prima y todos los productos asociados a ella se afectaran automaticamente!</h3>
                  <h5 class="text-white mb-3">"Fácil como nos gusta!"</h5>
                  <p>Tezlik Software</p>
                </div>
                <div class="item">
                  <h3 class="text-white mb-1">Analice facilmente las materias primas a la luz de sus costos para reducirlos!</h3>
                  <h5 class="text-white mb-3">"con un solo click!"</h5>
                  <p>Tezlik Software</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-5 col-xl-4 px-md-0 align-items-center">
          <div class="card mb-0 p-2 p-md-3 h-100">
            <div class="card-body">
              <div class="clearfix" style="text-align: center;">
                <img src="assets/images/logo/logo_tezlik1.png" height="55" class="" alt="logo tezlik">
              </div>
              <h5 class="mt-4 font-weight-600">Bienvenido!</h5>
              <!-- <p class="text-muted mb-4">Login</p> -->
              <form id="loginForm" name="loginForm" novalidate>
                <div class="form-group floating-label">
                  <input type="email" class="form-control" name="validation-email" id="email" />
                  <label for="email">Email</label>
                  <div class="validation-error d-none font-size-13">
                    <p>Ingrese una dirección de correo electronico valida</p>
                  </div>
                </div>
                <div class="form-group floating-label">
                  <input type="password" class="form-control" name="validation-password" id="password" />
                  <label for="password">Password</label>
                  <div class="validation-error d-none font-size-13">
                    <p>Este campo es requerido</p>
                  </div>
                </div>

                <div class="form-group">
                  <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="checkbox-signin" checked>
                    <label class="custom-control-label" for="checkbox-signin">Recordarme</label>
                  </div>
                </div>

                <div class="form-group text-center">
                  <button class="btn btn-primary btn-block" data-effect="wave" type="submit"> Ingresar
                  </button>
                </div>
                <div class="clearfix text-center">
                  <a href="#" class="text-primary">¿Olvido su password?</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- end row -->
    </div>
    <!-- end container -->
  </div>

  <!-- ================== BEGIN BASE JS ================== -->
  <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
  <script src="assets/js/vendor.min.js"></script>
  <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>

  <!-- ================== BEGIN PAGE LEVEL JS ================== -->
  <script src="assets/js/utils/colors.js"></script>
  <script src="assets/libs/owl-carousel/js/owl.carousel.min.js"></script>
  <script src="assets/libs/jquery-validation/js/jquery.validate.min.js"></script>
  <script src="assets/libs/jquery-validation/js/additional-methods.min.js"></script>

  <!-- ================== BEGIN PAGE JS ================== -->
  <script src="assets/js/app.js"></script>
  <script src="app/js/login/autentication.js"></script>
  <!-- <script src="app/js/login/code.js"></script> -->

  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>

</body>

</html>