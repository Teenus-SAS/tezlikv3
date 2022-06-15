<div class="modal fade" id="createPayroll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Crear Nomina</h5>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
            </div>
            <div class="modal-body">
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <div class="vertical-app-tabs" id="rootwizard">
                            <div class="col-md-12 col-lg-12 InputGroup">
                                <form id="formCreatePayroll">
                                    <div class="row mt-5">
                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Descripción</b></label>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="employee" name="employee" type="text" class="form-control">
                                                <label for="employee">Nombres y Apellidos <span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <select class="form-control" name="idProcess" id="idProcess"></select>
                                                <!-- <input id="process" name="validation-process" type="text" class="form-control"> -->
                                                <label for="process">Proceso <span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Devengado</b></label>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="basicSalary" name="basicSalary" type="text" class="form-control text-center number">
                                                <label for="basicSalary">Salario Básico<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="transport" name="transport" type="text" class="form-control text-center number">
                                                <label for="transport">Transporte<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="endowment" name="endowment" type="text" class="form-control text-center number">
                                                <label for="endowment">Dotaciones<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-3">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="extraTime" name="extraTime" type="text" class="form-control text-center number">
                                                <label for="extraTime">Horas Extras<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-3">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="bonification" name="bonification" type="text" class="form-control text-center number">
                                                <label for="otherIncome">Otros Ingresos<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Jornada</b></label>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="workingHoursDay" name="workingHoursDay" type="number" class="form-control text-center">
                                                <label for="workingHoursDay">Horas Trabajo x Día<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="workingDaysMonth" name="workingDaysMonth" type="number" class="form-control text-center">
                                                <label for="workingDaysMonth">Dias Trabajo x Mes<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Factor Prestacional</b></label>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <select id="typeFactor" name="typeFactor" type="number" class="form-control">
                                                    <option selected disabled value="0" >Seleccionar</option>
                                                    <option value="1">Nómina</option>
                                                    <option value="2">Servicios</option>
                                                    <option value="3">Calculo Manual</option>
                                                </select>
                                                <label for="typeFactor">Tipo Nómina<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="factor" name="factor" type="number" class="form-control text-center">
                                                <label for="factor">Factor<span class="text-danger">*</span></label>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseCardPayroll">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnCreatePayroll">Crear</button>
            </div>
        </div>
    </div>
</div>