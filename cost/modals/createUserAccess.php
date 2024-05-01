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
                                            <label><b>Usuario</b></label>
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
                                            <label><b>Accesos De Usuario</b></label>
                                        </div>

                                        <div class="row ml-2">
                                            <div class="col-12 col-lg-12">
                                                <label><b>Menú Configuración:</b></label>
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
                                                <!-- <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-7" name="externalService" type="checkbox">
                                                    <label for="checkbox-7">Servicios Externos</label>
                                                </div> -->
                                            </div>
                                            <div class="col-sm-3 pb-2">
                                                Configuración General
                                                <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                    <input class="typeCheckbox" id="checkbox-7" name="factoryLoad" type="checkbox">
                                                    <label for="checkbox-7">Cargar Nómina</label>
                                                </div>
                                                <div class="form-group floating-label enable-floating-label show-label my-2 cardTypePayroll" style="width: 150px">
                                                    <select class="form-control" name="typePayroll" id="typePayroll">
                                                        <option value="0" selected disabled>Seleccionar</option>
                                                        <option value="1">TODO</option>
                                                        <option value="2">PROCESOS</option>
                                                    </select>

                                                    <label>Tipo de Nomina<span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>


                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input class="typeCheckbox" id="chckExpenses" name="expense" type="checkbox">
                                                    <label for="chckExpenses">Gastos</label>
                                                </div>

                                                <div class="form-group floating-label enable-floating-label show-label my-2 cardChkExpenses" style="width: 150px; display: none;">
                                                    <select class="form-control" name="selectExpenses" id="selectExpenses">
                                                        <option selected disabled>Seleccionar</option>
                                                        <option value="0">TODO</option>
                                                        <option value="1">ASIGNACIÓN</option>
                                                        <?php if ($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) { ?>
                                                            <option value="2">DISTRIBUCIÓN</option>
                                                        <?php } else { ?>
                                                            <option value="2">RECUPERACIÓN</option>
                                                        <?php } ?>
                                                        <?php if ($_SESSION['flag_production_center'] == 1) { ?>
                                                            <option value="3">C. PRODUCCIÓN</option>
                                                        <?php } ?>
                                                    </select>
                                                    <label>Tipo Gastos<span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                                <?php if ($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) { ?>
                                                    <div class="checkbox checkbox-success checkbox-circle cardChkExpenses cardTypeExpenses" style="display: none;">
                                                        <input id="typeExpenses" name="typeExpenses" type="checkbox">
                                                        <label for="typeExpenses">Metodo Distribución</label>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <div class="col-sm-2 pb-3">
                                                Creación Usuarios
                                                <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-8" name="costUser" type="checkbox">
                                                    <label for="checkbox-8">Usuarios</label>
                                                </div>
                                                <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-9" name="costBackup" type="checkbox">
                                                    <label for="checkbox-9">Backup</label>
                                                </div>
                                            </div>

                                            <div class="col-12 col-lg-12">
                                                <label><b>Menú Cotización:</b></label>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-10" name="quotePaymentMethod" type="checkbox">
                                                    <label for="checkbox-10">Metodos De Pago</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-11" name="quoteCompany" type="checkbox">
                                                    <label for="checkbox-11">Compañias</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 pb-3">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-12" name="quoteContact" type="checkbox">
                                                    <label for="checkbox-12">Contactos</label>
                                                </div>
                                            </div>

                                            <div class="col-12 col-lg-12">
                                                <label><b>Menú Navegación:</b></label>
                                            </div>

                                            <div class="col-sm-3 pb-2">Precios
                                                <?php if ($_SESSION['plan_cost_price'] == 1) { ?>
                                                    <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-13" name="prices" type="checkbox">
                                                        <label for="checkbox-13">Precios COP</label>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($_SESSION['plan_cost_price_usd'] == 1) { ?>
                                                    <!-- <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-14" name="pricesUSD" type="checkbox">
                                                        <label for="checkbox-14">Precios USD</label>
                                                    </div> -->
                                                <?php } ?>

                                                <?php if ($_SESSION['plan_custom_price'] == 1) { ?>
                                                    <div class="mb-2 checkbox checkbox-success checkbox-circle">
                                                        <input class="typeCheckbox" id="checkbox-14" name="customPrices" type="checkbox">
                                                        <label for="checkbox-14">Precios Personalizados</label>
                                                    </div>
                                                <?php } ?>
                                                <li class="col-sm-8 pb-2 cardTypePrices pricesList">
                                                </li>
                                            </div>

                                            <div class="col-sm-3">Herramientas
                                                <?php if ($_SESSION['plan_cost_analysis_material'] == 1) { ?>
                                                    <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-15" name="analysisRawMaterials" type="checkbox">
                                                        <label for="checkbox-15">Analisis Materia Prima</label>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($_SESSION['plan_cost_economy_sale'] == 1) { ?>
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-16" name="economyScale" type="checkbox">
                                                        <label for="checkbox-16">Economia De Escala</label>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($_SESSION['plan_cost_multiproduct'] == 1) { ?>
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-17" name="multiproduct" type="checkbox">
                                                        <label for="checkbox-17">Pto De Equilibrio Multiproducto</label>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($_SESSION['plan_cost_simulator'] == 1) { ?>
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-18" name="simulator" type="checkbox">
                                                        <label for="checkbox-18">Simulador</label>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($_SESSION['plan_cost_historical'] == 1) { ?>
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-19" name="historical" type="checkbox">
                                                        <label for="checkbox-19">Historico</label>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <?php if ($_SESSION['plan_cost_quote'] == 1) { ?>
                                                <div class="mt-4 col-sm-3">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-20" name="quotes" type="checkbox">
                                                        <label for="checkbox-20">Cotización</label>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <?php if ($_SESSION['plan_cost_support'] == 1) { ?>
                                                <div class="mt-4 col-sm-3">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-21" name="tools" type="checkbox">
                                                        <label for="checkbox-21">Soporte</label>
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