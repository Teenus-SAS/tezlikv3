<div class="modal fade" id="modalHistorical" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Historico</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="page-content-wrapper">
                    <div class="container-fluid">
                        <div class="vertical-app-tabs" id="rootwizard">
                            <div class="col-md-12 col-lg-12 InputGroup">
                                <div class="row mt-5">
                                    <div class="col-12 col-lg-12">
                                        <label><b>Ingrese Mes y Año</b></label>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <div class="form-group floating-label enable-floating-label show-label">
                                            <input type="month" name="datepicker" id="datepicker" class="form-control">
                                            <label for="datepicker"><span class="text-danger">*</span></label>
                                            <div class="validation-error d-none font-size-13">Requerido</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseHistorical">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSaveManualHistorical">Guardar</button>
            </div>
        </div>
    </div>
</div>