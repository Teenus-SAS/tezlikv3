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
                                            <label for=""><b>Asignar rol</b></label>
                                        </div>
                                        <div class="col-12 col-lg-6 mb-4">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input switch" id="switchCost">
                                                <label class="custom-control-label" for="switchCost">Costos</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-6 mb-4">
                                            <div class="custom-control custom-switch">
                                                <input class="custom-control-input switch" type="checkbox" id="switchPlanning">
                                                <label class="custom-control-label" for="switchPlanning">Planeación</label>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Asignar accesos</b></label>
                                        </div>

                                        <div class="cardAccessCost" style="margin-bottom: 40px;">
                                            <div class="col-12 col-lg-12">
                                                <label for=""><b>Costos.</b></label><br>
                                                <label for=""><b>Menú Configuración:</b></label>
                                            </div>

                                            <div class="row mb-4" style="margin:auto;">
                                                <div class="col-lg-3 mb-3">Básico</div>
                                                <div class="col col-lg-3">
                                                    Configuración
                                                </div>
                                                <div class="col col-lg-3">
                                                    Configuración General
                                                </div>
                                                <div class="col">
                                                    Creación Usuarios
                                                </div>
                                                <div class="w-100"></div>

                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-1" name="createProduct" type="checkbox">
                                                        <label for="checkbox-1">Crear Productos</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-5" name="costProductMaterials" type="checkbox">
                                                        <label for="checkbox-5">Ficha Técnica Materiales</label>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-9" name="factoryLoad" type="checkbox">
                                                        <label for="checkbox-9">Cargar Nómina</label>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-12" name="costUser" type="checkbox">
                                                        <label for="checkbox-12">Usuarios</label>
                                                    </div>
                                                </div>
                                                <div class="w-100"></div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-2" name="costCreateMaterials" type="checkbox">
                                                        <label for="checkbox-2">Crear Materiales</label>
                                                    </div>
                                                </div>
                                                <div class="col col-lg-4">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-6" name="costProductProcess" type="checkbox">
                                                        <label for="checkbox-6">Ficha Técnica Procesos</label>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-10" name="expense" type="checkbox">
                                                        <label for="checkbox-10">Asignar Gastos</label>
                                                    </div>
                                                </div>
                                                <div class="col"></div>
                                                <div class="w-100"></div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-3" name="costCreateMachines" type="checkbox">
                                                        <label for="checkbox-3">Crear Máquinas</label>
                                                    </div>
                                                </div>
                                                <div class="col col-lg-4">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-7" name="payrollLoad" type="checkbox">
                                                        <label for="checkbox-7">Carga Fabril</label>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-11" name="expenseDistribution" type="checkbox">
                                                        <label for="checkbox-11">Distribuir Gastos</label>
                                                    </div>
                                                </div>
                                                <div class="col"></div>
                                                <div class="w-100"></div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-4" name="costCreateProcess" type="checkbox">
                                                        <label for="checkbox-4">Crear Procesos</label>
                                                    </div>
                                                </div>
                                                <div class="col col-lg-4">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-8" name="externalService" type="checkbox">
                                                        <label for="checkbox-8">Servicios Externos</label>
                                                    </div>
                                                </div>
                                                <div class="col"></div>
                                                <div class="col"></div>
                                            </div>

                                            <div class="col-12 col-lg-12">
                                                <label for=""><b>Menú Navegación:</b></label>
                                            </div>
                                            <div class="container">
                                                <div class="row" style="margin:auto;">
                                                    <!-- <div class="col">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-1" name="molds" type="checkbox">
                                                    <label for="checkbox-1"></label>
                                                </div>
                                            </div> -->
                                                    <div class="col">
                                                        <div class="checkbox checkbox-success checkbox-circle">
                                                            <input id="checkbox-13" name="prices" type="checkbox">
                                                            <label for="checkbox-13">Generar Precios</label>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="checkbox checkbox-success checkbox-circle">
                                                            <input id="checkbox-14" name="analysisRawMaterials" type="checkbox">
                                                            <label for="checkbox-14">Analisis Materia Prima</label>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="checkbox checkbox-success checkbox-circle">
                                                            <input id="checkbox-15" name="tools" type="checkbox">
                                                            <label for="checkbox-15">Herramientas</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-12 titlePayroll separator">
                                            <label for=""></label>
                                        </div>

                                        <div class="cardAccessPlanning" style="margin-bottom: 40px;">
                                            <div class="col-12 col-lg-12">
                                                <label for=""><b>Planeación.</b></label><br>
                                                <label for=""><b>Menú Configuración:</b></label>
                                            </div>

                                            <div class="row mb-4" style="margin:auto;">
                                                <div class="col-lg-3 mb-3">Básico</div>
                                                <div class="col col-lg-3">
                                                    Configuración
                                                </div>
                                                <div class="col col-lg-3">
                                                    Configuración General
                                                </div>
                                                <div class="col">
                                                    Creación Usuarios
                                                </div>
                                                <div class="w-100"></div>

                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-16" name="molds" type="checkbox">
                                                        <label for="checkbox-16">Moldes</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-21" name="productMaterials" type="checkbox">
                                                        <label for="checkbox-21">Ficha Técnica Materiales</label>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-25" name="invCategories" type="checkbox">
                                                        <label for="checkbox-25">Categorias</label>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-27" name="user" type="checkbox">
                                                        <label for="checkbox-27">Usuarios</label>
                                                    </div>
                                                </div>
                                                <div class="w-100"></div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-17" name="planningCreateProduct" type="checkbox">
                                                        <label for="checkbox-17">Productos</label>
                                                    </div>
                                                </div>
                                                <div class="col col-lg-4">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-22" name="productProcess" type="checkbox">
                                                        <label for="checkbox-22">Ficha Técnica Procesos</label>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-26" name="sales" type="checkbox">
                                                        <label for="checkbox-26">Ventas</label>
                                                    </div>
                                                </div>
                                                <div class="col"></div>
                                                <div class="w-100"></div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-18" name="planningCreateMaterials" type="checkbox">
                                                        <label for="checkbox-18">Materiales</label>
                                                    </div>
                                                </div>
                                                <div class="col col-lg-4">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-23" name="planningMachine" type="checkbox">
                                                        <label for="checkbox-23">Programación Maquina</label>
                                                    </div>
                                                </div>
                                                <div class="col"></div>
                                                <div class="col"></div>
                                                <div class="w-100"></div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-19" name="planningCreateMachines" type="checkbox">
                                                        <label for="checkbox-19">Máquinas</label>
                                                    </div>
                                                </div>
                                                <div class="col col-lg-4">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-24" name="planCiclesMachine" type="checkbox">
                                                        <label for="checkbox-24">Plan Ciclos Maquina</label>
                                                    </div>
                                                </div>
                                                <div class="col"></div>
                                                <div class="col"></div>
                                                <div class="w-100"></div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-20" name="planningCreateProcess" type="checkbox">
                                                        <label for="checkbox-20">Procesos</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-lg-12">
                                                <label for=""><b>Menú Navegación:</b></label>
                                            </div>
                                            <div class="row" style="margin:auto;">
                                                <!-- <div class="col">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-1" name="molds" type="checkbox">
                                                    <label for="checkbox-1"></label>
                                                </div>
                                            </div> -->
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-28" name="inventories" type="checkbox">
                                                        <label for="checkbox-28">Inventarios</label>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-29" name="orders" type="checkbox">
                                                        <label for="checkbox-29">Pedidos</label>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-30" name="programming" type="checkbox">
                                                        <label for="checkbox-30">Programación</label>
                                                    </div>
                                                </div>
                                                <div class="w-100"></div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-31" name="loads" type="checkbox">
                                                        <label for="checkbox-31">Cargues</label>
                                                    </div>
                                                </div>
                                                <div class="col col-lg-4">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-32" name="explosionOfMaterials" type="checkbox">
                                                        <label for="checkbox-32">Explosión de Materiales</label>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-33" name="offices" type="checkbox">
                                                        <label for="checkbox-33">Despachos</label>
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