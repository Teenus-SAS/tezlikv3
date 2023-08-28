<div class="modal fade" id="modalChangeCompany" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Cambiar Compañia Usuario</h5>
            </div>
            <div class="modal-body">
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <div class="vertical-app-tabs" id="rootwizard">
                            <div class="col-md-12 col-lg-12 InputGroup">
                                <form id="formChangeCompany">
                                    <div class="row mt-5">
                                        <div class="col-12 col-lg-12 titlePayroll pt-2">
                                            <label for=""><b>Usuario</b></label>
                                        </div>
                                        <div class="col-12 col-lg-12">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <select name="idUser" class="form-control" id="user" disabled>
                                                    <option value="1">sergio.velandia@gmail.com</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-12 titlePayroll pt-2">
                                            <label for=""><b>Empresa</b></label>
                                        </div>
                                        <div class="col-12 col-lg-12">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <select name="idCompany" class="form-control company" id="company"></select>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseChangeCompany">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnChangeCompany">Cambiar</button>
            </div>
        </div>
    </div>
</div>