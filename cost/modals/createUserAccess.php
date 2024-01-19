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
                                                    <label for="checkbox-5">Ficha Técnica Productos</label>
                                                </div>
                                                <!-- <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-6" name="costProductProcess" type="checkbox">
                                                    <label for="checkbox-6">Ficha Técnica Procesos</label>
                                                </div> -->
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-6" name="payrollLoad" type="checkbox">
                                                    <label for="checkbox-6">Carga Fabril</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-7" name="externalService" type="checkbox">
                                                    <label for="checkbox-7">Servicios Externos</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-3 pb-2">
                                                Configuración General
                                                <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                    <input class="typeCheckbox" id="checkbox-8" name="factoryLoad" type="checkbox">
                                                    <label for="checkbox-8">Cargar Nómina</label>
                                                </div>
                                                <div class="form-group floating-label enable-floating-label show-label my-2 cardTypePayroll" style="width: 150px">
                                                    <select class="form-control" name="typePayroll" id="typePayroll">
                                                        <option value="0" selected disabled>Seleccionar</option>
                                                        <option value="1">TODO</option>
                                                        <option value="2">PROCESOS</option>
                                                    </select>

                                                    <label for="typePayroll">Tipo de Nomina<span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input class="typeCheckbox" id="checkbox-9" name="expense" type="checkbox">
                                                    <label for="checkbox-9">Gastos</label>
                                                </div>
                                                <!-- <div class="checkbox checkbox-success checkbox-circle">
                                                    <input class="typeCheckbox" id="checkbox-11" name="expenseDistribution" type="checkbox">
                                                    <label for="checkbox-11">Distribuir Gastos</label>
                                                </div> -->
                                                <div class="form-group floating-label enable-floating-label show-label my-2 cardTypeExpenses" style="width: 150px">
                                                    <select class="form-control" name="typeExpenses" id="typeExpenses">
                                                        <option selected disabled>Seleccionar</option>
                                                        <option value="0">Desactivado</option>
                                                        <option value="1">Activado</option>
                                                    </select>
                                                    <label for="typePayroll">Metodo<span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2 pb-3">
                                                Creación Usuarios
                                                <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-10" name="costUser" type="checkbox">
                                                    <label for="checkbox-10">Usuarios</label>
                                                </div>
                                                <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-11" name="costBackup" type="checkbox">
                                                    <label for="checkbox-11">Backup</label>
                                                </div>
                                            </div>

                                            <div class="col-12 col-lg-12">
                                                <label for=""><b>Menú Cotización:</b></label>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-12" name="quotePaymentMethod" type="checkbox">
                                                    <label for="checkbox-12">Metodos De Pago</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-13" name="quoteCompany" type="checkbox">
                                                    <label for="checkbox-13">Compañias</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 pb-3">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-14" name="quoteContact" type="checkbox">
                                                    <label for="checkbox-14">Contactos</label>
                                                </div>
                                            </div>

                                            <div class="col-12 col-lg-12">
                                                <label for=""><b>Menú Navegación:</b></label>
                                            </div>

                                            <div class="col-sm-3 pb-2">Precios
                                                <?php if ($_SESSION['plan_cost_price'] == 1) { ?>
                                                    <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-15" name="prices" type="checkbox">
                                                        <label for="checkbox-15">Precios COP</label>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($_SESSION['plan_cost_price_usd'] == 1) { ?>
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-16" name="pricesUSD" type="checkbox">
                                                        <label for="checkbox-16">Precios USD</label>
                                                    </div>
                                                <?php } ?>

                                                <?php if ($_SESSION['plan_custom_price'] == 1) { ?>
                                                    <div class="mb-2 checkbox checkbox-success checkbox-circle">
                                                        <input class="typeCheckbox" id="checkbox-17" name="customPrices" type="checkbox">
                                                        <label for="checkbox-17">Precios Personalizados</label>
                                                    </div>
                                                <?php } ?>
                                                <li class="col-sm-8 pb-2 cardTypePrices pricesList">

                                                </li>
                                            </div>



                                            <div class="col-sm-3">Herramientas

                                                <?php if ($_SESSION['plan_cost_analysis_material'] == 1) { ?>
                                                    <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-18" name="analysisRawMaterials" type="checkbox">
                                                        <label for="checkbox-18">Analisis Materia Prima</label>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($_SESSION['plan_cost_economy_sale'] == 1) { ?>
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-19" name="economyScale" type="checkbox">
                                                        <label for="checkbox-19">Economia De Escala</label>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($_SESSION['plan_cost_multiproduct'] == 1) { ?>
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-20" name="multiproduct" type="checkbox">
                                                        <label for="checkbox-20">Pto De Equilibrio Multiproducto</label>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($_SESSION['plan_cost_simulator'] == 1) { ?>
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-21" name="simulator" type="checkbox">
                                                        <label for="checkbox-21">Simulador</label>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($_SESSION['plan_cost_historical'] == 1) { ?>
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-22" name="historical" type="checkbox">
                                                        <label for="checkbox-22">Historico</label>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <?php if ($_SESSION['plan_cost_quote'] == 1) { ?>
                                                <div class="mt-4 col-sm-3">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-23" name="quotes" type="checkbox">
                                                        <label for="checkbox-23">Cotización</label>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <?php if ($_SESSION['plan_cost_support'] == 1) { ?>
                                                <div class="mt-4 col-sm-3">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-24" name="tools" type="checkbox">
                                                        <label for="checkbox-24">Soporte</label>
                                                    </div>
                                                </div>
                                            <?php } ?>
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