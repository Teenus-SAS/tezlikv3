<div class="modal fade" id="createPlansAccess" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Crear Accesos De Planes</h5>
            </div>
            <div class="modal-body">
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <div class="vertical-app-tabs" id="rootwizard">
                            <div class="col-md-12 col-lg-12 InputGroup">
                                <form id="formCreatePlan">
                                    <div class="row mt-5">
                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label><b>Plan</b></label>
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
                                            <label><b>Asignar accesos</b></label>
                                        </div>

                                        <div class="container" style="margin-bottom: 40px;">
                                            <div class="col-12 col-lg-12 mb-2">
                                                <label><b>Costos.</b></label><br>
                                                <label><b>Menú Navegación:</b></label>
                                            </div>

                                            <div class="row ml-2">
                                                <div class="col-sm-4">Lista de Precios
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-1" name="prices" type="checkbox">
                                                        <label for="checkbox-1">Precios COP (Detalle * Producto)</label>
                                                    </div>
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-2" name="customPrices" type="checkbox">
                                                        <label for="checkbox-2">Precios Personalizados</label>
                                                    </div>
                                                </div>

                                                <div class="col-sm-4">Herramientas
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-3" name="analysisRawMaterials" type="checkbox">
                                                        <label for="checkbox-3">Analisis Materia Prima</label>
                                                    </div>
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-4" name="economyScale" type="checkbox">
                                                        <label for="checkbox-4">Economias de Escala</label>
                                                    </div>
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-5" name="salesObjective" type="checkbox">
                                                        <label for="checkbox-5">Objetivos De Ventas</label>
                                                    </div>
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-6" name="priceObjective" type="checkbox">
                                                        <label for="checkbox-6">Objetivos De Precio</label>
                                                    </div>
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-7" name="multiproduct" type="checkbox">
                                                        <label for="checkbox-7">Pto De Equilibrio Multiproducto</label>
                                                    </div>
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-8" name="simulator" type="checkbox">
                                                        <label for="checkbox-8">Simulacion</label>
                                                    </div>
                                                </div>

                                                <div class="col-sm-4">
                                                    <div class="mt-4 checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-9" name="quotes" type="checkbox">
                                                        <label for="checkbox-9">Cotizaciones</label>
                                                    </div>
                                                    <div class="checkbox checkbox-success checkbox-circle">
                                                        <input id="checkbox-10" name="support" type="checkbox">
                                                        <label for="checkbox-10">Soporte</label>
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