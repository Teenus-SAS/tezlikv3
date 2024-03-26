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
                                            <label><b>Asignar rol</b></label>
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
                                            <label><b>Asignar accesos</b></label>
                                        </div>

                                        <div class="row ml-2 mb-2 cardAccessCost">
                                            <div class="col-12 col-lg-12">
                                                <label><b>Costos.</b></label><br>
                                                <label><b>Menú Configuración:</b></label>
                                            </div>

                                            <div class="col-sm-3 pb-2">
                                                Básico
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
                                                    <input id="checkbox-7" name="factoryLoad" type="checkbox">
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
                                                    <input id="checkbox-9" name="payroll" type="checkbox">
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
                                                <label><b>Menú Cotización:</b></label>
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
                                                <label><b>Menú Navegación:</b></label>
                                            </div>

                                            <div class="col-sm-3 pb-2">Precios
                                                <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-16" name="prices" type="checkbox">
                                                    <label for="checkbox-16">Precios COP</label>
                                                </div>
                                                <div class="mb-2 checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-17" name="pricesUSD" type="checkbox">
                                                    <label for="checkbox-17">Precios USD</label>
                                                </div>
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

                                        <div class="col-12 col-lg-12 titlePayroll separator">
                                            <label></label>
                                        </div>

                                        <div class="row ml-2 mb-2 cardAccessPlanning">
                                            <div class="col-12 col-lg-12">
                                                <label><b>Planeación.</b></label><br>
                                                <label><b>Menú Configuración:</b></label>
                                            </div>

                                            <div class="col-sm-3 pb-2">Básico
                                                <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-23" name="molds" type="checkbox">
                                                    <label for="checkbox-23">Moldes</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-24" name="planningCreateProduct" type="checkbox">
                                                    <label for="checkbox-24">Productos</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-25" name="planningCreateMaterials" type="checkbox">
                                                    <label for="checkbox-25">Materiales</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-26" name="planningCreateMachines" type="checkbox">
                                                    <label for="checkbox-26">Máquinas</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-27" name="planningCreateProcess" type="checkbox">
                                                    <label for="checkbox-27">Procesos</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-3 pb-2">
                                                Configuración
                                                <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-28" name="productMaterials" type="checkbox">
                                                    <label for="checkbox-28">Ficha Técnica Materiales</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-29" name="productProcess" type="checkbox">
                                                    <label for="checkbox-29">Ficha Técnica Procesos</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-30" name="planningMachine" type="checkbox">
                                                    <label for="checkbox-30">Programación Maquina</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-31" name="planCiclesMachine" type="checkbox">
                                                    <label for="checkbox-31">Plan Ciclos Maquina</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-3 pb-2">
                                                Configuración General
                                                <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-32" name="invCategories" type="checkbox">
                                                    <label for="checkbox-32">Categorias</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-33" name="sales" type="checkbox">
                                                    <label for="checkbox-33">Ventas</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-2 pb-3">
                                                Administracion
                                                <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-34" name="user" type="checkbox">
                                                    <label for="checkbox-34">Usuarios</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-35" name="sales" type="checkbox">
                                                    <label for="checkbox-35">Clientes</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-36" name="orderTypes" type="checkbox">
                                                    <label for="checkbox-36">Tipos Pedidos</label>
                                                </div>
                                            </div>

                                            <div class="col-12 col-lg-12">
                                                <label><b>Menú Navegación:</b></label>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-37" name="inventories" type="checkbox">
                                                    <label for="checkbox-37">Inventarios</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-38" name="orders" type="checkbox">
                                                    <label for="checkbox-38">Pedidos</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-39" name="programming" type="checkbox">
                                                    <label for="checkbox-39">Programación</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-40" name="loads" type="checkbox">
                                                    <label for="checkbox-40">Cargues</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-41" name="explosionOfMaterials" type="checkbox">
                                                    <label for="checkbox-41">Explosión de Materiales</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-42" name="offices" type="checkbox">
                                                    <label for="checkbox-42">Despachos</label>
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
</div>