<div class="modal fade" id="createPlanMachine" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Crear Plan Maquina</h5>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
            </div>
            <div class="modal-body">
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <div class="vertical-app-tabs" id="rootwizard">
                            <div class="col-md-12 col-lg-12 InputGroup">
                                <form id="formCreatePlanMachine">
                                    <div class="row mt-5">
                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Descripción</b></label>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <select class="form-control" name="idMachine" id="idMachine"></select>
                                                <label for="idMachine">Maquina<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <!-- <select class="form-control" name="idProcess" id="idProcess"></select> -->
                                                <input id="numberWorkers" name="numberWorkers" type="text" class="form-control number">
                                                <label for="numberWorkers">No Trabajadores<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Horario</b></label>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="hoursDay" name="hoursDay" type="number" class="form-control text-center" min="1" max="24">
                                                <label for="hoursDay">Horas x Dia<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="input-group floating-label enable-floating-label show-label date" id="hourStartPicker" data-target-input="nearest">
                                                <input id="hourStart" name="hourStart" type="text" class="form-control text-center datetimepicker-input" data-target="#hourStartPicker">
                                                <label for="hourStart">Horas Inicio<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                                <div class="input-group-append" data-target="#hourStartPicker" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="input-group floating-label enable-floating-label show-label date" id="hourEndPicker" data-target-input="nearest">
                                                <input id="hourEnd" name="hourEnd" type="text" class="form-control text-center datetimepicker-input" data-target="#hourEndPicker">
                                                <label for="hourEnd">Horas Fin<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                                <div class="input-group-append" data-target="#hourEndPicker" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Meses</b></label>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="january" name="january" type="number" class="form-control text-center month" min="1" max="31">
                                                <label for="january">Enero<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="february" name="february" type="number" class="form-control text-center month" min="1" max="28">
                                                <label for="february">Febrero<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="march" name="march" type="number" class="form-control text-center month" min="1" max="31">
                                                <label for="march">Marzo<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="april" name="april" type="number" class="form-control text-center month" min="1" max="30">
                                                <label for="april">Abril<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="may" name="may" type="number" class="form-control text-center month" min="1" max="31">
                                                <label for="may">Mayo<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="june" name="june" type="number" class="form-control text-center month" min="1" max="30">
                                                <label for="june">Junio<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="july" name="july" type="number" class="form-control text-center month" min="1" max="31">
                                                <label for="july">Julio<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="august" name="august" type="number" class="form-control text-center month" min="1" max="31">
                                                <label for="august">Agosto<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="september" name="september" type="number" class="form-control text-center month" min="1" max="30">
                                                <label for="september">Septiembre<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="october" name="october" type="number" class="form-control text-center month" min="1" max="31">
                                                <label for="october">Octubre<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="november" name="november" type="number" class="form-control text-center month" min="1" max="30">
                                                <label for="november">Noviembre<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="december" name="december" type="number" class="form-control text-center month" min="1" max="31">
                                                <label for="december">Diciembre<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <hr>
                                    </div>
                                    <!-- This button link with id-sw-default-step-1 if you change it change in serial number like below -->
                                    <div class="d-none">
                                        <button class="btn btn-primary" id="btn">submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnClosePlanMachine">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnCreatePlanMachine">Crear</button>
            </div>
        </div>
    </div>
</div>