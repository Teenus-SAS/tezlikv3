<div class="modal fade" id="modalFirstLogin" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Actualizar datos</h5>
                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> -->
            </div>
            <div class="modal-body">
                <form id="formFirstLogin">
                    <div class="row mt-5">
                        <div class="col-12 col-lg-12">
                            <div class="form-group">
                                <label for="firstname">Nombres<span class="text-danger">*</span></label>
                                <input id="firstname" name="firstname" type="text" class="form-control">
                                <div class="validation-error d-none font-size-13">Requerido</div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-12">
                            <div class="form-group">
                                <label for="lastname">Apellidos <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="lastname" id="lastname">
                                <div class="validation-error d-none font-size-13">Requerido</div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label for="telephone">Telefono <span class="text-danger">*</span></label>
                                <input class="form-control" type="number" name="telephone" id="telephone">
                                <div class="validation-error d-none font-size-13">Requerido</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                <button type="button" class="btn btn-primary" id="btnSaveFirstLogin">Guardar</button>
            </div>
        </div>
    </div>
</div>