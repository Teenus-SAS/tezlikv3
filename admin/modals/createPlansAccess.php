<div class="modal fade" id="createPlansAccess" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Crear Accesos De Planes</h5>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
            </div>
            <div class="modal-body">
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <div class="vertical-app-tabs" id="rootwizard">
                            <div class="col-md-12 col-lg-12 InputGroup">
                                <form id="formCreatePlan">
                                    <div class="row mt-5">
                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Plan</b></label>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <select name="idPlan" class="form-control" id="plan" disabled></select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-4 inputCantProducts">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input type="number" class="form-control text-center" id="cantProducts" name="cantProducts">
                                                <label for="cantProducts">Creación Productos<span class="text-danger">*</span></label>
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

                                        <div class="container cardAccessCost" style="margin-bottom: 40px;">
                                            <div class="col-12 col-lg-12">
                                                <label for=""><b>Costos.</b></label><br>
                                                <label for=""><b>Menú Navegación:</b></label>
                                            </div>
                                            <div class="row" style="margin:auto;">
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-1" name="prices" type="checkbox">
                                                        <label for="checkbox-1">Precios (Detalle * Producto)</label>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-2" name="analysisRawMaterials" type="checkbox">
                                                        <label for="checkbox-2">Analisis Materia Prima</label>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-3" name="tools" type="checkbox">
                                                        <label for="checkbox-3">Herramientas</label>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-12 titlePayroll separator">
                                            <label for=""></label>
                                        </div>

                                        <div class="container cardAccessPlanning" style="margin-bottom: 40px;">
                                            <div class="col-12 col-lg-12">
                                                <label for=""><b>Planeación.</b></label><br>
                                                <label for=""><b>Menú Navegación:</b></label>
                                            </div>
                                            <div class="row" style="margin:auto;">
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-4" name="inventories" type="checkbox">
                                                        <label for="checkbox-4">Inventarios</label>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-5" name="orders" type="checkbox">
                                                        <label for="checkbox-5">Pedidos</label>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-6" name="programming" type="checkbox">
                                                        <label for="checkbox-6">Programación</label>
                                                    </div>
                                                </div>
                                                <div class="w-100"></div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-7" name="loads" type="checkbox">
                                                        <label for="checkbox-7">Cargues</label>
                                                    </div>
                                                </div>
                                                <div class="col col-lg-4">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-8" name="explosionOfMaterials" type="checkbox">
                                                        <label for="checkbox-8">Explosión de Materiales</label>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-9" name="offices" type="checkbox">
                                                        <label for="checkbox-9">Despachos</label>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnClosePlan">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnCreatePlanAccess">Crear</button>
            </div>
        </div>
    </div>
</div>