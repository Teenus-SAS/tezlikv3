<div class="modal fade" id="createUserAccess" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Crear Accesos De Usuario</h5>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
            </div>
            <div class="modal-body">
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <div class="vertical-app-tabs" id="rootwizard">
                            <div class="col-md-12 col-lg-12 InputGroup">
                                <form id="formCreateUser">
                                    <div class="row mt-5">
                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Usuario</b></label>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input type="text" class="form-control" id="nameUser" name="names">
                                                <label for="nameUser">Nombres<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input type="text" class="form-control" id="lastnameUser" name="lastnames">
                                                <label for="lastnameUser">Apellidos<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input type="text" class="form-control" id="emailUser" name="email">
                                                <label for="emailUser">Email<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Accesos De Usuario</b></label>
                                        </div>
                                        <div class="col-12 col-lg-12">
                                            <label for=""><b>Menú Configuración:</b></label>
                                        </div>

                                        <div class="row mb-4" style="margin-left:11px">
                                            <div class="col mb-3">Creación De Usuarios</div>
                                            <div class="w-100"></div>
                                            <div class="col">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-1" name="user" type="checkbox">
                                                    <label for="checkbox-1">Usuarios</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-12">
                                            <label for=""><b>Menú Navegación:</b></label>
                                        </div>
                                        <div class="container">
                                            <div class="row" style="margin:auto;">
                                                <div class="col-4">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-2" name="prices" type="checkbox">
                                                        <label for="checkbox-2">Empresas</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-3" name="analysisRawMaterials" type="checkbox">
                                                        <label for="checkbox-3">Sesiones Activas De Usuario</label>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-4" name="tools" type="checkbox">
                                                        <label for="checkbox-4">Cuentas</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseUser">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnCreateUserAndAccess">Crear</button>
            </div>
        </div>
    </div>
</div>