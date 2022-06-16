<?php

use tezlikv3\dao\UserInactiveTimeDao;

require_once(dirname(dirname(dirname(dirname(__DIR__)))) . "../../api/src/dao/app/cost/login/UserInactiveTimeDao.php");
$userinactivetimeDao = new UserInactiveTimeDao();
$userinactivetimeDao->findSession();
?>

<div class="page-title-box">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-5 col-xl-6">
                <div class="page-title">
                    <h3 class="mb-1 font-weight-bold text-dark">Máquinas</h3>
                    <ol class="breadcrumb mb-3 mb-md-0">
                        <li class="breadcrumb-item active">Creación de Máquinas</li>
                    </ol>
                </div>
            </div>
            <div class="col-sm-7 col-xl-6">
                <div class="form-inline justify-content-sm-end">
                    <button class="btn btn-warning" id="btnNewMachine" name="btnNewMachine">Nueva Máquina</button>
                    <button class="btn btn-info ml-3" id="btnImportNewMachines" name="btnNewImportMachines">Importar Máquinas</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardCreateMachines">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <!--  <div class="card-header">
                        <h5 class="card-title">Crear Máquina</h5>
                    </div> -->
                    <div class="card-body">
                        <form id="formCreateMachine">
                            <div class="gridx4cm">
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                    <!-- <input type="text" class="form-control" name="idMachine" id="idMachine" hidden> -->
                                    <input type="text" class="form-control" name="machine" id="machine">
                                    <label for="">Nombre</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                    <input type="text" class="form-control money text-center" name="cost" id="costMachine">
                                    <label for="">Precio</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                    <input type="text" class="form-control money text-center" name="residualValue" id="residualValue">
                                    <label for="">Valor Residual</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                    <input type="text" class="form-control number text-center" name="depreciationYears" id="depreciationYears">
                                    <label for="">Años Depreciación</label>
                                </div>
                            </div>
                            <div class="gridx4m mt-3">
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                    <input type="number" class="form-control text-center" name="hoursMachine" id="hoursMachine">
                                    <label for="">Horas de Trabajo</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                    <input type="number" class="form-control text-center" name="daysMachine" id="daysMachine">
                                    <label for="">Dias de Trabajo</label>
                                </div>
                                <!--<div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                    <input type="text" class="form-control money text-center" name="depreciationMinute" id="depreciationMinute">
                                    <label for="">Depreciación x Min</label>
                                </div>-->
                                <div style="margin-bottom:0px;margin-top:5px;">
                                    <button class="btn btn-success" id="btnCreateMachine">Crear Máquina</button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardImportMachines">
    <div class="container-fluid">
        <div class="row">
            <form id="formImportMachines" enctype="multipart/form-data">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body pt-3 pb-0">
                            <div class="gridx4ip">
                                <div class="form-group floating-label enable-floating-label show-label mt-3 drag-area" style="margin-top:0px!important">
                                    <input class="form-control" type="file" id="fileMachines" accept=".xls,.xlsx">
                                    <label for="formFile" class="form-label"> Importar Máquinas</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-success" id="btnImportMachines">Importar</button>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-info" id="btnDownloadImportsMachines">Descarga Formato</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- page content -->
<div class="page-content-wrapper mt--45">
    <div class="container-fluid">
        <!-- Row 5 -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Máquinas</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tblMachines">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../../app/cost/js/global/number.js"></script>
<script src="../../app/global/js/machines/tblMachines.js"></script>
<script src="../../app/global/js/machines/machines.js"></script>
<script src="../../app/cost/js/import/import.js"></script>
<script src="../../app/global/js/machines/importMachines.js"></script>
<script src="../../app/cost/js/import/file.js"></script>
<script src="../../app/cost/js/global/validateExt.js"></script>