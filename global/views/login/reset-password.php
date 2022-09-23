<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="bg-white text-left p-5 mt-3 center col-md-6">
        <div class="clearfix" style="text-align: center;">
            <img src="/assets/images/logo/logo_tezlik1.png" height="55" class="" alt="logo tezlik">
        </div>
        <h5 class="mt-4">Cambio de Contraseña</h5>
        <!-- <p class="text-muted mb-4">Login</p> -->
        <form id="frmChangePasword" name="frmChangePasword" novalidate>
            <div class="form-group floating-label">
                <input type="password" class="form-control" name="inputNewPass" id="inputNewPass" />
                <label for="inputNewPass">Nueva Contraseña</label>
                <div class="validation-error d-none font-size-13">
                    <p>Este campo es requerido</p>
                </div>
            </div>
            <div class="form-group floating-label">
                <input type="password" class="form-control" name="inputNewPass1" id="inputNewPass1" />
                <label for="inputNewPass1">Confirmar Contraseña</label>
                <div class="validation-error d-none font-size-13">
                    <p>Este campo es requerido</p>
                </div>
            </div>
            <div class="form-group text-center">
                <button class="btn btn-primary btn-block" data-effect="wave" type="submit" id="btnChangePass">Cambiar Contraseña
                </button>
            </div>
            <div class="clearfix text-center">
                <a href="javascript:history.go(0);" class="text-primary">Volver al login</a>
            </div>
        </form>
    </div>
</div>

<script src="/global/js/login/reset-password.js"></script>