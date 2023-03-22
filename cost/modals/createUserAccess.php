<div class="modal fade" id="createUserAccess" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Crear Accesos De Usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
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

                                        <div class="row ml-2">
                                            <div class="col-12 col-lg-12">
                                                <label for=""><b>Menú Configuración:</b></label>
                                            </div>

                                            <div class="col-sm-3 pb-2">Básico
                                                <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-1" name="createProduct" type="checkbox">
                                                    <label for="checkbox-1">Crear Productos</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-2" name="costCreateMaterials" type="checkbox">
                                                    <label for="checkbox-2">Crear Materiales</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-3" name="costCreateMachines" type="checkbox">
                                                    <label for="checkbox-3">Crear Máquinas</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-4" name="costCreateProcess" type="checkbox">
                                                    <label for="checkbox-4">Crear Procesos</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-3 pb-2">
                                                Configuración
                                                <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-5" name="costProductMaterials" type="checkbox">
                                                    <label for="checkbox-5">Ficha Técnica Materiales</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-6" name="costProductProcess" type="checkbox">
                                                    <label for="checkbox-6">Ficha Técnica Procesos</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-7" name="payrollLoad" type="checkbox">
                                                    <label for="checkbox-7">Carga Fabril</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-8" name="externalService" type="checkbox">
                                                    <label for="checkbox-8">Servicios Externos</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-3 pb-2">
                                                Configuración General
                                                <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-9" name="factoryLoad" type="checkbox">
                                                    <label for="checkbox-9">Cargar Nómina</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-10" name="expense" type="checkbox">
                                                    <label for="checkbox-10">Asignar Gastos</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-11" name="expenseDistribution" type="checkbox">
                                                    <label for="checkbox-11">Distribuir Gastos</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-2 pb-3">
                                                Creación Usuarios
                                                <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-12" name="costUser" type="checkbox">
                                                    <label for="checkbox-12">Usuarios</label>
                                                </div>
                                            </div>

                                            <div class="col-12 col-lg-12">
                                                <label for=""><b>Menú Cotización:</b></label>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-13" name="quotePaymentMethod" type="checkbox">
                                                    <label for="checkbox-13">Metodos De Pago</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-14" name="quoteCompany" type="checkbox">
                                                    <label for="checkbox-14">Compañias</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 pb-3">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-15" name="quoteContact" type="checkbox">
                                                    <label for="checkbox-15">Contactos</label>
                                                </div>
                                            </div>

                                            <div class="col-12 col-lg-12">
                                                <label for=""><b>Menú Navegación:</b></label>
                                            </div>

                                            <div class="col-sm-3 pb-2">Precios
                                                <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-16" name="prices" type="checkbox">
                                                    <label for="checkbox-16">Precios COP</label>
                                                </div>
                                                <?php if ($_SESSION['plan_cost_price_usd'] == 1) { ?>
                                                    <div class="mb-2 checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-17" name="pricesUSD" type="checkbox">
                                                        <label for="checkbox-17">Precios USD</label>
                                                    </div>
                                                <?php } ?>
                                            </div>



                                            <div class="col-sm-3">Herramientas
                                                <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-18" name="analysisRawMaterials" type="checkbox">
                                                    <label for="checkbox-18">Analisis Materia Prima</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-19" name="economyScale" type="checkbox">
                                                    <label for="checkbox-19">Economia De Escala</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-20" name="multiproduct" type="checkbox">
                                                    <label for="checkbox-20">Pto De Equilibrio Multiproducto</label>
                                                </div>
                                            </div>

                                            <div class="mt-4 col-sm-3">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-21" name="quotes" type="checkbox">
                                                    <label for="checkbox-21">Cotización</label>
                                                </div>
                                            </div>
                                            <div class="mt-4 col-sm-3">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-22" name="tools" type="checkbox">
                                                    <label for="checkbox-22">Soporte</label>
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